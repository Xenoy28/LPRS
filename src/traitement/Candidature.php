<?php

use repository\CandidatureRepository;

require_once __DIR__ . '/../garde.php';
require_once __DIR__ . '/../repository/OffreRepository.php';
require_once __DIR__ . '/../repository/CandidatureRepository.php';
require_once __DIR__ . '/../repository/UtilisateurRepository.php';

exiger(['etudiant', 'alumni', 'partenaire']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../index.php?page=offres');
    exit;
}

$offres = new OffreRepository();
$candidatures = new CandidatureRepository();
$utilisateurs = new UtilisateurRepository();
$idUser = $_SESSION['user_id'];
$role = $_SESSION['user_role'];
$action = $_POST['action'] ?? '';

function retour($page, $message, $type = 'succes')
{
    header('Location: ../../index.php?page=' . $page . '&' . $type . '=' . urlencode($message));
    exit;
}

if ($action === 'postuler') {
    if ($role !== 'etudiant' && $role !== 'alumni') {
        retour('offres', 'Seuls les étudiants et les alumni peuvent postuler.', 'erreur');
    }
    $idOffre = (int)($_POST['id_offre'] ?? 0);
    $motivation = trim($_POST['motivation'] ?? '');
    $offre = $offres->getById($idOffre);

    if (!$offre || !$offres->peutConsulter($offre, $idUser, $role)) {
        retour('offres', 'Offre introuvable.', 'erreur');
    }
    if ($offre->etat !== 'Ouverte') {
        retour('offre&id=' . $idOffre, 'Cette offre est clôturée.', 'erreur');
    }
    if ($motivation === '') {
        retour('offre&id=' . $idOffre, 'Écrivez votre motivation.', 'erreur');
    }
    if ($candidatures->existe($idOffre, $idUser)) {
        retour('offre&id=' . $idOffre, 'Vous avez déjà postulé à cette offre.', 'erreur');
    }

    $candidatures->postuler($idOffre, $idUser, $motivation);


}


retour('offres', 'Action inconnue.', 'erreur');
