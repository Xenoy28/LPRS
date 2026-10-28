<?php
require_once __DIR__ . '/../repository/EntrepriseRepository.php';
$repo = new EntrepriseRepository();
$id = (int)($_GET['id'] ?? 0);
$e = $id ? $repo->getById($id) : new Entreprise();
if ($id && (!$e || !$repo->estMembre($id, $_SESSION['user_id']))) {
    header('Location: index.php?page=entreprises&erreur=' . urlencode('Vous ne pouvez pas modifier cette fiche.'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $id ? 'Modifier' : 'Créer' ?> une entreprise</title>
</head>
<body>
<?php require __DIR__ . '/_menu.php'; ?>
<h1><?= $id ? 'Modifier la fiche entreprise' : 'Créer une fiche entreprise' ?></h1>

<form method="post" action="src/traitement/Entreprise.php">
    <input type="hidden" name="action" value="<?= $id ? 'modifier' : 'creer' ?>">
    <input type="hidden" name="id_entreprise" value="<?= $id ?>">
    <p>Nom <input type="text" name="nom" value="<?= h($e->nom) ?>" required></p>
    <p>Adresse <input type="text" name="adresse" value="<?= h($e->adresse) ?>" required></p>
    <p>Code postal <input type="text" name="code_postal" value="<?= h($e->code_postal) ?>"></p>
    <p>Ville <input type="text" name="ville" value="<?= h($e->ville) ?>"></p>
    <p>Site web (contrôle des doublons) <input type="text" name="site_web" value="<?= h($e->site_web) ?>" required></p>
    <p>Secteur d'activité <input type="text" name="secteur_activite" value="<?= h($e->secteur_activite) ?>"></p>
    <p>Description <textarea name="description"><?= h($e->description) ?></textarea></p>
    <?php if (!$id): ?>
        <p>Votre poste dans l'entreprise <input type="text" name="poste" required></p>
    <?php endif; ?>
    <p><button type="submit">Enregistrer</button></p>
</form>
</body>
</html>
