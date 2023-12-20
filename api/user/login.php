<?php
error_reporting(E_ALL);
ini_set("display_errors",1);

Header('Access-Control-Allow-Origin: *');
Header('Content-Type: application/json');
Header('Access-Control-Allow-Method: POST');

require_once($_SERVER['DOCUMENT_ROOT'] . '/daily-task-report/configuration/dbConfig.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/daily-task-report/model/User.php');

$pdo = new Database;
$db = $pdo->connect();

$authentication = new User($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'));

    $username = $data->username;
    $password = $data->password;
    
    if($authentication->login($username, $password)) {
        http_response_code(200);
        echo "Welcome!";
    } else {
        http_response_code(401);
        echo "Unauthorized access";
    }
} else {
    http_response_code(405);
    echo "Invalid request";
}