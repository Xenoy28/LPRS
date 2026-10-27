<?php ?>
<p>
    <a href ="index.php">Accueil</a>
    <?php if (!empty($_SESSION['user_id'])): ?>
        <?php if (in_array($_SESSION['user_role'], MEMBRES)): ?>
            | <a href="index.php?page=offres">Offres</a>
            | <a href="index.php?page=evenements">Événements</a>
            | <a href="index.php?page=annuaire">Annuaire</a>
            | <a href="index.php?page=entreprises">Entreprises</a>
        <?php else: ?>
            | <a href="src/admin/dashboard.php">Tableau de bord</a>
        <?php endif; ?>
        | <a href="index.php?page=profil">Profil</a>
        | <a href="src/traitement/Logout.php">Déconnexion</a>
    <?php else: ?>
        | <a href="index.php?page=login">Se connecter</a>
        | <a href="index.php?page=inscription">S'inscrire</a>
    <?php endif; ?>
    | <a href="index.php?page=contact">Contact</a>
</p>
<?php if (!empty($_GET['succes'])): ?><p><strong><?= h($_GET['succes']) ?></strong></p><?php endif; ?>
<?php if (!empty($_GET['erreur'])): ?><p><strong><?= h($_GET['erreur']) ?></strong></p><?php endif; ?>
<hr>
