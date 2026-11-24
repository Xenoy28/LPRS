<?php

require_once __DIR__ . '/../garde.php';
require_once __DIR__ . '/../repository/UtilisateurRepository.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../index.php?page=login');
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$jeton = (string)($_POST['jeton'] ?? '');
$mdp = $_POST['mot_de_passe'] ?? '';
$mdpConfirm = $_POST['mot_de_passe_confirm'] ?? '';
$retour = '../../index.php?page=reinitialiser&id=' . $id . '&jeton=' . urlencode($jeton) . '&erreur=';

if (!hash_equals($_SESSION['csrf_mdp'] ?? '', (string)($_POST['csrf'] ?? ''))) {
    header('Location: ' . $retour . urlencode('Session expirée, recharge la page et réessaie.'));
    exit;
}

$repo = new UtilisateurRepository();

if (!$repo->jetonValide($id, $jeton)) {
    header('Location: ../../index.php?page=login&erreur=' . urlencode('Ce lien est invalide ou a expiré. Refaites une demande.'));
    exit;
}
if (strlen($mdp) < 6) {
    header('Location: ' . $retour . urlencode('Le mot de passe doit contenir au moins 6 caractères.'));
    exit;
}
if ($mdp !== $mdpConfirm) {
    header('Location: ' . $retour . urlencode('Les mots de passe ne correspondent pas.'));
    exit;
}

$repo->reinitialiserMotDePasse($id, password_hash($mdp, PASSWORD_DEFAULT));
header('Location: ../../index.php?page=login&succes=' . urlencode('Mot de passe modifié, vous pouvez vous connecter.'));
exit;
