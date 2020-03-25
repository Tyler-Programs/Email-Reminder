<?php declare(strict_types = 1);
define("DB_CONN_STRING", "192.168.0.35");

class DB {
    // Connects to a mysql database. Must specify the database name.
    public function connect(string $db_name, ...$login) : mysqli {
        // Returns a mysqli connection
        if (count($login) === 1) {
            $db_user = $login[0];
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
    // $bind_types: The string of types for mysqli to bind (e.g: 'ssi' = string, string, int).
    // ...$args: The arguments for mysqli to bind.
    public function execute(mysqli $conn, string $sql, string $bind_types, ...$args) : mysqli_stmt {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($bind_types, ...$args);
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