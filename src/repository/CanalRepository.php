<?php

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Canal.php';

class CanalRepository
{
    private $bdd;

    public function __construct()
    {
        $this->bdd = (new Bdd())->getConnexionBdd();
    }

    private function lire($sql, $params = [])
    {
        $r = $this->bdd->prepare($sql);
        $r->execute($params);
        return array_map(function ($l) { return new Canal($l); }, $r->fetchAll(PDO::FETCH_ASSOC));
    }

    public function tous()
    {
        return $this->lire('SELECT c.*, (SELECT COUNT(*) FROM post p WHERE p.id_canal = c.id_canal) AS nb_posts
            FROM canal_forum c ORDER BY c.id_canal');
    }

    public function parId($id)
    {
        return $this->lire('SELECT * FROM canal_forum WHERE id_canal = ?', [$id])[0] ?? null;
    }
}