<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: auth/login-process.php
 * PURPOSE: Refined, Production-Ready Authentication & Multi-Role Routing
 * SECURITY: Bcrypt Verification, Input Trimming, and Session Anchoring.
 */

// 1. System Initialization
session_start();
$include_path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;

// 2. Core Configuration Link
if (file_exists($include_path . 'config.php')) {
    require_once $include_path . 'config.php';
} else {
    die("Critical Error: Core configuration missing.");
}

// 3. Gateway Check: Ensure the request is via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 4. Data Extraction & Surgical Cleaning
    // trim() prevents invisible spaces from causing "Invalid Credentials" errors.
    $email    = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $password = trim($_POST['password']); 

    try {
        // 5. The Identity Lookup Node
        // We fetch the email as well for session persistence.
        $sql = "SELECT id, full_name, email, password, role, status FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            
            // 6. Security Protocol: Bcrypt Key Verification
            if (password_verify($password, $user['password'])) {
                
                // 7. Status Check: Account Integrity
                if ($user['status'] !== 'active') {
                    header("Location: login.php?error=account_suspended");
                    exit();
                }

                // 8. Session Management: Identity Anchoring
                session_regenerate_id(true); // Immunity against Session Fixation
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email']; // Added for Dashboard use
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['logged_in'] = true;

                // 9. THE SMART SWITCH: Secure Directory Routing
                // This logic ensures every user role reaches its designated home.
                if ($user['role'] === 'worker') {
                    header("Location: ../worker/dashboard.php");
                } 
                else if ($user['role'] === 'client') {
                    header("Location: ../client/dashboard.php");
                } 
                else if ($user['role'] === 'admin') {
                    header("Location: ../admin/dashboard.php");
                }
                else {
                    // Fallback for unknown roles
                    header("Location: login.php?error=access_denied");
                }
                exit();

            } else {
                // Invalid Password Handshake
                header("Location: login.php?error=invalid_credentials");
                exit();
            }
        } else {
            // Identity Not Found
            header("Location: login.php?error=invalid_credentials");
            exit();
        }

        $stmt->close();

    } catch (Exception $e) {
        // Log the error internally; show a generic message to the user for security.
        error_log("Login Error: " . $e->getMessage());
        header("Location: login.php?error=system_error");
        exit();
    }

} else {
    // SECURITY: Block direct file access
    header("Location: login.php");
    exit();
}