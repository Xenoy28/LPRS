<?php
require_once __DIR__ . '/garde_admin.php';

$typesProfil = [
    'etudiant' => 'Étudiant', 'alumni' => 'Alumni', 'professeur' => 'Professeur',
    'partenaire' => 'Partenaire', 'gestionnaire' => 'Gestionnaire',
];
$statuts = ['En attente', 'Validé', 'Refusé'];

/* ---------- Actions (POST) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifierCsrf();
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($id <= 0) {
        flash('erreur', 'Utilisateur invalide.');
    } elseif ($id === (int)$adminConnecte['id_utilisateur'] && in_array($action, ['refuser', 'supprimer'], true)) {
        flash('erreur', 'Vous ne pouvez pas modifier ou supprimer votre propre compte.');
    } else {
        switch ($action) {
            case 'valider':
                $bdd->prepare("UPDATE utilisateur SET statut_validation = 'Validé' WHERE id_utilisateur = :id")->execute(['id' => $id]);
                flash('ok', 'Compte validé.');
                break;
            case 'refuser':
                $bdd->prepare("UPDATE utilisateur SET statut_validation = 'Refusé' WHERE id_utilisateur = :id")->execute(['id' => $id]);
                flash('ok', 'Compte refusé / bloqué.');
                break;
            case 'supprimer':
                supprimerUtilisateur($bdd, $id);
                flash('ok', 'Compte supprimé.');
                break;
            default:
                flash('erreur', 'Action inconnue.');
        }
    }
    redirige($_SERVER['REQUEST_URI']);
}

/* ---------- Filtres (GET) ---------- */
$fType = $_GET['type'] ?? '';
$fStatut = $_GET['statut'] ?? '';
$recherche = trim($_GET['q'] ?? '');
$where = [];
$params = [];
if (isset($typesProfil[$fType])) {
    $where[] = 'u.type_profil = :type';
    $params['type'] = $fType;
}
if (in_array($fStatut, $statuts, true)) {
    $where[] = 'u.statut_validation = :statut';
    $params['statut'] = $fStatut;
}
if ($recherche !== '') {
    $where[] = '(u.nom LIKE :q1 OR u.prenom LIKE :q2 OR u.email LIKE :q3)';
    $params['q1'] = $params['q2'] = $params['q3'] = '%' . $recherche . '%';
}

$sql = "SELECT u.id_utilisateur, u.nom, u.prenom, u.email, u.type_profil, u.statut_validation,
               et.promotion AS promo_etu, f.nom_formation,
               al.promotion AS promo_alu, al.situation_professionnelle,
               pr.specialite,
               pa.poste AS poste_partenaire, pa.motif_inscription
        FROM utilisateur u
        LEFT JOIN etudiant et ON et.id_utilisateur = u.id_utilisateur
        LEFT JOIN formation f ON f.id_formation = et.id_formation
        LEFT JOIN alumni al ON al.id_utilisateur = u.id_utilisateur
        LEFT JOIN professeur pr ON pr.id_utilisateur = u.id_utilisateur
        LEFT JOIN partenaire pa ON pa.id_utilisateur = u.id_utilisateur"
    . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
    . " ORDER BY (u.statut_validation = 'En attente') DESC, u.id_utilisateur DESC LIMIT 200";
$requete = $bdd->prepare($sql);
$requete->execute($params);
$utilisateurs = $requete->fetchAll(PDO::FETCH_ASSOC);

function detailsUtilisateur(array $u)
{
    $morceaux = [];
    switch ($u['type_profil']) {
        case 'etudiant':
            $morceaux = ['Promo : ' . $u['promo_etu'], 'Formation : ' . $u['nom_formation']];
            break;
        case 'alumni':
            $morceaux = ['Promo : ' . $u['promo_alu'], (string)$u['situation_professionnelle']];
            break;
        case 'professeur':
            $morceaux = ['Spécialité : ' . $u['specialite']];
            break;
        case 'partenaire':
            $morceaux = ['Poste : ' . $u['poste_partenaire'], 'Motif : ' . $u['motif_inscription']];
            break;
    }
    return implode(' | ', array_filter($morceaux, fn($m) => trim($m, ' :') !== '' && !preg_match('/: $/', $m)));
}

$classeBadge = ['En attente' => 'attente', 'Validé' => 'valide', 'Refusé' => 'refuse'];

$titrePage = 'Utilisateurs';
require __DIR__ . '/entete.php';
?>
<form method="get" class="filtres">
    <input type="search" name="q" placeholder="Nom, prénom, email" value="<?= e($recherche) ?>">
    <select name="type">
        <option value="">Tous les profils</option>
        <?php foreach ($typesProfil as $cle => $libelle): ?>
            <option value="<?= e($cle) ?>" <?= $fType === $cle ? 'selected' : '' ?>><?= e($libelle) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="statut">
        <option value="">Tous les statuts</option>
        <?php foreach ($statuts as $s): ?>
            <option value="<?= e($s) ?>" <?= $fStatut === $s ? 'selected' : '' ?>><?= e($s) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Filtrer</button>
    <a class="bouton gris" href="utilisateurs.php">Réinitialiser</a>
</form>

<div class="defilement">
    <table>
        <tr><th>Nom</th><th>Email</th><th>Profil</th><th>Informations</th><th>Statut</th><th>Actions</th></tr>
        <?php if (!$utilisateurs): ?>
            <tr><td colspan="6">Aucun utilisateur.</td></tr>
        <?php endif; ?>
        <?php foreach ($utilisateurs as $u): ?>
            <tr>
                <td><?= e($u['prenom'] . ' ' . $u['nom']) ?></td>
                <td><?= e($u['email']) ?></td>
                <td><?= e($typesProfil[$u['type_profil']] ?? $u['type_profil']) ?></td>
                <td class="petit"><?= e(detailsUtilisateur($u)) ?></td>
                <td><span class="badge <?= e($classeBadge[$u['statut_validation']] ?? 'neutre') ?>"><?= e($u['statut_validation']) ?></span></td>
                <td>
                    <?php $estMoi = (int)$u['id_utilisateur'] === (int)$adminConnecte['id_utilisateur']; ?>
                    <?php if ($u['statut_validation'] !== 'Validé'): ?>
                        <form method="post" class="en-ligne"><?= csrfChamp() ?>
                            <input type="hidden" name="id" value="<?= (int)$u['id_utilisateur'] ?>">
                            <button class="vert" name="action" value="valider">Valider</button>
                        </form>
                    <?php endif; ?>
                    <?php if (!$estMoi && $u['statut_validation'] !== 'Refusé'): ?>
                        <form method="post" class="en-ligne"><?= csrfChamp() ?>
                            <input type="hidden" name="id" value="<?= (int)$u['id_utilisateur'] ?>">
                            <button class="gris" name="action" value="refuser">Refuser</button>
                        </form>
                    <?php endif; ?>
                    <?php if (!$estMoi): ?>
                        <form method="post" class="en-ligne" onsubmit="return confirm('Supprimer définitivement ce compte et tout son contenu ?');"><?= csrfChamp() ?>
                            <input type="hidden" name="id" value="<?= (int)$u['id_utilisateur'] ?>">
                            <button class="rouge" name="action" value="supprimer">Supprimer</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php require __DIR__ . '/pied.php'; ?>
