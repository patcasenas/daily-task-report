<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Method: DELETE');

require_once($_SERVER['DOCUMENT_ROOT'] . '/daily-task-report/configuration/dbConfig.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/daily-task-report/model/Policy.php');

$pdo = new Database;
$db = $pdo->connect();

$policy = new Policy($db);

if($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (isset($_GET['id'])) {
        $policy_id = $_GET['id'];

        if($policy->deletePolicy($policy_id)) {
            http_response_code(200);
            echo json_encode(['message' => 'Record deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message'=> 'Failed to delete record']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message'=> 'Invalid input data']);
    }
} else {
    http_response_code(405);
    echo json_encode(['message'=> 'Method not allowed']);
}