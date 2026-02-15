<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: auth/signup-process.php
 * PURPOSE: Secure Data Processing & Identity Seeding
 * REFINEMENTS: Added Success Transition Redirect & Identity Handoff.
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

// 3. Gateway Check
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 4. Data Extraction & Sanitization
    $full_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
    $email     = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone     = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS);
    $county    = filter_input(INPUT_POST, 'county', FILTER_SANITIZE_SPECIAL_CHARS);
    $area      = filter_input(INPUT_POST, 'area', FILTER_SANITIZE_SPECIAL_CHARS);
    $password  = $_POST['password']; 
    $role      = $_POST['role'];     

    // 5. Security Protocol: Password Hashing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // 6. Identity Collision Check
        $check_sql = "SELECT id FROM users WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            die("Error: An account with this email address already exists.");
        }
        $check_stmt->close();

        // 7. Data Transaction: Committing the Identity Node
        $sql = "INSERT INTO users (full_name, email, phone, county, area, password, role) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $full_name, $email, $phone, $county, $area, $hashed_password, $role);

        if ($stmt->execute()) {
            // SUCCESS: Identity Handoff
            // We store the name temporarily so success.php can greet them personally
            $_SESSION['temp_name'] = $full_name;
            $_SESSION['temp_role'] = $role;
            
            // Redirect to the new Success Transition page
            header("Location: success.php");
            exit();
        } else {
            echo "Error: Database transaction failed.";
        }
        $stmt->close();

    } catch (Exception $e) {
        error_log($e->getMessage());
        die("A system error occurred.");
    }

} else {
    header("Location: signup.php");
    exit();
}