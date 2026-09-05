<?php

$driver   = getenv('DB_DRIVER') ?: ($_ENV['DB_DRIVER'] ?? 'mysql');
$host     = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? 'localhost');
$port     = getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? '3306');
$dbname   = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? 'task_manager');
$username = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? 'root');
$password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : ($_ENV['DB_PASS'] ?? '');
$dbPath   = getenv('DB_PATH') ?: ($_ENV['DB_PATH'] ?? __DIR__ . '/../database.sqlite');

try {
    if ($driver === 'sqlite') {
        $dsn = "sqlite:" . $dbPath;
        $pdo = new PDO($dsn);
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL
        );");
        $pdo->exec("CREATE TABLE IF NOT EXISTS tasks (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title VARCHAR(255) NOT NULL,
            priority VARCHAR(50) NOT NULL,
            user_id INTEGER NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );");
    } else {
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password);
    }

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
