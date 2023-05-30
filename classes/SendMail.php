<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';

class SendMail
{
    public function __construct ($address, $content)
    {
        $mail = new PHPMailer(true);

        try {
            // Serwer SMTP Mailtrap
            $mail->isSMTP();
            $mail->Host = 'smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Username = '5268cd86305112'; // Podaj swój login z Mailtrap
            $mail->Password = 'a723ccd5a3651b'; // Podaj swoje hasło z Mailtrap
            $mail->SMTPSecure = 'tls';
            $mail->Port = 2525;

            // Nadawca i odbiorca
            $mail->setFrom('messages@page.com', 'MessagePage Login');
            $mail->addAddress($address, 'Recipient');

            // Treść e-maila
            $mail->isHTML(true);
            $mail->Subject = 'Kod do logowania';
            $mail->Body = $content;

            $mail->send();
            echo 'Wiadomość została wysłana';
        } catch (Exception $e) {
            echo "Nie udało się wysłać wiadomości. Błąd: {$mail->ErrorInfo}";
        }
    }
}