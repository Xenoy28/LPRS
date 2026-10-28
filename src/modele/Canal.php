<?php

class Canal
{
    public $id_canal, $nom, $description, $type_acces, $roles_createurs, $nb_posts;

    public function __construct(array $ligne = [])
    {
        foreach ($ligne as $cle => $valeur) {
            if (property_exists($this, $cle)) {
                $this->$cle = $valeur;
            }
        }
    }

    private function dans($liste, $role)
    {
        return in_array($role, explode(',', (string)$liste), true);
    }

    // Lire et répondre : 'tous' ou un rôle de la liste (ex : 'alumni,partenaire')
    public function peutAcceder($role)
    {
        return $this->type_acces === 'tous' || $this->dans($this->type_acces, $role);
    }

    // Créer un sujet : avoir accès + être dans roles_createurs s'il est renseigné
    public function peutCreer($role)
    {
        return $this->peutAcceder($role)
            && (empty($this->roles_createurs) || $this->dans($this->roles_createurs, $role));
    }
}