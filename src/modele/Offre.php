<?php
class Offre
{
    public $id_offre;
    public $id_entreprise;
    public $id_auteur;
    public $titre;
    public $description;
    public $missions;
    public $type_offre;
    public $salaire;
    public $lieu;
    public $date_publication;
    public $date_expiration;
    public $etat;
    public $profil_cible;
    public $entreprise_nom;
    public $auteur_nom;
    public $auteur_prenom;
    public function __construct(array $ligne = []){
        foreach ($ligne as $cle => $valeur) {
            if (property_exists($this, $cle)) {
                $this->$cle = $valeur;
            }
        }
    }
}