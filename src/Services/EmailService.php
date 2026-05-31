<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    public static function send($to, $subject, $body) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'] ?? 'mailpit';
            $mail->Port       = $_ENV['SMTP_PORT'] ?? 1025;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            
            // Authentification
            if (!empty($_ENV['SMTP_USER'])) {
                $mail->SMTPAuth = true;
                $mail->Username = $_ENV['SMTP_USER'];
                $mail->Password = $_ENV['SMTP_PASS'];
                
            } else {
                $mail->SMTPAuth = false;
            }
            $mail->CharSet = 'UTF-8';

            // Expéditeur et Destinataire
            $mail->setFrom($_ENV['FROM_EMAIL'] ?? 'noreply@jobflow.local', $_ENV['FROM_NAME'] ?? 'JobFlow');
            $mail->addAddress($to);

            // Contenu du mail
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags($body); 

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("[EmailService] Erreur lors de l'envoi à $to : " . $mail->ErrorInfo);
            return false;
        }
    }
}
