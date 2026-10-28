<?php
require_once __DIR__ . '/../repository/EntrepriseRepository.php';
require_once __DIR__ . '/../repository/OffreRepository.php';
$repo = new EntrepriseRepository();
$e = $repo->getById((int)($_GET['id'] ?? 0));
if (!$e) {
    header('Location: index.php?page=entreprises&erreur=' . urlencode('Entreprise introuvable.'));
    exit;
}
$estMembre = $repo->estMembre($e->id_entreprise, $_SESSION['user_id']);
$peutRejoindre = in_array($_SESSION['user_role'], ['partenaire', 'alumni']);
$membres = $repo->membres($e->id_entreprise);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= h($e->nom) ?></title>
</head>
<body>
<?php require __DIR__ . '/_menu.php'; ?>
<h1><?= h($e->nom) ?></h1>

<p>Adresse : <?= h($e->adresse) ?> <?= h($e->code_postal) ?> <?= h($e->ville) ?></p>
<p>Site web : <a href="https://<?= h($e->site_web) ?>" target="_blank" rel="noopener"><?= h($e->site_web) ?></a></p>
<p>Secteur d'activité : <?= h($e->secteur_activite) ?></p>
<p><?= nl2br(h($e->description)) ?></p>

<h2>Membres</h2>
<ul>
    <?php foreach ($membres as $m): ?>
        <li><?= h($m['prenom'] . ' ' . $m['nom']) ?> — <?= h($m['poste']) ?> (<?= h($m['type_profil']) ?>)</li>
    <?php endforeach; ?>
    <?php if (!$membres): ?><li>Aucun membre.</li><?php endif; ?>
</ul>

<?php if ($estMembre): ?>
    <p><a href="index.php?page=entreprise_form&id=<?= (int)$e->id_entreprise ?>">Modifier la fiche</a></p>
    <form method="post" action="src/traitement/Entreprise.php" onsubmit="return confirm('Quitter cette entreprise ?');">
        <input type="hidden" name="action" value="quitter">
        <input type="hidden" name="id_entreprise" value="<?= (int)$e->id_entreprise ?>">
        <button type="submit">Quitter cette entreprise</button>
    </form>
<?php elseif ($peutRejoindre): ?>
    <h2>Rejoindre cette entreprise</h2>
    <form method="post" action="src/traitement/Entreprise.php">
        <input type="hidden" name="action" value="rattacher">
        <input type="hidden" name="id_entreprise" value="<?= (int)$e->id_entreprise ?>">
        <p>Votre poste <input type="text" name="poste" required></p>
        <p><button type="submit">Me rattacher</button></p>
    </form>
<?php endif; ?>
</body>
</html>
