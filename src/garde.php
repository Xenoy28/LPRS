<?php
// Session + contrôle d'accès des pages "membres" (étudiant, alumni, partenaire, professeur).
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
const MEMBRES = ['etudiant', 'alumni', 'partenaire', 'professeur'];
function h($texte)
{
    return htmlspecialchars((string)$texte, ENT_QUOTES, 'UTF-8');
}
function exiger(array $roles = [], $racine = '../../')
{
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . $racine . 'index.php?page=login&erreur=' . urlencode('Veuillez vous connecter.'));
        exit;
    }
    if ($roles && !in_array($_SESSION['user_role'], $roles, true)) {
        header('Location: ' . $racine . 'index.php?erreur=' . urlencode('Accès refusé pour votre profil.'));
        exit;
    }
}
function urlSite()
{
    $schema = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $schema . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME'], 3), '/');
}
