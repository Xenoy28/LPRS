<?php
require_once __DIR__ . '/../repository/UtilisateurRepository.php';
$nom = trim($_GET['nom'] ?? '');
$promotion = trim($_GET['promotion'] ?? '');
$secteur = trim($_GET['secteur'] ?? '');
$alumni = (new UtilisateurRepository())->annuaireAlumni($nom, $promotion, $secteur);
?>
<?php
$titre = 'Annuaire des anciens élèves';
require __DIR__ . '/_entete.php';
?>
<?php if ($_SESSION['user_role'] === 'professeur'): ?>
    <p><a href="index.php?page=annuaire_etudiants">Annuaire des étudiants actuels</a></p>
<?php endif; ?>

<form method="get" action="index.php">
    <input type="hidden" name="page" value="annuaire">
    <input type="text" name="nom" value="<?= h($nom) ?>" placeholder="Nom ou prénom">
    <input type="text" name="promotion" value="<?= h($promotion) ?>" placeholder="Promotion">
    <input type="text" name="secteur" value="<?= h($secteur) ?>" placeholder="Secteur d'activité">
    <button type="submit">Rechercher</button>
</form>

<table border="1" cellpadding="4">
    <tr><th>Nom</th><th>Prénom</th><th>Promotion</th><th>Entreprise(s)</th><th>Poste(s)</th><th>Secteur(s)</th></tr>
    <?php foreach ($alumni as $a): ?>
        <tr>
            <td><?= h($a['nom']) ?></td>
            <td><?= h($a['prenom']) ?></td>
            <td><?= h($a['promotion']) ?></td>
            <td><?= h($a['entreprises'] ?: $a['situation_professionnelle']) ?></td>
            <td><?= h($a['postes']) ?></td>
            <td><?= h($a['secteurs']) ?></td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$alumni): ?><tr><td colspan="6">Aucun résultat.</td></tr><?php endif; ?>
</table>
<<<<<<< HEAD

<?php require __DIR__ . '/_pied.php'; ?>
=======
<footer class="footer">
    <ul class="footer-liens">
        <li><a href="index.php?page=plan_site">Plan du site</a></li>
        <li><a href="index.php?page=mentions_legales">Mentions légales</a></li>
        <li><a href="index.php?page=contact">Contact</a></li>
    </ul>
    <p class="footer-copy">© 2026 LPRS – Tous droits réservés</p>
</footer>
</body>
</html>
>>>>>>> 5ac582da7984e4e8177bda977d22c1c4811ab93f
