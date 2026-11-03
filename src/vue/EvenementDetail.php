<?php
require_once __DIR__ . '/../repository/EvenementRepository.php';
$repo = new EvenementRepository();
$e = $repo->getById((int)($_GET['id'] ?? 0));
$idUser = $_SESSION['user_id'];
$estOrganisateur = $e && $repo->estOrganisateur($e->id_evenement, $idUser);
// un événement non publié n'est visible que par ses organisateurs
if (!$e || (!$e->publie && !$estOrganisateur)) {
    header('Location: index.php?page=evenements&erreur=' . urlencode('Événement introuvable.'));
    exit;
}
$organisateurs = $repo->organisateurs($e->id_evenement);
$inscription = $repo->inscription($e->id_evenement, $idUser);
$termine = strtotime($e->date_debut) < time();
$complet = $e->nombre_places !== null && $e->places_prises >= $e->nombre_places;
$date = function ($d) { return $d ? date('d/m/Y H:i', strtotime($d)) : ''; };
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= h($e->titre) ?></title>
</head>
<body>
<?php require __DIR__ . '/_menu.php'; ?>
<h1><?= h($e->titre) ?></h1>

<?php if (!$e->publie): ?>
    <p><strong>Non publié : un professeur doit faire partie des organisateurs pour que l'événement apparaisse sur le site.</strong></p>
<?php endif; ?>

<p>Type : <?= h($e->type_evenement) ?></p>
<p>Du <?= h($date($e->date_debut)) ?><?= $e->date_fin ? ' au ' . h($date($e->date_fin)) : '' ?></p>
<p>Lieu : <?= h($e->lieu) ?></p>
<p>Places : <?= $e->nombre_places === null ? 'illimité' : (int)$e->places_prises . ' / ' . (int)$e->nombre_places ?></p>
<p><?= nl2br(h($e->description)) ?></p>
<?php if ($e->elements_requis): ?><p>À prévoir : <?= nl2br(h($e->elements_requis)) ?></p><?php endif; ?>

<h2>Organisateurs</h2>
<ul>
    <?php foreach ($organisateurs as $o): ?>
        <li><?= h($o['prenom'] . ' ' . $o['nom']) ?> (<?= h($o['type_profil']) ?>)
            <?php if ($estOrganisateur && count($organisateurs) > 1): ?>
                <form method="post" action="src/traitement/Evenement.php" style="display:inline">
                    <input type="hidden" name="action" value="retirer">
                    <input type="hidden" name="id_evenement" value="<?= (int)$e->id_evenement ?>">
                    <input type="hidden" name="id_utilisateur" value="<?= (int)$o['id_utilisateur'] ?>">
                    <button type="submit">Retirer</button>
                </form>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>

<?php if ($e->publie): ?>
    <h2>Inscription</h2>
    <?php if ($inscription): ?>
        <p>Votre inscription : <strong><?= h($inscription['statut']) ?></strong></p>
        <?php if ($inscription['statut'] === 'Inscrit'): ?>
            <form method="post" action="src/traitement/Evenement.php">
                <input type="hidden" name="action" value="annuler">
                <input type="hidden" name="id_evenement" value="<?= (int)$e->id_evenement ?>">
                <button type="submit">Annuler mon inscription</button>
            </form>
        <?php endif; ?>
    <?php elseif ($termine): ?>
        <p>Cet événement est terminé ou déjà commencé.</p>
    <?php elseif ($complet): ?>
        <p>Complet.</p>
    <?php else: ?>
        <form method="post" action="src/traitement/Evenement.php">
            <input type="hidden" name="action" value="inscrire">
            <input type="hidden" name="id_evenement" value="<?= (int)$e->id_evenement ?>">
            <button type="submit">M'inscrire</button>
        </form>
    <?php endif; ?>
<?php endif; ?>

<?php if ($estOrganisateur): ?>
    <h2>Gestion de l'événement</h2>
    <p><a href="index.php?page=evenement_form&id=<?= (int)$e->id_evenement ?>">Modifier la fiche</a></p>

    <h3>Ajouter un organisateur</h3>
    <form method="post" action="src/traitement/Evenement.php">
        <input type="hidden" name="action" value="organisateur">
        <input type="hidden" name="id_evenement" value="<?= (int)$e->id_evenement ?>">
        Email du membre <input type="email" name="email" required>
        <button type="submit">Ajouter</button>
    </form>

    <h3>Inscriptions</h3>
    <table border="1" cellpadding="4">
        <tr><th>Nom</th><th>Profil</th><th>Date</th><th>Statut</th><th></th></tr>
        <?php foreach ($repo->inscrits($e->id_evenement) as $i): ?>
            <tr>
                <td><?= h($i['prenom'] . ' ' . $i['nom']) ?></td>
                <td><?= h($i['type_profil']) ?></td>
                <td><?= h($i['date_inscription']) ?></td>
                <td><?= h($i['statut']) ?></td>
                <td>
                    <?php if ($i['statut'] === 'Inscrit'): ?>
                        <form method="post" action="src/traitement/Evenement.php">
                            <input type="hidden" name="action" value="refuser">
                            <input type="hidden" name="id_inscription" value="<?= (int)$i['id_inscription'] ?>">
                            <button type="submit">Refuser</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<p><a href="index.php?page=evenements">← Retour aux événements</a></p>
</body>
</html>