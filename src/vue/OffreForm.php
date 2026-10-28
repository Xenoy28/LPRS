<?php

use repository\EntrepriseRepository;

require_once __DIR__ . '/../repository/OffreRepository.php';
require_once __DIR__ . '/../repository/EntrepriseRepository.php';
$repo = new OffreRepository();
$id = (int)($_GET['id'] ?? 0);
$o = $id ? $repo->getById($id) : new Offre(['profil_cible' => 'tous']);
if ($id && (!$o || (int)$o->id_auteur !== (int)$_SESSION['user_id'])) {
    header('Location: index.php?page=offres&erreur=' . urlencode('Seul l\'auteur peut modifier l\'offre.'));
    exit;
}
$mesEntreprises = (new EntrepriseRepository())->mesEntreprises($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $id ? 'Modifier' : 'Publier' ?> une offre</title>
</head>
<body>
<?php require __DIR__ . '/_menu.php'; ?>
<h1><?= $id ? 'Modifier l\'offre' : 'Publier une offre' ?></h1>

<?php if (!$mesEntreprises): ?>
    <p>Pour publier une offre, rattachez-vous d'abord à une entreprise (<a href="index.php?page=entreprises">voir les entreprises</a>).</p>
<?php else: ?>
    <form method="post" action="src/traitement/Offre.php">
        <input type="hidden" name="action" value="<?= $id ? 'modifier' : 'creer' ?>">
        <input type="hidden" name="id_offre" value="<?= $id ?>">
        <p>Entreprise
            <select name="id_entreprise">
                <?php foreach ($mesEntreprises as $e): ?>
                    <option value="<?= (int)$e->id_entreprise ?>" <?= (int)$e->id_entreprise === (int)$o->id_entreprise ? 'selected' : '' ?>><?= h($e->nom) ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>Titre <input type="text" name="titre" value="<?= h($o->titre) ?>" required></p>
        <p>Type
            <select name="type_offre">
                <?php foreach (OffreRepository::TYPES as $t): ?>
                    <option value="<?= $t ?>" <?= $t === $o->type_offre ? 'selected' : '' ?>><?= $t ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>Description<br><textarea name="description" rows="5" cols="60" required><?= h($o->description) ?></textarea></p>
        <p>Missions<br><textarea name="missions" rows="5" cols="60" required><?= h($o->missions) ?></textarea></p>
        <p>Salaire en € (facultatif) <input type="number" step="0.01" min="0" name="salaire" value="<?= h($o->salaire) ?>"></p>
        <p>Lieu <input type="text" name="lieu" value="<?= h($o->lieu) ?>"></p>
        <p>Date limite (facultatif) <input type="date" name="date_expiration" value="<?= h($o->date_expiration) ?>"></p>
        <p>Offre ouverte à
            <select name="profil_cible">
                <?php foreach (OffreRepository::CIBLES as $valeur => $libelle): ?>
                    <option value="<?= $valeur ?>" <?= $valeur === $o->profil_cible ? 'selected' : '' ?>><?= h($libelle) ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <p><button type="submit">Enregistrer</button></p>
    </form>
<?php endif; ?>
</body>
</html>
