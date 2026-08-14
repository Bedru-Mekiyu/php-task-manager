
<?php
require_once "auth/auth.php";
require_once "auth/csrf.php";
require_once "config/database.php";
require_once "config/validation.php";
$message = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $token = $_POST["csrf_token"] ?? "";

    if (!verify_csrf_token($token)) {
        http_response_code(403);
        die("Invalid CSRF token.");
    }

    $title = trim($_POST["title"] ?? "");
    $priority = $_POST["priority"] ?? "";

    $errors = validateTask($title, $priority);

    if (empty($errors)) {

        $sql = "
            INSERT INTO tasks (
                title,
                priority,
                user_id
            )
            VALUES (
                :title,
                :priority,
                :user_id
            )
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":title" => $title,
            ":priority" => $priority,
            ":user_id" => $_SESSION["user_id"]
        ]);

        header("Location: index.php");
        exit;
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

    <title>PHP Task Manager</title>

</head>

<body>
    <p>
    Welcome,
    <?= htmlspecialchars($_SESSION["user_name"]) ?>
</p>

<a href="logout.php">
    Logout
</a>

    <h1>Task Manager</h1>
    <?php if (!empty($errors)): ?>

    <div>

        <h3>Please fix the following:</h3>

        <ul>

            <?php foreach ($errors as $error): ?>

                <li>
                    <?= htmlspecialchars($error) ?>
                </li>

            <?php endforeach; ?>

        </ul>

    </div>

<?php endif; ?>

    <?php if ($message): ?>

        <p>
            <?= htmlspecialchars($message) ?>
        </p>

    <?php endif; ?>


    <!-- CREATE TASK FORM -->

    <h2>Add Task</h2>
<form method="POST">

    <input
        type="hidden"
        name="csrf_token"
        value="<?= htmlspecialchars(csrf_token()) ?>"
    >

    <input
        type="text"
        name="title"
        required
    >

    <select name="priority">

        <option value="low">
            Low
        </option>

        <option value="medium">
            Medium
        </option>

        <option value="high">
            High
        </option>

    </select>

    <button type="submit">
        Add Task
    </button>

</form>

    <!-- TASK LIST -->

    <h2>Tasks</h2>

    <?php if (count($tasks) > 0): ?>

        <?php foreach ($tasks as $task): ?>

            <div>

                <h3>
                    <?= htmlspecialchars($task["title"]) ?>
                </h3>

                <p>
                    Priority:
                    <?= htmlspecialchars($task["priority"]) ?>
                </p>

               <p>
    Created:
    <?= htmlspecialchars($task["created_at"]) ?>
</p>

<a href="edit.php?id=<?= $task["id"] ?>">
    Edit
</a>

<form method="POST" action="delete.php">

    <input
        type="hidden"
        name="id"
        value="<?= (int) $task["id"] ?>"
    >

    <input
        type="hidden"
        name="csrf_token"
        value="<?= htmlspecialchars(csrf_token()) ?>"
    >

    <button type="submit">
        Delete
    </button>

</form>



            </div>

            <hr>

        <?php endforeach; ?>

    <?php else: ?>

        <p>No tasks found.</p>

    <?php endif; ?>

</body>

</html>