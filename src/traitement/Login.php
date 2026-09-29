<?php


session_start();
require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Utilisateur.php';
require_once __DIR__ . '/../repository/UtilisateurRepository.php';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mdp = $_POST['mot_de_passe'] ?? '';

    if (empty($email) || empty($mdp)) {
        $erreur = 'Veuillez renseigner votre email et votre mot de passe.';
    } else {
        $repo = new UtilisateurRepository();
        $user = $repo->getUtilisateurByEmail($email);

        if (!$user || !password_verify($mdp, $user->getMotDePasse())) {
            $erreur = 'Email ou mot de passe incorrect.';
        } elseif (!$user->isActif()) {
            $erreur = 'Votre compte est bloqué. Veuillez contacter l\'accueil.';
        } else {
            $_SESSION['user_id'] = $user->getIdUtilisateur();
            $_SESSION['user_nom'] = $user->getNomComplet();
            $_SESSION['user_role'] = $user->getRole();
            $_SESSION['user_email'] = $user->getEmail();

            switch ($user->getRole()) {
                case 'administrateur':
                    header('Location: ../../index.php?page=admin');
                    break;
                case 'accueil':
                    header('Location: ../../index.php?page=accueil');
                    break;
                default:
                    header('Location: ../../index.php?page=client');
            }
            exit;
        }
    }
}

$_SESSION['login_erreur'] = $erreur;
header('Location: ../../index.php?page=login&erreur=' . urlencode($erreur));
exit;