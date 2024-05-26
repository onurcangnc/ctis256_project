<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once './vendor/autoload.php';

class Mail {
    public static function send($to, $subject, $message, $FullName) {
        $mail = new PHPMailer(true);
        try {
            // SMTP Server settings
            $mail->isSMTP();
            $mail->Host       = 'mail.ctis256project.net.tr'; // CTIS256PROJECT SMTP Server
            $mail->SMTPAuth   = true;
            $mail->Username   = '';
            $mail->Password   = ''; // Uygulama şifresi
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            // Recipients
            $mail->setFrom('_mainaccount@ctis256project.net.tr', 'System Admin');
            $mail->addAddress($to, $FullName);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;

            $mail->send();
            echo 'Message has been sent to ' . $to . "<br>";
        } catch (Exception $e) {
            echo "<p>Message could not be sent. Mailer Error: {$mail->ErrorInfo}</p>";
        }
    }
}
?>