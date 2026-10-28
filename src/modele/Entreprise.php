<?php

namespace modele;

class Entreprise
{
    public $id_entreprise, $nom, $adresse, $code_postal, $ville, $site_web, $secteur_activite, $description;
    // issus de jointures
    public $nombre_membres, $poste;

    public function __construct(array $ligne = [])
    {
        foreach ($ligne as $cle => $valeur) {
            if (property_exists($this, $cle)) {
                $this->$cle = $valeur;
            }
        }
    }

}