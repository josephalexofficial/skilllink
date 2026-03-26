<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: auth/forgot-password-process.php
 * VERSION: 1.1 "The Log-Aware Sentinel"
 */

// 1. THE STEEL VAULT: Security Guard
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("UNAUTHORIZED_PROTOCOL_VIOLATION");
}

// 2. System Initialization
$base_dir = dirname(__DIR__);
require_once $base_dir . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'config.php';

// 3. Input Sanitization
$email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';

if (empty($email)) {
    header("Location: forgot-password.php?error=empty_field");
    exit();
}

/**
 * 4. THE ANALYST: Registry Search
 */
$user_check = $conn->query("SELECT id FROM users WHERE email = '$email' AND status = 'active' LIMIT 1");

if ($user_check->num_rows === 0) {
    header("Location: forgot-password.php?error=node_not_found");
    exit();
}

/**
 * 5. THE ARCHITECT: Token Generation
 */
$token = bin2hex(random_bytes(32));
$expires_at = date("Y-m-d H:i:s", strtotime('+1 hour'));

// Clean old tokens
$conn->query("DELETE FROM password_resets WHERE email = '$email'");

// Store new handshake
$insert_token = $conn->query("INSERT INTO password_resets (email, token, expires_at) 
                             VALUES ('$email', '$token', '$expires_at')");

if ($insert_token) {
    /**
     * 6. THE LOG DISPATCHER: Persistent Handshake Logging
     */
    $reset_link = "http://localhost/skilllink/auth/reset-password.php?token=" . $token;
    $log_dir = $base_dir . DIRECTORY_SEPARATOR . 'logs';
    $log_file = $log_dir . DIRECTORY_SEPARATOR . 'recovery_links.txt';

    // DIRECTORY VERIFICATION: Ensure the log node exists
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0777, true);
    }

    // Write to the Log File
    $log_entry = "[" . date('Y-m-d H:i:s') . "] RESET_NODE: $email | LINK: $reset_link" . PHP_EOL;
    file_put_contents($log_file, $log_entry, FILE_APPEND);

    // Final Handshake: Success Redirect
    header("Location: forgot-password.php?sent=success");
    exit();
} else {
    header("Location: forgot-password.php?error=system_failure");
    exit();
}