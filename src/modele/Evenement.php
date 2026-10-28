<?php

class Evenement
{
    public $id_evenement, $titre, $type_evenement, $description, $date_debut, $date_fin, $lieu,
        $nombre_places, $elements_requis;
    // issus de jointures / calculs
    public $places_prises, $publie, $statut_inscription;

    public function __construct(array $ligne = [])
    {
        foreach ($ligne as $cle => $valeur) {
            if (property_exists($this, $cle)) {
                $this->$cle = $valeur;
            }
        }
    }
}
