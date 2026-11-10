<?php
$titre = 'Plan du site';
require __DIR__ . '/_entete.php';
?>

<div class="grille">

    <section class="bloc">
        <h2>Pages générales</h2>
        <ul>
            <li><a href="index.php">Accueil</a></li>
            <li><a href="index.php?page=contact">Contact</a></li>
            <li><a href="index.php?page=mentions_legales">Mentions légales</a></li>
            <li><a href="index.php?page=plan_site">Plan du site</a></li>
        </ul>
    </section>

    <section class="bloc">
        <h2>Mon compte</h2>
        <ul>
            <?php if ($connecte): ?>
                <li><a href="index.php?page=profil">Mon profil</a></li>
                <?php if ($_SESSION['user_role'] === 'gestionnaire'): ?>
                    <li><a href="src/admin/dashboard.php">Tableau de bord</a></li>
                <?php endif; ?>
                <li><a href="src/traitement/Logout.php">Déconnexion</a></li>
            <?php else: ?>
                <li><a href="index.php?page=login">Se connecter</a></li>
                <li><a href="index.php?page=inscription">S'inscrire</a></li>
            <?php endif; ?>
        </ul>
    </section>

    <section class="bloc">
        <h2>Espace membres</h2>
        <p class="maj">Réservé aux membres connectés.</p>
        <ul>
            <li><a href="index.php?page=offres">Offres de stage et d'emploi</a></li>
            <li><a href="index.php?page=entreprises">Entreprises partenaires</a></li>
            <li><a href="index.php?page=evenements">Événements</a></li>
            <li><a href="index.php?page=annuaire">Annuaire des alumnis</a></li>
            <li><a href="index.php?page=forum">Forum</a></li>
        </ul>
    </section>

</div>

<?php require __DIR__ . '/_pied.php'; ?>
