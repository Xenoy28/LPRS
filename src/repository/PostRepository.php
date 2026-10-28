<?php

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Post.php';

class PostRepository
{
    private $bdd;

    // Requête commune : auteur ("Ancien membre" si compte supprimé), nb de réponses, canal
    const SELECT = "SELECT p.*, COALESCE(CONCAT(u.prenom, ' ', u.nom), 'Ancien membre') AS auteur, u.type_profil,
            (SELECT COUNT(*) FROM reponse r WHERE r.id_post = p.id_post) AS nb_reponses,
            c.nom AS canal_nom, c.type_acces
        FROM post p
        JOIN canal_forum c ON c.id_canal = p.id_canal
        LEFT JOIN utilisateur u ON u.id_utilisateur = p.id_utilisateur ";

    public function __construct()
    {
        $this->bdd = (new Bdd())->getConnexionBdd();
    }

    private function lire($sql, $params = [])
    {
        $r = $this->bdd->prepare($sql);
        $r->execute($params);
        return array_map(function ($l) { return new Post($l); }, $r->fetchAll(PDO::FETCH_ASSOC));
    }

    public function parId($id)
    {
        return $this->lire(self::SELECT . 'WHERE p.id_post = ?', [$id])[0] ?? null;
    }

    public function compterParCanal($idCanal)
    {
        $r = $this->bdd->prepare('SELECT COUNT(*) FROM post WHERE id_canal = ?');
        $r->execute([$idCanal]);
        return (int)$r->fetchColumn();
    }

    public function listerParCanal($idCanal, $limite, $decalage)
    {
        return $this->lire(self::SELECT . 'WHERE p.id_canal = ? ORDER BY p.date_publication DESC
            LIMIT ' . (int)$limite . ' OFFSET ' . (int)$decalage, [$idCanal]);
    }

    // Posts écrits par l'utilisateur OU dans lesquels il a répondu
    public function mesPosts($idUtilisateur)
    {
        return $this->lire(self::SELECT . 'WHERE p.id_utilisateur = ?
            OR p.id_post IN (SELECT id_post FROM reponse WHERE id_utilisateur = ?)
            ORDER BY p.date_publication DESC LIMIT 200', [$idUtilisateur, $idUtilisateur]);
    }

    public function creer($idCanal, $idUtilisateur, $titre, $contenu)
    {
        $this->bdd->prepare('INSERT INTO post (id_canal, id_utilisateur, titre, contenu) VALUES (?, ?, ?, ?)')
            ->execute([$idCanal, $idUtilisateur, $titre, $contenu]);
        return (int)$this->bdd->lastInsertId();
    }
}