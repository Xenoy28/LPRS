<?php

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Offre.php';

class OffreRepository
{
    const TYPES = ['Stage', 'Alternance', 'CDD', 'CDI'];
    const CIBLES = ['tous' => 'Tous (étudiants et alumni)', 'etudiant' => 'Étudiants uniquement', 'alumni' => 'Alumni uniquement'];

    private $bdd;

    private $select = 'SELECT o.*, e.nom AS entreprise_nom, u.nom AS auteur_nom, u.prenom AS auteur_prenom
                       FROM offre o
                       LEFT JOIN entreprise e ON e.id_entreprise = o.id_entreprise
                       LEFT JOIN utilisateur u ON u.id_utilisateur = o.id_auteur';

    public function __construct()
    {
        $this->bdd = (new Bdd())->getConnexionBdd();
    }

    private function liste_depuis(PDOStatement $r)
    {
        return array_map(function ($l) { return new Offre($l); }, $r->fetchAll(PDO::FETCH_ASSOC));
    }

    //Créer//
    public function creer(Offre $o)
    {
        $this->bdd->prepare('INSERT INTO offre (id_entreprise, id_auteur, titre, description, missions, type_offre, salaire,
                             lieu, date_publication, date_expiration, etat, profil_cible)
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), ?, \'Ouverte\', ?)')
            ->execute([$o->id_entreprise, $o->id_auteur, $o->titre, $o->description, $o->missions, $o->type_offre,
                $o->salaire, $o->lieu, $o->date_expiration, $o->profil_cible]);
        return (int)$this->bdd->lastInsertId();
    }
    public function getById($id)
    {
        $r = $this->bdd->prepare($this->select . ' WHERE o.id_offre = ?');
        $r->execute([$id]);
        $l = $r->fetch(PDO::FETCH_ASSOC);
        return $l ? new Offre($l) : null;
    }
    // offres ouvertes visibles par un étudiant / alumni / professeur
    public function listeOuvertes($role, $type = '', $recherche = '')
    {
        $sql = $this->select . " WHERE o.etat = 'Ouverte' AND (o.date_expiration IS NULL OR o.date_expiration >= CURDATE())";
        $params = [];
        if ($role === 'etudiant' || $role === 'alumni') {
            $sql .= " AND o.profil_cible IN ('tous', ?)";
            $params[] = $role;
        }
        if ($type !== '') {
            $sql .= ' AND o.type_offre = ?';
            $params[] = $type;
        }
        if ($recherche !== '') {
            $sql .= ' AND (o.titre LIKE ? OR o.description LIKE ? OR e.nom LIKE ?)';
            array_push($params, "%$recherche%", "%$recherche%", "%$recherche%");
        }
        $r = $this->bdd->prepare($sql . ' ORDER BY o.id_offre DESC');
        $r->execute($params);
        return $this->liste_depuis($r);
    }
    // offres d'un auteur + offres de ses entreprises (ouvertes ou non)
    public function listeGeree($idUtilisateur)
    {
        $r = $this->bdd->prepare($this->select . ' WHERE o.id_auteur = ?
            OR o.id_entreprise IN (SELECT id_entreprise FROM appartient_entreprise WHERE id_utilisateur = ?)
            ORDER BY o.id_offre DESC');
        $r->execute([$idUtilisateur, $idUtilisateur]);
        return $this->liste_depuis($r);
    }
    // Accès offre pour utilisateur
    public function peutConsulter(Offre $o, $idUtilisateur, $role)
    {
        if ((int)$o->id_auteur === (int)$idUtilisateur) {
            return true;
        }
        if ($o->id_entreprise) {
            $r = $this->bdd->prepare('SELECT COUNT(*) FROM appartient_entreprise WHERE id_entreprise = ? AND id_utilisateur = ?');
            $r->execute([$o->id_entreprise, $idUtilisateur]);
            if ($r->fetchColumn() > 0) {
                return true;
            }
        }
        if ($role === 'professeur') {
            return true;
        }
        return ($role === 'etudiant' || $role === 'alumni') && ($o->profil_cible === 'tous' || $o->profil_cible === $role);
    }
    public function modifier(Offre $o)
    {
        $this->bdd->prepare('UPDATE offre SET id_entreprise = ?, titre = ?, description = ?, missions = ?, type_offre = ?,
                             salaire = ?, lieu = ?, date_expiration = ?, profil_cible = ? WHERE id_offre = ?')
            ->execute([$o->id_entreprise, $o->titre, $o->description, $o->missions, $o->type_offre, $o->salaire,
                $o->lieu, $o->date_expiration, $o->profil_cible, $o->id_offre]);
    }

    // Statut de l'offre
    public function changerEtat($id, $etat)
    {
        $this->bdd->prepare('UPDATE offre SET etat = ? WHERE id_offre = ?')->execute([$etat, $id]);
    }
}
