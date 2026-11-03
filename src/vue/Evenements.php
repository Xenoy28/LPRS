<?php
require_once __DIR__ . '/../repository/EvenementRepository.php';
$repo = new EvenementRepository();
$passes = !empty($_GET['passes']);
$evenements = $repo->listePublies($passes);
$organises = $repo->organisesPar($_SESSION['user_id']);
$inscriptions = $repo->inscritsPar($_SESSION['user_id']);
$date = function ($d) { return $d ? date('d/m/Y H:i', strtotime($d)) : ''; };
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Événements</title>
</head>
<body>
<?php require __DIR__ . '/_menu.php'; ?>
<h1>Événements</h1>
<p><a href="index.php?page=evenement_form">Proposer un événement</a> |
    <?php if ($passes): ?><a href="index.php?page=evenements">Événements à venir</a>
    <?php else: ?><a href="index.php?page=evenements&passes=1">Événements passés</a><?php endif; ?></p>

<table border="1" cellpadding="4">
    <tr><th>Date</th><th>Titre</th><th>Type</th><th>Lieu</th><th>Places</th></tr>
    <?php foreach ($evenements as $e): ?>
        <tr>
            <td><?= h($date($e->date_debut)) ?></td>
            <td><a href="index.php?page=evenement&id=<?= (int)$e->id_evenement ?>"><?= h($e->titre) ?></a></td>
            <td><?= h($e->type_evenement) ?></td>
            <td><?= h($e->lieu) ?></td>
            <td><?= $e->nombre_places === null ? 'illimité' : (int)$e->places_prises . ' / ' . (int)$e->nombre_places ?></td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$evenements): ?><tr><td colspan="5">Aucun événement.</td></tr><?php endif; ?>
</table>

<h2>Les événements que j'organise</h2>
<ul>
    <?php foreach ($organises as $e): ?>
        <li><a href="index.php?page=evenement&id=<?= (int)$e->id_evenement ?>"><?= h($e->titre) ?></a>
            (<?= h($date($e->date_debut)) ?>)<?= $e->publie ? '' : ' — en attente de publication (il manque un professeur)' ?></li>
    <?php endforeach; ?>
    <?php if (!$organises): ?><li>Aucun.</li><?php endif; ?>
</ul>

<h2>Mes inscriptions</h2>
<ul>
    <?php foreach ($inscriptions as $e): ?>
        <li><a href="index.php?page=evenement&id=<?= (int)$e->id_evenement ?>"><?= h($e->titre) ?></a>
            (<?= h($date($e->date_debut)) ?>) — <?= h($e->statut_inscription) ?></li>
    <?php endforeach; ?>
    <?php if (!$inscriptions): ?><li>Aucune.</li><?php endif; ?>
</ul>
</body>
</html>
