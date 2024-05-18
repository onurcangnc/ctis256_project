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
            $mail->Host       = 'smtp.gmail.com'; // GMAIL SMTP Server
            $mail->SMTPAuth   = true;
            $mail->Username   = 'ctis256projectsys@gmail.com';
            $mail->Password   = 'nodx jmbn dibb jbfz'; // Uygulama şifresi
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('ctis256projectsys@gmail.com', 'System Admin');
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
