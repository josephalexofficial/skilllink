<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: worker/save-profile.php
 * PURPOSE: Secure Data Processing & Onboarding Completion
 */

session_start();

// 1. Security Guard: Prevent unauthorized access
if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'worker') {
    header("Location: ../auth/login.php");
    exit();
}

// 2. System Initialization
$base_dir = dirname(__DIR__);
require_once $base_dir . '/includes/config.php';

// 3. The "Judge" - Validation Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id     = $_SESSION['user_id'];
    $category_id = mysqli_real_escape_with_quotes($conn, $_POST['category_id']);
    $bio         = mysqli_real_escape_with_quotes($conn, $_POST['bio']);
    $upload_dir  = $base_dir . '/assets/uploads/profiles/';
    $photo_name  = 'default-pro.png'; // Default if none uploaded

    // Ensure upload directory exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // 4. Identity Check: Handle Profile Photo Upload
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === 0) {
        $file_ext = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
        $new_name = "pro_" . $user_id . "_" . time() . "." . $file_ext;
        $target   = $upload_dir . $new_name;

        if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target)) {
            $photo_name = $new_name;
        }
    }

    // 5. The Transaction: Atomic Profile Creation
    // We use a transaction to ensure either everything saves or nothing saves.
    $conn->begin_transaction();

    try {
        // Insert into the Professional Vault
        $stmt = $conn->prepare("INSERT INTO worker_profiles (user_id, category_id, bio, profile_photo) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $user_id, $category_id, $bio, $photo_name);
        
        if (!$stmt->execute()) {
            throw new Exception("Profile creation failed: " . $stmt->error);
        }

        // The Logic Gate Key: The profile now exists, so dashboard.php will allow entry.
        $conn->commit();
        
        // 6. Success Bridge: Pass a success flag to the dashboard
        $_SESSION['onboarding_complete'] = true;
        header("Location: dashboard.php?status=welcome");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        // Redirect back to wizard with error context
        header("Location: setup-wizard.php?error=system_failure");
        exit();
    }
} else {
    // Prevent direct URL access to this processing script
    header("Location: setup-wizard.php");
    exit();
}

/**
 * Helper to keep queries clean
 */
function mysqli_real_escape_with_quotes($conn, $data) {
    return mysqli_real_escape_string($conn, trim($data));
}