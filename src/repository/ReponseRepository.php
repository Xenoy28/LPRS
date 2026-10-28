<?php

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Reponse.php';

class ReponseRepository
{
    private $bdd;

    public function __construct()
    {
        $this->bdd = (new Bdd())->getConnexionBdd();
    }

    public function listerParPost($idPost)
    {
        $r = $this->bdd->prepare("SELECT r.*, COALESCE(CONCAT(u.prenom, ' ', u.nom), 'Ancien membre') AS auteur, u.type_profil
            FROM reponse r LEFT JOIN utilisateur u ON u.id_utilisateur = r.id_utilisateur
            WHERE r.id_post = ? ORDER BY r.date_publication");
        $r->execute([$idPost]);
        return array_map(function ($l) { return new Reponse($l); }, $r->fetchAll(PDO::FETCH_ASSOC));
    }

    public function creer($idPost, $idUtilisateur, $contenu)
    {
        $this->bdd->prepare('INSERT INTO reponse (id_post, id_utilisateur, contenu) VALUES (?, ?, ?)')
            ->execute([$idPost, $idUtilisateur, $contenu]);
    }
}