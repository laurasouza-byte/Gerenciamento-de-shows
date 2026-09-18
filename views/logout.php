<?php
session_start();
require_once __DIR__ . '/../Controller/UserController.php';

$userController = new UserController();
$userController->logout();

header('Location: ../index.php');
exit();
