<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: auth/signup-process.php
 * PURPOSE: Secure Data Processing & Identity Seeding
 * REFINEMENTS: Password Hashing, Role-Based Routing, and Prepared Statements.
 */

// 1. System Initialization
session_start();
$include_path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;

// 2. Explicitly link the Central Connection Node
if (file_exists($include_path . 'config.php')) {
    require_once $include_path . 'config.php';
} else {
    die("Critical Error: Core configuration (config.php) missing at " . $include_path);
}

// 3. Gateway Check: Ensure the request is coming via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 4. Data Extraction & Sanitization
    // We strip dangerous characters to protect the database from 'normie' input errors
    $full_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
    $email     = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone     = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS);
    $county    = filter_input(INPUT_POST, 'county', FILTER_SANITIZE_SPECIAL_CHARS);
    $area      = filter_input(INPUT_POST, 'area', FILTER_SANITIZE_SPECIAL_CHARS);
    $password  = $_POST['password']; // Raw password to be hashed
    $role      = $_POST['role'];     // 'worker' or 'client'

    // 5. Security Protocol: Password Hashing
    // We never store plain text. This creates a secure, randomized hash string.
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // 6. Identity Collision Check: Ensure email is unique
        $check_sql = "SELECT id FROM users WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            // If email exists, we stop the process
            die("Error: An account with this email address already exists.");
        }
        $check_stmt->close();

        // 7. Data Transaction: Committing the Identity Node to the Database
        // We use a Prepared Statement to prevent SQL Injection attacks.
        $sql = "INSERT INTO users (full_name, email, phone, county, area, password, role) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        // "sssssss" binds 7 string variables to the query placeholders
        $stmt->bind_param("sssssss", $full_name, $email, $phone, $county, $area, $hashed_password, $role);

        if ($stmt->execute()) {
            // SUCCESS: Redirect to login page with a success signal
            header("Location: login.php?signup=success");
            exit();
        } else {
            echo "Error: Database transaction failed. Please check your SQL structure.";
        }

        $stmt->close();

    } catch (Exception $e) {
        // Error Logging: Record the error without exposing secrets to the user
        error_log($e->getMessage());
        die("A system error occurred. Please try again later.");
    }

} else {
    // SECURITY: Redirect users who try to access this file directly
    header("Location: signup.php");
    exit();
}
?>