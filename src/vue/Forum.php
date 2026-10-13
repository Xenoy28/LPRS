<?php
require __DIR__ . '/../traitement/Forum.php';

/** @var PDO $pdo */

$c       = (int)($_GET['c'] ?? 0);
$id      = (int)($_GET['id'] ?? 0);
$nouveau = isset($_GET['nouveau']);
$erreur  = '';
$canal   = null;
$post    = null;

/* ---------- 1. Quelle page afficher ? ---------- */
if ($id > 0) {
    $req = $pdo->prepare('
        SELECT p.id_post, p.titre, p.contenu, p.date_publication, p.id_canal,
               c.nom AS canal_nom, c.type_acces,
               u.prenom, u.nom AS auteur_nom, u.type_profil
        FROM post p
        JOIN canal_forum c ON c.id_canal = p.id_canal
        JOIN utilisateur u ON u.id_utilisateur = p.id_utilisateur
        WHERE p.id_post = ?
    ');
    $req->execute([$id]);
    $post = $req->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        http_response_code(404);
        exit('Sujet introuvable.');
    }
    if (!peut_acceder($post)) {
        http_response_code(403);
        exit('Accès refusé.');
    }
    $page = 'sujet';
} elseif ($c > 0) {
    $req = $pdo->prepare('SELECT id_canal, nom, description, type_acces FROM canal_forum WHERE id_canal = ?');
    $req->execute([$c]);
    $canal = $req->fetch(PDO::FETCH_ASSOC);

    if (!$canal) {
        http_response_code(404);
        exit('Section introuvable.');
    }
    if (!peut_acceder($canal)) {
        http_response_code(403);
        exit('Accès refusé.');
    }
    $page = $nouveau ? 'nouveau' : 'section';
} else {
    $page = 'accueil';
}

/* ---------- 2. Traitement des formulaires ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Création d'un sujet
    if ($page === 'nouveau') {
        $titre   = trim($_POST['titre'] ?? '');
        $contenu = trim($_POST['contenu'] ?? '');

        if ($titre === '' || $contenu === '') {
            $erreur = 'Le titre et le message sont obligatoires.';
        } elseif (mb_strlen($titre) > 150) {
            $erreur = 'Le titre est trop long (150 caractères max).';
        } else {
            $pdo->prepare('INSERT INTO post (id_canal, id_utilisateur, titre, contenu) VALUES (?, ?, ?, ?)')
                ->execute([$c, $_SESSION['user_id'], $titre, $contenu]);

            header('Location: Forum.php?id=' . $pdo->lastInsertId());
            exit;
        }
    }

    // Réponse dans un sujet
    if ($page === 'sujet') {
        $contenu = trim($_POST['contenu'] ?? '');

        if ($contenu === '') {
            $erreur = 'Le message ne peut pas être vide.';
        } else {
            $pdo->prepare('INSERT INTO reponse (id_post, id_utilisateur, contenu) VALUES (?, ?, ?)')
                ->execute([$id, $_SESSION['user_id'], $contenu]);
            header('Location: Forum.php?id=' . $id . '#bas');
            exit;
        }
    }
}

/* ---------- 3. Données à afficher ---------- */
if ($page === 'accueil') {
    $canaux = $pdo->query('SELECT id_canal, nom, description, type_acces FROM canal_forum ORDER BY id_canal')
        ->fetchAll(PDO::FETCH_ASSOC);
}

if ($page === 'section') {
    $req = $pdo->prepare('
        SELECT p.id_post, p.titre, p.date_publication, u.prenom, u.nom AS auteur_nom,
               (SELECT COUNT(*) FROM reponse r WHERE r.id_post = p.id_post) AS nb_reponses
        FROM post p
        JOIN utilisateur u ON u.id_utilisateur = p.id_utilisateur
        WHERE p.id_canal = ?
        ORDER BY p.date_publication DESC
    ');
    $req->execute([$c]);
    $posts = $req->fetchAll(PDO::FETCH_ASSOC);
}

if ($page === 'sujet') {
    $req = $pdo->prepare('
        SELECT r.contenu, r.date_publication, u.prenom, u.nom AS auteur_nom, u.type_profil
        FROM reponse r
        JOIN utilisateur u ON u.id_utilisateur = r.id_utilisateur
        WHERE r.id_post = ?
        ORDER BY r.date_publication ASC
    ');
    $req->execute([$id]);
    $reponses = $req->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Forum - LPRS</title>
    <style>
        body { margin: 0; font-family: sans-serif; background: #f5f7f8; min-height: 100vh; display: flex; flex-direction: column; }
        .topbar { display: grid; grid-template-columns: 1fr 2fr 1fr; height: 60px; background: #0f4c5c; color: #fff; }
        .topbar > div { display: flex; align-items: center; padding: 0 20px; }
        .centre { justify-content: center; gap: 20px; }
        .droite { justify-content: flex-end; position: relative; }
        .centre a { color: #fff; text-decoration: none; }
        .burger { background: none; border: none; color: #fff; font-size: 30px; cursor: pointer; }
        .menu { display: none; position: absolute; top: 60px; right: 0; background: #fff; list-style: none; margin: 0; padding: 0; min-width: 180px; box-shadow: 0 4px 12px rgba(0,0,0,.2); z-index: 10; }
        .menu a { display: block; padding: 14px 20px; color: #222; text-decoration: none; }
        .menu a:hover { background: #e6eef0; }

        .contenu { flex: 1; max-width: 900px; width: 100%; margin: 0 auto; padding: 30px 20px; box-sizing: border-box; }
        .carte { background: #fff; border-left: 4px solid #0f4c5c; padding: 16px 20px; margin-bottom: 14px; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .carte h3 { margin: 0 0 6px; }
        .carte a { color: #0f4c5c; text-decoration: none; }
        .carte a:hover { text-decoration: underline; }
        .meta { color: #666; font-size: 13px; }
        .message { background: #fff; padding: 14px 18px; margin-bottom: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .message .meta { margin-bottom: 8px; }
        input[type=text], textarea { width: 100%; padding: 10px; border: 1px solid #c9d3d6; box-sizing: border-box; font-family: inherit; font-size: 15px; margin-bottom: 10px; }
        .btn { background: #0f4c5c; color: #fff; border: none; padding: 10px 20px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #0b3a47; }
        .erreur { background: #fde8e8; color: #9b1c1c; padding: 10px; margin-bottom: 14px; }

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
        <a href="Accueil.php">Accueil</a>
        <a href="#">Services</a>
        <a href="Forum.php">Forum</a>
    </div>
    <div class="droite">
        <button class="burger" onclick="toggleMenu()">☰</button>
        <ul class="menu" id="menu">
            <li><a href="#">Profil</a></li>
            <li><a href="Login.php">Se connecter</a></li>
            <li><a href="../traitement/Logout.php">Déconnexion</a></li>
            <li><a href="#">Paramètres</a></li>
        </ul>
    </div>
</div>

<div class="contenu">

    <?php if ($page === 'accueil'): ?>

        <h1>Forum</h1>
        <?php foreach ($canaux as $ca):
            if (!peut_acceder($ca)) continue;
            $nb = $pdo->prepare('SELECT COUNT(*) FROM post WHERE id_canal = ?');
            $nb->execute([$ca['id_canal']]);
            ?>
            <div class="carte">
                <h3><a href="Forum.php?c=<?= (int)$ca['id_canal'] ?>"><?= e($ca['nom']) ?></a></h3>
                <div><?= e($ca['description']) ?></div>
                <div class="meta"><?= (int)$nb->fetchColumn() ?> sujet(s)</div>
            </div>
        <?php endforeach; ?>

    <?php elseif ($page === 'section'): ?>

        <p><a href="Forum.php">← Retour au forum</a></p>
        <h1><?= e($canal['nom']) ?></h1>
        <p><a class="btn" href="Forum.php?c=<?= (int)$c ?>&nouveau=1">Nouveau sujet</a></p>

        <?php if (!$posts): ?>
            <p>Aucun sujet pour le moment. Lance la discussion !</p>
        <?php endif; ?>

        <?php foreach ($posts as $p): ?>
            <div class="carte">
                <h3><a href="Forum.php?id=<?= (int)$p['id_post'] ?>"><?= e($p['titre']) ?></a></h3>
                <div class="meta">
                    Par <?= e($p['prenom'] . ' ' . $p['auteur_nom']) ?> le <?= date('d/m/Y à H:i', strtotime($p['date_publication'])) ?>
                    – <?= (int)$p['nb_reponses'] ?> réponse(s)
                </div>
            </div>
        <?php endforeach; ?>

    <?php elseif ($page === 'nouveau'): ?>

        <p><a href="Forum.php?c=<?= (int)$c ?>">← Retour à <?= e($canal['nom']) ?></a></p>
        <h1>Nouveau sujet</h1>

        <?php if ($erreur): ?><div class="erreur"><?= e($erreur) ?></div><?php endif; ?>

        <form method="post">
            <input type="text" name="titre" placeholder="Titre du sujet" maxlength="150" value="<?= e($_POST['titre'] ?? '') ?>" required>
            <textarea name="contenu" rows="8" placeholder="Ton message..." required><?= e($_POST['contenu'] ?? '') ?></textarea>
            <button class="btn" type="submit">Publier</button>
        </form>

    <?php elseif ($page === 'sujet'): ?>

        <p><a href="Forum.php?c=<?= (int)$post['id_canal'] ?>">← Retour à <?= e($post['canal_nom']) ?></a></p>
        <h1><?= e($post['titre']) ?></h1>

        <div class="message">
            <div class="meta">
                <strong><?= e($post['prenom'] . ' ' . $post['auteur_nom']) ?></strong> (<?= e($post['type_profil']) ?>)
                – <?= date('d/m/Y à H:i', strtotime($post['date_publication'])) ?>
            </div>
            <div><?= nl2br(e($post['contenu'])) ?></div>
        </div>

        <?php foreach ($reponses as $r): ?>
            <div class="message">
                <div class="meta">
                    <strong><?= e($r['prenom'] . ' ' . $r['auteur_nom']) ?></strong> (<?= e($r['type_profil']) ?>)
                    – <?= date('d/m/Y à H:i', strtotime($r['date_publication'])) ?>
                </div>
                <div><?= nl2br(e($r['contenu'])) ?></div>
            </div>
        <?php endforeach; ?>

        <h3 id="bas">Répondre</h3>
        <?php if ($erreur): ?><div class="erreur"><?= e($erreur) ?></div><?php endif; ?>
        <form method="post">
            <textarea name="contenu" rows="5" placeholder="Ta réponse..." required></textarea>
            <button class="btn" type="submit">Envoyer</button>
        </form>

    <?php endif; ?>

</div>

<footer class="footer">
    <ul class="footer-liens">
        <li><a href="#">Plan du site</a></li>
        <li><a href="#">Mentions légales</a></li>
        <li><a href="#">Contact</a></li>
    </ul>
    <p class="footer-copy">© 2026 LPRS – Tous droits réservés</p>
</footer>

<script>
    function toggleMenu() {
        const menu = document.getElementById("menu");
        menu.style.display = menu.style.display === "block" ? "none" : "block";
    }
</script>
</body>
</html>