<?php

require_once __DIR__ . '/../garde.php';
require_once __DIR__ . '/../repository/EvenementRepository.php';
require_once __DIR__ . '/../repository/UtilisateurRepository.php';

// tout le monde peut créer un événement et s'y inscrire, sauf les gestionnaires
exiger(MEMBRES);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../index.php?page=evenements');
    exit;
}

$repo = new EvenementRepository();
$idUser = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';
$idEvenement = (int)($_POST['id_evenement'] ?? 0);

function champ($cle)
{
    return trim($_POST[$cle] ?? '');
}

function retour($page, $message, $type = 'succes')
{
    header('Location: ../../index.php?page=' . $page . '&' . $type . '=' . urlencode($message));
    exit;
}

// "2026-11-05T14:30" (champ datetime-local) => "2026-11-05 14:30:00"
function dateSql($valeur)
{
    $d = DateTime::createFromFormat('Y-m-d\TH:i', $valeur);
    return $d ? $d->format('Y-m-d H:i:s') : null;
}

// seuls les organisateurs gèrent l'événement
function exigerOrganisateur(EvenementRepository $repo, $idEvenement, $idUser)
{
    if (!$repo->getById($idEvenement) || !$repo->estOrganisateur($idEvenement, $idUser)) {
        retour('evenements', 'Seuls les organisateurs peuvent gérer cet événement.', 'erreur');
    }
}

switch ($action) {
    case 'creer':
    case 'modifier':
        $retourErreur = $action === 'creer' ? 'evenement_form' : 'evenement_form&id=' . $idEvenement;
        $places = champ('nombre_places');
        $debut = dateSql(champ('date_debut'));
        $fin = champ('date_fin') === '' ? null : dateSql(champ('date_fin'));

        $e = new Evenement([
            'id_evenement' => $idEvenement,
            'titre' => champ('titre'),
            'type_evenement' => champ('type_evenement'),
            'description' => champ('description'),
            'date_debut' => $debut,
            'date_fin' => $fin,
            'lieu' => champ('lieu'),
            'nombre_places' => $places === '' ? null : (int)$places,
            'elements_requis' => champ('elements_requis'),
        ]);

        if ($e->titre === '' || $e->type_evenement === '' || $e->description === '' || $e->lieu === '' || !$debut) {
            retour($retourErreur, 'Le titre, le type, la description, le lieu et la date de début sont obligatoires.', 'erreur');
        }
        if (champ('date_fin') !== '' && (!$fin || $fin < $debut)) {
            retour($retourErreur, 'La date de fin doit être postérieure à la date de début.', 'erreur');
        }
        if ($places !== '' && (!ctype_digit($places) || (int)$places < 1)) {
            retour($retourErreur, 'Le nombre de places doit être un entier positif.', 'erreur');
        }

        if ($action === 'creer') {
            $id = $repo->creer($e, $idUser);
            $message = $repo->getById($id)->publie
                ? 'Événement créé et publié.'
                : 'Événement créé. Il sera publié dès qu\'un professeur sera ajouté aux organisateurs.';
            retour('evenement&id=' . $id, $message);
        }

        exigerOrganisateur($repo, $idEvenement, $idUser);
        $repo->modifier($e);
        retour('evenement&id=' . $idEvenement, 'Événement modifié.');

    case 'organisateur':
        exigerOrganisateur($repo, $idEvenement, $idUser);
        $nouveau = (new UtilisateurRepository())->getUtilisateurByEmail(champ('email'));
        if (!$nouveau || $nouveau->getStatusValidation() !== 'Validé' || !in_array($nouveau->getTypeProfil(), MEMBRES)) {
            retour('evenement&id=' . $idEvenement, 'Aucun membre trouvé avec cet email.', 'erreur');
        }
        $repo->ajouterOrganisateur($idEvenement, $nouveau->getIdUtilisateur());
        retour('evenement&id=' . $idEvenement, $nouveau->getPrenomUtilisateur() . ' ' . $nouveau->getNomUtilisateur() . ' est maintenant organisateur.');

    case 'retirer':
        exigerOrganisateur($repo, $idEvenement, $idUser);
        if (!$repo->retirerOrganisateur($idEvenement, (int)($_POST['id_utilisateur'] ?? 0))) {
            retour('evenement&id=' . $idEvenement, 'Un événement doit garder au moins un organisateur.', 'erreur');
        }
        retour('evenement&id=' . $idEvenement, 'Organisateur retiré.');

    case 'inscrire':
        $e = $repo->getById($idEvenement);
        if (!$e || !$e->publie) {
            retour('evenements', 'Événement introuvable.', 'erreur');
        }
        if (strtotime($e->date_debut) < time()) {
            retour('evenement&id=' . $idEvenement, 'Cet événement est déjà commencé ou terminé.', 'erreur');
        }
        if ($repo->inscription($idEvenement, $idUser)) {
            retour('evenement&id=' . $idEvenement, 'Vous êtes déjà inscrit à cet événement.', 'erreur');
        }
        if ($e->nombre_places !== null && $e->places_prises >= $e->nombre_places) {
            retour('evenement&id=' . $idEvenement, 'Il n\'y a plus de place disponible.', 'erreur');
        }
        $repo->inscrire($idEvenement, $idUser);
        retour('evenement&id=' . $idEvenement, 'Inscription enregistrée.');

    case 'annuler':
        $i = $repo->inscription($idEvenement, $idUser);
        if ($i && $i['statut'] === 'Inscrit') {
            $repo->annulerInscription($idEvenement, $idUser);
            retour('evenement&id=' . $idEvenement, 'Inscription annulée.');
        }
        retour('evenement&id=' . $idEvenement, 'Aucune inscription à annuler.', 'erreur');

    case 'refuser':
        $i = $repo->inscriptionParId((int)($_POST['id_inscription'] ?? 0));
        if (!$i) {
            retour('evenements', 'Inscription introuvable.', 'erreur');
        }
        exigerOrganisateur($repo, $i['id_evenement'], $idUser);
        $repo->changerStatutInscription($i['id_inscription'], 'Refusé');
        retour('evenement&id=' . $i['id_evenement'], 'Inscription refusée.');
}

retour('evenements', 'Action inconnue.', 'erreur');
