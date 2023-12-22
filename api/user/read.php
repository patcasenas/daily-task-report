<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

Header('Access-Control-Allow-Origin: *');
Header('Content-Type: application/json');
Header('Access-Control-Allow-Method: GET');

require_once($_SERVER['DOCUMENT_ROOT'] . '/daily-task-report/configuration/dbConfig.php');

$pdo = new Database;
$db = $pdo->connect();

$users = new User($db);
$data = $users->readUsers();

if($_SERVER['REQUEST_METHOD'] === 'GET') {
    $users = [];

    if($data->rowCount()) {
        while ($row = $data->fetch(PDO::FETCH_OBJ)) {
            $users[] = [
                'username' => $row->username,
                'policy' => $row->policy,
                'last_name' => $row->last_name,
                'first_name' => $row->first_name,
                'employee_id' => $row->employee_id,
            ];
        }
    } else {
        http_response_code(500);
        echo json_encode(['error'=> 'Unable to retrieve users']);
    }
    echo json_encode($users);
} else {
    http_response_code(405);
    echo json_encode(['error'=> 'Method not allowed']);
    exit;
}
$db = null;