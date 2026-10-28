<?php

session_start();
require_once __DIR__ . '/../repository/UtilisateurRepository.php';
require_once __DIR__ . '/../repository/FormationRepository.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['user_id'])) {
    header('Location: ../../index.php?page=login');
    exit;
}

$repo = new UtilisateurRepository();
$user = $repo->getById($_SESSION['user_id']);
if (!$user) { // compte supprimé : on déconnecte
    header('Location: Logout.php');
    exit;
}

$nom = trim($_POST['nom'] ?? '');
$prenom = trim($_POST['prenom'] ?? '');
$email = trim($_POST['email'] ?? '');
$nouveauMdp = $_POST['nouveau_mot_de_passe'] ?? '';
$erreur = '';
$details = $user->getDetails();
$formationsChoisies = [];

if (empty($nom) || empty($prenom) || empty($email)) {
    $erreur = 'Le nom, le prénom et l\'email sont obligatoires.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreur = 'L\'adresse email n\'est pas valide.';
} elseif ($repo->emailExiste($email, $user->getIdUtilisateur())) {
    $erreur = 'Cette adresse email est déjà utilisée.';
} elseif ($nouveauMdp !== '' && strlen($nouveauMdp) < 6) {
    $erreur = 'Le mot de passe doit contenir au moins 6 caractères.';
} elseif ($user->getTypeProfil() !== 'gestionnaire') {
    foreach (UtilisateurRepository::COLONNES[$user->getTypeProfil()] as $champ) {
        $details[$champ] = trim($_POST[$champ] ?? '');
        if ($details[$champ] === '') {
            $erreur = 'Tous les champs obligatoires doivent être remplis.';
        }
    }
    if ($user->getTypeProfil() === 'professeur') {
        $formationsChoisies = array_map('intval', (array)($_POST['formations'] ?? []));
        if (!$formationsChoisies) {
            $erreur = 'Choisissez au moins une formation.';
        }
    }
}

if ($erreur === '') {
    $user->setNomUtilisateur($nom);
    $user->setPrenomUtilisateur($prenom);
    $user->setEmailUtilisateur($email);
    if ($nouveauMdp !== '') {
        $user->setMdpUtilisateur(password_hash($nouveauMdp, PASSWORD_DEFAULT));
    }
    $user->setDetails($details);
    $repo->modifier($user);
    if ($user->getTypeProfil() === 'professeur') {
        (new FormationRepository())->definirPourProfesseur($user->getIdUtilisateur(), $formationsChoisies);
    }
    $_SESSION['user_nom'] = $prenom . ' ' . $nom;
    header('Location: ../../index.php?page=profil&succes=' . urlencode('Profil mis à jour.'));
    exit;
}

header('Location: ../../index.php?page=profil&erreur=' . urlencode($erreur));
exit;
