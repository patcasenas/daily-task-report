<?php
error_reporting(E_ALL);
ini_set("display_errors",1);

Header('Access-Control-Allow-Origin: *');
Header('Content-Type: application/json');
Header('Access-Control-Allow-Method: POST');

require_once($_SERVER['DOCUMENT_ROOT'] . '/daily-task-report/configuration/dbConfig.php');
include($_SERVER['DOCUMENT_ROOT'] . '/daily-task-report/model/User.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/daily-task-report/vendor/autoload.php');

$config = include($_SERVER['DOCUMENT_ROOT'] . '/daily-task-report/auth/config.php');
$key = $config['jwt_secret_key'];

use Firebase\JWT\JWT;

$pdo = new Database;
$db = $pdo->connect();

$authentication = new User($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'));

    $username = $data->username;
    $password = $data->password;
    $date = new DateTimeImmutable();
    
    if($authentication->login($username, $password)) {
        http_response_code(200);
        
        $payload = [
            "iss" => "dailytaskreport",
            "iat" => $date->getTimestamp(),
            "username" => $username,
            "nbf" => $date->getTimestamp(),
            "exp" => $date->modify("+60 minutes")->getTimestamp(),
        ];
        $token = JWT::encode($payload, $config["jwt_secret_key"], 'HS256');

        $response = [
            "message" => "Welcome",
            "token" => $token,
            "username" => $data->username,
        ];
        echo json_encode($response);
    } else {
        http_response_code(401);
        echo "Unauthorized access";
    }
} else {
    http_response_code(405);
    echo "Invalid request";
}