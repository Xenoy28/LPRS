<?php
require_once __DIR__ . '/outils.php';
require_once __DIR__ . '/../bdd/Bdd.php';

$bdd = (new Bdd())->getConnexionBdd();
$existe = (int)$bdd->query("SELECT COUNT(*) FROM utilisateur WHERE type_profil = 'gestionnaire'")->fetchColumn() > 0;

if ($existe) {
    http_response_code(403);
    exit('Un gestionnaire existe déjà. Connectez-vous pour en créer d\'autres.');
}

$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifierCsrf();
    $erreur = creerGestionnaire(
        $bdd,
        trim($_POST['nom'] ?? ''),
        trim($_POST['prenom'] ?? ''),
        trim($_POST['email'] ?? ''),
        $_POST['mot_de_passe'] ?? '',
        $_POST['mot_de_passe_confirm'] ?? '',
        'Administrateur principal'
    );
    if ($erreur === '') {
        flash('ok', 'Premier gestionnaire créé. Vous pouvez vous connecter.');
        redirige('../../index.php?page=login');
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Créer le premier gestionnaire - LPRS</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f5f7f8; display: flex; justify-content: center; padding: 40px 16px; }
        form { background: #fff; padding: 24px; border-radius: 8px; width: 100%; max-width: 380px; box-shadow: 0 1px 4px rgba(0,0,0,.15); }
        label { display: block; margin: 12px 0 4px; font-weight: 600; }
        input { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #b8c4c8; border-radius: 4px; }
        button { margin-top: 18px; width: 100%; padding: 10px; background: #0f4c5c; color: #fff; border: 0; border-radius: 4px; cursor: pointer; }
        .erreur { background: #fde0dd; color: #7f1d1d; padding: 10px; border-radius: 4px; }
    </style>
</head>
<body>
<form method="post">
    <h2>Créer le premier gestionnaire</h2>
    <?php if ($erreur): ?><div class="erreur"><?= e($erreur) ?></div><?php endif; ?>
    <?= csrfChamp() ?>
    <label>Nom</label><input name="nom" required value="<?= e($_POST['nom'] ?? '') ?>">
    <label>Prénom</label><input name="prenom" required value="<?= e($_POST['prenom'] ?? '') ?>">
    <label>Email</label><input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>">
    <label>Mot de passe (10 caractères min.)</label><input type="password" name="mot_de_passe" required>
    <label>Confirmer le mot de passe</label><input type="password" name="mot_de_passe_confirm" required>
    <button type="submit">Créer</button>
</form>
</body>
</html>
