<?php
$pageCourante = basename($_SERVER['SCRIPT_NAME']);
$menu = [
    'dashboard.php'     => 'Tableau de bord',
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
    <style>
        body { margin: 0; font-family: system-ui, sans-serif; background: #f5f7f8; color: #222; }
        header { background: #0f4c5c; color: #fff; display: flex; flex-wrap: wrap; align-items: center; gap: 8px 20px; padding: 10px 20px; }
        header .logo { font-weight: bold; font-size: 18px; }
        header nav { display: flex; flex-wrap: wrap; gap: 4px; flex: 1; }
        header nav a, header .quitter { color: #fff; text-decoration: none; padding: 8px 12px; border-radius: 4px; }
        header nav a:hover, header .quitter:hover { background: rgba(255,255,255,.15); }
        header nav a.actif { background: rgba(255,255,255,.25); }
        .pastille { background: #e63946; border-radius: 10px; padding: 1px 7px; font-size: 12px; margin-left: 4px; }
        main { max-width: 1200px; margin: 24px auto; padding: 0 16px; }
        h1 { margin-top: 0; }
        .carte { background: #fff; border-radius: 8px; padding: 16px 20px; box-shadow: 0 1px 4px rgba(0,0,0,.1); margin-bottom: 20px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 20px; }
        .stats .carte { margin: 0; text-align: center; }
        .stats .nombre { font-size: 32px; font-weight: bold; color: #0f4c5c; }
        .defilement { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { text-align: left; padding: 8px 10px; border-bottom: 1px solid #e3e8ea; vertical-align: top; font-size: 14px; }
        th { background: #e6eef0; }
        form.en-ligne { display: inline; }
        input, select, textarea { padding: 7px 9px; border: 1px solid #b8c4c8; border-radius: 4px; font: inherit; box-sizing: border-box; }
        textarea { width: 100%; }
        label { display: block; margin: 10px 0 4px; font-weight: 600; }
        .filtres { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
        button, .bouton { padding: 7px 12px; border: none; border-radius: 4px; cursor: pointer; background: #0f4c5c; color: #fff; text-decoration: none; font: inherit; font-size: 14px; display: inline-block; }
        button.vert { background: #2a9d57; } button.rouge { background: #c0392b; } button.gris, .bouton.gris { background: #6c7a80; }
        .badge { padding: 2px 8px; border-radius: 10px; font-size: 12px; color: #fff; white-space: nowrap; }
        .badge.attente { background: #e08a00; } .badge.valide { background: #2a9d57; } .badge.refuse { background: #c0392b; } .badge.neutre { background: #6c7a80; }
        .message { padding: 10px 14px; border-radius: 4px; margin-bottom: 16px; }
        .message.ok { background: #d8f3e3; color: #14532d; } .message.erreur { background: #fde0dd; color: #7f1d1d; }
        .grille-form { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0 16px; }
        .petit { color: #6c7a80; font-size: 12px; }
    </style>
</head>
<body>
<header>
    <span class="logo">LPRS · Admin</span>
    <nav>
        <?php foreach ($menu as $fichier => $libelle): ?>
            <a href="<?= e($fichier) ?>" class="<?= $pageCourante === $fichier ? 'actif' : '' ?>">
                <?= e($libelle) ?>
                <?php if ($fichier === 'utilisateurs.php' && $nbAttente > 0): ?>
                    <span class="pastille"><?= $nbAttente ?></span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </nav>
    <span><?= e($adminConnecte['prenom'] . ' ' . $adminConnecte['nom']) ?></span>
    <a class="quitter" href="../traitement/Logout.php">Déconnexion</a>
</header>
<main>
    <h1><?= e($titrePage ?? 'Administration') ?></h1>
    <?php foreach (recupererFlash() as $f): ?>
        <div class="message <?= e($f['type']) ?>"><?= e($f['msg']) ?></div>
    <?php endforeach; ?>
