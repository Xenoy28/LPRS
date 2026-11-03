<?php

namespace repository;

use Bdd;
use modele\Candidature;
use PDO;
use PDOStatement;

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Candidature.php';

class CandidatureRepository
{
    private $bdd;

    private $select = 'SELECT c.*, u.nom, u.prenom, u.email, u.type_profil, o.titre AS offre_titre
                       FROM candidature c
                       JOIN utilisateur u ON u.id_utilisateur = c.id_utilisateur
                       JOIN offre o ON o.id_offre = c.id_offre';

    public function __construct()
    {
        $this->bdd = (new Bdd())->getConnexionBdd();
    }

    private function liste_depuis(PDOStatement $r)
    {
        return array_map(function ($l) {
            return new Candidature($l);
        }, $r->fetchAll(PDO::FETCH_ASSOC));
    }

    public function postuler($idOffre, $idUtilisateur, $motivation)
    {
        $this->bdd->prepare('INSERT INTO candidature (id_utilisateur, id_offre, motivation, date_candidature, statut)
                             VALUES (?, ?, ?, CURDATE(), \'En attente\')')
            ->execute([$idUtilisateur, $idOffre, $motivation]);
    }

    public function existe($idOffre, $idUtilisateur)
    {
        $r = $this->bdd->prepare('SELECT COUNT(*) FROM candidature WHERE id_offre = ? AND id_utilisateur = ?');
        $r->execute([$idOffre, $idUtilisateur]);
        return $r->fetchColumn() > 0;
    }

    public function getById($id)
    {
        $r = $this->bdd->prepare($this->select . ' WHERE c.id_candidature = ?');
        $r->execute([$id]);
        $l = $r->fetch(PDO::FETCH_ASSOC);
        return $l ? new Candidature($l) : null;
    }

    // candidatures visibles par l'auteur (les candidatures refusées sont cachées)
    public function listeParOffre($idOffre)
    {
        $r = $this->bdd->prepare($this->select . " WHERE c.id_offre = ? AND c.statut <> 'Refusée' ORDER BY c.id_candidature DESC");
        $r->execute([$idOffre]);
        return $this->liste_depuis($r);
    }

    public function listeParUtilisateur($idUtilisateur)
    {
        $r = $this->bdd->prepare($this->select . ' WHERE c.id_utilisateur = ? ORDER BY c.id_candidature DESC');
        $r->execute([$idUtilisateur]);
        return $this->liste_depuis($r);
    }

    // refus = la candidature est cachée à l'auteur
    public function refuser($id)
    {
        $this->bdd->prepare("UPDATE candidature SET statut = 'Refusée' WHERE id_candidature = ?")->execute([$id]);
    }
}
