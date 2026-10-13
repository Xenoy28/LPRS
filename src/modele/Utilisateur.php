<?php

class Utilisateur {
    private $idUtilisateur;
    private $nomUtilisateur;
    private $prenomUtilisateur;
    private $emailUtilisateur;
    private $mdpUtilisateur;
    private $type_profil;
    private $status_validation;
    private $details; // champs propres au profil (promotion, cv, poste, ...)

    public function __construct($idUtilisateur = null, $nom = null, $prenom = null, $email = null, $mdp = null,
                                $type_profil = null, $status_validation = 'En attente', $details = []){
        $this->idUtilisateur = $idUtilisateur;
        $this->nomUtilisateur = $nom;
        $this->prenomUtilisateur = $prenom;
        $this->emailUtilisateur = $email;
        $this->mdpUtilisateur = $mdp;
        $this->type_profil = $type_profil;
        $this->status_validation = $status_validation;
        $this->details = $details;
    }
    public function getIdUtilisateur(){
        return $this->idUtilisateur;
    }
    public function setIdUtilisateur($idUtilisateur){
        $this->idUtilisateur = $idUtilisateur;
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

    public function getDetails()
    {
        return $this->details;
    }

    public function setDetails($details): void
    {
        $this->details = $details;
    }

}