<?php
/**
 * SkinCheck - PDO Database Class
 * Establishes a database connection, prepares statements,
 * binds values, and returns results.
 */

class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;

    private $dbh;
    private $stmt;
    private $error;
    private $connected = false;

    public function __construct() {
        // Set DSN (Database Source Name)
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ];

        // Attempt Connection
        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
            $this->connected = true;
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            // Gracefully catch database unavailability during project bootstrap.
            // We do not crash the app so that static placeholder screens load.
            error_log("Database Connection Error: " . $this->error);
        }
    }

    /**
     * Checks if database is successfully connected.
     * @return bool
     */
    public function isConnected() {
        return $this->connected;
    }

    /**
     * Gets the connection error message.
     * @return string|null
     */
    public function getError() {
        return $this->error;
    }

    /**
     * Prepares an SQL query.
     * @param string $sql
     */
    public function query($sql) {
        if (!$this->connected) return;
        $this->stmt = $this->dbh->prepare($sql);
    }

    /**
     * Binds query parameters.
     * @param string $param Parameter placeholder
     * @param mixed $value Bound parameter value
     * @param int|null $type PDO data type (integer, string, bool, null)
     */
    public function bind($param, $value, $type = null) {
        if (!$this->connected || !$this->stmt) return;
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    /**
     * Executes the prepared statement.
     * @return bool
     */
    public function execute() {
        if (!$this->connected || !$this->stmt) return false;
        try {
            return $this->stmt->execute();
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            error_log("Query Execution Error: " . $this->error);
            return false;
        }
    }

    /**
     * Gets result set as array of objects.
     * @return array
     */
    public function resultSet() {
        if (!$this->connected) return [];
        $this->execute();
        return $this->stmt->fetchAll();
    }

    /**
     * Gets single record as object.
     * @return object|bool
     */
    public function single() {
        if (!$this->connected) return false;
        $this->execute();
        return $this->stmt->fetch();
    }

    /**
     * Gets row count.
     * @return int
     */
    public function rowCount() {
        if (!$this->connected || !$this->stmt) return 0;
        return $this->stmt->rowCount();
    }

    /**
     * Gets the last inserted ID.
     * @return string
     */
    public function lastInsertId() {
        if (!$this->connected) return "0";
        return $this->dbh->lastInsertId();
    }
}
