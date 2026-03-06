<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: worker/accept-logic.php
 * PURPOSE: Surgical Transaction Engine - Finalizing the Professional Handshake
 */

session_start();

// 1. Security Guard: Role-Gate & Method Validation
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'worker') {
    header("Location: dashboard.php");
    exit();
}

// 2. System Initialization
$base_dir = dirname(__DIR__);
require_once $base_dir . '/includes/config.php';

$worker_id = $_SESSION['user_id'];
$task_id = isset($_POST['task_id']) ? (int)$_POST['task_id'] : 0;

// 3. The "Double-Claim" Guard
// We must verify the task is still 'open' before committing
$check_sql = "SELECT status FROM tasks WHERE id = ? LIMIT 1";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $task_id);
$check_stmt->execute();
$result = $check_stmt->get_result();
$task_data = $result->fetch_assoc();

if (!$task_data || $task_data['status'] !== 'open') {
    // Failure: Task was either taken or cancelled in the last millisecond
    header("Location: dashboard.php?status=taken");
    exit();
}

// 4. Atomic Execution: Binding Pro to Project
try {
    // Shift status to 'assigned' and lock the worker_id
    $update_sql = "UPDATE tasks SET worker_id = ?, status = 'assigned' WHERE id = ? AND status = 'open'";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ii", $worker_id, $task_id);
    
    if ($update_stmt->execute() && $update_stmt->affected_rows > 0) {
        // SUCCESS: The handshake is officially committed to the ledger
        header("Location: manage-tasks.php?status=committed");
        exit();
    } else {
        throw new Exception("Transaction collision detected.");
    }

} catch (Exception $e) {
    // System Error or Collision
    header("Location: dashboard.php?status=error");
    exit();
}