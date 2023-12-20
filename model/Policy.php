<?php
error_reporting(E_ALL);
ini_set("display_errors",1);

class Policy {
    public $policy_id, $policy;

    private $connection;
    private $table = "policy";

    public function __construct($pdo) {
        $this->connection = $pdo;
    }

    public function readPolicy(){
        $query = 'SELECT policy_id, policy FROM' . $this->table .'ORDER BY policy';
        $stmt = $this->connection->prepare($query);

        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error'=> $e->getMessage()]);
        }
    }

    public function createPolicy($params){
        try {
            $query = 'INSERT INTO ' . $this->table . '(policy_id, policy) ' .
                'VALUES (uuid_generate_v4(), :policy)';
            $stmt = $this->connection->prepare($query);

            $stmt->bindValue(':policy', $params['policy'], PDO::PARAM_STR);

            if($stmt->execute()){
                return true;
            }
            return false;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error'=> $e->getMessage()]);
        }
    }

    public function updatePolicy($params){
        try {
            $query = 'UPDATE '. $this->table . 'SET policy = :policy WHERE policy_id = :policy_id';
            $stmt = $this->connection->prepare($query);

            $stmt->bindValue(':policy_id', $_GET['id'], PDO::PARAM_STR);
            $stmt->bindValue(':policy', $params['policy'], PDO::PARAM_STR);

            if($stmt->execute()){
                return true;
            }
            return false;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error'=> $e->getMessage()]);
        }
    }

    public function deletePolicy($policy_id){
        try {
            $query = 'DELETE FROM'. $this->table . ' WHERE policy_id = :policy_id';
            $stmt = $this->connection->prepare($query);

            $stmt->bindValue(':policy_id', $_GET['policy_id'], PDO::PARAM_STR);

            if($stmt->execute()){
                return true;
            }
            return false;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error'=> $e->getMessage()]);
        }
    }
}