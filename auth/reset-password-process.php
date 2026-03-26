<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: auth/reset-password-process.php
 * VERSION: 1.0 "The Credential Finalization Node"
 * PURPOSE: Logic for updating user passwords and purging recovery tokens.
 */

// 1. THE STEEL VAULT: Security Guard
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("UNAUTHORIZED_PROTOCOL_VIOLATION");
}

// 2. System Initialization
$base_dir = dirname(__DIR__);
require_once $base_dir . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'config.php';

/**
 * 3. THE ANALYST: Input Processing
 */
$token    = isset($_POST['token']) ? mysqli_real_escape_string($conn, $_POST['token']) : '';
$email    = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Basic Validation Node
if (empty($token) || empty($password) || empty($email)) {
    header("Location: login.php?error=missing_data");
    exit();
}

/**
 * 4. THE ARCHITECT: Handshake Verification
 * Re-verifying token validity before committing the update
 */
$token_check = $conn->query("SELECT id FROM password_resets 
                             WHERE email = '$email' 
                             AND token = '$token' 
                             AND expires_at > NOW() 
                             LIMIT 1");

if ($token_check->num_rows === 0) {
    // Handshake failed or expired
    header("Location: forgot-password.php?error=expired_handshake");
    exit();
}

/**
 * 5. THE SURGEON: Credential Update & Vault Cleanup
 */
// Cryptographically hash the new credential
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// BEGIN ATOMIC UPDATE
$conn->begin_transaction();

try {
    // 1. Update the User Registry
    $update_user = $conn->query("UPDATE users SET password = '$hashed_password' WHERE email = '$email'");

    // 2. Purge the Recovery Token (Self-Destruct)
    $purge_token = $conn->query("DELETE FROM password_resets WHERE email = '$email'");

    if ($update_user && $purge_token) {
        $conn->commit();
        
        // Final Handshake: Success
        header("Location: login.php?reset=success");
        exit();
    } else {
        throw new Exception("REGISTRY_UPDATE_FAILURE");
    }

} catch (Exception $e) {
    $conn->rollback();
    error_log("Security Update Error: " . $e->getMessage());
    header("Location: reset-password.php?token=$token&error=system_fault");
    exit();
}