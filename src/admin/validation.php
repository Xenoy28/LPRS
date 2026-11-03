<?php
require_once __DIR__ . '/garde_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifierCsrf();
    $id = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($id > 0 && in_array($action, ['valider', 'refuser'], true)) {
        $nouveauStatut = $action === 'valider' ? 'Validé' : 'Refusé';
        // on ne modifie que les comptes encore en attente
        $requete = $bdd->prepare(
            "UPDATE utilisateur SET statut_validation = :statut
             WHERE id_utilisateur = :id AND statut_validation = 'En attente'"
        );
        $requete->execute(['statut' => $nouveauStatut, 'id' => $id]);

        if ($requete->rowCount() === 0) {
            flash('erreur', "Ce compte n'est plus en attente.");
        } elseif ($action === 'valider') {
            flash('ok', "Compte validé : l'utilisateur peut maintenant se connecter.");
        } else {
            flash('ok', 'Compte refusé.');
        }
    } else {
        flash('erreur', 'Action invalide.');
    }
    redirige('validation.php');
}

/* ---------- Comptes en attente ---------- */
$comptes = $bdd->query(
    "SELECT u.id_utilisateur, u.nom, u.prenom, u.email, u.type_profil,
            et.promotion AS promo_etu, et.cv AS cv_etu, f.nom_formation,
            al.promotion AS promo_alu, al.cv AS cv_alu, al.situation_professionnelle,
            pr.specialite,
            (SELECT GROUP_CONCAT(f2.nom_formation SEPARATOR ', ')
               FROM professeur_formation pf
               JOIN formation f2 ON f2.id_formation = pf.id_formation
              WHERE pf.id_utilisateur = u.id_utilisateur) AS formations_prof,
            pa.poste AS poste_partenaire, pa.motif_inscription
     FROM utilisateur u
     LEFT JOIN etudiant et ON et.id_utilisateur = u.id_utilisateur
     LEFT JOIN formation f ON f.id_formation = et.id_formation
     LEFT JOIN alumni al ON al.id_utilisateur = u.id_utilisateur
     LEFT JOIN professeur pr ON pr.id_utilisateur = u.id_utilisateur
     LEFT JOIN partenaire pa ON pa.id_utilisateur = u.id_utilisateur
     WHERE u.statut_validation = 'En attente'
     ORDER BY u.id_utilisateur"
)->fetchAll(PDO::FETCH_ASSOC);

$typesProfil = [
    'etudiant' => 'Étudiant', 'alumni' => 'Alumni', 'professeur' => 'Professeur',
    'partenaire' => 'Partenaire', 'gestionnaire' => 'Gestionnaire',
];

// Infos à afficher selon le profil (les champs vides sont ignorés)
function infosCompte(array $u)
{
    switch ($u['type_profil']) {
        case 'etudiant':
            $infos = ['Promotion' => $u['promo_etu'], 'Formation' => $u['nom_formation'], 'CV' => $u['cv_etu']];
            break;
        case 'alumni':
            $infos = ['Promotion' => $u['promo_alu'], 'CV' => $u['cv_alu'], 'Situation' => $u['situation_professionnelle']];
            break;
        case 'professeur':
            $infos = ['Spécialité' => $u['specialite'], 'Formations' => $u['formations_prof']];
            break;
        case 'partenaire':
            $infos = ['Poste' => $u['poste_partenaire'], "Motif d'inscription" => $u['motif_inscription']];
            break;
        default:
            $infos = [];
    }
    return array_filter($infos, fn($valeur) => trim((string)$valeur) !== '');
}

$titrePage = 'Validation des inscriptions';
require __DIR__ . '/entete.php';
?>
<?php if (!$comptes): ?>
    <div class="carte"><p>Aucun compte en attente de validation.</p></div>
<?php else: ?>
    <p class="petit"><?= count($comptes) ?> compte(s) en attente. Vérifie la pertinence de chaque inscription avant de valider.</p>
    <div class="defilement">
        <table>
            <tr><th>Nom</th><th>Email</th><th>Profil</th><th>Informations</th><th>Actions</th></tr>
            <?php foreach ($comptes as $u): ?>
                <tr>
                    <td><?= e($u['prenom'] . ' ' . $u['nom']) ?></td>
                    <td><?= e($u['email']) ?></td>
                    <td><span class="badge attente"><?= e($typesProfil[$u['type_profil']] ?? $u['type_profil']) ?></span></td>
                    <td class="petit">
                        <?php foreach (infosCompte($u) as $libelle => $valeur): ?>
                            <strong><?= e($libelle) ?> :</strong> <?= nl2br(e($valeur)) ?><br>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <form method="post" class="en-ligne"><?= csrfChamp() ?>
                            <input type="hidden" name="id" value="<?= (int)$u['id_utilisateur'] ?>">
                            <button class="vert" name="action" value="valider">Valider</button>
                        </form>
                        <form method="post" class="en-ligne" onsubmit="return confirm('Refuser cette inscription ?');"><?= csrfChamp() ?>
                            <input type="hidden" name="id" value="<?= (int)$u['id_utilisateur'] ?>">
                            <button class="rouge" name="action" value="refuser">Refuser</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/pied.php'; ?>