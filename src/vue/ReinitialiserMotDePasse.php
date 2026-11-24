<?php
require_once __DIR__ . '/../repository/UtilisateurRepository.php';

$titre = 'Nouveau mot de passe';
$id    = (int)($_GET['id'] ?? 0);
$jeton = (string)($_GET['jeton'] ?? '');

// lien invalide, expiré ou déjà utilisé : retour à la connexion
if (!(new UtilisateurRepository())->jetonValide($id, $jeton)) {
    header('Location: index.php?page=login&erreur=' . urlencode('Ce lien est invalide ou a expiré. Refaites une demande.'));
    exit;
}

if (empty($_SESSION['csrf_mdp'])) {
    $_SESSION['csrf_mdp'] = bin2hex(random_bytes(32));
}

require __DIR__ . '/_entete.php';
?>

<form method="post" action="src/traitement/ReinitialiserMotDePasse.php">
    <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf_mdp']) ?>">
    <input type="hidden" name="id" value="<?= $id ?>">
    <input type="hidden" name="jeton" value="<?= h($jeton) ?>">

    <label for="mot_de_passe">Nouveau mot de passe (6 caractères minimum)</label>
    <input type="password" id="mot_de_passe" name="mot_de_passe" required>

    <label for="mot_de_passe_confirm">Confirmer le mot de passe</label>
    <input type="password" id="mot_de_passe_confirm" name="mot_de_passe_confirm" required>

    <button class="btn" type="submit">Enregistrer</button>
</form>

<?php require __DIR__ . '/_pied.php'; ?>
