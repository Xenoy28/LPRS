<?php

class Utilisateur {
    private $idUtilisateur;
    private $nomUtilisateur;
    private $prenomUtilisateur;
    private $emailUtilisateur;
    private $mdpUtilisateur;
    private $type_profil;
    private $status_validation;
    public function __construct($connexionBdd){
        $this->connexionBdd = $connexionBdd;
    }
    public function getIdUtilisateur(){
        return $this->idUtilisateur;
    }
    public function setIdUtilisateur($idUtilisateur){
        $this->idUtilisateur = $idUtilisateur;
    }

    public function getConnexionBdd()
    {
        return $this->connexionBdd;
    }

    public function setConnexionBdd($connexionBdd): void
    {
        $this->connexionBdd = $connexionBdd;
    }

    public function getNomUtilisateur()
    {
        return $this->nomUtilisateur;
    }

    public function setNomUtilisateur($nomUtilisateur): void
    {
        $this->nomUtilisateur = $nomUtilisateur;
    }


    public function getPrenomUtilisateur()
    {
        return $this->prenomUtilisateur;
    }


    public function setPrenomUtilisateur($prenomUtilisateur): void
    {
        $this->prenomUtilisateur = $prenomUtilisateur;
    }


    public function getEmailUtilisateur()
    {
        return $this->emailUtilisateur;
    }


    public function setEmailUtilisateur($emailUtilisateur): void
    {
        $this->emailUtilisateur = $emailUtilisateur;
    }


    public function getMdpUtilisateur()
    {
        return $this->mdpUtilisateur;
    }


    public function setMdpUtilisateur($mdpUtilisateur): void
    {
        $this->mdpUtilisateur = $mdpUtilisateur;
    }


    public function getTypeProfil()
    {
        return $this->type_profil;
    }


    public function setTypeProfil($type_profil): void
    {
        $this->type_profil = $type_profil;
    }


    public function getStatusValidation()
    {
        return $this->status_validation;
    }


    public function setStatusValidation($status_validation): void
    {
        $this->status_validation = $status_validation;
    }

}
