<?php
    //include 'php/db.php';
    include 'php/mail.php';

    class User {
        private string $email;
        public int $event_list_uid;
        private mysqli $conn;

        // Attempts to login the user with the given password. Returns true on success, returns an error on failure.
        public function login_user(string $passwd) {
            $is_authed = false;
            $event_list_uid = -1;

            try {
                //$conn = connect();
                $sql = "SELECT * FROM `user` WHERE `user`.`email` = ?;";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param('s', $this->email);
                $stmt->execute();

                $res = $stmt->get_result();
                if ($res->num_rows == 1) {
                    // Check that the password match
                    $row = $res->fetch_array(MYSQLI_ASSOC);
                        
                    if ($row["password"] == $passwd) {
                        $is_authed = true;
                        $this->event_list_uid = $row['event_list_uid'];

                        $_SESSION['user_email'] = $this->email;
                        $_SESSION['valid_login'] = true;
                        $_SESSION['event_list_uid'] = $this->event_list_uid;
                    }
                }
            } catch (Exception $e) {
                echo 'Error: ' . $e.getMessage();
            } finally {
                $stmt->free_result();
                $stmt->close();
            }

            if ($is_authed == false) {
                // TODO: Display incorrect email/pass message, return error
            } else {
                return true;
            }
        }

        public function logout_user() {

        }

        // Attempts to register the user with the given email and password. Returns true on success, returns an error on failure.
        public function register_user(string $user_email, string $user_pass, string $confirm_user_pass) {
            $is_authed = false;

            if ($confirm_user_pass != $user_pass) {
                // TODO: Display password mismatch, return error
            }
            if ($user_email != "") {
                $this->set_email($user_email); // sanitize the email
            }
            $user_pass = $this->validate_pass($user_pass); // sanitize the password
                
            try {
            // Connect to the database to insert the new user
                //$conn = connect();
                $sql = "INSERT INTO `user` (`email`, `password`) VALUES (?, ?);";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param('ss', $user_email, $user_pass);
                $stmt->execute();

                if ($stmt->errno != null) {
                    throw new Exception('Error No: ' . $stmt->error_list[0]["errno"] . '<br />Message: ' . $stmt->error_list[0]["error"], $stmt->errno);
                }

                $is_authed = true;
                $this->event_list_uid = $stmt->insert_id;
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();

                if ($e->getCode() == 1062) { // the email is already used
                    // TODO: display email already in use error
                }
            } finally {
                // Free the stmt result and close the statement
                $stmt->free_result();
                $stmt->close();
            }

            if ($is_authed == true) {
                $this->send_verification_email();
                return true;
            }
            //return true;
        }

        public function send_verification_email() {
            $mailer = new Mailer(true);

            $sql = "UPDATE `user` SET `verifications_sent` = ?, `verification_code` = ?, `verification_sent_date_time_utc` = ?";
            $veri_sent = 1;
            $veri_sent_date_time = DB::formatDate(new DateTime('now'));

            $subject = "Verification Email";
            $code = generate_verification_code();
            $link = $_SERVER['SERVER_ADDR'] . "/verify.php?id=" . $code;
            $msg = "<h2>Thank you for registering with Dooter Services.</h2><p>Please click the link below to 
            verify your email:</p><br><a href='" . $link . "'>" . $link . "</a><br>
            <p>This link will expire in 15 minutes.</p>";

            $mailer->add_email(new Email($this->email, $subject, $msg));
            try {
                $mailer->send_all_emails();

                // Insert the verification information into the user's record in the database
                $stmt = $this->conn->prepare($sql);               
                $stmt->bind_param('iss', $veri_sent, $code, $veri_sent_date_time);
                $stmt->execute();
            } catch (Exception $e) {
                echo $e->getMessage();
                // TODO: Display error message (verification email failed to send)
            } finally {
                $stmt->free_result();
                $stmt->close();
            }
        }

        // ---------- Static Functions -----------
        public static function validate_email(string $email) : string {
            $email = trim($email);
            $email = htmlspecialchars($email);
            $email = stripslashes($email);
            filter_var($email, FILTER_VALIDATE_EMAIL);
            return $email;
        }

        public static function validate_pass(string $pass) : string {
            $pass = trim($pass);
            $pass = htmlspecialchars($pass);
            $pass = stripslashes($pass);
            return $pass;
        }

        // ---------- GETTERS and SETTERS --------
        // Sets the user's email after sanitizing it
        public function set_email($email) {
            $this->email = $this->validate_email($email);
        }

        public function get_email() {
            return $this->email;
        }

        public function set_conn(mysqli $conn) {
            $this->conn = $conn;
        }

        public function get_conn() : mysqli {
            return $this->conn;
        }
    }

    function generate_verification_code() : string {
        $characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        $randString = "";
    
        for ($i = 0; $i < 20; $i++) {
            $idx = rand(0, strlen($characters) - 1);
            $randString .= $characters[$idx];
        }
    
        return $randString;
    }
?>