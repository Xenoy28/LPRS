<?php
require_once __DIR__ . '/../repository/UtilisateurRepository.php';
$formations = (new UtilisateurRepository())->listeFormations();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
</head>
<body>
<h1>Inscription</h1>

<p><?= htmlspecialchars($_GET['erreur'] ?? '') ?></p>

<form method="post" action="src/traitement/Inscription.php">
    <p>Nom <input type="text" name="nom"></p>
    <p>Prénom <input type="text" name="prenom"></p>
    <p>Email <input type="email" name="email"></p>
    <p>Mot de passe <input type="password" name="mot_de_passe"></p>
    <p>Confirmer le mot de passe <input type="password" name="mot_de_passe_confirm"></p>

    <p>Je suis
        <select name="role" id="role">
            <option value="">-- choisir --</option>
            <option value="etudiant">Étudiant</option>
            <option value="alumni">Alumni</option>
            <option value="partenaire">Partenaire</option>
            <option value="professeur">Professeur</option>
        </select>
    </p>

    <fieldset class="profil" data-role="etudiant">
        <p>Promotion <input type="text" name="promotion"></p>
        <p>Formation
            <select name="id_formation">
                <?php foreach ($formations as $f): ?>
                    <option value="<?= $f['id_formation'] ?>"><?= htmlspecialchars($f['nom_formation']) ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>CV (lien ou nom du fichier) <input type="text" name="cv"></p>
    </fieldset>

    <fieldset class="profil" data-role="alumni">
        <p>Promotion <input type="text" name="promotion"></p>
        <p>CV (lien ou nom du fichier) <input type="text" name="cv"></p>
    </fieldset>

    <fieldset class="profil" data-role="partenaire">
        <p>Poste dans l'entreprise <input type="text" name="poste"></p>
        <p>Motif d'inscription <textarea name="motif_inscription"></textarea></p>
    </fieldset>

    <fieldset class="profil" data-role="professeur">
        <p>Spécialité / matière <input type="text" name="specialite"></p>
    </fieldset>

    <p><button type="submit">S'inscrire</button></p>
</form>

<p><a href="index.php?page=login">Déjà un compte ?</a> | <a href="index.php">Accueil</a></p>

<script>
    // n'affiche (et n'envoie) que les champs du profil choisi
    const select = document.getElementById('role');
    function afficher() {
        document.querySelectorAll('.profil').forEach(function (bloc) {
            const actif = bloc.dataset.role === select.value;
            bloc.style.display = actif ? '' : 'none';
            bloc.disabled = !actif;
        });
    }
    select.addEventListener('change', afficher);
    afficher();
</script>
</body>
</html>
