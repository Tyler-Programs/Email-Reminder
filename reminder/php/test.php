<?php
//require '../../php/mail.php';
//require 'php/user.php';
require 'php/db.php';

$user = new User();
/*
$user = new User();
$user->set_conn(DB::connect("reminder", "reminder_user", ""));
$user->register_user("tyler.r.lewis1@gmail.com", "testpass", "testpass");
*/

/*$mailer = new Mailer(true);
$code = generate_verification_code();
$user_email = "tyler.r.lewis1@gmail.com";
$subject = "Verification Email";
$msg = "<h2>Thank you for registering with Dooter Services.</h2><p>Please click the link below to 
verify your email:</p><br><a href='" . $_SERVER['SERVER_ADDR'] . "/verify.php'>" . $code . "</a><br>
<p>This link will expire in 15 minutes.</p>";
$mailer->add_email(new Email($user_email, $subject, $msg));
$mailer->send_all_emails();

function generate_verification_code() : string {
    $characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    $randString = "";

    for ($i = 0; $i < 20; $i++) {
        $idx = rand(0, strlen($characters) - 1);
        $randString .= $characters[$idx];
    }

    return $randString;
}*/
?>