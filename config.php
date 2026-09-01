<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'food_sharing_platform';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : 'dash';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("<div style='font-family:sans-serif; padding:20px; background:#fff3cd; color:#856404; border:1px solid #ffeeba; border-radius:8px; max-width:600px; margin:40px auto;'>" .
        "<h2>Database Connection Error</h2>" .
        "<p>Unable to connect to MySQL database <strong>$dbname</strong> on <strong>$host</strong>.</p>" .
        "<p><strong>Details:</strong> " . htmlspecialchars($e->getMessage()) . "</p>" .
        "<p>Please ensure your MySQL server is running and the database is imported using <code>database.sql</code>.</p>" .
        "</div>");
}

// Only define functions if they are not already defined
if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}

if (!function_exists('isDonor')) {
    function isDonor() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'donor';
    }
}

if (!function_exists('isReceiver')) {
    function isReceiver() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'receiver';
    }
}

if (!function_exists('redirect')) {
    function redirect($page) {
        header("Location: $page");
        exit();
    }
}
?>
