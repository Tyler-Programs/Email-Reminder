<?php 
    session_start(); // Begin the user's session
    //require 'php/consts.php';
    require 'php/db.php';
    include 'php/user.php';
    
    /*
        TMP -- DEV, DISABLE LOGIN.. SET USER AS test@test.net, event_list_uid AS 1
    */
    //$_SESSION['user_email'] = 'tyler.r.lewis1@gmail.com';
    //$_SESSION['event_list_uid'] = 1;
    //$_SESSION['valid_login'] = 'true';
    //header("Location: /my_dashboard/dashboard.php");

    auth_user();

    if (isset($_SESSION['valid_login']) && $_SESSION['valid_login'] === true) {
        // Assume that the user is logged in properly, redirect to dashboard
        // TODO: redirect the user to the dashboard. Remove current redirect.
        if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_REQUEST['logout']) && $_REQUEST['logout'] === "true") {
            session_unset();
            session_destroy();
            // let javascript do the redirecting
        } else {
            header("Location: /my_dashboard/dashboard.php");
            exit();
        }
    }

    function auth_user() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //$user_email = $_POST['email']; // TODO: Check if set
            //$user_pass = $_POST['password']; // TODO: Check if set, encrypt
            //$confirm_user_pass = $_POST['confirm_password']; // TODO: encrypt
            $is_authed = false;
            $event_list_uid = -1;

            if ($_POST['confirm_password'] !== "") {
                // The user is trying to register
                $user = new User();
                $user->set_conn(DB::connect("reminder", "reminder_user", ""));
                $is_authed = $user->register_user($_POST['email'], $_POST['password'], $_POST['confirm_password']);
            } else {
                // The user is trying to log in
                $user = new User();
                $user->set_email($_POST['email']);
                $user->set_conn(DB::connect("reminder", "reminder_user", ""));
                $is_authed = $user->login_user($_POST['password']);
            
            }

            if ($is_authed === true) {
                // TODO: Redirect to the dashboard (holds links to all modules). Remove current redirect.
                // TODO: set user information in the session variable to keep them logged in
                $_SESSION["user_email"] = $user->get_email();
                $_SESSION["event_list_id"] = $user->event_list_uid;
                $_SESSION["valid_login"] = true;
                //header("Location: " . SERVER_URL_DEV . "/reminder");
                header("Location: /my_dashboard/dashboard.php");
            }
        }
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