<?php

require_once __DIR__ . '/../garde.php';
require_once __DIR__ . '/../repository/EntrepriseRepository.php';
require_once __DIR__ . '/../repository/UtilisateurRepository.php';

exiger(['partenaire', 'alumni']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../index.php?page=entreprises');
    exit;
}

$repo = new EntrepriseRepository();
$idUser = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';
$idEntreprise = (int)($_POST['id_entreprise'] ?? 0);

function champ($cle)
{
    return trim($_POST[$cle] ?? '');
}

// redirection avec un message
function retour($page, $message, $type = 'succes')
{
    header('Location: ../../index.php?page=' . $page . '&' . $type . '=' . urlencode($message));
    exit;
}

switch ($action) {
    case 'creer':
    case 'modifier':
        $e = new Entreprise([
            'id_entreprise' => $idEntreprise,
            'nom' => champ('nom'),
            'adresse' => champ('adresse'),
            'code_postal' => champ('code_postal'),
            'ville' => champ('ville'),
            'site_web' => EntrepriseRepository::normaliserSite(champ('site_web')),
            'secteur_activite' => champ('secteur_activite'),
            'description' => champ('description'),
        ]);
        $retourErreur = $action === 'creer' ? 'entreprise_form' : 'entreprise_form&id=' . $idEntreprise;

        if ($e->nom === '' || $e->adresse === '' || $e->site_web === '') {
            retour($retourErreur, 'Le nom, l\'adresse et le site web sont obligatoires.', 'erreur');
        }
        if (strpos($e->site_web, '.') === false) {
            retour($retourErreur, 'L\'adresse web n\'est pas valide.', 'erreur');
        }

        // le site web sert de contrôle pour ne pas créer deux fois la même entreprise
        $existante = $repo->getParSite($e->site_web, $action === 'modifier' ? $idEntreprise : 0);
        if ($existante) {
            retour('entreprise&id=' . $existante->id_entreprise,
                'Cette entreprise existe déjà : rattachez-vous à sa fiche.', 'erreur');
        }

        if ($action === 'creer') {
            $poste = champ('poste');
            if ($poste === '') {
                retour($retourErreur, 'Indiquez votre poste dans l\'entreprise.', 'erreur');
            }
            $id = $repo->creer($e);
            $repo->rattacher($id, $idUser, $poste);
            retour('entreprise&id=' . $id, 'Fiche entreprise créée.');
        }

        if (!$repo->estMembre($idEntreprise, $idUser)) {
            retour('entreprises', 'Vous ne faites pas partie de cette entreprise.', 'erreur');
        }
        $repo->modifier($e);
        retour('entreprise&id=' . $idEntreprise, 'Fiche entreprise modifiée.');

    case 'rattacher':
        $poste = champ('poste');
        if (!$repo->getById($idEntreprise)) {
            retour('entreprises', 'Entreprise introuvable.', 'erreur');
        }
        if ($poste === '') {
            retour('entreprise&id=' . $idEntreprise, 'Indiquez votre poste dans l\'entreprise.', 'erreur');
        }
        $repo->rattacher($idEntreprise, $idUser, $poste);
        retour('entreprise&id=' . $idEntreprise, 'Vous êtes rattaché à cette entreprise.');

    case 'quitter':
        $repo->quitter($idEntreprise, $idUser);
        retour('entreprises', 'Vous ne faites plus partie de cette entreprise.');
}

retour('entreprises', 'Action inconnue.', 'erreur');

