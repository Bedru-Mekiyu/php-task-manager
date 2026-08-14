<?php

session_start();

require_once "config/database.php";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email.";
    }

    // Validate password
    if ($password === "") {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {

        $sql = "
            SELECT id, name, email, password
            FROM users
            WHERE email = :email
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":email" => $email
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (
            $user &&
            password_verify($password, $user["password"])
        ) {

            // Create a new session ID after login
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
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login</title>

</head>

<body>

    <h1>Login</h1>

    <?php if (!empty($errors)): ?>

        <ul>

            <?php foreach ($errors as $error): ?>

                <li>
                    <?= htmlspecialchars($error) ?>
                </li>

            <?php endforeach; ?>

        </ul>

    <?php endif; ?>

    <form method="POST">

        <div>

            <label for="email">
                Email
            </label>

            <input
                type="email"
                id="email"
                name="email"
                required
            >

        </div>

        <br>

        <div>

            <label for="password">
                Password
            </label>

            <input
                type="password"
                id="password"
                name="password"
                required
            >

        </div>

        <br>

        <button type="submit">
            Login
        </button>

    </form>

    <p>
        Don't have an account?

        <a href="auth/register.php">
            Register
        </a>
    </p>

</body>

</html>