<?php

require_once "../config/database.php";

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    // Validate name
    if ($name === "") {
        $errors[] = "Name is required.";
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email.";
    }

    // Validate password
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }

    // Check if email already exists
    if (empty($errors)) {

        $sql = "SELECT id FROM users WHERE email = :email";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":email" => $email
        ]);

        if ($stmt->fetch()) {
            $errors[] = "An account with this email already exists.";
        }
    }

    // Create account
    if (empty($errors)) {

        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $sql = "
            INSERT INTO users (name, email, password)
            VALUES (:name, :email, :password)
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":name" => $name,
            ":email" => $email,
            ":password" => $hashedPassword
        ]);

        $success = "Account created successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
</head>

<body>

    <h1>Create Account</h1>

    <?php if (!empty($errors)): ?>

        <ul>
            <?php foreach ($errors as $error): ?>

                <li>
                    <?= htmlspecialchars($error) ?>
                </li>

            <?php endforeach; ?>
        </ul>

    <?php endif; ?>

    <?php if ($success): ?>

        <p>
            <?= htmlspecialchars($success) ?>
        </p>

    <?php endif; ?>

    <form method="POST">

        <div>
            <label for="name">
                Name
            </label>

            <input
                type="text"
                id="name"
                name="name"
                required
            >
        </div>

        <br>

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
            Register
        </button>

    </form>

    <p>
        Already have an account?
        <a href="../login.php">Login</a>
    </p>

</body>

</html>