<?php
require_once __DIR__ . '/garde_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifierCsrf();
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    switch ($action) {
        case 'cloturer':
            $bdd->prepare("UPDATE offre SET etat = 'Clôturée' WHERE id_offre = :id")->execute(['id' => $id]);
            flash('ok', 'Offre clôturée.');
            break;
        case 'rouvrir':
            $bdd->prepare("UPDATE offre SET etat = 'Ouverte' WHERE id_offre = :id")->execute(['id' => $id]);
            flash('ok', 'Offre rouverte.');
            break;
        case 'supprimer':
            $bdd->prepare('DELETE FROM candidature WHERE id_offre = :id')->execute(['id' => $id]);
            $bdd->prepare('DELETE FROM offre WHERE id_offre = :id')->execute(['id' => $id]);
            flash('ok', 'Offre supprimée.');
            break;
    }
    redirige($_SERVER['REQUEST_URI']);
}

$fEtat = $_GET['etat'] ?? '';
$where = '';
$params = [];
if (in_array($fEtat, ['Ouverte', 'Clôturée'], true)) {
    $where = 'WHERE o.etat = :etat';
    $params['etat'] = $fEtat;
}
$requete = $bdd->prepare(
    "SELECT o.id_offre, o.titre, o.type_offre, o.etat, o.date_publication,
            en.nom AS entreprise, CONCAT(u.prenom, ' ', u.nom) AS auteur,
            (SELECT COUNT(*) FROM candidature c WHERE c.id_offre = o.id_offre) AS nb_candidatures
     FROM offre o
     LEFT JOIN entreprise en ON en.id_entreprise = o.id_entreprise
     LEFT JOIN utilisateur u ON u.id_utilisateur = o.id_auteur
     $where
     ORDER BY o.id_offre DESC LIMIT 200"
);
$requete->execute($params);
$offres = $requete->fetchAll(PDO::FETCH_ASSOC);

$titrePage = 'Offres d\'emploi / stage';
require __DIR__ . '/entete.php';
?>
<form method="get" class="filtres">
    <select name="etat">
        <option value="">Toutes</option>
        <option value="Ouverte" <?= $fEtat === 'Ouverte' ? 'selected' : '' ?>>Ouvertes</option>
        <option value="Clôturée" <?= $fEtat === 'Clôturée' ? 'selected' : '' ?>>Clôturées</option>
    </select>
    <button type="submit">Filtrer</button>
</form>

<div class="defilement">
    <table>
        <tr><th>Titre</th><th>Type</th><th>Entreprise</th><th>Auteur</th><th>Publiée le</th><th>Candidatures</th><th>État</th><th>Actions</th></tr>
        <?php if (!$offres): ?><tr><td colspan="8">Aucune offre.</td></tr><?php endif; ?>
        <?php foreach ($offres as $o): ?>
            <tr>
                <td><?= e($o['titre']) ?></td>
                <td><?= e($o['type_offre']) ?></td>
                <td><?= e($o['entreprise']) ?></td>
                <td><?= e($o['auteur']) ?></td>
                <td><?= e($o['date_publication']) ?></td>
                <td><?= (int)$o['nb_candidatures'] ?></td>
                <td><span class="badge <?= $o['etat'] === 'Ouverte' ? 'valide' : 'neutre' ?>"><?= e($o['etat']) ?></span></td>
                <td>
                    <form method="post" class="en-ligne"><?= csrfChamp() ?>
                        <input type="hidden" name="id" value="<?= (int)$o['id_offre'] ?>">
                        <?php if ($o['etat'] === 'Ouverte'): ?>
                            <button class="gris" name="action" value="cloturer">Clôturer</button>
                        <?php else: ?>
                            <button class="vert" name="action" value="rouvrir">Rouvrir</button>
                        <?php endif; ?>
                    </form>
                    <form method="post" class="en-ligne" onsubmit="return confirm('Supprimer cette offre et ses candidatures ?');"><?= csrfChamp() ?>
                        <input type="hidden" name="id" value="<?= (int)$o['id_offre'] ?>">
                        <button class="rouge" name="action" value="supprimer">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php require __DIR__ . '/pied.php'; ?>
