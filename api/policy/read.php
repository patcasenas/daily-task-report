<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

Header('Access-Control-Allow-Origin: *');
Header('Content-Type: application/json');
Header('Access-Control-Allow-Method: GET');

require_once($_SERVER['DOCUMENT_ROOT'] . '/daily-task-report/configuration/dbConfig.php');

$pdo = new Database;
$db = $pdo->connect();

$policy = new Policy($db);
$data = $policy->readPolicy();

if($_SERVER['REQUEST_METHOD'] === 'GET') {
    $policy = [];

    if($data->rowCount()) {
        while ($row = $data->fetch(PDO::FETCH_OBJ)) {
            $policy[] = [
                'policy_id' => $row->policy_id,
                'policy' => $row->policy,
            ];
        }
    } else {
        http_response_code(500);
        echo json_encode(['error'=> 'Unable to retrieve data']);
    }
    echo json_encode($policy);
} else {
    http_response_code(405);
    echo json_encode(['error'=> 'Method not allowed']);
    exit;
}
$db = null;