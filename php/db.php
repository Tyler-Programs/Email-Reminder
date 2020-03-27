<?php declare(strict_types = 1);
define("DB_CONN_STRING", "192.168.0.35");

class DB {
    // Connects to a mysql database. Must specify the database name.
    public function connect(string $db_name, ...$login) : mysqli {
        // Returns a mysqli connection
        if (count($login) === 1) {
            $db_user = $login[0];
            $db_pass = "";
        } else if (count($login) === 2) {
            $db_user = $login[0];
            $db_pass = $login[1];
        } else {
            $db_user = "";
            $db_pass = "";
        }
        $mysqli = new mysqli(DB_CONN_STRING, $db_user, $db_pass, $db_name);

        if ($mysqli->connect_errno) {
            echo "ERROR CONNECTING TO DB\n";
            echo "Errno: " . $mysqli->connect_errno . "\n";
            echo "Error: " . $mysqli->connect_error . "\n";
        } else {
            return $mysqli;
        }
    }

    // Runs the given query as a prepared statement.
    // $conn: The mysqli connection to execute against.
    // $sql: The query with placeholders to run.
    // ...$args: The bind types and the arguments for mysqli to bind. Bind types must come first (e.g: 'ssi' = string, string, int).
    public function execute(mysqli $conn, string $sql, ...$args) : mysqli_stmt {
        $stmt = $conn->prepare($sql);

        if (count($args) > 1) { // if an argument is given, there must be a bind_types
            $stmt->bind_param(...$args);
        }

        $stmt->execute();

        if ($stmt->errno) {
            echo "Errno: " . $stmt->errno . "\n";
            echo "Error: " . $stmt->error . "\n";
            return null;
        } else {
            return $stmt;
        }
    }
}

?>