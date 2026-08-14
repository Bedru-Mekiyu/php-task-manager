<?php

namespace App\Controllers;

use App\Models\User;

class AuthController
{
    private $userModel;

    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }

    public function showLogin()
    {
        require_once __DIR__ . '/../Views/auth/login.php';
    }

    public function login()
    {
        $email = trim($_POST["email"] ?? "");
        $password = $_POST["password"] ?? "";

        $errors = [];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email.";
        }
        if ($password === "") {
            $errors[] = "Password is required.";
        }

        if (empty($errors)) {
            $user = $this->userModel->findByEmail($email);

            if ($user && password_verify($password, $user["password"])) {
                session_regenerate_id(true);
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["user_email"] = $user["email"];

                header("Location: index.php");
                exit;
            } else {
                $errors[] = "Invalid email or password.";
            }
        }

        require_once __DIR__ . '/../Views/auth/login.php';
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        session_destroy();
        header("Location: index.php?action=login");
        exit;
    }

    public function showRegister()
    {
        require_once __DIR__ . '/../Views/auth/register.php';
    }

    public function register()
    {
        $name = trim($_POST["name"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $password = $_POST["password"] ?? "";

        $errors = [];
        if ($name === "") $errors[] = "Name is required.";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Please enter a valid email.";
        if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters.";

        if (empty($errors)) {
            if ($this->userModel->findByEmail($email)) {
                $errors[] = "An account with this email already exists.";
            } else {
                $this->userModel->create($name, $email, $password);
                $success = "Account created successfully!";
                require_once __DIR__ . '/../Views/auth/register.php';
                return;
            }
        }

        require_once __DIR__ . '/../Views/auth/register.php';
    }
}
