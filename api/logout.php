<?php

session_start();

require('../vendor/autoload.php');

$userController = new App\Controllers\UserController();

$userController->logout();

header('Location: ../login.php');