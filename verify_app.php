<?php

// Mocking session for CLI testing
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function test($name, $callback) {
    try {
        $callback();
        echo "Testing $name... PASSED\n";
    } catch (Exception $e) {
        echo "Testing $name... FAILED: " . $e->getMessage() . "\n";
    }
}

ob_start();

// Check Database Connection and extract $pdo to global scope
test("Database Connection", function() {
    global $pdo;
    require 'config/database.php';
    if (!isset($pdo)) throw new Exception("PDO not initialized");
    $pdo->query("SELECT 1");
});

// Check Autoloading
test("Autoloading Classes", function() {
    require_once 'autoload.php';
    if (!class_exists('App\Models\Task')) throw new Exception("Task model not loaded");
    if (!class_exists('App\Controllers\TaskController')) throw new Exception("TaskController not loaded");
});

// Check Routing Logic (Simulate index.php)
test("Routing - Login Page", function() {
    global $pdo;
    $_GET['action'] = 'login';
    $_SERVER['REQUEST_METHOD'] = 'GET';
    
    ob_start();
    require 'index.php';
    $output = ob_get_clean();
    
    if (strpos($output, '<h1>Login</h1>') === false) {
        throw new Exception("Login page content not found in output");
    }
});

test("Routing - Register Page", function() {
    global $pdo;
    $_GET['action'] = 'register';
    $_SERVER['REQUEST_METHOD'] = 'GET';
    
    ob_start();
    require 'index.php';
    $output = ob_get_clean();
    
    if (strpos($output, '<h1>Create Account</h1>') === false) {
        throw new Exception("Register page content not found in output");
    }
});

test("Functional - User Registration", function() {
    global $pdo;
    
    // Clean up if exists
    $stmt = $pdo->prepare("DELETE FROM users WHERE email = ?");
    $stmt->execute(['test@example.com']);
    
    $_GET['action'] = 'register';
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST['name'] = 'Test User';
    $_POST['email'] = 'test@example.com';
    $_POST['password'] = 'password123';
    
    // Use a separate process to avoid require_once and exit() issues
    $innerCommand = 'if (session_status() === PHP_SESSION_NONE) session_start(); ' .
                   'require \'config/database.php\'; ' .
                   'require \'app/Helpers/csrf.php\'; ' .
                   'require \'autoload.php\'; ' .
                   '$_GET = [\'action\' => \'register\']; ' .
                   '$_SERVER = [\'REQUEST_METHOD\' => \'POST\']; ' .
                   '$_POST = [\'name\' => \'Test User\', \'email\' => \'test@example.com\', \'password\' => \'password123\']; ' .
                   'ob_start(); ' .
                   'require \'index.php\'; ' .
                   '$out = ob_get_clean(); ' .
                   'echo $out;';
    
    file_put_contents('temp_reg.php', '<?php ' . $innerCommand);
    $output = shell_exec('php temp_reg.php 2>&1');
    unlink('temp_reg.php');
    
    if (strpos($output, 'Account created successfully!') === false) {
        file_put_contents('test_output.html', $output);
        throw new Exception("Registration failed or success message missing.");
    }
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute(['test@example.com']);
    if (!$stmt->fetch()) {
        throw new Exception("User not found in database after registration");
    }
});

ob_end_clean();
echo "Testing Functional - User Login... ";
try {
    // Functional - User Login Logic
    global $pdo;
    $userModel = new App\Models\User($pdo);
    $authController = new App\Controllers\AuthController($userModel);
    $_GET = ["action" => "login"];
    $_SERVER = ["REQUEST_METHOD" => "POST"];
    $_POST = ["email" => "test@example.com", "password" => "password123"];
    ob_start();
    try { $authController->login(); } catch (Throwable $e) {}
    ob_end_clean();
    if (!isset($_SESSION['user_id'])) throw new Exception("Login failed");
    $_SESSION['csrf_token'] = csrf_token();
    echo "PASSED\n";
} catch (Exception $e) { echo "FAILED: " . $e->getMessage() . "\n"; }

echo "Testing Functional - Create Task... ";
try {
    global $pdo;
    $taskModel = new App\Models\Task($pdo);
    $taskController = new App\Controllers\TaskController($taskModel);
    $_POST = ["title" => "Test Task", "priority" => "high", "csrf_token" => $_SESSION['csrf_token']];
    ob_start();
    try { $taskController->store(); } catch (Throwable $e) {}
    ob_end_clean();
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE title = ? AND user_id = ?");
    $stmt->execute(['Test Task', $_SESSION['user_id']]);
    $task = $stmt->fetch();
    if (!$task) throw new Exception("Task not created");
    $_SESSION['last_task_id'] = $task['id'];
    echo "PASSED\n";
} catch (Exception $e) { echo "FAILED: " . $e->getMessage() . "\n"; }

echo "Testing Functional - Edit Task... ";
try {
    global $pdo;
    $taskModel = new App\Models\Task($pdo);
    $taskController = new App\Controllers\TaskController($taskModel);
    $_GET = ["action" => "edit", "id" => $_SESSION['last_task_id']];
    $_POST = ["title" => "Updated Task", "priority" => "low"];
    ob_start();
    try { $taskController->update(); } catch (Throwable $e) {}
    ob_end_clean();
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$_SESSION['last_task_id']]);
    $task = $stmt->fetch();
    if ($task['title'] !== 'Updated Task') throw new Exception("Task not updated");
    echo "PASSED\n";
} catch (Exception $e) { echo "FAILED: " . $e->getMessage() . "\n"; }

echo "Testing Functional - Delete Task... ";
try {
    global $pdo;
    $taskModel = new App\Models\Task($pdo);
    $taskController = new App\Controllers\TaskController($taskModel);
    $_POST = ["id" => $_SESSION['last_task_id'], "csrf_token" => $_SESSION['csrf_token']];
    ob_start();
    try { $taskController->delete(); } catch (Throwable $e) {}
    ob_end_clean();
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$_SESSION['last_task_id']]);
    if ($stmt->fetch()) throw new Exception("Task not deleted");
    echo "PASSED\n";
} catch (Exception $e) { echo "FAILED: " . $e->getMessage() . "\n"; }

echo "\nVerification complete.\n";
