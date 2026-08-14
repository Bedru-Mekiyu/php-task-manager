<?php

require_once "auth/auth.php";
require_once "auth/csrf.php";
require_once "config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    die("Method not allowed.");
}

$token = $_POST["csrf_token"] ?? "";

if (!verify_csrf_token($token)) {
    http_response_code(403);
    die("Invalid CSRF token.");
}

$id = filter_input(
    INPUT_POST,
    "id",
    FILTER_VALIDATE_INT
);

if ($id === false || $id === null) {
    http_response_code(400);
    die("Invalid task ID.");
}

$sql = "
    DELETE FROM tasks
    WHERE id = :id
    AND user_id = :user_id
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ":id" => $id,
    ":user_id" => $_SESSION["user_id"]
]);

header("Location: index.php");
exit;