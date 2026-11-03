<?php
require_once __DIR__ . '/garde_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifierCsrf();
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    $champs = [
        'nom'         => trim($_POST['nom_formation'] ?? ''),
        'niveau'      => trim($_POST['niveau'] ?? ''),
        'duree'       => trim($_POST['duree'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'type'        => trim($_POST['type_formation'] ?? ''),
    ];

    if ($action === 'ajouter' || $action === 'modifier') {
        if ($champs['nom'] === '' || strlen($champs['nom']) > 100) {
            flash('erreur', 'Le nom de la formation est obligatoire (100 caractères max).');
            redirige($_SERVER['REQUEST_URI']);
        }
        if ($action === 'ajouter') {
            $bdd->prepare('INSERT INTO formation (nom_formation, niveau, duree, description, type_formation)
                           VALUES (:nom, :niveau, :duree, :description, :type)')->execute($champs);
            flash('ok', 'Formation ajoutée.');
        } else {
            $champs['id'] = $id;
            $bdd->prepare('UPDATE formation SET nom_formation = :nom, niveau = :niveau, duree = :duree,
                           description = :description, type_formation = :type WHERE id_formation = :id')->execute($champs);
            flash('ok', 'Formation modifiée.');
        }
        redirige('formations.php');
    }

    if ($action === 'supprimer') {
        $requete = $bdd->prepare('SELECT COUNT(*) FROM etudiant WHERE id_formation = :id');
        $requete->execute(['id' => $id]);
        if ($requete->fetchColumn() > 0) {
            flash('erreur', 'Impossible : des étudiants sont rattachés à cette formation.');
        } else {
            $bdd->prepare('DELETE FROM formation WHERE id_formation = :id')->execute(['id' => $id]);
            flash('ok', 'Formation supprimée.');
        }
        redirige('formations.php');
    }
    redirige('formations.php');
}

$edition = null;
if (isset($_GET['edit'])) {
    $requete = $bdd->prepare('SELECT * FROM formation WHERE id_formation = :id');
    $requete->execute(['id' => (int)$_GET['edit']]);
    $edition = $requete->fetch(PDO::FETCH_ASSOC) ?: null;
}
$formations = $bdd->query(
    'SELECT f.*, (SELECT COUNT(*) FROM etudiant e WHERE e.id_formation = f.id_formation) AS nb_etudiants
     FROM formation f ORDER BY f.type_formation, f.nom_formation'
)->fetchAll(PDO::FETCH_ASSOC);

$titrePage = 'Formations';
require __DIR__ . '/entete.php';
?>
<div class="carte">
    <h2><?= $edition ? 'Modifier la formation' : 'Ajouter une formation' ?></h2>
    <form method="post">
        <?= csrfChamp() ?>
        <input type="hidden" name="action" value="<?= $edition ? 'modifier' : 'ajouter' ?>">
        <input type="hidden" name="id" value="<?= (int)($edition['id_formation'] ?? 0) ?>">
        <div class="grille-form">
            <div><label>Nom *</label><input name="nom_formation" required maxlength="100" value="<?= e($edition['nom_formation'] ?? '') ?>"></div>
            <div><label>Type</label>
                <input name="type_formation" list="types" maxlength="50" value="<?= e($edition['type_formation'] ?? '') ?>">
                <datalist id="types"><option>Bac Pro</option><option>Bac Techno</option><option>BTS</option><option>3e Prépa-Pro</option></datalist>
            </div>
            <div><label>Niveau</label><input name="niveau" maxlength="50" value="<?= e($edition['niveau'] ?? '') ?>"></div>
            <div><label>Durée</label><input name="duree" maxlength="50" placeholder="3 ans" value="<?= e($edition['duree'] ?? '') ?>"></div>
        </div>
        <label>Description</label>
        <textarea name="description" rows="3"><?= e($edition['description'] ?? '') ?></textarea>
        <p>
            <button type="submit"><?= $edition ? 'Enregistrer' : 'Ajouter' ?></button>
            <?php if ($edition): ?><a class="bouton gris" href="formations.php">Annuler</a><?php endif; ?>
        </p>
    </form>
</div>

<div class="defilement">
    <table>
        <tr><th>Nom</th><th>Type</th><th>Niveau</th><th>Durée</th><th>Étudiants</th><th>Actions</th></tr>
        <?php foreach ($formations as $f): ?>
            <tr>
                <td><?= e($f['nom_formation']) ?></td>
                <td><?= e($f['type_formation']) ?></td>
                <td><?= e($f['niveau']) ?></td>
                <td><?= e($f['duree']) ?></td>
                <td><?= (int)$f['nb_etudiants'] ?></td>
                <td>
                    <a class="bouton" href="formations.php?edit=<?= (int)$f['id_formation'] ?>">Modifier</a>
                    <form method="post" class="en-ligne" onsubmit="return confirm('Supprimer cette formation ?');">
                        <?= csrfChamp() ?>
                        <input type="hidden" name="id" value="<?= (int)$f['id_formation'] ?>">
                        <button class="rouge" name="action" value="supprimer">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php require __DIR__ . '/pied.php'; ?>
