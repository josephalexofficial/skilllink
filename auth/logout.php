<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: auth/logout.php
 * PURPOSE: Secure System Reset & Session Liquidation
 * REFINEMENTS: Anti-Cache Headers, Cookie Purge, and State-Aware Redirection.
 */

// 1. Initialize Session Context
session_start();

// 2. The "No-Cache" Protocol
// These headers force the browser to re-verify identity if the user hits "Back"
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// 3. Variable Liquidation
// Removes all specific identity data (ID, Name, Role) from the server's RAM
session_unset();

// 4. Session Destruction
// Kills the session life-cycle on the server
session_destroy();

// 5. The Cookie Purge
// Deletes the "Key" (Session ID) from the user's browser by setting its expiry to the past
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// 6. State-Aware Redirection
// Sends the user to the Login page with a success flag for UI feedback
header("Location: login.php?logout=success");
exit();
?>