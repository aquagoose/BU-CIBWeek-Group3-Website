<?php
// Database include file - contains all DB functionality + default login info.

const DB_HOST = '';
const DB_NAME = '';
const DB_USER = '';
const DB_PASS = '';

// Make debugging easier
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Database class to aid DB usage.
class Database {
    // I cloned the database over to my MySQL as I know how to use it in PHP, sorry!!
    // Functionally, it's exactly the same as the Oracle database, and I have purposefully not used any MySQL specific
    // features.
    // If I used PDO, I would be able to connect to the Oracle database directly, but it's been a few years since I last
    // used PDO so I'm using mysqli instead.
    private mysqli $conn;

    public function __construct(string $host, string $name, string $user, string $pass) {
        $this->conn = new mysqli($host, $user, $pass, $name);

        if ($this->conn->connect_error) {
            throw new Exception("Failed to connect to database: {$this->conn->connect_error}");
        }
    }

    public function __destruct() {
        $this->conn->close();
    }

    public function executeStatement(string $query, ...$args): mysqli_stmt {
        $stmt = $this->conn->prepare($query);

        if (count($args) > 0) {
            $types = "";
            foreach ($args as $arg) {
                switch (gettype($arg)) {
                    case "string":
                        $types .= 's';
                        break;

                    case "integer":
                        $types .= 'i';
                        break;

                    case "double":
                        $types .= 'd';
                        break;
                }
            }

            $stmt->bind_param($types, ...$args);
        }

        if (!$stmt->execute())
            throw new Exception("Failed to execute statement: $stmt->error");

        return $stmt;
    }

    public function execute(string $query, ...$args): mysqli_result {
        $stmt = $this->executeStatement($query, ...$args);

        $result = $stmt->get_result();
        if (!$result)
            throw new Exception("Failed to get result: $stmt->error");

        return $result;
    }

    public function executeNoResult(string $query, ...$args): void {
        $this->executeStatement($query, ...$args);
    }

    public function beginTransaction(): void {
        $this->conn->begin_transaction();
    }

    public function commit(): void {
        try {
            $this->conn->commit();
        } catch (Exception $e) {
            die("Failed to commit transaction: $e");
        }
    }

    public static function default(): Database {
        return new Database(DB_HOST, DB_NAME, DB_USER, DB_PASS);
    }
}