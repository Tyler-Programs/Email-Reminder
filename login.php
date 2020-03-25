<?php 
    session_start(); // Begin the user's session
    require './php/db.php';
    require './php/consts.php';

    /*
        TMP -- DEV, DISABLE LOGIN.. SET USER AS test@test.net, event_list_uid AS 1
    */
    $_SESSION['user_email'] = 'test@test.net';
    $_SESSION['event_list_uid'] = 1;
    $_SESSION['valid_login'] = 'true';
    header("LOCATION:" . URL_PATH . "/dashboard/dashboard.php");

    auth_user();

    if (isset($_SESSION['valid_login']) && $_SESSION['valid_login'] === true) {
        // Assume that the user is logged in properly, redirect to dashboard
        // TODO: redirect the user to the dashboard. Remove current redirect.
        if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_REQUEST['logout']) && $_REQUEST['logout'] === "true") {
            session_unset();
            session_destroy();
            // let javascript do the redirecting
        } else {
            header("Location:" . URL_PATH . "/reminder");
        }
    }

    function auth_user() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_email = $_POST['email']; // TODO: Check if set
            $user_pass = $_POST['password']; // TODO: Check if set, encrypt
            $confirm_user_pass = $_POST['confirm_password']; // TODO: encrypt
            $is_authed = false;
            $event_list_uid = -1;

            if ($confirm_user_pass !== "") {
                // The user is trying to register
                if ($confirm_user_pass === $user_pass) {
                    $user_email = validate_email($user_email);
                    $user_pass = validate_pass($user_pass);
                    if ($user_email !== "") {
                        // TODO: insert into DB.
                        $conn = connect();
                        $sql = "INSERT INTO `user` (`email`, `password`) VALUES (?, ?);";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param('ss', $user_email, $user_pass);
                        $stmt->execute();

                        if ($stmt->errno) {
                            echo "Errno: " . $stmt->errno . "\n";
                            echo "Error: " . $stmt->error . "\n";
                        } else {
                            echo "success";
                            $is_authed = true;
                            $event_list_uid = $stmt->insert_id;
                        }
                        // Free the stmt result and close the statement
                        $stmt->free_result();
                        $stmt->close();
                    } else {
                        // TODO: display error (invalid email address)
                    }
                } else {
                    // TODO: display error (passwords do not match)
                }
            } else {
                // The user is trying to log in
                $conn = connect();
                $sql = "SELECT * FROM `user` WHERE `user`.`email` = ?;";
	            $stmt = $conn->prepare($sql);
	            $stmt->bind_param('s', $user_email);
	            $stmt->execute();
	
                if ($stmt->errno) {
                    echo "Errno: " . $stmt->errno . "\n";
                    echo "Error: " . $stmt->error . "\n";
                } else {
                    $res = $stmt->get_result();
                    if ($res->num_rows === 0) {
                        // No matching email
                        // TODO: Display error (wrong email or password)
                    } else {
                        // Check that the password match
                        $row = $res->fetch_row();
                        //$is_authed = false;

                        foreach ($res->fetch_fields() as $key => $value) {
                            switch ($value->name) {
                                case "email":
                                    $email_authed = false;
                                    if (strtolower($row[$key]) === strtolower($user_email)) {
                                        $email_authed = true;
                                    }
                                break;
                                case "password":
                                    $pass_authed = false;
                                    if ($row[$key] === $user_pass) {
                                        $pass_authed = true;
                                    }
                                break;
                                case "event_list_uid":
                                    $event_list_uid = $row[$key];
                                break;
                            }
                        }
                        if ($email_authed === true && $pass_authed == true) {
                            $is_authed = true;
                        }

                        if ($is_authed === true) {
                            echo "logged in";
                            // TODO: Redirect user
                        } else {
                            // TODO: Display error (wrong email or password)
                            echo "incorrect log in information.";
                        }
                    }
                }
                // Free the stmt result and close the statement
                $stmt->free_result();
                $stmt->close();
            }

            if ($is_authed === true) {
                // TODO: Redirect to the dashboard (holds links to all modules). Remove current redirect.
                // TODO: set user information in the session variable to keep them logged in
                $_SESSION["user_email"] = $user_email;
                $_SESSION["event_list_id"] = $event_list_uid;
                $_SESSION["valid_login"] = true;
                header("Location: " . URL_PATH . "/reminder");
            }
        }
    }

    function validate_email(string $email) : string {
        $email = trim($email);
        $email = htmlspecialchars($email);
        $email = stripslashes($email);
        filter_var($email, FILTER_VALIDATE_EMAIL);
        return $email;
    }

    function validate_pass(string $pass) : string {
        $pass = trim($pass);
        $pass = htmlspecialchars($pass);
        $pass = stripslashes($pass);
        return $pass;
    }
?>


<html>
<body>
<script src="./index.js"></script>
<h1>Login</h1><br>
<div id="login_container">
    <form id="login_form" name="login_form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <!-- Begin - Login panel -->
        Email
        <br>
        <input id="email" name="email" type="text" autocomplete="off">
        <br>
        <br>
        Password
        <br>
        <input id="password" name="password" type="password">
        <span id="confirm_password_label" hidden=true><br><br>Retype Password<br></span>
        <input id="confirm_password_field" name="confirm_password" type="password" hidden=true>
        <!-- End - Login panel -->
        <!-- Begin - Links to create an account / recover an account -->
        <div>
            <span id="register" class="link" onclick="toggle_register();">Register</span><span style="padding-left: 45px;" id="forgot_pass" class="link">Forgot Password?</span>
        </div>
        <!-- End - Begin - Links to create an account / recover an account -->
        <br>
        <input id="login_button" type="submit" value="Log in">
    </form>
</div>
</body>
</html>

<style>
.link
{
    text-decoration: underline;
    color: blue;
    cursor: pointer;
}
#register, #forgot_pass
{
    font-size: 10px;
}
</style>