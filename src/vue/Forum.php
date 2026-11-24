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
    $titreOnglet = $page === 'sujet' ? $post->titre
        : ($page === 'nouveau' ? 'Nouveau sujet'
            : ($page === 'section' ? $canal->nom
                : ($page === 'mes' ? 'Mes posts' : 'Forum')));
    $sansFil = true;
    require __DIR__ . '/_entete.php';
    ?>

    <?php if ($page === 'accueil'): ?>

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
            <p class="meta">Les sujets que tu as écrits et ceux dans lesquels tu as répondu.</p>
        <?php else: ?>
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
        <?php if ($erreur): ?><div class="erreur"><?= e($erreur) ?></div><?php endif; ?>

        <form method="post">
            <?= csrf_champ() ?>
            <input type="text" name="titre" placeholder="Titre du sujet" maxlength="<?= LONGUEUR_TITRE ?>" value="<?= e($_POST['titre'] ?? '') ?>" required>
            <textarea name="contenu" rows="8" placeholder="Ton message..." maxlength="<?= LONGUEUR_MESSAGE ?>" required><?= e($_POST['contenu'] ?? '') ?></textarea>
            <button class="btn" type="submit">Publier</button>
        </form>

    <?php elseif ($page === 'sujet'): ?>

        <p><a href="<?= e(lien_forum(['c' => $c])) ?>">← Retour à <?= e($canal->nom) ?></a></p>

        <?php foreach (array_merge([$post], $reponses) as $m): ?>
            <div class="publication">
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

<?php require __DIR__ . '/_pied.php'; ?>
