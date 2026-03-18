<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: client/finalize-logic.php
 * PURPOSE: Atomic Transaction Engine - Review Logging & Task Finalization
 */

session_start();

// 1. Security Guard: Role-Gate
if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'client') {
    header("Location: ../auth/login.php");
    exit();
}

// 2. System Initialization
$base_dir = dirname(__DIR__);
require_once $base_dir . '/includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 3. Surgical Data Extraction
    $task_id = mysqli_real_escape_string($conn, $_POST['task_id']);
    $rating  = intval($_POST['rating']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    $client_id = $_SESSION['user_id'];

    // 4. Mission Integrity Check
    // Verify the task belongs to this client and is currently in 'completed' status
    $check_query = $conn->query("SELECT id, worker_id FROM tasks 
                                 WHERE id = '$task_id' 
                                 AND client_id = '$client_id' 
                                 AND status = 'completed'");

    if ($check_query->num_rows === 1) {
        $task_data = $check_query->fetch_assoc();
        $worker_id = $task_data['worker_id'];

        // 5. THE ATOMIC COMMIT
        // Start transaction to ensure both review and status update succeed together
        $conn->begin_transaction();

        try {
            // Step A: Insert the Review into the Registry
            $stmt_review = $conn->prepare("INSERT INTO reviews (task_id, worker_id, client_id, rating, comment) VALUES (?, ?, ?, ?, ?)");
            $stmt_review->bind_param("iiiis", $task_id, $worker_id, $client_id, $rating, $comment);
            $stmt_review->execute();

            // Step B: Finalize the Task Status (Archive it)
            $stmt_task = $conn->prepare("UPDATE tasks SET status = 'finalized', updated_at = NOW() WHERE id = ?");
            $stmt_task->bind_param("i", $task_id);
            $stmt_task->execute();

            // Step C: Flush the Transaction
            $conn->commit();

            // 6. Success Pulse Redirect
            header("Location: my-bookings.php?status=finalized");
            exit();

        } catch (Exception $e) {
            // Rollback if any part of the handshake fails
            $conn->rollback();
            header("Location: my-bookings.php?status=error");
            exit();
        }
    } else {
        // Task either doesn't exist, isn't owned by the user, or isn't ready for finalization
        header("Location: my-bookings.php?status=unauthorized");
        exit();
    }
} else {
    // Protocol Violation: Direct access denied
    header("Location: my-bookings.php");
    exit();
}