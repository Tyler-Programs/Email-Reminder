<?php 
//require 'php/consts.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'php/external/PHPMailer-master/src/Exception.php';
require 'php/external/PHPMailer-master/src/PHPMailer.php';
require 'php/external/PHPMailer-master/src/SMTP.php';



class Email {
    private $event_uid;
    public $recipient;
    public $subject;
    public $msg;

    public function __construct(string $recipient, string $subject, string $msg) {
        $this->recipient = $recipient;
        $this->subject = $subject;
        $this->msg = $msg;
    }

    // Build the email to send.
    public function build_reminder_email($reminder) {
        $this->uid = $reminder->get_uid();
        $this->recipient = $reminder->get_email();
        
        if ($reminder->get_title() != "") {
            $this->subject = $reminder->get_title();
        } else {
            $this->subject = "Event Reminder";
        }
        
        $this->msg = "This is a reminder for an event you have scheduled ";
        if ($reminder->get_end_time() != "") {
            $this->msg = $this->msg . "from " . $reminder->get_start_time() . " until " . $reminder->get_end_time();
        } else {
            $this->msg = $this->msg . "for " . $reminder->get_start_time();
        }
        
        if ($reminder->get_location() != "") {
            $this->msg = $this->msg . " at " . $reminder->get_location();
        }

        if ($reminder->get_notes() != "") {
            $this->msg = $this->msg . "\n\n" . $reminder->get_notes();
        }
    }
}

class Mailer {
    private const SERVER_URL = "192.168.0.35";
    private $mail;
    private $headers;
    private $emails = [];

    public function __construct($isSMTP) {
        $this->mail = new PHPMailer(true);
        $this->setHeaders();
        $this->setOptions($isSMTP);
        $this->setCredentials();
    }

    public function add_email($email) {
        array_push($this->emails, $email);
    }

    public function send_all_emails() {
        foreach ($this->emails as $email) {
            try {
                $this->mail->addAddress($email->recipient);
                $this->mail->setFrom('dooterservice@gmail.com', 'Dooter Services');
                $this->mail->Subject = $email->subject;
                $this->mail->Body = $email->msg;
                //$mail->setFrom('dooterservice@gmail.com');
                //$mail->addAddress('tyler.r.lewis1@gmail.com');
                //$mail->Subject = 'Subject line stuff';
                //$mail->Body = 'Message body here!';
                $this->mail->send();
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}";
            }
        }
    }

    private function setHeaders() {
        $this->headers = "MIME-Version: 1.0" . "\r\n";
        $this->headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $this->headers .= 'From: DooterService@gmail.com' . "\r\n";
    }

    private function setOptions($isHTML) {
        if ($isHTML) {
            $this->mail->isHTML(true);
        }
        $this->mail->isSMTP();
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->SMTPOptions = array(
            'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
            )
        );
    }

    private function setCredentials() {
        $this->mail->Host = SERVER_URL; // localhost (on server)
        $this->mail->Port = 587;
        $this->mail->SMTPAuth = true; // false (on server)
        $this->mail->Username = 'dooter'; // remove (on server)
        $this->mail->Password = 'TouchMe1998'; // remove (on server)
    }
}
?>