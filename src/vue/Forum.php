<?php
require __DIR__ . '/../traitement/Forum.php';

function arreter($code, $message)
{
    http_response_code($code);
    exit($message);
}

$role = $_SESSION['user_role'];
$moi  = (int)$_SESSION['user_id'];

$canalRepo   = new CanalRepository();
$postRepo    = new PostRepository();
$reponseRepo = new ReponseRepository();

$id      = (int)($_GET['id'] ?? 0);
$c       = (int)($_GET['c'] ?? 0);
$nouveau = $id === 0 && isset($_GET['nouveau']);
$erreur  = '';

/* ---------- 1. Quelle page afficher ? ---------- */
if ($id > 0) {
    $post = $postRepo->parId($id) ?? arreter(404, 'Sujet introuvable.');
    $c = (int)$post->id_canal;
}
if ($c > 0) {
    $canal = $canalRepo->parId($c) ?? arreter(404, 'Section introuvable.');
    if (!$canal->peutAcceder($role)) {
        arreter(403, 'Accès refusé.');
    }
    if ($nouveau && !$canal->peutCreer($role)) {
        arreter(403, 'Tu ne peux pas créer de sujet dans cette section.');
    }
}
$page = $id > 0 ? 'sujet' : ($c > 0 ? ($nouveau ? 'nouveau' : 'section') : (isset($_GET['mes']) ? 'mes' : 'accueil'));

/* ---------- 2. Formulaires (nouveau sujet / réponse) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($page === 'nouveau' || $page === 'sujet')) {
    $titre   = trim($_POST['titre'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');

    if (!csrf_valide()) {
        $erreur = 'Session expirée, recharge la page et réessaie.';
    } elseif ($contenu === '' || ($page === 'nouveau' && $titre === '')) {
        $erreur = 'Tous les champs sont obligatoires.';
    } elseif (mb_strlen($titre) > LONGUEUR_TITRE || mb_strlen($contenu) > LONGUEUR_MESSAGE) {
        $erreur = 'Texte trop long (titre : ' . LONGUEUR_TITRE . ' caractères max, message : ' . LONGUEUR_MESSAGE . ' max).';
    } else {
        if ($page === 'nouveau') {
            $id = $postRepo->creer($c, $moi, $titre, $contenu);
        } else {
            $reponseRepo->creer($id, $moi, $contenu);
        }
        header('Location: ' . lien_forum(['id' => $id]) . ($page === 'sujet' ? '#bas' : ''));
        exit;
    }
}

/* ---------- 3. Données à afficher ---------- */
if ($page === 'accueil') {
    $canaux = array_filter($canalRepo->tous(), function ($ca) use ($role) {
        return $ca->peutAcceder($role);
    });
} elseif ($page === 'section') {
    $nb_pages = max(1, (int)ceil($postRepo->compterParCanal($c) / POSTS_PAR_PAGE));
    $numero   = min(max(1, (int)($_GET['p'] ?? 1)), $nb_pages);
    $liste    = $postRepo->listerParCanal($c, POSTS_PAR_PAGE, ($numero - 1) * POSTS_PAR_PAGE);
} elseif ($page === 'mes') {
    // Uniquement les sections auxquelles l'utilisateur a encore accès
    $liste = array_filter($postRepo->mesPosts($moi), function ($p) use ($role) {
        return (new Canal(['type_acces' => $p->type_acces]))->peutAcceder($role);
    });
} elseif ($page === 'sujet') {
    $reponses = $reponseRepo->listerParPost($id);
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
        .carte, .message { background: #fff; padding: 16px 20px; margin-bottom: 14px; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .carte { border-left: 4px solid #0f4c5c; }
        .carte h3 { margin: 0 0 6px; }
        .carte a { color: #0f4c5c; text-decoration: none; }
        .carte a:hover { text-decoration: underline; }
        .meta { color: #666; font-size: 13px; }
        .message .meta { margin-bottom: 8px; }
        .badge { background: #e6eef0; color: #0f4c5c; font-size: 12px; padding: 2px 8px; margin-left: 6px; }
        input[type=text], textarea { width: 100%; padding: 10px; border: 1px solid #c9d3d6; box-sizing: border-box; font-family: inherit; font-size: 15px; margin-bottom: 10px; }
        .btn { background: #0f4c5c; color: #fff; border: none; padding: 10px 20px; cursor: pointer; text-decoration: none; display: inline-block; }
        .erreur { background: #fde8e8; color: #9b1c1c; padding: 10px; margin-bottom: 14px; }
        .pagination a, .pagination strong { display: inline-block; padding: 6px 12px; margin-right: 6px; border: 1px solid #c9d3d6; background: #fff; color: #0f4c5c; text-decoration: none; }
        .pagination strong { background: #0f4c5c; color: #fff; border-color: #0f4c5c; }

        .footer { background: #fff; border-top: 3px solid #0f4c5c; padding: 20px 30px; }
        .footer-liens { list-style: none; margin: 0; padding: 0; display: flex; flex-wrap: wrap; }
        .footer-liens li { padding: 0 16px; border-left: 1px solid #c9d3d6; }
        .footer-liens li:first-child { padding-left: 0; border-left: none; }
        .footer-liens a { color: #161616; font-size: 14px; text-decoration: none; }
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
        <p><a class="btn" href="<?= e(lien_forum(['mes' => 1])) ?>">Mes posts</a></p>

        <?php if (!$canaux): ?><p>Aucune section disponible pour ton profil.</p><?php endif; ?>

        <?php foreach ($canaux as $ca): ?>
            <div class="carte">
                <h3><a href="<?= e(lien_forum(['c' => $ca->id_canal])) ?>"><?= e($ca->nom) ?></a></h3>
                <div><?= e($ca->description) ?></div>
                <div class="meta"><?= (int)$ca->nb_posts ?> sujet(s)</div>
            </div>
        <?php endforeach; ?>

    <?php elseif ($page === 'section' || $page === 'mes'): ?>

        <p><a href="<?= e(lien_forum()) ?>">← Retour au forum</a></p>

        <?php if ($page === 'mes'): ?>
            <h1>Mes posts</h1>
            <p class="meta">Les sujets que tu as écrits et ceux dans lesquels tu as répondu.</p>
        <?php else: ?>
            <h1><?= e($canal->nom) ?></h1>
            <?php if ($canal->peutCreer($role)): ?>
                <p><a class="btn" href="<?= e(lien_forum(['c' => $c, 'nouveau' => 1])) ?>">Nouveau sujet</a></p>
            <?php else: ?>
                <p class="meta">Tu peux répondre aux sujets, mais seuls certains profils peuvent en créer dans cette section.</p>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (!$liste): ?><p>Aucun sujet pour le moment.</p><?php endif; ?>

        <?php foreach ($liste as $p): ?>
            <div class="carte">
                <h3>
                    <a href="<?= e(lien_forum(['id' => $p->id_post])) ?>"><?= e($p->titre) ?></a>
                    <?php if ($page === 'mes'): ?>
                        <span class="badge"><?= (int)$p->id_utilisateur === $moi ? 'Mon sujet' : 'J\'ai répondu' ?></span>
                    <?php endif; ?>
                </h3>
                <div class="meta">
                    <?= $page === 'mes' ? e($p->canal_nom) . ' – ' : '' ?>Par <?= e($p->auteur) ?>
                    le <?= format_date($p->date_publication) ?> – <?= (int)$p->nb_reponses ?> réponse(s)
                </div>
            </div>
        <?php endforeach; ?>

        <?php if ($page === 'section' && $nb_pages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $nb_pages; $i++): ?>
                    <?php if ($i === $numero): ?>
                        <strong><?= $i ?></strong>
                    <?php else: ?>
                        <a href="<?= e(lien_forum(['c' => $c, 'p' => $i])) ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

    <?php elseif ($page === 'nouveau'): ?>

        <p><a href="<?= e(lien_forum(['c' => $c])) ?>">← Retour à <?= e($canal->nom) ?></a></p>
        <h1>Nouveau sujet</h1>
        <?php if ($erreur): ?><div class="erreur"><?= e($erreur) ?></div><?php endif; ?>

        <form method="post">
            <?= csrf_champ() ?>
            <input type="text" name="titre" placeholder="Titre du sujet" maxlength="<?= LONGUEUR_TITRE ?>" value="<?= e($_POST['titre'] ?? '') ?>" required>
            <textarea name="contenu" rows="8" placeholder="Ton message..." maxlength="<?= LONGUEUR_MESSAGE ?>" required><?= e($_POST['contenu'] ?? '') ?></textarea>
            <button class="btn" type="submit">Publier</button>
        </form>

    <?php elseif ($page === 'sujet'): ?>

        <p><a href="<?= e(lien_forum(['c' => $c])) ?>">← Retour à <?= e($canal->nom) ?></a></p>
        <h1><?= e($post->titre) ?></h1>

        <?php foreach (array_merge([$post], $reponses) as $m): ?>
            <div class="message">
                <div class="meta">
                    <strong><?= e($m->auteur) ?></strong><?= $m->type_profil ? ' (' . e($m->type_profil) . ')' : '' ?>
                    – <?= format_date($m->date_publication) ?>
                </div>
                <div><?= nl2br(e($m->contenu)) ?></div>
            </div>
        <?php endforeach; ?>

        <h3 id="bas">Répondre</h3>
        <?php if ($erreur): ?><div class="erreur"><?= e($erreur) ?></div><?php endif; ?>
        <form method="post">
            <?= csrf_champ() ?>
            <textarea name="contenu" rows="5" placeholder="Ta réponse..." maxlength="<?= LONGUEUR_MESSAGE ?>" required></textarea>
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