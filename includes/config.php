<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: includes/config.php
 * PURPOSE: Central System Constants & DB Connection Protocol
 * REFINEMENTS: Added Brand & Route Constants to support Header Logic.
 */

// 1. Database Credentials (Standard XAMPP Defaults)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); 
define('DB_NAME', 'skilllink_db');

// 2. Branding & Navigation Nodes (Required by your Header)
// These constants allow your header.php to render without fatal errors.
define('BRAND_COLOR', '#3b82f6'); // The specific SkillLink Blue hex code
define('SITE_NAME', 'SkillLink');
define('BASE_URL', 'http://localhost/skilllink/');

// 3. Establishing the Connection Node
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// 4. Connection Protocol Check
if ($conn->connect_error) {
    // Stops execution if the database is unreachable
    die("Critical Error: Database connection failed. " . $conn->connect_error);
}

// 5. Set Charset for Elite Character Support
// Essential for correctly storing Kenyan names and localized data.
$conn->set_charset("utf8mb4");

/**
 * 🧪 DEVELOPMENT DEBUGGING PROTOCOL
 * Keep these ON during this build phase to see errors instead of a blank screen.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

// The connection variable $conn is now globally available to your process files.
?>