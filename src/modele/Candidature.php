<?php

namespace modele;
class Candidature
{
    public $id_candidature, $id_utilisateur, $id_offre, $motivation, $date_candidature, $statut;
    // issus de jointures
    public $nom, $prenom, $email, $type_profil, $offre_titre;

    public function __construct(array $ligne = [])
    {
        foreach ($ligne as $cle => $valeur) {
            if (property_exists($this, $cle)) {
                $this->$cle = $valeur;
            }
        }
    }
}
