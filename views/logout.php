<?php

session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Model/Connection.php';
require_once __DIR__ . '/../Model/User.php';
require_once __DIR__ . '/../Controller/UserController.php';

use Controller\UserController;

$userController = new UserController();
$userController->logout();

header('Location: ../index.php');
exit();
