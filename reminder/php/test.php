<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../php/external/PHPMailer-master/src/Exception.php';
require '../../php/external/PHPMailer-master/src/PHPMailer.php';
require '../../php/external/PHPMailer-master/src/SMTP.php';

/*$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= 'From: <Event_Reminder@example.com>' . "\r\n";
mail("tyler.r.lewis1@gmail.com", "Tset Subject", "test body", $headers);*/

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'localhost';
    $mail->Port = 25;
    
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->SMTPOptions = array(
        'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
        )
        );

    $mail->setFrom('dooterservice@gmail.com');
    $mail->addAddress('tyler.r.lewis1@gmail.com');
    $mail->Subject = 'Subject line stuff';
    $mail->Body = 'Message body here!';

    $mail->send();
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>