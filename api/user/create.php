<?php
error_reporting(E_ALL);
ini_set("display_errors",1);

Header('Access-Control-Allow-Origin: *');
Header('Content-Type: application/json');
Header('Access-Control-Allow-Method: POST');

require_once($_SERVER['DOCUMENT_ROOT'] . '/daily-task-report/configuration/dbConfig.php');

$pdo = new Database;
$db = $pdo->connect();

$user = new User($db);
$data = json_decode(file_get_contents('php://input'));

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($data)) {
        $params = [
            'username' => $data->username,
            'last_name' => $data->last_name,
            'first_name' => $data->first_name,
            'policy' => $data->policy,
            'employee_id' => $data->employee_id,
            'password' => $data->password,
        ];

        if($user->createUser($params)) {
            http_response_code(200);
            echo json_encode(['message' => 'Success']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create new user']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error'=> 'Invalid input data']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error'=> 'Method not allowed']);
    exit;
}