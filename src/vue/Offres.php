<?php
require_once __DIR__ . '/../repository/OffreRepository.php';
require_once __DIR__ . '/../repository/CandidatureRepository.php';
$repo = new OffreRepository();
$role = $_SESSION['user_role'];
$type = in_array($_GET['type'] ?? '', OffreRepository::TYPES) ? $_GET['type'] : '';
$q = trim($_GET['q'] ?? '');
$offres = $repo->listeOuvertes($role, $type, $q);
$peutPublier = in_array($role, ['partenaire', 'alumni']);
$mesOffres = $peutPublier ? $repo->listeGeree($_SESSION['user_id']) : [];
$mesCandidatures = in_array($role, ['etudiant', 'alumni']) ? (new CandidatureRepository())->listeParUtilisateur($_SESSION['user_id']) : [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Offres</title>
</head>
<body>
<?php require __DIR__ . '/_menu.php'; ?>
<h1>Offres d'emploi, de stage et d'alternance</h1>

<?php if ($peutPublier): ?>
    <p><a href="index.php?page=offre_form">Publier une offre</a></p>
<?php endif; ?>

<form method="get" action="index.php">
    <input type="hidden" name="page" value="offres">
    <input type="text" name="q" value="<?= h($q) ?>" placeholder="Mot-clé, entreprise…">
    <select name="type">
        <option value="">Tous les types</option>
        <?php foreach (OffreRepository::TYPES as $t): ?>
            <option value="<?= $t ?>" <?= $t === $type ? 'selected' : '' ?>><?= $t ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Filtrer</button>
</form>

<table border="1" cellpadding="4">
    <tr><th>Titre</th><th>Entreprise</th><th>Type</th><th>Lieu</th><th>Publiée le</th></tr>
    <?php foreach ($offres as $o): ?>
        <tr>
            <td><a href="index.php?page=offre&id=<?= (int)$o->id_offre ?>"><?= h($o->titre) ?></a></td>
            <td><?= h($o->entreprise_nom) ?></td>
            <td><?= h($o->type_offre) ?></td>
            <td><?= h($o->lieu) ?></td>
            <td><?= h($o->date_publication) ?></td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$offres): ?><tr><td colspan="5">Aucune offre ouverte.</td></tr><?php endif; ?>
</table>

<?php if ($peutPublier): ?>
    <h2>Les offres de mes entreprises</h2>
    <table border="1" cellpadding="4">
        <tr><th>Titre</th><th>Entreprise</th><th>Auteur</th><th>État</th><th></th></tr>
        <?php foreach ($mesOffres as $o): ?>
            <tr>
                <td><a href="index.php?page=offre&id=<?= (int)$o->id_offre ?>"><?= h($o->titre) ?></a></td>
                <td><?= h($o->entreprise_nom) ?></td>
                <td><?= h($o->auteur_prenom . ' ' . $o->auteur_nom) ?></td>
                <td><?= h($o->etat) ?></td>
                <td><?php if ((int)$o->id_auteur === (int)$_SESSION['user_id']): ?><a href="index.php?page=candidatures&id=<?= (int)$o->id_offre ?>">Candidatures</a><?php endif; ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$mesOffres): ?><tr><td colspan="5">Aucune offre.</td></tr><?php endif; ?>
    </table>
<?php endif; ?>

<?php if (in_array($role, ['etudiant', 'alumni'])): ?>
    <h2>Mes candidatures</h2>
    <ul>
        <?php foreach ($mesCandidatures as $c): ?>
            <li><a href="index.php?page=offre&id=<?= (int)$c->id_offre ?>"><?= h($c->offre_titre) ?></a>
                — <?= $c->statut === 'Refusée' ? 'non retenue' : h($c->statut) ?> (<?= h($c->date_candidature) ?>)</li>
        <?php endforeach; ?>
        <?php if (!$mesCandidatures): ?><li>Aucune candidature.</li><?php endif; ?>
    </ul>
<?php endif; ?>
</body>
</html>
