<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
</head>
<body>
<h1>Connexion</h1>

<p><?= htmlspecialchars($_GET['succes'] ?? '') ?></p>
<p><?= htmlspecialchars($_GET['erreur'] ?? '') ?></p>

<form method="post" action="src/traitement/Login.php">
    <p>Email <input type="email" name="email" required></p>
    <p>Mot de passe <input type="password" name="mot_de_passe" required></p>
    <p><button type="submit">Se connecter</button></p>
</form>

<p><a href="index.php?page=inscription">Créer un compte</a> | <a href="index.php">Accueil</a></p>
</body>
</html>
