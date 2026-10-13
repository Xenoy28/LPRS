<?php
require_once __DIR__ . '/garde_admin.php';

$titrePage = 'Tableau de bord';

function compter(PDO $bdd, $sql)
{
    return (int)$bdd->query($sql)->fetchColumn();
}

$stats = [
    'Inscriptions en attente' => compter($bdd, "SELECT COUNT(*) FROM utilisateur WHERE statut_validation = 'En attente'"),
    'Utilisateurs validés'    => compter($bdd, "SELECT COUNT(*) FROM utilisateur WHERE statut_validation = 'Validé'"),
    'Entreprises'             => compter($bdd, 'SELECT COUNT(*) FROM entreprise'),
    'Offres ouvertes'         => compter($bdd, "SELECT COUNT(*) FROM offre WHERE etat = 'Ouverte'"),
    'Candidatures'            => compter($bdd, 'SELECT COUNT(*) FROM candidature'),
    'Événements à venir'      => compter($bdd, 'SELECT COUNT(*) FROM evenement WHERE date_debut >= NOW()'),
    'Posts du forum'          => compter($bdd, 'SELECT COUNT(*) FROM post'),
    'Formations'              => compter($bdd, 'SELECT COUNT(*) FROM formation'),
];

$attente = $bdd->query(
    "SELECT id_utilisateur, nom, prenom, email, type_profil
     FROM utilisateur WHERE statut_validation = 'En attente'
     ORDER BY id_utilisateur DESC LIMIT 5"
)->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/entete.php';
?>
<div class="stats">
    <?php foreach ($stats as $libelle => $valeur): ?>
        <div class="carte">
            <div class="nombre"><?= $valeur ?></div>
            <div><?= e($libelle) ?></div>
        </div>
    <?php endforeach; ?>
</div>

<div class="carte">
    <h2>Dernières inscriptions à valider</h2>
    <?php if (!$attente): ?>
        <p>Aucune inscription en attente.</p>
    <?php else: ?>
        <div class="defilement">
            <table>
                <tr><th>Nom</th><th>Email</th><th>Profil</th></tr>
                <?php foreach ($attente as $u): ?>
                    <tr>
                        <td><?= e($u['prenom'] . ' ' . $u['nom']) ?></td>
                        <td><?= e($u['email']) ?></td>
                        <td><?= e($u['type_profil']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <p><a class="bouton" href="utilisateurs.php?statut=En+attente">Traiter les inscriptions</a></p>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/pied.php'; ?>
