<?php include __DIR__ . '/../layout/header.php'; ?>
    <h1>Login</h1>

    <?php if (!empty($errors)): ?>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST">
        <div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        <br>
        <div>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <br>
        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="index.php?action=register">Register</a></p>
<?php include __DIR__ . '/../layout/footer.php'; ?>
