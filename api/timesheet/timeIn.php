<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

Header('Access-Control-Allow-Origin: *');
Header('Content-Type: application/json');
Header('Access-Control-Allow-Method: POST');

require_once($_SERVER['DOCUMENT_ROOT'] . '/daily-task-report/configuration/dbConfig.php');

$pdo = new Database;
$db = $pdo->connect();

$timesheet = new Timesheet($db);
$data = json_decode(file_get_contents('php://input'));

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($data)) {
        $params = [
            'employee_id' => $data->employee_id,
        ];

        if ($timesheet->createTimeIn($params)) {
            http_response_code(200);
            echo json_encode(['message' => 'Time in recorded successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to time in']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid input data']);
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
    exit;
}