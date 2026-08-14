<?php

require_once "autoload.php";
require_once "config/database.php";
require_once "app/Helpers/auth.php";
require_once "app/Helpers/csrf.php";
require_once "config/validation.php";

use App\Models\Task;
use App\Models\User;
use App\Controllers\TaskController;
use App\Controllers\AuthController;

$taskModel = new Task($pdo);
$userModel = new User($pdo);
$taskController = new TaskController($taskModel);
$authController = new AuthController($userModel);

$action = $_GET["action"] ?? "index";

switch ($action) {
    case "login":
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $authController->login();
        } else {
            $authController->showLogin();
        }
        break;

    case "logout":
        $authController->logout();
        break;

    case "register":
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $authController->register();
        } else {
            $authController->showRegister();
        }
        break;

    case "edit":
        check_auth();
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $taskController->update();
        } else {
            $taskController->edit();
        }
        break;

    case "delete":
        check_auth();
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $token = $_POST["csrf_token"] ?? "";
            if (!verify_csrf_token($token)) {
                http_response_code(403);
                die("Invalid CSRF token.");
            }
            $taskController->delete();
        }
        break;

    case "index":
    default:
        check_auth();
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $token = $_POST["csrf_token"] ?? "";
            if (!verify_csrf_token($token)) {
                http_response_code(403);
                die("Invalid CSRF token.");
            }
            $taskController->store();
        } else {
            $taskController->index();
        }
        break;
}