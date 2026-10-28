<?php

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Utilisateur.php';

class UtilisateurRepository
{
    // profil => colonnes de sa table (la table porte le même nom que le profil)
    const COLONNES = [
        'etudiant' => ['id_formation', 'promotion', 'cv'],
        'alumni' => ['promotion', 'cv'],
        'partenaire' => ['poste', 'motif_inscription'],
        'professeur' => ['specialite'],
        'gestionnaire' => ['role'],
    ];

    private $bdd;

    public function __construct()
    {
        $this->bdd = (new Bdd())->getConnexionBdd();
    }

    private function hydrater($l, $details = [])
    {
        return new Utilisateur($l['id_utilisateur'], $l['nom'], $l['prenom'], $l['email'],
            $l['mot_de_passe'] ?? null, $l['type_profil'], $l['statut_validation'], $details);
    }

    // CREATE
    public function inscrire(Utilisateur $u)
    {
        $type = $u->getTypeProfil();
        $r = $this->bdd->prepare('INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, type_profil, statut_validation) VALUES (?, ?, ?, ?, ?, ?)');
        $r->execute([$u->getNomUtilisateur(), $u->getPrenomUtilisateur(), $u->getEmailUtilisateur(),
            $u->getMdpUtilisateur(), $type, $u->getStatusValidation()]);
        $id = $this->bdd->lastInsertId();

        $colonnes = self::COLONNES[$type];
        $valeurs = [$id];
        foreach ($colonnes as $c) {
            $valeurs[] = $u->getDetails()[$c] ?? null;
        }
        $this->bdd->prepare('INSERT INTO ' . $type . ' (id_utilisateur, ' . implode(', ', $colonnes) . ') VALUES (?' . str_repeat(', ?', count($colonnes)) . ')')
            ->execute($valeurs);
        return $id;
    }

    // READ
    public function getById($id)
    {
        $r = $this->bdd->prepare('SELECT * FROM utilisateur WHERE id_utilisateur = ?');
        $r->execute([$id]);
        $l = $r->fetch(PDO::FETCH_ASSOC);
        if (!$l) {
            return null;
        }
        $type = $l['type_profil'];
        $r = $this->bdd->prepare('SELECT ' . implode(', ', self::COLONNES[$type]) . ' FROM ' . $type . ' WHERE id_utilisateur = ?');
        $r->execute([$id]);
        return $this->hydrater($l, $r->fetch(PDO::FETCH_ASSOC) ?: []);
    }

    public function getUtilisateurByEmail($email)
    {
        $r = $this->bdd->prepare('SELECT * FROM utilisateur WHERE email = ?');
        $r->execute([$email]);
        $l = $r->fetch(PDO::FETCH_ASSOC);
        return $l ? $this->hydrater($l) : null;
    }

    public function emailExiste($email, $sauf = 0)
    {
        $r = $this->bdd->prepare('SELECT COUNT(*) FROM utilisateur WHERE email = ? AND id_utilisateur <> ?');
        $r->execute([$email, $sauf]);
        return $r->fetchColumn() > 0;
    }

    public function liste()
    {
        $liste = [];
        foreach ($this->bdd->query('SELECT * FROM utilisateur ORDER BY id_utilisateur DESC') as $l) {
            $liste[] = $this->hydrater($l);
        }
        return $liste;
    }

    public function listeFormations()
    {
        return $this->bdd->query('SELECT id_formation, nom_formation FROM formation')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function nombreGestionnaires()
    {
        return $this->bdd->query("SELECT COUNT(*) FROM utilisateur WHERE type_profil = 'gestionnaire'")->fetchColumn();
    }

//UPDATE
    public function modifier(Utilisateur $u)
    {
        $this->bdd->prepare('UPDATE utilisateur SET nom = ?, prenom = ?, email = ?, mot_de_passe = ? WHERE id_utilisateur = ?')
            ->execute([$u->getNomUtilisateur(), $u->getPrenomUtilisateur(), $u->getEmailUtilisateur(),
                $u->getMdpUtilisateur(), $u->getIdUtilisateur()]);

        $type = $u->getTypeProfil();
        $sets = [];
        $valeurs = [];
        foreach (self::COLONNES[$type] as $c) {
            $sets[] = $c . ' = ?';
            $valeurs[] = $u->getDetails()[$c] ?? null;
        }
        $valeurs[] = $u->getIdUtilisateur();
        $this->bdd->prepare('UPDATE ' . $type . ' SET ' . implode(', ', $sets) . ' WHERE id_utilisateur = ?')->execute($valeurs);
    }
    // DELETE : supprime l'utilisateur et tout ce qui lui est rattaché
    public function supprimer($id)
    {
        $u = $this->getById($id);
        if (!$u) {
            return;
        }
        $requetes = [
            'DELETE FROM candidature WHERE id_offre IN (SELECT id_offre FROM offre WHERE id_auteur = ?)',
            'DELETE FROM candidature WHERE id_utilisateur = ?',
            'DELETE FROM offre WHERE id_auteur = ?',
            'DELETE FROM inscription_evenement WHERE id_utilisateur = ?',
            'DELETE FROM organise WHERE id_utilisateur = ?',
            'DELETE FROM appartient_entreprise WHERE id_utilisateur = ?',
            'DELETE FROM professeur_formation WHERE id_utilisateur = ?',
            'DELETE FROM reinitialisation_mdp WHERE id_utilisateur = ?',
            'DELETE FROM ' . $u->getTypeProfil() . ' WHERE id_utilisateur = ?',
            'DELETE FROM utilisateur WHERE id_utilisateur = ?',
        ];
        foreach ($requetes as $sql) {
            $this->bdd->prepare($sql)->execute([$id]);
        }
    }
}

