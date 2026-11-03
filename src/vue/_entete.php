<?php
// En-tête commun (barre du haut + début de page). Variable attendue : $titre
$connecte = !empty($_SESSION['user_id']);
$membre   = $connecte && in_array($_SESSION['user_role'], MEMBRES);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($titre) ?> - LPRS</title>
    <style>
        body { margin: 0; font-family: sans-serif; background: #f5f7f8; color: #161616; min-height: 100vh; display: flex; flex-direction: column; }

        .topbar { display: grid; grid-template-columns: 1fr 2fr 1fr; height: 60px; background: #0f4c5c; color: #fff; }
        .topbar > div { display: flex; align-items: center; padding: 0 20px; }
        .centre { justify-content: center; gap: 20px; }
        .droite { justify-content: flex-end; position: relative; }
        .centre a { color: #fff; text-decoration: none; }
        .burger { background: none; border: none; color: #fff; font-size: 30px; cursor: pointer; }
        .menu { display: none; position: absolute; top: 60px; right: 0; background: #fff; list-style: none; margin: 0; padding: 0; min-width: 180px; box-shadow: 0 4px 12px rgba(0,0,0,.2); z-index: 10; }
        .menu a { display: block; padding: 14px 20px; color: #222; text-decoration: none; }
        .menu a:hover { background: #e6eef0; }

        .page { flex: 1; width: 100%; max-width: 900px; margin: 0 auto; padding: 24px 20px 40px; box-sizing: border-box; }
        .fil { font-size: 13px; color: #666; margin-bottom: 16px; }
        .page a { color: #0f4c5c; }
        h1 { margin: 0 0 24px; padding-bottom: 12px; border-bottom: 3px solid #0f4c5c; }
        h2 { color: #0f4c5c; margin: 28px 0 10px; font-size: 1.25rem; }
        .maj { color: #666; font-size: 13px; }

        .grille { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; }
        .bloc { background: #fff; padding: 16px 20px; border-left: 4px solid #0f4c5c; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .bloc h2 { margin-top: 0; }
        .bloc ul { margin: 0; padding-left: 18px; line-height: 1.9; }

        label { display: block; font-weight: bold; margin-bottom: 4px; }
        input[type=text], input[type=email], textarea { width: 100%; padding: 10px; border: 1px solid #c9d3d6; box-sizing: border-box; font-family: inherit; font-size: 15px; margin-bottom: 14px; }
        .btn { background: #0f4c5c; color: #fff; border: none; padding: 10px 24px; font-size: 15px; cursor: pointer; }
        .erreur { background: #fde8e8; color: #9b1c1c; padding: 10px; margin-bottom: 14px; }
        .succes { background: #e6f4ea; color: #1e6b34; padding: 10px; margin-bottom: 14px; }
        .piege { position: absolute; left: -9999px; }

        .footer { background: #fff; border-top: 3px solid #0f4c5c; padding: 20px 30px; }
        .footer-liens { list-style: none; margin: 0; padding: 0; display: flex; flex-wrap: wrap; }
        .footer-liens li { padding: 0 16px; border-left: 1px solid #c9d3d6; }
        .footer-liens li:first-child { padding-left: 0; border-left: none; }
        .footer-liens a { color: #161616; font-size: 14px; text-decoration: none; }
        .footer-liens a:hover { text-decoration: underline; color: #0f4c5c; }
        .footer-copy { margin: 14px 0 0; font-size: 12px; color: #666; }
    </style>
</head>
<body>

<div class="topbar">
    <div>LPRS</div>

    <div class="centre">
        <a href="index.php">Accueil</a>
        <?php if ($membre): ?>
            <a href="index.php?page=offres">Offres</a>
            <a href="index.php?page=evenements">Événements</a>
            <a href="index.php?page=annuaire">Annuaire</a>
            <a href="index.php?page=forum">Forum</a>
        <?php endif; ?>
    </div>

    <div class="droite">
        <button class="burger" onclick="toggleMenu()">☰</button>
        <ul class="menu" id="menu">
            <?php if ($connecte): ?>
                <li><a href="index.php?page=profil">Profil</a></li>
                <?php if ($_SESSION['user_role'] === 'gestionnaire'): ?>
                    <li><a href="src/admin/dashboard.php">Tableau de bord</a></li>
                <?php endif; ?>
                <li><a href="src/traitement/Logout.php">Déconnexion</a></li>
            <?php else: ?>
                <li><a href="index.php?page=login">Se connecter</a></li>
                <li><a href="index.php?page=inscription">S'inscrire</a></li>
            <?php endif; ?>
            <li><a href="index.php?page=contact">Contact</a></li>
        </ul>
    </div>
</div>

<main class="page">
    <nav class="fil"><a href="index.php">Accueil</a> › <?= h($titre) ?></nav>
    <h1><?= h($titre) ?></h1>