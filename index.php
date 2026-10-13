<?php
session_start();

$page = $_GET['page'] ?? 'accueil';

switch ($page) {
    case 'login':
        require __DIR__ . '/src/vue/Login.php';
        break;
    case 'inscription':
        require __DIR__ . '/src/vue/Inscription.php';
        break;
    case 'profil':
        if (empty($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        require __DIR__ . '/src/vue/Profil.php';
        break;
    default:
        require __DIR__ . '/src/vue/Accueil.php';
}
