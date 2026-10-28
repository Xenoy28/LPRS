<?php

require_once __DIR__ . '/../garde.php';
require_once __DIR__ . '/../repository/OffreRepository.php';
require_once __DIR__ . '/../repository/EntrepriseRepository.php';

exiger(['partenaire', 'alumni']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../index.php?page=offres');
    exit;
}

$repo = new OffreRepository();
$entreprises = new EntrepriseRepository();
$idUser = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';
$idOffre = (int)($_POST['id_offre'] ?? 0);

function champ($cle)
{
    return trim($_POST[$cle] ?? '');
}

function retour($page, $message, $type = 'succes')
{
    header('Location: ../../index.php?page=' . $page . '&' . $type . '=' . urlencode($message));
    exit;
}

switch ($action) {
    case 'creer':
    case 'modifier':
        $retourErreur = $action === 'creer' ? 'offre_form' : 'offre_form&id=' . $idOffre;
        $salaire = champ('salaire');
        $expiration = champ('date_expiration');

        $o = new Offre([
            'id_offre' => $idOffre,
            'id_entreprise' => (int)champ('id_entreprise'),
            'id_auteur' => $idUser,
            'titre' => champ('titre'),
            'description' => champ('description'),
            'missions' => champ('missions'),
            'type_offre' => champ('type_offre'),
            'salaire' => $salaire === '' ? null : $salaire,
            'lieu' => champ('lieu'),
            'date_expiration' => $expiration === '' ? null : $expiration,
            'profil_cible' => champ('profil_cible'),
        ]);

        if ($o->titre === '' || $o->description === '' || $o->missions === '') {
            retour($retourErreur, 'Le titre, la description et les missions sont obligatoires.', 'erreur');
        }
        if (!in_array($o->type_offre, OffreRepository::TYPES)) {
            retour($retourErreur, 'Type d\'offre invalide.', 'erreur');
        }
        if (!isset(OffreRepository::CIBLES[$o->profil_cible])) {
            retour($retourErreur, 'Public visé invalide.', 'erreur');
        }
        if ($o->salaire !== null && (!is_numeric($o->salaire) || $o->salaire < 0)) {
            retour($retourErreur, 'Le salaire doit être un nombre positif.', 'erreur');
        }
        // l'offre est publiée au nom d'une entreprise dont on fait partie
        if (!$entreprises->estMembre($o->id_entreprise, $idUser)) {
            retour($retourErreur, 'Choisissez une de vos entreprises (rattachez-vous d\'abord à une fiche entreprise).', 'erreur');
        }

        if ($action === 'creer') {
            $id = $repo->creer($o);
            retour('offre&id=' . $id, 'Offre publiée.');
        }

        $existante = $repo->getById($idOffre);
        if (!$existante || (int)$existante->id_auteur !== (int)$idUser) {
            retour('offres', 'Seul l\'auteur peut modifier l\'offre.', 'erreur');
        }
        $repo->modifier($o);
        retour('offre&id=' . $idOffre, 'Offre modifiée.');

    case 'etat':
        $offre = $repo->getById($idOffre);
        if (!$offre || (int)$offre->id_auteur !== (int)$idUser) {
            retour('offres', 'Seul l\'auteur peut bloquer ou rouvrir l\'offre.', 'erreur');
        }
        $etat = ($_POST['etat'] ?? '') === 'Ouverte' ? 'Ouverte' : 'Clôturée';
        $repo->changerEtat($idOffre, $etat);
        retour('offre&id=' . $idOffre, $etat === 'Ouverte' ? 'Offre rouverte aux candidatures.' : 'Offre clôturée : plus de candidature possible.');
}

retour('offres', 'Action inconnue.', 'erreur');
