<?php

$connecte = !empty($_SESSION['user_id']);
$membre   = $connecte && in_array($_SESSION['user_role'], MEMBRES);
$xRole    = $_SESSION['user_role'] ?? '';
$xTitre   = $titreOnglet ?? $titre ?? 'LPRS';

if (!function_exists('icone_x')) {
    function icone_x($nom)
    {
        static $formes = [
            'home'      => '<path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/>',
            'offres'    => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>',
            'entreprise'=> '<rect x="4" y="3" width="16" height="18" rx="1"/><path d="M9 7h2M13 7h2M9 11h2M13 11h2M9 15h2M13 15h2"/>',
            'agenda'    => '<rect x="3" y="4" width="18" height="17" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
            'annuaire'  => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.6 2.9-6 6.5-6s6.5 2.4 6.5 6"/><path d="M16 4.6a3.5 3.5 0 0 1 0 6.8M18 14.3c2 .7 3.5 2.5 3.5 5.7"/>',
            'forum'     => '<path d="M21 12a8 8 0 0 1-11.6 7.1L3 21l1.9-5.4A8 8 0 1 1 21 12z"/>',
            'profil'    => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7"/>',
            'connexion' => '<path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/>',
            'inscrire'  => '<circle cx="9" cy="8" r="4"/><path d="M2 21c0-4 3.1-6.5 7-6.5s7 2.5 7 6.5M19 8v6M16 11h6"/>',
            'contact'   => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
            'admin'     => '<rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/>',
            'sortie'    => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>',
        ];
        return '<svg class="ico" viewBox="0 0 24 24" aria-hidden="true">' . ($formes[$nom] ?? '') . '</svg>';
    }
}

$xPage  = $_GET['page'] ?? 'accueil';
$xAlias = [
    'offre' => 'offres', 'offre_form' => 'offres', 'candidatures' => 'offres',
    'evenement' => 'evenements', 'evenement_form' => 'evenements',
    'entreprise' => 'entreprises', 'entreprise_form' => 'entreprises',
    'annuaire_etudiants' => 'annuaire', 'mdp_oublie' => 'login', 'reinitialiser' => 'login',
];
$xActif = $xAlias[$xPage] ?? $xPage;

$xNav = [['accueil', 'index.php', 'Accueil', 'home']];
if ($membre) {
    $xNav[] = ['offres',      'index.php?page=offres',      'Offres',      'offres'];
    $xNav[] = ['entreprises', 'index.php?page=entreprises', 'Entreprises', 'entreprise'];
    $xNav[] = ['evenements',  'index.php?page=evenements',  'Événements',  'agenda'];
    $xNav[] = ['annuaire',    'index.php?page=annuaire',    'Annuaire',    'annuaire'];
    $xNav[] = ['forum',       'index.php?page=forum',       'Forum',       'forum'];
}
if ($connecte) {
    $xNav[] = ['profil', 'index.php?page=profil', 'Profil', 'profil'];
    if ($xRole === 'gestionnaire') {
        $xNav[] = ['admin', 'src/admin/dashboard.php', 'Tableau de bord', 'admin'];
    }
} else {
    $xNav[] = ['login',       'index.php?page=login',       'Se connecter', 'connexion'];
    $xNav[] = ['inscription', 'index.php?page=inscription', "S'inscrire",   'inscrire'];
}
$xNav[] = ['contact', 'index.php?page=contact', 'Contact', 'contact'];
if ($connecte) {
    $xNav[] = ['deconnexion', 'src/traitement/Logout.php', 'Déconnexion', 'sortie'];
}

$xLibelleRole = ['etudiant' => 'Étudiant', 'alumni' => 'Alumni', 'partenaire' => 'Partenaire',
                 'professeur' => 'Professeur', 'gestionnaire' => 'Gestionnaire'];
$xCss = 'assets/css/style.css?v=' . (int)@filemtime(__DIR__ . '/../../assets/css/style.css');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#000000">
    <title><?= h($xTitre) ?> - LPRS</title>
    <link rel="stylesheet" href="<?= h($xCss) ?>">
</head>
<body>

<div class="x-layout">

    <header class="x-nav">
        <a class="x-logo" href="index.php" aria-label="LPRS - Accueil">
            <span class="x-logo-pastille">L</span><span class="x-logo-texte">LPRS</span>
        </a>

        <nav class="x-menu" aria-label="Navigation principale">
            <?php foreach ($xNav as $xLien): ?>
                <a class="x-lien<?= $xLien[0] === $xActif ? ' actif' : '' ?>"
                   href="<?= h($xLien[1]) ?>" title="<?= h($xLien[2]) ?>"
                   <?= $xLien[0] === $xActif ? 'aria-current="page"' : '' ?>>
                    <?= icone_x($xLien[3]) ?><span class="x-lien-texte"><?= h($xLien[2]) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <?php if ($connecte): ?>
            <a class="x-compte" href="index.php?page=profil" title="Mon profil">
                <span class="x-avatar"><?= h(mb_substr($_SESSION['user_nom'] ?? '?', 0, 1)) ?></span>
                <span class="x-compte-texte">
                    <span class="x-compte-nom"><?= h($_SESSION['user_nom'] ?? '') ?></span>
                    <span class="x-compte-role"><?= h($xLibelleRole[$xRole] ?? $xRole) ?></span>
                </span>
            </a>
        <?php endif; ?>
    </header>

    <main class="x-col">
        <div class="x-tete"><h1><?= h($xTitre) ?></h1></div>

        <div class="x-corps">
            <?php if (empty($sansFil)): ?>
                <nav class="fil" aria-label="Fil d'Ariane"><a href="index.php">Accueil</a> › <?= h($xTitre) ?></nav>
            <?php endif; ?>
            <?php if (!empty($_GET['succes'])): ?><div class="succes"><?= h($_GET['succes']) ?></div><?php endif; ?>
            <?php if (!empty($_GET['erreur'])): ?><div class="erreur"><?= h($_GET['erreur']) ?></div><?php endif; ?>