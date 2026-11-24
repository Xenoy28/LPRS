<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

class Mail
{
    // Envoie un email texte avec PHPMailer (SMTP). Retourne true si l'email est parti.
    // Si config_mail.php est absent ou si l'envoi échoue (poste de dev), le message est
    // écrit dans mails.log à la racine du projet pour pouvoir quand même tester.
    public static function envoyer($destinataire, $sujet, $corps)
    {
        $fichierConfig = __DIR__ . '/config_mail.php';
        if (!is_file($fichierConfig)) {
            self::journaliser($destinataire, $sujet, $corps, 'config_mail.php absent');
            return false;
        }
        $config = require $fichierConfig;

        $mail = new PHPMailer(true); // true : les erreurs sont des exceptions
        try {
            $mail->isSMTP();
            $mail->Host = $config['hote'];
            $mail->Port = (int)$config['port'];
            $mail->Timeout = 10;
            $mail->SMTPAuth = !empty($config['authentification']);
            if ($mail->SMTPAuth) {
                $mail->Username = $config['identifiant'];
                $mail->Password = $config['mot_de_passe'];
            }
            if ($config['chiffrement'] === 'tls') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } elseif ($config['chiffrement'] === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = '';
                $mail->SMTPAutoTLS = false;
            }

            $mail->CharSet = 'UTF-8';
            $mail->setFrom($config['expediteur_email'], $config['expediteur_nom']);
            $mail->addAddress($destinataire);
            $mail->isHTML(false);
            $mail->Subject = $sujet;
            $mail->Body = $corps;

            $mail->send();
            return true;
        } catch (Exception $e) {
            self::journaliser($destinataire, $sujet, $corps, 'échec : ' . $mail->ErrorInfo);
            return false;
        }
    }

    private static function journaliser($destinataire, $sujet, $corps, $raison)
    {
        file_put_contents(__DIR__ . '/../../mails.log',
            date('Y-m-d H:i:s') . " | A : $destinataire | $sujet | ($raison)\n$corps\n----\n", FILE_APPEND);
    }
}
