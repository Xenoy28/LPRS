<?php

session_start();
require_once __DIR__ . '/../repository/UtilisateurRepository.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../index.php?page=inscription');
    exit;
}

$nom = trim($_POST['nom'] ?? '');
$prenom = trim($_POST['prenom'] ?? '');
$email = trim($_POST['email'] ?? '');
$mdp = $_POST['mot_de_passe'] ?? '';
$mdpConfirm = $_POST['mot_de_passe_confirm'] ?? '';
$role = trim($_POST['role'] ?? '');
$erreur = '';
$details = [];

if (empty($nom) || empty($prenom) || empty($email) || empty($mdp) || empty($role)) {
    $erreur = 'Tous les champs obligatoires doivent être remplis.';
} elseif (!in_array($role, ['etudiant', 'alumni', 'partenaire', 'professeur'])) {
    $erreur = 'Profil invalide.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreur = 'L\'adresse email n\'est pas valide.';
} elseif (strlen($mdp) < 6) {
    $erreur = 'Le mot de passe doit contenir au moins 6 caractères.';
} elseif ($mdp !== $mdpConfirm) {
    $erreur = 'Les mots de passe ne correspondent pas.';
} else {
    // champs propres au profil : tous obligatoires
    foreach (UtilisateurRepository::COLONNES[$role] as $champ) {
        $details[$champ] = trim($_POST[$champ] ?? '');
        if ($details[$champ] === '') {
            $erreur = 'Tous les champs obligatoires doivent être remplis.';
        }
    }
}

if ($erreur === '') {
    $repo = new UtilisateurRepository();
    if ($repo->emailExiste($email)) {
        $erreur = 'Cette adresse email est déjà utilisée.';
    } else {
        // compte "En attente" : un gestionnaire doit le valider avant la connexion
        $user = new Utilisateur(null, $nom, $prenom, $email, password_hash($mdp, PASSWORD_DEFAULT),
            $role, 'En attente', $details);
        $repo->inscrire($user);
        header('Location: ../../index.php?page=login&succes=' . urlencode('Inscription enregistrée, en attente de validation par un gestionnaire.'));
        exit;
    }
}

header('Location: ../../index.php?page=inscription&erreur=' . urlencode($erreur));
exit;
