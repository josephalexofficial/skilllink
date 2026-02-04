<?php
/**
 * PROJECT: SkillLink
 * FILE: config.php
 * PURPOSE: Global Constants & Environment Settings
 */

// 1. ERROR REPORTING
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. SITE IDENTITY
define('SITE_NAME', 'SkillLink');
define('BRAND_COLOR', '#0056d2'); 

// 3. PATH MANAGEMENT
define('BASE_URL', 'http://localhost/skilllink/');

// 4. DATABASE CREDENTIALS
define('DB_HOST', 'localhost');
define('DB_NAME', 'skilllink_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// 5. USER ROLES
define('ROLE_CLIENT', 'client');
define('ROLE_WORKER', 'worker');
define('ROLE_ADMIN', 'admin');

/* Note: We purposefully leave out the closing PHP tag below 
   to prevent accidental whitespace from causing header errors. 
*/