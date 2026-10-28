<?php

class Post
{
    public $id_post, $id_canal, $id_utilisateur, $titre, $contenu, $date_publication;
    // issus des jointures
    public $auteur, $type_profil, $nb_reponses, $canal_nom, $type_acces;

    public function __construct(array $ligne = [])
    {
        foreach ($ligne as $cle => $valeur) {
            if (property_exists($this, $cle)) {
                $this->$cle = $valeur;
            }
        }
    }
}