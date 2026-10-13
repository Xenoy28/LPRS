<?php
require_once __DIR__ . '/garde_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifierCsrf();
    if (($_POST['action'] ?? '') === 'supprimer') {
        $id = (int)($_POST['id'] ?? 0);
        $bdd->prepare('DELETE FROM inscription_evenement WHERE id_evenement = :id')->execute(['id' => $id]);
        $bdd->prepare('DELETE FROM organise WHERE id_evenement = :id')->execute(['id' => $id]);
        $bdd->prepare('DELETE FROM evenement WHERE id_evenement = :id')->execute(['id' => $id]);
        flash('ok', 'Événement supprimé.');
    }
    redirige($_SERVER['REQUEST_URI']);
}

$evenements = $bdd->query(
    "SELECT ev.id_evenement, ev.titre, ev.type_evenement, ev.date_debut, ev.lieu, ev.nombre_places,
            (SELECT COUNT(*) FROM inscription_evenement i WHERE i.id_evenement = ev.id_evenement) AS nb_inscrits,
            (SELECT GROUP_CONCAT(CONCAT(u.prenom, ' ', u.nom) SEPARATOR ', ')
               FROM organise o JOIN utilisateur u ON u.id_utilisateur = o.id_utilisateur
              WHERE o.id_evenement = ev.id_evenement) AS organisateurs
     FROM evenement ev
     ORDER BY ev.date_debut DESC LIMIT 200"
)->fetchAll(PDO::FETCH_ASSOC);

$titrePage = 'Événements';
require __DIR__ . '/entete.php';
?>
<div class="defilement">
    <table>
        <tr><th>Titre</th><th>Type</th><th>Date</th><th>Lieu</th><th>Inscrits / places</th><th>Organisateurs</th><th></th></tr>
        <?php if (!$evenements): ?><tr><td colspan="7">Aucun événement.</td></tr><?php endif; ?>
        <?php foreach ($evenements as $ev): ?>
            <tr>
                <td><?= e($ev['titre']) ?></td>
                <td><?= e($ev['type_evenement']) ?></td>
                <td><?= e($ev['date_debut']) ?></td>
                <td><?= e($ev['lieu']) ?></td>
                <td><?= (int)$ev['nb_inscrits'] ?> / <?= e($ev['nombre_places'] ?? '∞') ?></td>
                <td class="petit"><?= e($ev['organisateurs']) ?></td>
                <td>
                    <form method="post" class="en-ligne" onsubmit="return confirm('Supprimer cet événement et ses inscriptions ?');"><?= csrfChamp() ?>
                        <input type="hidden" name="id" value="<?= (int)$ev['id_evenement'] ?>">
                        <button class="rouge" name="action" value="supprimer">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php require __DIR__ . '/pied.php'; ?>
