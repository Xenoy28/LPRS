<?php

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Evenement.php';

class EvenementRepository
{
    private $bdd;

    // Un événement est publié s'il n'a aucun organisateur étudiant, ou s'il a au moins un professeur parmi ses organisateurs.
    private $publie = "(EXISTS (SELECT 1 FROM organise o JOIN utilisateur u ON u.id_utilisateur = o.id_utilisateur
                                 WHERE o.id_evenement = e.id_evenement AND u.type_profil = 'professeur')
                        OR NOT EXISTS (SELECT 1 FROM organise o JOIN utilisateur u ON u.id_utilisateur = o.id_utilisateur
                                 WHERE o.id_evenement = e.id_evenement AND u.type_profil = 'etudiant'))";

    private $select;

    public function __construct()
    {
        $this->bdd = (new Bdd())->getConnexionBdd();
        $this->select = "SELECT e.*,
            (SELECT COUNT(*) FROM inscription_evenement i WHERE i.id_evenement = e.id_evenement AND i.statut = 'Inscrit') AS places_prises,
            " . $this->publie . " AS publie
            FROM evenement e";
    }

    private function liste_depuis(PDOStatement $r)
    {
        return array_map(function ($l) { return new Evenement($l); }, $r->fetchAll(PDO::FETCH_ASSOC));
    }

    /* ---------- CREATE ---------- */
    // le créateur devient automatiquement organisateur
    public function creer(Evenement $e, $idCreateur)
    {
        $this->bdd->prepare('INSERT INTO evenement (titre, type_evenement, description, date_debut, date_fin, lieu, nombre_places, elements_requis)
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?)')
            ->execute([$e->titre, $e->type_evenement, $e->description, $e->date_debut, $e->date_fin, $e->lieu,
                $e->nombre_places, $e->elements_requis]);
        $id = (int)$this->bdd->lastInsertId();
        $this->ajouterOrganisateur($id, $idCreateur);
        return $id;
    }

    /* ---------- READ ---------- */
    public function getById($id)
    {
        $r = $this->bdd->prepare($this->select . ' WHERE e.id_evenement = ?');
        $r->execute([$id]);
        $l = $r->fetch(PDO::FETCH_ASSOC);
        return $l ? new Evenement($l) : null;
    }

    // événements publiés, à venir (ou passés si $passes = true)
    public function listePublies($passes = false, $limite = 0)
    {
        $sql = $this->select . ' WHERE ' . $this->publie . ' AND e.date_debut ' . ($passes ? '<' : '>=') . ' CURDATE()
                ORDER BY e.date_debut ' . ($passes ? 'DESC' : 'ASC');
        if ($limite > 0) {
            $sql .= ' LIMIT ' . (int)$limite;
        }
        return $this->liste_depuis($this->bdd->query($sql));
    }

    public function organisesPar($idUtilisateur)
    {
        $r = $this->bdd->prepare($this->select . ' WHERE e.id_evenement IN (SELECT id_evenement FROM organise WHERE id_utilisateur = ?)
                                  ORDER BY e.date_debut DESC');
        $r->execute([$idUtilisateur]);
        return $this->liste_depuis($r);
    }

    public function inscritsPar($idUtilisateur)
    {
        $r = $this->bdd->prepare('SELECT e.*, i.statut AS statut_inscription FROM evenement e
                                  JOIN inscription_evenement i ON i.id_evenement = e.id_evenement
                                  WHERE i.id_utilisateur = ? ORDER BY e.date_debut DESC');
        $r->execute([$idUtilisateur]);
        return $this->liste_depuis($r);
    }

    public function organisateurs($idEvenement)
    {
        $r = $this->bdd->prepare('SELECT u.id_utilisateur, u.nom, u.prenom, u.type_profil
                                  FROM organise o JOIN utilisateur u ON u.id_utilisateur = o.id_utilisateur
                                  WHERE o.id_evenement = ? ORDER BY u.nom');
        $r->execute([$idEvenement]);
        return $r->fetchAll(PDO::FETCH_ASSOC);
    }

    public function estOrganisateur($idEvenement, $idUtilisateur)
    {
        $r = $this->bdd->prepare('SELECT COUNT(*) FROM organise WHERE id_evenement = ? AND id_utilisateur = ?');
        $r->execute([$idEvenement, $idUtilisateur]);
        return $r->fetchColumn() > 0;
    }

    // inscription d'un utilisateur à un événement (ou null)
    public function inscription($idEvenement, $idUtilisateur)
    {
        $r = $this->bdd->prepare('SELECT * FROM inscription_evenement WHERE id_evenement = ? AND id_utilisateur = ?');
        $r->execute([$idEvenement, $idUtilisateur]);
        return $r->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function inscriptionParId($idInscription)
    {
        $r = $this->bdd->prepare('SELECT * FROM inscription_evenement WHERE id_inscription = ?');
        $r->execute([$idInscription]);
        return $r->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function inscrits($idEvenement)
    {
        $r = $this->bdd->prepare('SELECT i.id_inscription, i.statut, i.date_inscription, u.nom, u.prenom, u.email, u.type_profil
                                  FROM inscription_evenement i JOIN utilisateur u ON u.id_utilisateur = i.id_utilisateur
                                  WHERE i.id_evenement = ? ORDER BY i.id_inscription');
        $r->execute([$idEvenement]);
        return $r->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ---------- UPDATE ---------- */
    public function modifier(Evenement $e)
    {
        $this->bdd->prepare('UPDATE evenement SET titre = ?, type_evenement = ?, description = ?, date_debut = ?, date_fin = ?,
                             lieu = ?, nombre_places = ?, elements_requis = ? WHERE id_evenement = ?')
            ->execute([$e->titre, $e->type_evenement, $e->description, $e->date_debut, $e->date_fin, $e->lieu,
                $e->nombre_places, $e->elements_requis, $e->id_evenement]);
    }

    public function ajouterOrganisateur($idEvenement, $idUtilisateur)
    {
        if (!$this->estOrganisateur($idEvenement, $idUtilisateur)) {
            $this->bdd->prepare('INSERT INTO organise (id_utilisateur, id_evenement) VALUES (?, ?)')
                ->execute([$idUtilisateur, $idEvenement]);
        }
    }

    public function inscrire($idEvenement, $idUtilisateur)
    {
        $this->bdd->prepare("INSERT INTO inscription_evenement (id_utilisateur, id_evenement, date_inscription, statut)
                             VALUES (?, ?, CURDATE(), 'Inscrit')")->execute([$idUtilisateur, $idEvenement]);
    }

    // 'Inscrit' ou 'Refusé'
    public function changerStatutInscription($idInscription, $statut)
    {
        $this->bdd->prepare('UPDATE inscription_evenement SET statut = ? WHERE id_inscription = ?')
            ->execute([$statut, $idInscription]);
    }

    /* ---------- DELETE ---------- */
    // un événement garde toujours au moins un organisateur
    public function retirerOrganisateur($idEvenement, $idUtilisateur)
    {
        if (count($this->organisateurs($idEvenement)) <= 1) {
            return false;
        }
        $this->bdd->prepare('DELETE FROM organise WHERE id_evenement = ? AND id_utilisateur = ?')
            ->execute([$idEvenement, $idUtilisateur]);
        return true;
    }

    public function annulerInscription($idEvenement, $idUtilisateur)
    {
        $this->bdd->prepare('DELETE FROM inscription_evenement WHERE id_evenement = ? AND id_utilisateur = ?')
            ->execute([$idEvenement, $idUtilisateur]);
    }
}
