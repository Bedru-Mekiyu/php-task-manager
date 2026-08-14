<?php 
$title = 'Edit Task';
include __DIR__ . '/../layout/header.php'; 
?>
    <h1>Edit Task</h1>

    <form method="POST">
        <div>
            <label for="title">Task</label>
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
            <label for="priority">Priority</label>
            <select id="priority" name="priority">
                <option value="low" <?= $task["priority"] === "low" ? "selected" : "" ?>>Low</option>
                <option value="medium" <?= $task["priority"] === "medium" ? "selected" : "" ?>>Medium</option>
                <option value="high" <?= $task["priority"] === "high" ? "selected" : "" ?>>High</option>
            </select>
        </div>
        <br>
        <button type="submit">Update Task</button>
    </form>
    <br>
    <a href="index.php?action=index">Cancel</a>
<?php include __DIR__ . '/../layout/footer.php'; ?>
