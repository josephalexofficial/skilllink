<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: auth/login-process.php
 * PURPOSE: Secure Authentication & Role-Based Dashboard Routing
 * REFINEMENTS: Path Alignment (worker/ vs client/), Session Anchoring, and Security Checks.
 */

// 1. System Initialization
session_start();
$include_path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;

// 2. Explicitly link the Central Connection Node
if (file_exists($include_path . 'config.php')) {
    require_once $include_path . 'config.php';
} else {
    die("Critical Error: Core configuration missing.");
}

// 3. Gateway Check: Ensure the request is coming via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 4. Data Extraction & Sanitization
    $email    = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password']; // Raw input to be verified against hash

    try {
        // 5. The Identity Lookup Node
        // We fetch the hashed password and the role to execute the Smart Switch
        $sql = "SELECT id, full_name, password, role, status FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            
            // 6. Security Protocol: Key Verification
            // password_verify() mathematically compares the raw input to the stored hash
            if (password_verify($password, $user['password'])) {
                
                // 7. Status Check: Ensure the account is active
                if ($user['status'] !== 'active') {
                    die("Error: This account has been suspended. Please contact support.");
                }

                // 8. Session Management: Anchoring the User Identity
                session_regenerate_id(true); // Prevents session fixation
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['logged_in'] = true;

                // 9. THE SMART SWITCH: Corrected Directory Routing
                // Aligned with your actual folder tree (worker/ and client/)
                if ($user['role'] === 'worker') {
                    header("Location: ../worker/dashboard.php");
                } else if ($user['role'] === 'client') {
                    header("Location: ../client/dashboard.php");
                }
                exit();

            } else {
                // Invalid Password
                header("Location: login.php?error=invalid_credentials");
                exit();
            }
        } else {
            // Email not found
            header("Location: login.php?error=invalid_credentials");
            exit();
        }

        $stmt->close();

    } catch (Exception $e) {
        error_log($e->getMessage());
        die("A system error occurred. Please try again later.");
    }

} else {
    // SECURITY: Redirect users who try to access this file directly
    header("Location: login.php");
    exit();
}