<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'PHP Task Manager' ?></title>
</head>
<body>
    <?php if (isset($_SESSION["user_id"])): ?>
        <p>Welcome, <?= htmlspecialchars($_SESSION["user_name"]) ?></p>
        <a href="index.php?action=logout">Logout</a>
    <?php endif; ?>
