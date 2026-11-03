<?php

require_once __DIR__ . '/../bdd/Bdd.php';

class ContactRepository
{
    private $bdd;

    public function __construct()
    {
        $this->bdd = (new Bdd())->getConnexionBdd();
    }

    public function creer($nom, $email, $sujet, $message)
    {
        $this->bdd->prepare('INSERT INTO message_contact (nom, email, sujet, message) VALUES (?, ?, ?, ?)')
            ->execute([$nom, $email, $sujet, $message]);
    }
}