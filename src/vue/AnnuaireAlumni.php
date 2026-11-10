<?php
require_once __DIR__ . '/../repository/UtilisateurRepository.php';
$nom = trim($_GET['nom'] ?? '');
$promotion = trim($_GET['promotion'] ?? '');
$secteur = trim($_GET['secteur'] ?? '');
$alumni = (new UtilisateurRepository())->annuaireAlumni($nom, $promotion, $secteur);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Annuaire des anciens élèves</title>
</head>
<body>
<?php require __DIR__ . '/_menu.php'; ?>
<h1>Annuaire des anciens élèves</h1>
<?php if ($_SESSION['user_role'] === 'professeur'): ?>
    <p><a href="index.php?page=annuaire_etudiants">Annuaire des étudiants actuels</a></p>
<?php endif; ?>

<form method="get" action="index.php">
    <input type="hidden" name="page" value="annuaire">
    <input type="text" name="nom" value="<?= h($nom) ?>" placeholder="Nom ou prénom">
    <input type="text" name="promotion" value="<?= h($promotion) ?>" placeholder="Promotion">
    <input type="text" name="secteur" value="<?= h($secteur) ?>" placeholder="Secteur d'activité">
    <button type="submit">Rechercher</button>
</form>

<table border="1" cellpadding="4">
    <tr><th>Nom</th><th>Prénom</th><th>Promotion</th><th>Entreprise(s)</th><th>Poste(s)</th><th>Secteur(s)</th></tr>
    <?php foreach ($alumni as $a): ?>
        <tr>
            <td><?= h($a['nom']) ?></td>
            <td><?= h($a['prenom']) ?></td>
            <td><?= h($a['promotion']) ?></td>
            <td><?= h($a['entreprises'] ?: $a['situation_professionnelle']) ?></td>
            <td><?= h($a['postes']) ?></td>
            <td><?= h($a['secteurs']) ?></td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$alumni): ?><tr><td colspan="6">Aucun résultat.</td></tr><?php endif; ?>
</table>
</body>
</html>
