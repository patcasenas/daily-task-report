<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

Header('Access-Control-Allow-Origin: *');
Header('Content-Type: application/json');
Header('Access-Control-Allow-Method: PUT, PATCH');

require_once($_SERVER['DOCUMENT_ROOT'] . '/daily-task-report/configuration/dbConfig.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/daily-task-report/model/Timesheet.php');

$pdo = new Database;
$db = $pdo->connect();

$timesheet = new Timesheet($db);
$data = json_decode(file_get_contents('php://input'));

if($_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'PATCH') {
    if(isset($data)) {
        $params = [
            'timesheet_id' => $_GET['id'],
        ];

        if($timesheet->updateTimeOut($params)) {
            http_response_code(200);
            echo json_encode(['message' => 'Time out recorded successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to time out']);
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