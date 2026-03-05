<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: worker/update-logic.php
 * PURPOSE: Elite Data Sync with Security & Verification Logic
 */

session_start();

// 1. Security Guard
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['logged_in'])) {
    header("Location: dashboard.php");
    exit();
}

// 2. System Initialization
$base_dir = dirname(__DIR__);
require_once $base_dir . '/includes/config.php';

$user_id = $_SESSION['user_id'];
$errors = [];

// 3. Data Sanitization
$full_name      = mysqli_real_escape_string($conn, $_POST['full_name']);
$category_id    = (int)$_POST['category_id'];
$bio            = mysqli_real_escape_string($conn, $_POST['bio']);
$hourly_rate    = (float)$_POST['hourly_rate'];
$service_radius = (int)$_POST['service_radius'];
$is_live        = isset($_POST['is_live']) ? 1 : 0;

// 4. File Logic (Identity & Verification Vault)
$photo_sql = "";
$vault_sql = "";

// Handle Profile Photo
if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === 0) {
    $p_dir = $base_dir . '/assets/uploads/profiles/';
    $p_ext = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
    $p_name = "pro_" . $user_id . "_" . time() . "." . $p_ext;
    if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $p_dir . $p_name)) {
        $photo_sql = ", profile_photo = '$p_name'";
    }
}

// Handle ID/Permit Verification
if (isset($_FILES['id_proof']) && $_FILES['id_proof']['error'] === 0) {
    $v_dir = $base_dir . '/assets/uploads/verification/';
    if (!is_dir($v_dir)) mkdir($v_dir, 0777, true); // Ensure dir exists
    
    $v_ext = pathinfo($_FILES['id_proof']['name'], PATHINFO_EXTENSION);
    $v_name = "id_" . $user_id . "_" . time() . "." . $v_ext;
    if (move_uploaded_file($_FILES['id_proof']['tmp_name'], $v_dir . $v_name)) {
        $vault_sql = ", id_proof_path = '$v_name'";
    }
}

// 5. Security Shield: Password Logic
$password_sql = "";
if (!empty($_POST['new_password'])) {
    $current_pass = $_POST['current_password'];
    $new_pass     = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    // Match Check
    if ($new_pass !== $confirm_pass) {
        header("Location: edit-profile.php?status=mismatch");
        exit();
    }

    // Database Hash Check
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    if (password_verify($current_pass, $res['password'])) {
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
        $password_sql = ", password = '$hashed'";
    } else {
        header("Location: edit-profile.php?status=wrong_pass");
        exit();
    }
}

// 6. Atomic Sync: Update All Tables
$conn->begin_transaction();

try {
    // Update Users Table (Name & Password)
    $user_sql = "UPDATE users SET full_name = ? $password_sql WHERE id = ?";
    $stmt_u = $conn->prepare($user_sql);
    $stmt_u->bind_param("si", $full_name, $user_id);
    $stmt_u->execute();

    // Update Worker Profile (The "Pillars")
    $prof_sql = "UPDATE worker_profiles SET 
                 category_id = ?, bio = ?, hourly_rate = ?, 
                 service_radius = ?, is_live = ? 
                 $photo_sql $vault_sql 
                 WHERE user_id = ?";
    
    $stmt_p = $conn->prepare($prof_sql);
    $stmt_p->bind_param("isdiii", $category_id, $bio, $hourly_rate, $service_radius, $is_live, $user_id);
    $stmt_p->execute();

    $conn->commit();
    $_SESSION['full_name'] = $full_name;
    header("Location: edit-profile.php?status=synced");

} catch (Exception $e) {
    $conn->rollback();
    header("Location: edit-profile.php?status=error");
}
exit();