<?php

namespace App\Controllers;

use App\Models\Task;

class TaskController
{
    private $taskModel;

    public function __construct(Task $taskModel)
    {
        $this->taskModel = $taskModel;
    }

    public function index()
    {
        $tasks = $this->taskModel->getAllByUserId($_SESSION['user_id']);
        require_once __DIR__ . '/../Views/tasks/index.php';
    }

    public function store()
    {
        $title = trim($_POST["title"] ?? "");
        $priority = $_POST["priority"] ?? "";

        $errors = validateTask($title, $priority);

        if (empty($errors)) {
            $this->taskModel->create($title, $priority, $_SESSION["user_id"]);
            header("Location: index.php");
            exit;
        }

        $tasks = $this->taskModel->getAllByUserId($_SESSION['user_id']);
        require_once __DIR__ . '/../Views/tasks/index.php';
    }

    public function delete()
    {
        $id = $_POST["id"] ?? "";
        if ($id) {
            $this->taskModel->delete($id, $_SESSION["user_id"]);
        }
        header("Location: index.php");
        exit;
    }

    public function edit()
    {
        $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
        if ($id === false || $id === null) {
            die("Invalid task ID.");
        }

        $task = $this->taskModel->find($id, $_SESSION["user_id"]);
        if (!$task) {
            http_response_code(404);
            die("Task not found.");
        }

        require_once __DIR__ . '/../Views/tasks/edit.php';
    }

    public function update()
    {
        $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
        if ($id === false || $id === null) {
            die("Invalid task ID.");
        }

        $title = trim($_POST["title"] ?? "");
        $priority = $_POST["priority"] ?? "";

        $this->taskModel->update($id, $title, $priority, $_SESSION["user_id"]);
        header("Location: index.php");
        exit;
    }
}
