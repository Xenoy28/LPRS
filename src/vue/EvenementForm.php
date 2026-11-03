<?php
require_once __DIR__ . '/../repository/EvenementRepository.php';
$repo = new EvenementRepository();
$id = (int)($_GET['id'] ?? 0);
$e = $id ? $repo->getById($id) : new Evenement();
if ($id && (!$e || !$repo->estOrganisateur($id, $_SESSION['user_id']))) {
    header('Location: index.php?page=evenements&erreur=' . urlencode('Seuls les organisateurs peuvent modifier l\'événement.'));
    exit;
}
// "2026-11-05 14:30:00" => "2026-11-05T14:30" (champ datetime-local)
$champDate = function ($d) { return $d ? date('Y-m-d\TH:i', strtotime($d)) : ''; };
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $id ? 'Modifier' : 'Proposer' ?> un événement</title>
</head>
<body>
<?php require __DIR__ . '/_menu.php'; ?>
<h1><?= $id ? 'Modifier l\'événement' : 'Proposer un événement' ?></h1>
<?php if (!$id && $_SESSION['user_role'] === 'etudiant'): ?>
    <p>Un événement créé par un étudiant n'est publié que si un professeur fait partie des organisateurs : vous pourrez en ajouter un après la création.</p>
<?php endif; ?>

<form method="post" action="src/traitement/Evenement.php">
    <input type="hidden" name="action" value="<?= $id ? 'modifier' : 'creer' ?>">
    <input type="hidden" name="id_evenement" value="<?= $id ?>">
    <p>Titre <input type="text" name="titre" value="<?= h($e->titre) ?>" required></p>
    <p>Type d'événement (libre) <input type="text" name="type_evenement" value="<?= h($e->type_evenement) ?>" required></p>
    <p>Description<br><textarea name="description" rows="5" cols="60" required><?= h($e->description) ?></textarea></p>
    <p>Début <input type="datetime-local" name="date_debut" value="<?= h($champDate($e->date_debut)) ?>" required></p>
    <p>Fin (facultatif) <input type="datetime-local" name="date_fin" value="<?= h($champDate($e->date_fin)) ?>"></p>
    <p>Lieu (adresse) <input type="text" name="lieu" value="<?= h($e->lieu) ?>" required></p>
    <p>Nombre de places (vide = illimité) <input type="number" min="1" name="nombre_places" value="<?= h($e->nombre_places) ?>"></p>
    <p>Éléments requis (si nécessaire)<br><textarea name="elements_requis" rows="3" cols="60"><?= h($e->elements_requis) ?></textarea></p>
    <p><button type="submit">Enregistrer</button></p>
</form>
</body>
</html>
