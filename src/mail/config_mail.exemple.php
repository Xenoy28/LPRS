<?php
// Copie ce fichier en "config_mail.php" (même dossier) et remplis tes informations.
// config_mail.php contient un mot de passe : il est ignoré par Git, ne le commite jamais.
//
// Exemple Gmail : hote smtp.gmail.com, port 587, chiffrement 'tls'.
//   identifiant = ton adresse Gmail
//   mot_de_passe = un "mot de passe d'application" (compte Google > Sécurité > Validation en 2 étapes),
//   PAS ton mot de passe Gmail habituel.
//
// Pour tester en local sans vrai serveur : Mailpit ou MailHog
//   hote 'localhost', port 1025, chiffrement '', authentification false
return [
    'hote'             => 'smtp.gmail.com',
    'port'             => 587,
    'chiffrement'      => 'tls',          // 'tls' (port 587), 'ssl' (port 465) ou '' (aucun)
    'authentification' => true,
    'identifiant'      => 'ton.adresse@gmail.com',
    'mot_de_passe'     => 'mot-de-passe-application',
    'expediteur_email' => 'ton.adresse@gmail.com',
    'expediteur_nom'   => 'LPRS',
];
