<?php
session_start();
require_once __DIR__ . '/../bdd/Bdd.php';

function refuserAcces()
{
    header('Location: ../../index.php?page=login');
    exit;
}

if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'gestionnaire') {
    refuserAcces();
}
$bdd = (new Bdd())->getConnexionBdd();
$requete = $bdd->prepare(
    "SELECT id_utilisateur, nom, prenom
     FROM utilisateur
     WHERE id_utilisateur = :id
       AND type_profil = 'gestionnaire'
       AND statut_validation = 'Validé'"
);
$requete->execute(['id' => $_SESSION['user_id']]);
$adminConnecte = $requete->fetch(PDO::FETCH_ASSOC);

if (!$adminConnecte) {
    session_destroy();
    refuserAcces();
}

function e($texte)
{
    return htmlspecialchars((string)$texte, ENT_QUOTES, 'UTF-8');
}