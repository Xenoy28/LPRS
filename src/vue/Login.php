<?php
$titre = 'Connexion';
require __DIR__ . '/_entete.php';
?>

<form method="post" action="src/traitement/Login.php">
    <p>Email <input type="email" name="email" required></p>
    <p>Mot de passe <input type="password" name="mot_de_passe" required></p>
    <p><button type="submit">Se connecter</button></p>
</form>

<p><a href="index.php?page=inscription">Créer un compte</a> | <a href="index.php">Accueil</a></p>

<?php require __DIR__ . '/_pied.php'; ?>
