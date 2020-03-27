<?php declare(strict_types=1);
require './reminder.php';
require '../../php/db.php';
// This file is to be called from the terminal at one minute intervals.

date_default_timezone_set("UTC");
echo nl2br(date("Y-M-D H:i:s", time()) . "\n");

// Get the connection to the reminder database
$db = new DB();
$conn = $db->connect("reminder", "reminder_user");


$cur_minute = date("m", time());

if ($cur_minute == "59") { // only run once every hour
    find_next_hour_events();
}
find_cur_minute_events();

$conn->close();


class Email {
    public $recipient;
    public $subject;
    public $msg;
}

// Every hour, check the database for events that are to be sent out
// within the next hour (e.g: at 12:59 all events scheduled for 13:00 - 13:59)
// should be targeted. Insert the found records into send_ready.
function find_next_hour_events() {
    global $db;
    global $conn;
    $sql = "SELECT `UID` FROM `event` WHERE `send_date_time_utc` BETWEEN UTC_TIMESTAMP() AND ADDTIME(UTC_TIMESTAMP(), '0:59:0') AND `UID` NOT IN (SELECT `event_UID` FROM `send_ready`)";
    $res = $conn->query($sql);


    $uids = [];
    $bind_types = "";
    $sql = "INSERT INTO `send_ready` VALUES ";
    while($row = $res->fetch_row()) {
        array_push($uids, $row[0]);
        $bind_types = $bind_types . "s";
        $sql = $sql . "(?),";
    }
    $sql = rtrim($sql, ",");
    $res->free();

    if ($stmt = $conn->prepare($sql)) {
        $stmt = $db->execute($conn, $sql, $bind_types, ...$uids);
        $stmt->close();
    }
}


// Every minute, check the database for events that are to be sent out
// within the minute (send_ready table to narrow events).
function find_cur_minute_events() {
    global $conn;

    $emails = [];

    $sql = "SELECT `event`.*, `user`.`email` FROM `event` 
    INNER JOIN `send_ready` ON `event`.`UID` = `send_ready`.`event_UID` 
    INNER JOIN `event_list` ON `event`.`UID` = `event_list`.`event_UID`
    INNER JOIN `user` ON `event_list`.`ID` = `user`.`event_list_uid`
    WHERE `event`.`send_date_time_utc` BETWEEN UTC_TIMESTAMP() AND ADDTIME(UTC_TIMESTAMP(), '0:01:0')";
    
    $res = $conn->query($sql);

    while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
        var_dump($row);
        array_push($emails, build_email(new Reminder($row)));
    }

    // set headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: <Event_Reminder@example.com>' . "\r\n";
    // send the emails out
    foreach ($emails as $email) {
        mail($email->recipient, $email->subject, $email->msg, $headers);
    }
}


// Build the email to send.
function build_email($reminder) {
    $email = new Email();

    $email->recipient = $reminder->get_email();
    
    if ($reminder->get_title() != "") {
        $email->subject = $reminder->get_title();
    } else {
        $email->subject = "Event Reminder";
    }
    

    $email->msg = "This is a reminder for an event you have scheduled ";
    if ($reminder->get_end_time() != "") {
        $email->msg = $email->msg . "from " . $reminder->get_start_time() . " until " . $reminder->get_end_time();
    } else {
        $email->msg = $email->msg . "for " . $reminder->get_start_time();
    }
    
    if ($reminder->get_location() != "") {
        $email->msg = $email->msg . " at " . $reminder->get_location();
    }

    if ($reminder->get_notes() != "") {
        $email->msg = $email->msg . "\n\n" . $reminder->get_notes();
    }

    return $email;
}
?>