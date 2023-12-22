<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/daily-task-report/configuration/dbConfig.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/daily-task-report/vendor/autoload.php');

require_once($_SERVER['DOCUMENT_ROOT'] . '/daily-task-report/model/Policy.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/daily-task-report/model/Timesheet.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/daily-task-report/model/User.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function authenticate() {
    $config = include($_SERVER['DOCUMENT_ROOT'] . '/daily-task-report/auth/config.php');
    $key = $config['jwt_secret_key'];
    $table = 'users';

    function normalizeHeaders($headers) {
        $normalizeHeader = [];

        foreach($headers as $name => $value) {
            $normalizedName = strtoupper($name); // Converts authorization to uppercase
            $normalizeHeader[$normalizedName] = $value;
        }
        return $normalizeHeader; // Returns uppercase authorization
    }

    $allHeaders = getallheaders();
    $normalizeHeaders = normalizeHeaders($allHeaders); // Uses normalizeHeaders function

    $authorization = $normalizeHeaders['AUTHORIZATION']; // Gets capitalized authorization header

    if(isset($authorization)) {
        if(strpos($authorization,'Bearer') !== false) {
            $token = substr($authorization, 7);

            try {
                $decoded = JWT::decode($token, new Key($key, 'HS256'));
                return $decoded;
            } catch (Exception $e) {
                return false;
            }
        }
    }
    return false;
}