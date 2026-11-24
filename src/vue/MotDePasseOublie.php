<?php
$titre = 'Mot de passe oublié';

if (empty($_SESSION['csrf_mdp'])) {
    $_SESSION['csrf_mdp'] = bin2hex(random_bytes(32));
}

require __DIR__ . '/_entete.php';
?>

<p>
    Indiquez votre adresse email : si un compte existe, vous recevrez un lien
    (valable 1 heure) pour choisir un nouveau mot de passe.
</p>

<form method="post" action="src/traitement/MotDePasseOublie.php">
    <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf_mdp']) ?>">

    <label for="email">Adresse email</label>
    <input type="email" id="email" name="email" maxlength="150" required>

    <button class="btn" type="submit">Envoyer le lien</button>
</form>

<p class="maj"><a href="index.php?page=login">← Retour à la connexion</a></p>

<?php require __DIR__ . '/_pied.php'; ?>
