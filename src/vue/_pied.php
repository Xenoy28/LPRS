<?php
$xConnecte = !empty($_SESSION['user_id']);
$xRoleSession = $_SESSION['user_role'] ?? '';
?>
</div>

<footer class="footer x-pied-colonne">
    <ul class="footer-liens">
        <li><a href="index.php?page=plan_site">Plan du site</a></li>
        <li><a href="index.php?page=mentions_legales">Mentions légales</a></li>
        <li><a href="index.php?page=contact">Contact</a></li>
    </ul>
    <p class="footer-copy">© 2026 LPRS – Tous droits réservés</p>
</footer>
</main>

<aside class="x-droite">
    <?php if (!$xConnecte): ?>
        <section class="x-widget">
            <h2>Nouveau sur LPRS ?</h2>
            <p>Inscris-toi pour accéder aux offres, aux événements, à l'annuaire et au forum.</p>
            <a class="btn" href="index.php?page=inscription">Créer un compte</a>
            <a class="btn gris" href="index.php?page=login">Se connecter</a>
        </section>
    <?php else: ?>
        <section class="x-widget">
            <h2>Raccourcis</h2>
            <ul>
                <?php if (in_array($xRoleSession, MEMBRES)): ?>
                    <li><a href="index.php?page=evenement_form">Proposer un événement</a></li>
                    <li><a href="index.php?page=forum&amp;mes=1">Mes posts du forum</a></li>
                <?php endif; ?>
                <?php if (in_array($xRoleSession, ['partenaire', 'alumni'])): ?>
                    <li><a href="index.php?page=offre_form">Publier une offre</a></li>
                    <li><a href="index.php?page=entreprise_form">Créer une fiche entreprise</a></li>
                <?php endif; ?>
                <?php if ($xRoleSession === 'gestionnaire'): ?>
                    <li><a href="src/admin/dashboard.php">Tableau de bord</a></li>
                <?php endif; ?>
                <li><a href="index.php?page=contact">Contacter les gestionnaires</a></li>
            </ul>
        </section>
    <?php endif; ?>

    <footer class="footer">
        <ul class="footer-liens">
            <li><a href="index.php?page=plan_site">Plan du site</a></li>
            <li><a href="index.php?page=mentions_legales">Mentions légales</a></li>
            <li><a href="index.php?page=contact">Contact</a></li>
        </ul>
        <p class="footer-copy">© 2026 LPRS – Tous droits réservés</p>
    </footer>
</aside>

</div>
</body>
</html>