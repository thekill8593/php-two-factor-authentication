<?php

session_start();

require('../vendor/autoload.php');

use App\Controllers\UserController;

$userController = new UserController();

if (!$userController->isUserLoggedIn()) {
    http_response_code(401);
    echo "No existe autenticacion";
    exit;
}

$rawData = file_get_contents("php://input");

$data = json_decode($rawData, true);

$res = $userController->activateSecondFactor($data['secret'], $data['code']);

if (!$res) {
    http_response_code(400);
    echo "Codigo Incorrecto";
} else {
    http_response_code(200);
}