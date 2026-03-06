<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: client/post-logic.php
 * PURPOSE: Surgical Data Engineering - Processing New Project Launches
 */

session_start();

// 1. Security Guard: Ensure only authorized clients can broadcast jobs
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'client') {
    header("Location: dashboard.php");
    exit();
}

// 2. System Initialization
$base_dir = dirname(__DIR__);
require_once $base_dir . '/includes/config.php';

$client_id = $_SESSION['user_id'];
$errors = [];

// 3. Data Extraction & Sanitization (The "Cleaning" Phase)
$category_id   = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
$title         = mysqli_real_escape_string($conn, trim($_POST['title']));
$description   = mysqli_real_escape_string($conn, trim($_POST['description']));
$location_name = mysqli_real_escape_string($conn, trim($_POST['location_name']));
$budget        = isset($_POST['budget']) ? (float)$_POST['budget'] : 0.00;
$budget_type   = in_array($_POST['budget_type'], ['fixed', 'negotiable']) ? $_POST['budget_type'] : 'fixed';

// Spatial Placeholders (Ready for future GPS integration)
$latitude      = NULL; 
$longitude     = NULL;

// 4. Surgical Validation Engine
if ($category_id === 0) $errors[] = "Please select a professional craft.";
if (empty($title))      $errors[] = "A project headline is required.";
if (empty($description))$errors[] = "Please provide project details.";
if (empty($location_name)) $errors[] = "Location is required for regional matching.";

// 5. Execution: Committing to the Task Vault
if (empty($errors)) {
    try {
        // Use Prepared Statements for Industrial Security
        $sql = "INSERT INTO tasks (client_id, category_id, title, description, location_name, latitude, longitude, budget, budget_type, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'open')";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisssddds", $client_id, $category_id, $title, $description, $location_name, $latitude, $longitude, $budget, $budget_type);
        
        if ($stmt->execute()) {
            // SUCCESS: Redirect to dashboard with a 'launched' pulse signal
            header("Location: dashboard.php?status=synced");
            exit();
        } else {
            throw new Exception("Execution failed at the database level.");
        }

    } catch (Exception $e) {
        // Log error and redirect with system error status
        header("Location: post-job.php?status=error");
        exit();
    }
} else {
    // REDIRECT: Validation failed - Return to studio
    header("Location: post-job.php?status=mismatch");
    exit();
}