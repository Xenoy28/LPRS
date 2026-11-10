<?php
require_once __DIR__ . '/../repository/UtilisateurRepository.php';
require_once __DIR__ . '/../repository/FormationRepository.php';
$nom = trim($_GET['nom'] ?? '');
$promotion = trim($_GET['promotion'] ?? '');
$idFormation = (int)($_GET['formation'] ?? 0);
$etudiants = (new UtilisateurRepository())->annuaireEtudiants($nom, $promotion, $idFormation);
$formations = (new FormationRepository())->liste();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Annuaire des étudiants</title>
</head>
<body>
<?php require __DIR__ . '/_menu.php'; ?>
<h1>Annuaire des étudiants actuels</h1>
<p><a href="index.php?page=annuaire">Annuaire des anciens élèves</a></p>

<form method="get" action="index.php">
    <input type="hidden" name="page" value="annuaire_etudiants">
    <input type="text" name="nom" value="<?= h($nom) ?>" placeholder="Nom ou prénom">
    <input type="text" name="promotion" value="<?= h($promotion) ?>" placeholder="Promotion">
    <select name="formation">
        <option value="0">Toutes les formations</option>
        <?php foreach ($formations as $f): ?>
            <option value="<?= (int)$f['id_formation'] ?>" <?= $idFormation === (int)$f['id_formation'] ? 'selected' : '' ?>><?= h($f['nom_formation']) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Rechercher</button>
</form>

<table border="1" cellpadding="4">
    <tr><th>Nom</th><th>Prénom</th><th>Promotion</th><th>Formation</th><th>CV</th></tr>
    <?php foreach ($etudiants as $et): ?>
        <tr>
            <td><?= h($et['nom']) ?></td>
            <td><?= h($et['prenom']) ?></td>
            <td><?= h($et['promotion']) ?></td>
            <td><?= h($et['nom_formation']) ?></td>
            <td><?= h($et['cv']) ?></td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$etudiants): ?><tr><td colspan="5">Aucun résultat.</td></tr><?php endif; ?>
</table>
</body>
</html>
