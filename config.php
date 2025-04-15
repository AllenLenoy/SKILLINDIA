<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Database configuration
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'new_skill_india');

// Email configuration
define('SMTP_HOST', $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com');
define('SMTP_PORT', $_ENV['SMTP_PORT'] ?? 587);
define('SMTP_USERNAME', $_ENV['SMTP_USERNAME']);
define('SMTP_PASSWORD', $_ENV['SMTP_PASSWORD']);

// YouTube API configuration
define('YOUTUBE_API_KEY', $_ENV['YOUTUBE_API_KEY']);

// Base URL
define('BASE_URL', $_ENV['BASE_URL']);

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.gc_maxlifetime', 3600); // 1 hour
ini_set('session.cookie_lifetime', 3600); // 1 hour

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Connection failed. Please try again later.");
}

// Function to send email using PHPMailer
function sendEmail($to, $subject, $body) {
    try {
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        
        // Recipients
        $mail->setFrom(SMTP_USERNAME, 'Skill India');
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->CharSet = 'UTF-8';
        
        // Send email
        $mail->send();
        error_log("Email sent successfully to: " . $to);
        return true;
        
    } catch (Exception $e) {
        error_log("Email error: " . $e->getMessage());
        return false;
    }
}

// Function to sanitize input data
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Function to generate random string
function generateRandomString($length = 10) {
    return bin2hex(random_bytes($length));
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && 
           isset($_SESSION['email']) && 
           isset($_SESSION['name']) && 
           isset($_SESSION['role']) &&
           isset($_SESSION['is_verified']) &&
           $_SESSION['is_verified'] === true;
}

// Function to check if user is admin
function isAdmin() {
    return isLoggedIn() && $_SESSION['role'] === 'admin';
}

// Function to require login
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['error'] = "Please login to continue";
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header("Location: login.php");
        exit();
    }
}

// Function to require admin
function requireAdmin() {
    if (!isAdmin()) {
        $_SESSION['error'] = "Access denied";
        header("Location: index.php");
        exit();
    }
}

// Function to set user session
function setUserSession($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['is_verified'] = $user['is_verified'];
    $_SESSION['last_activity'] = time();
}

// Function to clear user session
function clearUserSession() {
    unset($_SESSION['user_id']);
    unset($_SESSION['name']);
    unset($_SESSION['email']);
    unset($_SESSION['role']);
    unset($_SESSION['is_verified']);
    unset($_SESSION['last_activity']);
}

// Function to redirect
function redirect($url) {
    header("Location: $url");
    exit();
}
?>