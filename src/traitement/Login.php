<?php

session_start();
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

        if (!$user || !password_verify($mdp, $user->getMdpUtilisateur())) {
            $erreur = 'Email ou mot de passe incorrect.';
        } elseif ($user->getStatusValidation() === 'En attente') {
            $erreur = 'Votre inscription est en attente de validation par un gestionnaire.';
        } elseif ($user->getStatusValidation() !== 'Validé') {
            $erreur = 'Votre inscription a été refusée.';
        } else {
            $_SESSION['user_id'] = $user->getIdUtilisateur();
            $_SESSION['user_nom'] = $user->getPrenomUtilisateur() . ' ' . $user->getNomUtilisateur();
            $_SESSION['user_role'] = $user->getTypeProfil();
            $_SESSION['user_email'] = $user->getEmailUtilisateur();

            if ($user->getTypeProfil() === 'gestionnaire') {
                header('Location: ../admin/dashboard.php');
            } else {
                header('Location: ../../index.php?page=profil');
            }
            exit;
        }
    }
}

header('Location: ../../index.php?page=login&erreur=' . urlencode($erreur));
exit;
