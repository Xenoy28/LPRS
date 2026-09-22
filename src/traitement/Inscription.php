<?php

session_start();
require_once __DIR__ . '/../bdd/Bdd.php';
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mdp = $_POST['mot_de_passe'] ?? '';
    $mdpConfirm = $_POST['mot_de_passe_confirm'] ?? '';
    $telephone = trim($_POST['telephone'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $dateNaissance = $_POST['date_naissance'] ?? '';
    $role = trim($_POST['role'] ?? '');

    if (empty($nom) || empty($prenom) || empty($email) || empty($mdp) || empty($telephone) || empty($adresse) || empty($dateNaissance||empty($role))) {
        $erreur = 'Tous les champs obligatoires doivent être remplis.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = 'L\'adresse email n\'est pas valide.';
    } elseif (strlen($mdp) < 6) {
        $erreur = 'Le mot de passe doit contenir au moins 6 caractères.';
    } elseif ($mdp !== $mdpConfirm) {
        $erreur = 'Les mots de passe ne correspondent pas.';
    } else {
        $repo = new UtilisateurRepository();
        if ($repo->emailExiste($email)) {
            $erreur = 'Cette adresse email est déjà utilisée.';
        } else {
            $user = new Utilisateur(null, $nom, $prenom, $email,
                password_hash($mdp, PASSWORD_DEFAULT),
                $telephone, $adresse, $dateNaissance,$role);
            $id = $repo->inscrireClient($user);
            $_SESSION['user_id'] = $id;
            $_SESSION['user_nom'] = $prenom . ' ' . $nom;
            $_SESSION['user_role'] = $role;
            $_SESSION['user_email'] = $email;
            header('Location: ../../index.php?page=client&success=inscription');
            exit;
        }
    }
}

header('Location: ../../index.php?page=inscription&erreur=' . urlencode($erreur));
exit;