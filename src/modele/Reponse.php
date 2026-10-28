<?php

class Reponse
{
    public $id_reponse, $id_post, $id_utilisateur, $contenu, $date_publication;
    // issus de la jointure
    public $auteur, $type_profil;

    public function __construct(array $ligne = [])
    {
        foreach ($ligne as $cle => $valeur) {
            if (property_exists($this, $cle)) {
                $this->$cle = $valeur;
            }
        }
    }
}