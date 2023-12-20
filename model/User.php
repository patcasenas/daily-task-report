<?php
error_reporting(E_ALL);
ini_set("display_errors",1);

class User {
    public $user_id, $username, $password;

    private $connection;
    private $table = "users";

    public function __construct($pdo) {
        $this->connection = $pdo;
    }

    public function login($username, $password) {
        $query = 'SELECT username, password, salt, policy FROM ' . $this->table . ' WHERE username = :username';
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return false;
        }

        $storedPasswordHash = $row['password'];
        $salt = $row['salt'];
        $saltString = unpack('H*', stream_get_contents($salt))[1];

        if (password_verify($password . hex2bin($saltString), $storedPasswordHash)) {
            return [
                'username' => $row['username'],
                'policy' => $row['policy'],
            ];
        } else {
            http_response_code(401);
            return false;
        }
    }

    public function createUser($params) {
        try {
            $paramNames = [
                'username', 'policy', 'last_name', 'first_name', 'employee_id'
            ];
            $bindings = [];

            $this->password = $params['password'];
            $salt = random_bytes(16);
            $hashedPassword = password_hash($this->password . $salt, PASSWORD_BCRYPT);


            $query = 'INSERT INTO ' . $this->table . '(user_id, password, salt, ' . implode(',', $paramNames) .
                    ') VALUES (uuid_generate_v4(), :password, :salt, :' . implode(', :', $paramNames) . ')';
            $stmt = $this->connection->prepare($query);

            foreach($paramNames as $paramName) {
                $bindings[$paramName] = $params[$paramName];
                $stmt->bindValue(':' . $paramName, $bindings[$paramName], PDO::PARAM_STR);
            }

            $stmt->bindValue(':salt', $salt, PDO::PARAM_LOB);
            $stmt->bindValue(':password', $hashedPassword, PDO::PARAM_STR);

            if($stmt->execute()) {
                return true;
            } 
            return false;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function readUsers() {
        $paramNames = [
            'username', 'policy', 'last_name', 'first_name', 'employee_id'
        ];

        $query = 'SELECT ' .  implode(', ', $paramNames) . ' FROM ' . $this->table . ' ORDER BY last_name';
        $stmt = $this->connection->prepare($query);

        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error'=> $e->getMessage()]);
        }
    }
}