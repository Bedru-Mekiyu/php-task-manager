<?php include __DIR__ . '/../layout/header.php'; ?>
    <h1>Task Manager</h1>

    <?php if (!empty($errors)): ?>
        <div>
            <h3>Please fix the following:</h3>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (isset($message) && $message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <h2>Add Task</h2>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
        <input type="text" name="title" required>
        <select name="priority">
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
        </select>
        <button type="submit">Add Task</button>
    </form>

    <h2>Tasks</h2>
    <?php if (count($tasks) > 0): ?>
        <?php foreach ($tasks as $task): ?>
            <div>
                <h3><?= htmlspecialchars($task["title"]) ?></h3>
                <p>Priority: <?= htmlspecialchars($task["priority"]) ?></p>
                <p>Created: <?= htmlspecialchars($task["created_at"]) ?></p>
                <a href="index.php?action=edit&id=<?= $task["id"] ?>">Edit</a>
                <form method="POST" action="index.php?action=delete">
                    <input type="hidden" name="id" value="<?= (int) $task["id"] ?>">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                    <button type="submit">Delete</button>
                </form>
            </div>
            <hr>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No tasks found.</p>
    <?php endif; ?>
<?php include __DIR__ . '/../layout/footer.php'; ?>
