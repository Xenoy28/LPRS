<?php

use repository\CandidatureRepository;

require_once __DIR__ . '/../repository/OffreRepository.php';
require_once __DIR__ . '/../repository/CandidatureRepository.php';
$offre = (new OffreRepository())->getById((int)($_GET['id'] ?? 0));
if (!$offre || (int)$offre->id_auteur !== (int)$_SESSION['user_id']) {
    header('Location: index.php?page=offres&erreur=' . urlencode('Seul l\'auteur de l\'offre peut voir les candidatures.'));
    exit;
}
$candidatures = (new CandidatureRepository())->listeParOffre($offre->id_offre);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Candidatures</title>
</head>
<body>
<?php require __DIR__ . '/_menu.php'; ?>
<h1>Candidatures : <?= h($offre->titre) ?></h1>
<p><a href="index.php?page=offre&id=<?= (int)$offre->id_offre ?>">← Retour à l'offre</a></p>

<?php foreach ($candidatures as $c): ?>
    <fieldset>
        <legend><?= h($c->prenom . ' ' . $c->nom) ?> (<?= h($c->type_profil) ?>) — <?= h($c->date_candidature) ?></legend>
        <p>Email : <?= h($c->email) ?></p>
        <p><?= nl2br(h($c->motivation)) ?></p>
        <form method="post" action="src/traitement/Candidature.php" onsubmit="return confirm('Refuser cette candidature ? Le candidat sera prévenu par email.');">
            <input type="hidden" name="action" value="refuser">
            <input type="hidden" name="id_candidature" value="<?= (int)$c->id_candidature ?>">
            <button type="submit">Refuser</button>
        </form>
    </fieldset>
<?php endforeach; ?>
<?php if (!$candidatures): ?><p>Aucune candidature.</p><?php endif; ?>
</body>
</html>
