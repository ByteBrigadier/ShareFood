<?php
// ---------------------------------------------------------------
// Database credentials are NEVER stored in the repository.
// Provide them through environment variables (DB_HOST, DB_NAME,
// DB_USER, DB_PASS) or through an untracked config.local.php file
// (copy config.local.example.php to config.local.php).
// ---------------------------------------------------------------
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'food_sharing_platform';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

if (file_exists(__DIR__ . '/config.local.php')) {
    require __DIR__ . '/config.local.php';
}

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    die("<div style='font-family:sans-serif; padding:20px; background:#fff3cd; color:#856404; border:1px solid #ffeeba; border-radius:8px; max-width:600px; margin:40px auto;'>" .
        "<h2>Database Connection Error</h2>" .
        "<p>Unable to connect to the MySQL database. Please check that the MySQL server is running, " .
        "the database has been imported from <code>database.sql</code>, and your credentials are set " .
        "(see the README).</p>" .
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

// ---- CSRF protection helpers ----
if (!function_exists('csrfToken')) {
    function csrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrfField')) {
    function csrfField() {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrfToken()) . '">';
    }
}

if (!function_exists('csrfValid')) {
    function csrfValid() {
        return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }
}
?>
