<?php

namespace repository;

use Bdd;

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Entreprise.php';

class EntrepriseRepository
{
private $bdd;
public function __construct(){
    $this->bdd = (new Bdd())->getConnexionBdd();
}

// "https://www.Exemple.fr/" et "exemple.fr" doivent donner la même valeur (contrôle des doublons)
public static function normaliserSite($url){
    $u = strtolower(trim($url));
    $u = preg_replace('#^https?://#', '', $u);
    $u = preg_replace('#^www\.#', '', $u);
    return rtrim($u, '/');
}
    /* ---------- CREATE ---------- */
    public function creer(Entreprise $e)
    {
        $this->bdd->prepare('INSERT INTO entreprise (nom, adresse, code_postal, ville, site_web, secteur_activite, description)
                             VALUES (?, ?, ?, ?, ?, ?, ?)')
            ->execute([$e->nom, $e->adresse, $e->code_postal, $e->ville, $e->site_web, $e->secteur_activite, $e->description]);
        return (int)$this->bdd->lastInsertId();
    }
    /* ---------- READ ---------- */
    public function getById($id)
    {
        $r = $this->bdd->prepare('SELECT * FROM entreprise WHERE id_entreprise = ?');
        $r->execute([$id]);
        $l = $r->fetch(PDO::FETCH_ASSOC);
        return $l ? new Entreprise($l) : null;
    }

    // $sauf : ignorer cette entreprise (pour la modification)
    public function getParSite($siteNormalise, $sauf = 0)
    {
        $r = $this->bdd->prepare('SELECT * FROM entreprise WHERE site_web = ? AND id_entreprise <> ?');
        $r->execute([$siteNormalise, $sauf]);
        $l = $r->fetch(PDO::FETCH_ASSOC);
        return $l ? new Entreprise($l) : null;
    }

    public function liste($recherche = '')
    {
        $r = $this->bdd->prepare('SELECT e.*, (SELECT COUNT(*) FROM appartient_entreprise a WHERE a.id_entreprise = e.id_entreprise) AS nombre_membres
                                  FROM entreprise e WHERE e.nom LIKE ? ORDER BY e.nom');
        $r->execute(['%' . $recherche . '%']);
        return array_map(function ($l) { return new Entreprise($l); }, $r->fetchAll(PDO::FETCH_ASSOC));
    }

    // entreprises d'un utilisateur (avec son poste)
    public function mesEntreprises($idUtilisateur)
    {
        $r = $this->bdd->prepare('SELECT e.*, a.poste FROM entreprise e
                                  JOIN appartient_entreprise a ON a.id_entreprise = e.id_entreprise
                                  WHERE a.id_utilisateur = ? ORDER BY e.nom');
        $r->execute([$idUtilisateur]);
        return array_map(function ($l) { return new Entreprise($l); }, $r->fetchAll(PDO::FETCH_ASSOC));
    }

    public function membres($idEntreprise)
    {
        $r = $this->bdd->prepare('SELECT u.id_utilisateur, u.nom, u.prenom, u.type_profil, a.poste
                                  FROM appartient_entreprise a JOIN utilisateur u ON u.id_utilisateur = a.id_utilisateur
                                  WHERE a.id_entreprise = ? ORDER BY u.nom');
        $r->execute([$idEntreprise]);
        return $r->fetchAll(PDO::FETCH_ASSOC);
    }

    public function estMembre($idEntreprise, $idUtilisateur)
    {
        $r = $this->bdd->prepare('SELECT COUNT(*) FROM appartient_entreprise WHERE id_entreprise = ? AND id_utilisateur = ?');
        $r->execute([$idEntreprise, $idUtilisateur]);
        return $r->fetchColumn() > 0;
    }

    /* ---------- UPDATE ---------- */
    public function modifier(Entreprise $e)
    {
        $this->bdd->prepare('UPDATE entreprise SET nom = ?, adresse = ?, code_postal = ?, ville = ?, site_web = ?,
                             secteur_activite = ?, description = ? WHERE id_entreprise = ?')
            ->execute([$e->nom, $e->adresse, $e->code_postal, $e->ville, $e->site_web, $e->secteur_activite,
                $e->description, $e->id_entreprise]);
    }

    // rattachement d'un utilisateur à une entreprise (ou mise à jour de son poste)
    public function rattacher($idEntreprise, $idUtilisateur, $poste)
    {
        if ($this->estMembre($idEntreprise, $idUtilisateur)) {
            $this->bdd->prepare('UPDATE appartient_entreprise SET poste = ? WHERE id_entreprise = ? AND id_utilisateur = ?')
                ->execute([$poste, $idEntreprise, $idUtilisateur]);
        } else {
            $this->bdd->prepare('INSERT INTO appartient_entreprise (id_utilisateur, id_entreprise, poste) VALUES (?, ?, ?)')
                ->execute([$idUtilisateur, $idEntreprise, $poste]);
        }
    }

    /* ---------- DELETE ---------- */
    public function quitter($idEntreprise, $idUtilisateur)
    {
        $this->bdd->prepare('DELETE FROM appartient_entreprise WHERE id_entreprise = ? AND id_utilisateur = ?')
            ->execute([$idEntreprise, $idUtilisateur]);
    }
}