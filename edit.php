<?php

require_once "auth/auth.php";
require_once "config/database.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if ($id === false || $id === null) {
    die("Invalid task ID.");
}

// Update task

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = $_POST["title"];
    $priority = $_POST["priority"];

 $sql = "
    UPDATE tasks
    SET title = :title,
        priority = :priority
    WHERE id = :id
    AND user_id = :user_id
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ":title" => trim($title),
    ":priority" => $priority,
    ":id" => $id,
    ":user_id" => $_SESSION["user_id"]
]);
    header("Location: index.php");
    exit;
}


// Get existing task

$sql = "
    SELECT *
    FROM tasks
    WHERE id = :id
    AND user_id = :user_id
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ":id" => $id,
    ":user_id" => $_SESSION["user_id"]
]);

$task = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$task) {
    http_response_code(404);
    die("Task not found.");
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

    <title>Edit Task</title>

</head>

<body>

    <h1>Edit Task</h1>

    <form method="POST">

        <div>

            <label for="title">
                Task
            </label>

            <input
                type="text"
                id="title"
                name="title"
                value="<?= htmlspecialchars($task["title"]) ?>"
                required
            >

        </div>

        <br>

        <div>

            <label for="priority">
                Priority
            </label>

            <select
                id="priority"
                name="priority"
            >

                <option
                    value="low"
                    <?= $task["priority"] === "low" ? "selected" : "" ?>
                >
                    Low
                </option>

                <option
                    value="medium"
                    <?= $task["priority"] === "medium" ? "selected" : "" ?>
                >
                    Medium
                </option>

                <option
                    value="high"
                    <?= $task["priority"] === "high" ? "selected" : "" ?>
                >
                    High
                </option>

            </select>

        </div>

        <br>

        <button type="submit">
            Update Task
        </button>

    </form>

    <br>

    <a href="index.php">
        Cancel
    </a>

</body>

</html>