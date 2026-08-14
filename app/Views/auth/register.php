<?php 
$title = 'Register';
include __DIR__ . '/../layout/header.php'; 
?>
    <h1>Create Account</h1>

    <?php if (!empty($errors)): ?>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (isset($success) && $success): ?>
        <p><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form method="POST">
        <div>
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required>
        </div>
        <br>
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
        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="index.php?action=login">Login</a></p>
<?php include __DIR__ . '/../layout/footer.php'; ?>
