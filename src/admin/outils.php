<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($texte)
{
    return htmlspecialchars((string)$texte, ENT_QUOTES, 'UTF-8');
}

function csrfToken()
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrfChamp()
{
    return '<input type="hidden" name="csrf" value="' . e(csrfToken()) . '">';
}

function verifierCsrf()
{
    $jeton = $_POST['csrf'] ?? '';
    if (!is_string($jeton) || !hash_equals($_SESSION['csrf'] ?? '', $jeton)) {
        http_response_code(403);
        exit('Requête invalide (CSRF).');
    }
}

function flash($type, $message)
{
    $_SESSION['flash'][] = ['type' => $type, 'msg' => $message];
}

function recupererFlash()
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function redirige($url)
{
    header('Location: ' . $url);
    exit;
}

function creerGestionnaire(PDO $bdd, $nom, $prenom, $email, $mdp, $mdpConfirm, $role = 'Gestionnaire')
{
    if ($nom === '' || $prenom === '' || $email === '' || $mdp === '') {
        return 'Tous les champs sont obligatoires.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "L'adresse email n'est pas valide.";
    }
    if (strlen($mdp) < 10) {
        return 'Le mot de passe doit contenir au moins 10 caractères.';
    }
    if ($mdp !== $mdpConfirm) {
        return 'Les mots de passe ne correspondent pas.';
    }

    $requete = $bdd->prepare('SELECT COUNT(*) FROM utilisateur WHERE email = :email');
    $requete->execute(['email' => $email]);
    if ($requete->fetchColumn() > 0) {
        return 'Cette adresse email est déjà utilisée.';
    }

    $bdd->prepare(
        "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, type_profil, statut_validation)
         VALUES (:nom, :prenom, :email, :mdp, 'gestionnaire', 'Validé')"
    )->execute([
        'nom' => $nom,
        'prenom' => $prenom,
        'email' => $email,
        'mdp' => password_hash($mdp, PASSWORD_DEFAULT),
    ]);
    $id = (int)$bdd->lastInsertId();

    $bdd->prepare('INSERT INTO gestionnaire (id_utilisateur, role) VALUES (:id, :role)')
        ->execute(['id' => $id, 'role' => $role]);

    return '';
}
function supprimerUtilisateur(PDO $bdd, $id)
{
    $id = (int)$id;
    $requetes = [
        'DELETE FROM reponse WHERE id_post IN (SELECT id_post FROM post WHERE id_utilisateur = ?)',
        'DELETE FROM reponse WHERE id_utilisateur = ?',
        'DELETE FROM post WHERE id_utilisateur = ?',
        'DELETE FROM candidature WHERE id_offre IN (SELECT id_offre FROM offre WHERE id_auteur = ?)',
        'DELETE FROM candidature WHERE id_utilisateur = ?',
        'DELETE FROM offre WHERE id_auteur = ?',
        'DELETE FROM inscription_evenement WHERE id_utilisateur = ?',
        'DELETE FROM organise WHERE id_utilisateur = ?',
        'DELETE FROM appartient_entreprise WHERE id_utilisateur = ?',
        'DELETE FROM etudiant WHERE id_utilisateur = ?',
        'DELETE FROM alumni WHERE id_utilisateur = ?',
        'DELETE FROM professeur WHERE id_utilisateur = ?',
        'DELETE FROM partenaire WHERE id_utilisateur = ?',
        'DELETE FROM gestionnaire WHERE id_utilisateur = ?',
        'DELETE FROM utilisateur WHERE id_utilisateur = ?',
    ];
    $bdd->beginTransaction();
    try {
        foreach ($requetes as $sql) {
            $bdd->prepare($sql)->execute([$id]);
        }
        $bdd->commit();
    } catch (Throwable $erreur) {
        if ($bdd->inTransaction()) {
            $bdd->rollBack();
        }
        throw $erreur;
    }
}
