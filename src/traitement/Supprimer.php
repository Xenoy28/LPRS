<?php

session_start();
require_once __DIR__ . '/../repository/UtilisateurRepository.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['user_id'])) {
    header('Location: ../../index.php?page=login');
    exit;
}

$repo = new UtilisateurRepository();

if ($_SESSION['user_role'] === 'gestionnaire' && $repo->nombreGestionnaires() <= 1) {
    header('Location: ../../index.php?page=profil&erreur=' . urlencode('Impossible de supprimer le dernier gestionnaire.'));
    exit;
}

$repo->supprimer($_SESSION['user_id']);
session_destroy();
header('Location: ../../index.php');
exit;
