<?php
require_once __DIR__ . '/../repository/EntrepriseRepository.php';
$repo = new EntrepriseRepository();
$q = trim($_GET['q'] ?? '');
$entreprises = $repo->liste($q);
$mesEntreprises = $repo->mesEntreprises($_SESSION['user_id']);
$peutCreer = in_array($_SESSION['user_role'], ['partenaire', 'alumni']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Entreprises</title>
</head>
<body>
<?php require __DIR__ . '/_menu.php'; ?>
<h1>Entreprises partenaires</h1>

<?php if ($peutCreer): ?>
    <h2>Mes entreprises</h2>
    <?php if (!$mesEntreprises): ?>
        <p>Vous n'êtes rattaché à aucune entreprise : rejoignez une fiche existante ci-dessous ou créez-en une.</p>
    <?php endif; ?>
    <ul>
        <?php foreach ($mesEntreprises as $e): ?>
            <li><a href="index.php?page=entreprise&id=<?= (int)$e->id_entreprise ?>"><?= h($e->nom) ?></a> (<?= h($e->poste) ?>)</li>
        <?php endforeach; ?>
    </ul>
    <p><a href="index.php?page=entreprise_form">Créer une fiche entreprise</a></p>
<?php endif; ?>

<h2>Toutes les entreprises</h2>
<form method="get" action="index.php">
    <input type="hidden" name="page" value="entreprises">
    <input type="text" name="q" value="<?= h($q) ?>" placeholder="Nom de l'entreprise">
    <button type="submit">Rechercher</button>
</form>

<table border="1" cellpadding="4">
    <tr><th>Nom</th><th>Ville</th><th>Secteur</th><th>Site web</th><th>Membres</th></tr>
    <?php foreach ($entreprises as $e): ?>
        <tr>
            <td><a href="index.php?page=entreprise&id=<?= (int)$e->id_entreprise ?>"><?= h($e->nom) ?></a></td>
            <td><?= h($e->ville) ?></td>
            <td><?= h($e->secteur_activite) ?></td>
            <td><?= h($e->site_web) ?></td>
            <td><?= (int)$e->nombre_membres ?></td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$entreprises): ?><tr><td colspan="5">Aucune entreprise.</td></tr><?php endif; ?>
</table>
</body>
</html>
