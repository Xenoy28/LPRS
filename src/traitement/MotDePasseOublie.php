<?php

require_once __DIR__ . '/../garde.php';
require_once __DIR__ . '/../mail/Mail.php';
require_once __DIR__ . '/../repository/UtilisateurRepository.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../index.php?page=mdp_oublie');
    exit;
}

$retour = '../../index.php?page=mdp_oublie&erreur=';

if (!hash_equals($_SESSION['csrf_mdp'] ?? '', (string)($_POST['csrf'] ?? ''))) {
    header('Location: ' . $retour . urlencode('Session expirée, recharge la page et réessaie.'));
    exit;
}

$email = trim($_POST['email'] ?? '');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ' . $retour . urlencode('L\'adresse email n\'est pas valide.'));
    exit;
}

$repo = new UtilisateurRepository();
$user = $repo->getUtilisateurByEmail($email);
if ($user) {
    $jeton = $repo->creerJetonReinitialisation($user->getIdUtilisateur());
    $lien = urlSite() . '/index.php?page=reinitialiser&id=' . $user->getIdUtilisateur() . '&jeton=' . $jeton;
    Mail::envoyer($email, 'Réinitialisation de votre mot de passe',
        "Bonjour,\n\nPour choisir un nouveau mot de passe (lien valable 1 heure) :\n$lien\n\n"
        . "Si vous n'êtes pas à l'origine de cette demande, ignorez ce message.\n");
}

// même réponse que l'email existe ou non, pour ne pas révéler qui a un compte
header('Location: ../../index.php?page=login&succes=' . urlencode('Si cette adresse correspond à un compte, un email vient d\'être envoyé.'));
exit;
