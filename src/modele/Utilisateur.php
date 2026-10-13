<?php

class Utilisateur {
    private PDO $connexionBdd;
    private ?int $idUtilisateur = null;
    private ?string $nomUtilisateur = null;
    private ?string $prenomUtilisateur = null;
    private ?string $emailUtilisateur = null;
    private ?string $mdpUtilisateur = null;
    private ?string $type_profil = null;
    private ?string $statut_validation = null;

    public function __construct(PDO $connexionBdd){
        $this->connexionBdd = $connexionBdd;
    }

    public function getIdUtilisateur(): ?int
    {
        return $this->idUtilisateur;
    }

    public function setIdUtilisateur(int $idUtilisateur): void
    {
        $this->idUtilisateur = $idUtilisateur;
    }

    public function getConnexionBdd(): PDO
    {
        return $this->connexionBdd;
    }

    public function setConnexionBdd(PDO $connexionBdd): void
    {
        $this->connexionBdd = $connexionBdd;
    }

    public function getNomUtilisateur(): ?string
    {
        return $this->nomUtilisateur;
    }

    public function setNomUtilisateur(string $nomUtilisateur): void
    {
        $this->nomUtilisateur = $nomUtilisateur;
    }


    public function getPrenomUtilisateur(): ?string
    {
        return $this->prenomUtilisateur;
    }


    public function setPrenomUtilisateur(string $prenomUtilisateur): void
    {
        $this->prenomUtilisateur = $prenomUtilisateur;
    }


    public function getEmailUtilisateur(): ?string
    {
        return $this->emailUtilisateur;
    }


    public function setEmailUtilisateur(string $emailUtilisateur): void
    {
        $this->emailUtilisateur = $emailUtilisateur;
    }


    public function getMdpUtilisateur(): ?string
    {
        return $this->mdpUtilisateur;
    }


    public function setMdpUtilisateur(string $mdpUtilisateur): void
    {
        $this->mdpUtilisateur = $mdpUtilisateur;
    }


    public function getTypeProfil(): ?string
    {
        return $this->type_profil;
    }


    public function setTypeProfil(string $type_profil): void
    {
        $this->type_profil = $type_profil;
    }


    public function getStatutValidation(): ?string
    {
        return $this->statut_validation;
    }


    public function setStatutValidation(string $statut_validation): void
    {
        $this->statut_validation = $statut_validation;
    }

}
