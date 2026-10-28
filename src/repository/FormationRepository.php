<?php

require_once __DIR__ . '/../bdd/Bdd.php';

class FormationRepository
{
    private $bdd;

    public function __construct()
    {
        $this->bdd = (new Bdd())->getConnexionBdd();
    }

    public function liste()
    {
        return $this->bdd->query('SELECT id_formation, nom_formation FROM formation ORDER BY nom_formation')
            ->fetchAll(PDO::FETCH_ASSOC);
    }
    public function idsDuProfesseur($idProfesseur)
    {
        $r = $this->bdd->prepare('SELECT id_formation FROM professeur_formation WHERE id_utilisateur = ?');
        $r->execute([$idProfesseur]);
        return array_map('intval', $r->fetchAll(PDO::FETCH_COLUMN));
    }

    public function definirPourProfesseur($idProfesseur, array $ids)
    {
        $valides = array_column($this->liste(), 'id_formation');
        $this->bdd->prepare('DELETE FROM professeur_formation WHERE id_utilisateur = ?')->execute([$idProfesseur]);
        foreach (array_unique($ids) as $id) {
            if (in_array($id, $valides)) {
                $this->bdd->prepare('INSERT INTO professeur_formation (id_utilisateur, id_formation) VALUES (?, ?)')
                    ->execute([$idProfesseur, $id]);
            }
        }
    }
}
