<?php
require_once __DIR__ . '/../repository/UtilisateurRepository.php';
$repo = new UtilisateurRepository();
$user = $repo->getById($_SESSION['user_id']);
$type = $user->getTypeProfil();
$d = $user->getDetails();
$h = function ($v) { return htmlspecialchars((string)$v); };
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon profil</title>
</head>
<body>
<h1>Mon profil (<?= $h($type) ?>)</h1>

<p><?= $h($_GET['succes'] ?? '') ?></p>
<p><?= $h($_GET['erreur'] ?? '') ?></p>

<form method="post" action="src/traitement/Modifier.php">
    <p>Nom <input type="text" name="nom" value="<?= $h($user->getNomUtilisateur()) ?>"></p>
    <p>Prénom <input type="text" name="prenom" value="<?= $h($user->getPrenomUtilisateur()) ?>"></p>
    <p>Email <input type="email" name="email" value="<?= $h($user->getEmailUtilisateur()) ?>"></p>
    <p>Nouveau mot de passe (laisser vide pour ne pas changer) <input type="password" name="nouveau_mot_de_passe"></p>

    <?php if ($type === 'etudiant'): ?>
        <p>Promotion <input type="text" name="promotion" value="<?= $h($d['promotion'] ?? '') ?>"></p>
        <p>Formation
            <select name="id_formation">
                <?php foreach ($repo->listeFormations() as $f): ?>
                    <option value="<?= $f['id_formation'] ?>" <?= $f['id_formation'] == ($d['id_formation'] ?? 0) ? 'selected' : '' ?>><?= $h($f['nom_formation']) ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>CV <input type="text" name="cv" value="<?= $h($d['cv'] ?? '') ?>"></p>
    <?php elseif ($type === 'alumni'): ?>
        <p>Promotion <input type="text" name="promotion" value="<?= $h($d['promotion'] ?? '') ?>"></p>
        <p>CV <input type="text" name="cv" value="<?= $h($d['cv'] ?? '') ?>"></p>
    <?php elseif ($type === 'partenaire'): ?>
        <p>Poste <input type="text" name="poste" value="<?= $h($d['poste'] ?? '') ?>"></p>
        <p>Motif d'inscription <textarea name="motif_inscription"><?= $h($d['motif_inscription'] ?? '') ?></textarea></p>
    <?php elseif ($type === 'professeur'): ?>
        <p>Spécialité <input type="text" name="specialite" value="<?= $h($d['specialite'] ?? '') ?>"></p>
    <?php endif; ?>

    <p><button type="submit">Enregistrer</button></p>
</form>

<form method="post" action="src/traitement/Supprimer.php" onsubmit="return confirm('Supprimer définitivement votre compte ?');">
    <p><button type="submit">Supprimer mon compte</button></p>
</form>

<p><a href="index.php">Accueil</a> | <a href="src/traitement/Logout.php">Déconnexion</a></p>
</body>
</html>
