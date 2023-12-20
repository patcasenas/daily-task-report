<?php

class Database {
    private $config, $host, $dbname, $username, $password;
    private $pdo = null;

    public function __construct() {
        $this->config = require($_SERVER["DOCUMENT_ROOT"] ."/daily-task-report/configuration/connection.php");
        $this->host = $this->config["host"];
        $this->dbname = $this->config["dbname"];
        $this->username = $this->config["username"];
        $this->password = $this->config["password"];
    }

    public function connect() {
        try {
            $this->pdo = new PDO('pgsql: host='.$this->host . ';dbname=' . $this->dbname, $this->username, $this->password);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return $this->pdo;
    }
}