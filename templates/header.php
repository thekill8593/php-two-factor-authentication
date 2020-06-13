<?php

if (!isset($_SESSION)) {
    session_start();
}

require './vendor/autoload.php';
  
$userController = new App\Controllers\UserController();