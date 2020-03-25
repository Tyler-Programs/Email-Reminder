<?php 
declare(strict_types = 1);
session_start();
require '../php/consts.php';
require './php/reminder.php';
require '../php/db.php';

define("DB_USER", "reminder_user");

validateLogin(); // ensure that the user is logged in, else redirect them
?>
<!-- TODO: Add clock (in javascript) that displays the current UTC time -->
<html>
<script src="../index.js"></script>
<body>
<p style="text-decoration: underline; color: blue; cursor: pointer;" onclick="window.location.href = 'http:\/\/127.0.0.1/dashboard/dashboard.php'"><<< Return to dashboard</p>
<p> Enter your email and a starting time for an event to receive and email reminder!
* All times are UTC.
</p>

<form id="reminder_form" method="POST" onsubmit="" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<!-- E-mail: <input id="email" name="email" type="text" value="<?php fillAfterSubmit("email")?>"><?php isEmptyAndRequired("email") ?><br> -->
Event Title: <input id="title" name="title" type="text" value="<?php fillAfterSubmit("title")?>" autocomplete="off"><br>
Location: <input id="location" name="location" type="text" value="<?php fillAfterSubmit("location")?>" autocomplete="off"><br>
Starting Date: <input id="start_date" name="start_date" type="date" value="<?php fillAfterSubmit("start_date")?>"><br>
Starting Time: <input id="start_time" name="start_time" type="text" placeholder="1:35pm" value="<?php fillAfterSubmit("start_time")?>" autocomplete="off"><?php isEmptyAndRequired("start_time") ?><br>
Send reminder <input id="remind_before_mins" name="remind_before_mins" type="text" value="<?php fillAfterSubmit("remind_before_mins")?>" autocomplete="off"> minutes early<br>
Ending Date: <input id="end_date" name="end_date" type="date" value="<?php fillAfterSubmit("end_date")?>"><br>
Ending Time: <input id="end_time" name="end_time" type="text" placeholder="3:30pm" value="<?php fillAfterSubmit("end_time")?>" autocomplete="off"><br>
Notes: <input id="notes" name="notes" type="textarea" value="<?php fillAfterSubmit("notes")?>" autocomplete="off"><br>
<input type="submit" value="Submit">

</form>
</body>
</html>

<?php
date_default_timezone_set('UTC');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	validateData(); // ensure that the data is of proper types and values
	insert_reminder();
}



function fillAfterSubmit($fieldID) { // If the field was written into before submitting, add the value back in
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST[$fieldID]) && $_POST[$fieldID] !== "") {
            switch ($fieldID) { // special fields that need a default value
                default:
                    echo htmlspecialchars($_POST[$fieldID]);
            }
            return;
        }
    }
    switch ($fieldID) { // special fields that need a default value
        default:
            break;
    }
}

function validateData() {
    // TODO: validate time data do make sure it's in form: hh:mm[am/pm]
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        foreach ($_POST as $id => $field) {
            if ($field === "") {
                continue; // no data to validate, continue
            }
            $_POST[$id] = trim($_POST[$id]);
            $_POST[$id] = htmlspecialchars($_POST[$id]);
            switch ($id) { // the fields that are explicitly allowed to use slashes
                case "start_time":
                case "remind_before_mins":
                case "end_time":
                case "timezone":
                break;
                default:
                $_POST[$id] = stripslashes($_POST[$id]);
            }
        }
    }
}

function isEmptyAndRequired($fieldID) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!isset($_POST[$fieldID]) || $_POST[$fieldID] === "") {
            echo "<span style='color: red;'> * required</span>";
        }
    }
}

function insert_reminder() {
	$sql = "INSERT INTO `event` (`title`, `notes`, `start_date_time_utc`, `end_date_time_utc`, `send_date_time_utc`, `location`) VALUES (?, ?, ?, ?, ?, ?);";
    $db = new DB();
    $conn = $db->connect("reminder", DB_USER);
    $send_date_time = formatDate(calcSendTime());
	$start_date_time = formatDate(new DateTime($_POST['start_date'] . " " . $_POST['start_time']));
	$end_date_time = formatDate(new DateTime($_POST['end_date'] . " " . $_POST['end_time']));
    $stmt = $db->execute($conn, $sql, "ssssss", $_POST['title'], $_POST['notes'], $start_date_time, $end_date_time, $send_date_time, $_POST['location']);
    $sql = "INSERT INTO `event_list` (`event_UID`,`ID`) VALUES (?,?);";
    $stmt = $db->execute($conn, $sql, "ii", $stmt->insert_id, $_SESSION['event_list_uid']);
    $stmt->close();
}

function calcSendTime() : DateTime {
	// Subtract the minutes from the starting date+time
	$send_date_time = new DateTime($_POST['start_date'] . " " . $_POST['start_time']);
	$send_date_time->sub(new DateInterval('PT' . $_POST["remind_before_mins"] . 'M'));
	return $send_date_time;
}

// formateDate -- using the passed in DateTime object this function returns a string representation in the mysql compliant format
// YYYY-MM-DD hh:mm:ss
function formatDate(DateTime $date) : string {
	return $date->format('Y-m-d H:i:s');
}

function validateLogin() {
	if (!(isset($_SESSION["user_email"]) && $_SESSION["user_email"] !== "" && $_SESSION["valid_login"] === "true")) {
		header("Location: " . URL_PATH);
	}
}
?>
