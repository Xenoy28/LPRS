<?php
require_once __DIR__ . '/../repository/OffreRepository.php';
require_once __DIR__ . '/../repository/CandidatureRepository.php';
$repo = new OffreRepository();
$o = $repo->getById((int)($_GET['id'] ?? 0));
if (!$o || !$repo->peutConsulter($o, $_SESSION['user_id'], $_SESSION['user_role'])) {
    header('Location: index.php?page=offres&erreur=' . urlencode('Offre introuvable.'));
    exit;
}
$estAuteur = (int)$o->id_auteur === (int)$_SESSION['user_id'];
$peutPostuler = in_array($_SESSION['user_role'], ['etudiant', 'alumni']);
$dejaPostule = $peutPostuler && (new CandidatureRepository())->existe($o->id_offre, $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= h($o->titre) ?></title>
</head>
<body>
<?php require __DIR__ . '/_menu.php'; ?>
<h1><?= h($o->titre) ?></h1>

<p>
    <?= h($o->type_offre) ?> chez
    <?php if ($o->id_entreprise): ?><a href="index.php?page=entreprise&id=<?= (int)$o->id_entreprise ?>"><?= h($o->entreprise_nom) ?></a><?php endif; ?>
    — <strong><?= h($o->etat) ?></strong>
</p>
<p>Lieu : <?= h($o->lieu) ?> | Salaire : <?= $o->salaire !== null ? h($o->salaire) . ' €' : 'non précisé' ?></p>
<p>Publiée le <?= h($o->date_publication) ?><?= $o->date_expiration ? ', jusqu\'au ' . h($o->date_expiration) : '' ?>
    par <?= h($o->auteur_prenom . ' ' . $o->auteur_nom) ?></p>
<p>Public visé : <?= h(OffreRepository::CIBLES[$o->profil_cible] ?? $o->profil_cible) ?></p>

<h2>Description</h2>
<p><?= nl2br(h($o->description)) ?></p>
<h2>Missions</h2>
<p><?= nl2br(h($o->missions)) ?></p>

<?php if ($peutPostuler): ?>
    <h2>Postuler</h2>
    <?php if ($dejaPostule): ?>
        <p>Vous avez déjà postulé à cette offre.</p>
    <?php elseif ($o->etat !== 'Ouverte'): ?>
        <p>Cette offre est clôturée : il n'est plus possible de postuler.</p>
    <?php else: ?>
        <form method="post" action="src/traitement/Candidature.php">
            <input type="hidden" name="action" value="postuler">
            <input type="hidden" name="id_offre" value="<?= (int)$o->id_offre ?>">
            <p>Votre motivation<br><textarea name="motivation" rows="6" cols="60" required></textarea></p>
            <p><button type="submit">Envoyer ma candidature</button></p>
        </form>
    <?php endif; ?>
<?php endif; ?>

<?php if ($estAuteur): ?>
    <h2>Gestion de l'offre</h2>
    <p>
        <a href="index.php?page=offre_form&id=<?= (int)$o->id_offre ?>">Modifier</a> |
        <a href="index.php?page=candidatures&id=<?= (int)$o->id_offre ?>">Voir les candidatures</a>
    </p>
    <form method="post" action="src/traitement/Offre.php">
        <input type="hidden" name="action" value="etat">
        <input type="hidden" name="id_offre" value="<?= (int)$o->id_offre ?>">
        <?php if ($o->etat === 'Ouverte'): ?>
            <input type="hidden" name="etat" value="Clôturée">
            <button type="submit">Bloquer les candidatures (clôturer)</button>
        <?php else: ?>
            <input type="hidden" name="etat" value="Ouverte">
            <button type="submit">Rouvrir l'offre</button>
        <?php endif; ?>
    </form>
<?php endif; ?>

<p><a href="index.php?page=offres">← Retour aux offres</a></p>
</body>
</html>
