<?php
require_once __DIR__ . '/garde_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifierCsrf();
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'supprimer_post') {
        $bdd->prepare('DELETE FROM reponse WHERE id_post = :id')->execute(['id' => $id]);
        $bdd->prepare('DELETE FROM post WHERE id_post = :id')->execute(['id' => $id]);
        flash('ok', 'Post supprimé avec ses réponses.');
        redirige('forum.php');
    }
    if ($action === 'supprimer_reponse') {
        $bdd->prepare('DELETE FROM reponse WHERE id_reponse = :id')->execute(['id' => $id]);
        flash('ok', 'Réponse supprimée.');
    }
    redirige($_SERVER['REQUEST_URI']);
}

$idPost = (int)($_GET['post'] ?? 0);
$post = null;
$reponses = [];
$posts = [];

if ($idPost > 0) {
    $requete = $bdd->prepare(
        "SELECT p.*, c.nom AS canal, CONCAT(u.prenom, ' ', u.nom) AS auteur
         FROM post p
         LEFT JOIN canal_forum c ON c.id_canal = p.id_canal
         LEFT JOIN utilisateur u ON u.id_utilisateur = p.id_utilisateur
         WHERE p.id_post = :id"
    );
    $requete->execute(['id' => $idPost]);
    $post = $requete->fetch(PDO::FETCH_ASSOC) ?: null;

    $requete = $bdd->prepare(
        "SELECT r.*, CONCAT(u.prenom, ' ', u.nom) AS auteur
         FROM reponse r LEFT JOIN utilisateur u ON u.id_utilisateur = r.id_utilisateur
         WHERE r.id_post = :id ORDER BY r.date_publication"
    );
    $requete->execute(['id' => $idPost]);
    $reponses = $requete->fetchAll(PDO::FETCH_ASSOC);
}
if (!$post) {
    $posts = $bdd->query(
        "SELECT p.id_post, p.titre, p.date_publication, c.nom AS canal,
                CONCAT(u.prenom, ' ', u.nom) AS auteur,
                (SELECT COUNT(*) FROM reponse r WHERE r.id_post = p.id_post) AS nb_reponses
         FROM post p
         LEFT JOIN canal_forum c ON c.id_canal = p.id_canal
         LEFT JOIN utilisateur u ON u.id_utilisateur = p.id_utilisateur
         ORDER BY p.date_publication DESC LIMIT 200"
    )->fetchAll(PDO::FETCH_ASSOC);
}

$titrePage = 'Forum - modération';
require __DIR__ . '/entete.php';
?>
<?php if ($post): ?>
    <p><a class="bouton gris" href="forum.php">← Retour à la liste</a></p>
    <div class="carte">
        <h2><?= e($post['titre']) ?></h2>
        <p class="petit">Canal : <?= e($post['canal']) ?> · par <?= e($post['auteur']) ?> · <?= e($post['date_publication']) ?></p>
        <p><?= nl2br(e($post['contenu'])) ?></p>
        <form method="post" onsubmit="return confirm('Supprimer ce post et toutes ses réponses ?');"><?= csrfChamp() ?>
            <input type="hidden" name="id" value="<?= (int)$post['id_post'] ?>">
            <button class="rouge" name="action" value="supprimer_post">Supprimer le post</button>
        </form>
    </div>
    <h3>Réponses (<?= count($reponses) ?>)</h3>
    <?php foreach ($reponses as $r): ?>
        <div class="carte">
            <p class="petit"><?= e($r['auteur']) ?> · <?= e($r['date_publication']) ?></p>
            <p><?= nl2br(e($r['contenu'])) ?></p>
            <form method="post" onsubmit="return confirm('Supprimer cette réponse ?');"><?= csrfChamp() ?>
                <input type="hidden" name="id" value="<?= (int)$r['id_reponse'] ?>">
                <button class="rouge" name="action" value="supprimer_reponse">Supprimer</button>
            </form>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="defilement">
        <table>
            <tr><th>Titre</th><th>Canal</th><th>Auteur</th><th>Date</th><th>Réponses</th><th></th></tr>
            <?php if (!$posts): ?><tr><td colspan="6">Aucun post.</td></tr><?php endif; ?>
            <?php foreach ($posts as $p): ?>
                <tr>
                    <td><?= e($p['titre']) ?></td>
                    <td><?= e($p['canal']) ?></td>
                    <td><?= e($p['auteur']) ?></td>
                    <td><?= e($p['date_publication']) ?></td>
                    <td><?= (int)$p['nb_reponses'] ?></td>
                    <td><a class="bouton" href="forum.php?post=<?= (int)$p['id_post'] ?>">Ouvrir</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/pied.php'; ?>
