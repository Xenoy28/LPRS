<?php
$pageCourante = basename($_SERVER['SCRIPT_NAME']);
$menu = [
    'dashboard.php'     => 'Tableau de bord',
    'validation.php'    => 'Validation',
    'utilisateurs.php'  => 'Utilisateurs',
    'gestionnaires.php' => 'Gestionnaires',
    'formations.php'    => 'Formations',
    'offres.php'        => 'Offres',
    'evenements.php'    => 'Événements',
    'forum.php'         => 'Forum',
];
$nbAttente = (int)$bdd->query("SELECT COUNT(*) FROM utilisateur WHERE statut_validation = 'En attente'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titrePage ?? 'Administration') ?> - LPRS</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<header class="admin-entete">
    <span class="logo">LPRS · Admin</span>
    <nav>
        <?php foreach ($menu as $fichier => $libelle): ?>
            <a href="<?= e($fichier) ?>" class="<?= $pageCourante === $fichier ? 'actif' : '' ?>">
                <?= e($libelle) ?>
                <?php if ($fichier === 'validation.php' && $nbAttente > 0): ?>
                    <span class="pastille"><?= $nbAttente ?></span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </nav>
    <span class="utilisateur"><?= e($adminConnecte['prenom'] . ' ' . $adminConnecte['nom']) ?></span>
    <a class="quitter" href="../traitement/Logout.php">Déconnexion</a>
</header>
<main class="admin-contenu">
    <h1><?= e($titrePage ?? 'Administration') ?></h1>
    <?php foreach (recupererFlash() as $f): ?>
        <div class="message <?= e($f['type']) ?>"><?= e($f['msg']) ?></div>
    <?php endforeach; ?>

