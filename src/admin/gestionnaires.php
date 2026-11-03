<?php
require_once __DIR__ . '/garde_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifierCsrf();
    $erreur = creerGestionnaire(
        $bdd,
        trim($_POST['nom'] ?? ''),
        trim($_POST['prenom'] ?? ''),
        trim($_POST['email'] ?? ''),
        $_POST['mot_de_passe'] ?? '',
        $_POST['mot_de_passe_confirm'] ?? '',
        trim($_POST['role'] ?? '') ?: 'Gestionnaire'
    );
    if ($erreur === '') {
        flash('ok', 'Gestionnaire créé.');
    } else {
        flash('erreur', $erreur);
    }
    redirige('gestionnaires.php');
}

$gestionnaires = $bdd->query(
    "SELECT u.id_utilisateur, u.nom, u.prenom, u.email, g.role
     FROM utilisateur u
     LEFT JOIN gestionnaire g ON g.id_utilisateur = u.id_utilisateur
     WHERE u.type_profil = 'gestionnaire'
     ORDER BY u.nom"
)->fetchAll(PDO::FETCH_ASSOC);

$titrePage = 'Gestionnaires';
require __DIR__ . '/entete.php';
?>
<div class="carte">
    <h2>Créer un gestionnaire</h2>
    <p class="petit">Le compte est directement validé (seul un gestionnaire peut en créer un autre).</p>
    <form method="post">
        <?= csrfChamp() ?>
        <div class="grille-form">
            <div><label>Nom</label><input name="nom" required></div>
            <div><label>Prénom</label><input name="prenom" required></div>
            <div><label>Email</label><input type="email" name="email" required></div>
            <div><label>Rôle (libellé)</label><input name="role" placeholder="Gestionnaire"></div>
            <div><label>Mot de passe (10 car. min.)</label><input type="password" name="mot_de_passe" required></div>
            <div><label>Confirmation</label><input type="password" name="mot_de_passe_confirm" required></div>
        </div>
        <p><button type="submit">Créer</button></p>
    </form>
</div>

<div class="carte">
    <h2>Gestionnaires existants</h2>
    <div class="defilement">
        <table>
            <tr><th>Nom</th><th>Email</th><th>Rôle</th><th></th></tr>
            <?php foreach ($gestionnaires as $g): ?>
                <tr>
                    <td><?= e($g['prenom'] . ' ' . $g['nom']) ?></td>
                    <td><?= e($g['email']) ?></td>
                    <td><?= e($g['role']) ?></td>
                    <td>
                        <?php if ((int)$g['id_utilisateur'] === (int)$adminConnecte['id_utilisateur']): ?>
                            <span class="petit">(vous)</span>
                        <?php else: ?>
                            <a class="bouton gris" href="utilisateurs.php?type=gestionnaire">Gérer</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<?php require __DIR__ . '/pied.php'; ?>
