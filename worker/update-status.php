<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: worker/update-status.php
 * PURPOSE: Surgical State Engine - Managing Task Lifecycle Transitions
 */

session_start();

// 1. Security Guard: Role-Gate & Method Validation
if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'worker' || !isset($_GET['id']) || !isset($_GET['to'])) {
    header("Location: manage-tasks.php");
    exit();
}

// 2. System Initialization
$base_dir = dirname(__DIR__);
require_once $base_dir . '/includes/config.php';

$worker_id = $_SESSION['user_id'];
$task_id   = (int)$_GET['id'];
$new_status = mysqli_real_escape_string($conn, $_GET['to']);

// 3. Allowed Transitions Logic
$allowed_statuses = ['in_progress', 'completed'];
if (!in_array($new_status, $allowed_statuses)) {
    header("Location: manage-tasks.php?status=invalid_move");
    exit();
}

// 4. Ownership & State Verification
// We must ensure the worker actually owns this task before updating
$check_query = $conn->prepare("SELECT status FROM tasks WHERE id = ? AND worker_id = ? LIMIT 1");
$check_query->bind_param("ii", $task_id, $worker_id);
$check_query->execute();
$task_data = $check_query->get_result()->fetch_assoc();

if (!$task_data) {
    header("Location: manage-tasks.php?status=unauthorized");
    exit();
}

$current_status = $task_data['status'];

// 5. State Guarding: Logical Flow Validation
$is_valid_transition = false;

if ($current_status === 'assigned' && $new_status === 'in_progress') $is_valid_transition = true;
if ($current_status === 'in_progress' && $new_status === 'completed') $is_valid_transition = true;

if (!$is_valid_transition) {
    header("Location: manage-tasks.php?status=logic_error");
    exit();
}

// 6. Atomic Execution: Shifting the Gear
try {
    $update_stmt = $conn->prepare("UPDATE tasks SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND worker_id = ?");
    $update_stmt->bind_param("sii", $new_status, $task_id, $worker_id);
    
    if ($update_stmt->execute()) {
        // SUCCESS: Redirect back with a high-fidelity sync signal
        header("Location: manage-tasks.php?status=synced&state=" . $new_status);
        exit();
    } else {
        throw new Exception("Update failed.");
    }

} catch (Exception $e) {
    header("Location: manage-tasks.php?status=system_error");
    exit();
}