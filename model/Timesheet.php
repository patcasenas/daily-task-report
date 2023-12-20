<?php
error_reporting(E_ALL);
ini_set("display_errors",1);

class Timesheet {
    public $timesheet_id, $employee_id;
    private $connection;

    private $table = "timesheet";

    public function __construct($pdo) {
        $this->connection = $pdo;
    }

    public function createTimeIn($params) {
        try {
            $this->employee_id = $params["employee_id"];

            $query = 'INSERT INTO ' . $this->table . '(timesheet_id, employee_id) 
                    VALUES (uuid_generate_v4(), :employee_id)';
            $stmt = $this->connection->prepare($query);

            $stmt->bindValue(':employee_id', $this->employee_id, PDO::PARAM_STR);

            if($stmt->execute()) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function updateTimeOut($params) {
        try {
            $this->timesheet_id = $_GET['id'];

            $query = 'UPDATE ' . $this->table . ' SET time_out = CURRENT_TIME(0) WHERE timesheet_id = :timesheet_id';
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(':timesheet_id', $this->timesheet_id, PDO::PARAM_STR);

            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error'=> $e->getMessage()]);
        }
    }
}