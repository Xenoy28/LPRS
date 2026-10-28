<?php
require_once __DIR__ . '/src/garde.php'; // démarre la session

// page => [fichier de la vue, profils autorisés]   null = public, [] = tout utilisateur connecté
$pages = [
    'accueil'            => ['Accueil', null],
    'login'              => ['Login', null],
    'inscription'        => ['Inscription', null],
    'contact'            => ['Contact', null],
    'mdp_oublie'         => ['MotDePasseOublie', null],
    'reinitialiser'      => ['ReinitialiserMotDePasse', null],
    'profil'             => ['Profil', []],
    'entreprises'        => ['Entreprises', MEMBRES],
    'entreprise'         => ['EntrepriseFiche', MEMBRES],
    'entreprise_form'    => ['EntrepriseForm', ['partenaire', 'alumni']],
    'offres'             => ['Offres', MEMBRES],
    'offre'              => ['OffreDetail', MEMBRES],
    'offre_form'         => ['OffreForm', ['partenaire', 'alumni']],
    'candidatures'       => ['Candidatures', ['partenaire', 'alumni']],
    'evenements'         => ['Evenements', MEMBRES],
    'evenement'          => ['EvenementDetail', MEMBRES],
    'evenement_form'     => ['EvenementForm', MEMBRES],
    'annuaire'           => ['AnnuaireAlumni', MEMBRES],
    'annuaire_etudiants' => ['AnnuaireEtudiants', ['professeur']],
];

$page = $_GET['page'] ?? 'accueil';
if (!isset($pages[$page])) {
    $page = 'accueil';
}

list($vue, $roles) = $pages[$page];
if ($roles !== null) {
    exiger($roles, ''); // redirige vers le login si non connecté / profil non autorisé
}

require __DIR__ . '/src/vue/' . $vue . '.php';
