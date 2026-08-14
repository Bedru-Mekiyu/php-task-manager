<?php

require_once "config/database.php";

$stmt = $pdo->query("SELECT * FROM tasks");

$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
print_r($tasks);
echo "</pre>";