<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: admin/get_user_intel.php
 * VERSION: 1.0 "The Intel Node"
 * FOCUS: High-speed JSON metadata delivery for the Audit Drawer.
 */

// 1. THE STEEL VAULT: Session Guard
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized Access Protocol']);
    exit();
}

// 2. System Initialization
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'config.php';

header('Content-Type: application/json');

// 3. Validation
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'No Node ID provided']);
    exit();
}

$user_id = intval($_GET['id']);

/**
 * 4. THE ANALYST: Deep Metadata Extraction
 */
try {
    // We use a LEFT JOIN to ensure we get User data even if they don't have a worker profile yet
    $query = "
        SELECT 
            u.id, u.full_name, u.email, u.phone, u.role, u.status, u.county, u.area, u.created_at,
            wp.bio, wp.hourly_rate, wp.is_verified, wp.profile_photo,
            c.cat_name as industry
        FROM users u
        LEFT JOIN worker_profiles wp ON u.id = wp.user_id
        LEFT JOIN categories c ON wp.category_id = c.id
        WHERE u.id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $node = $result->fetch_assoc();
        
        // 5. THE PAYLOAD: Surgical Data Mapping
        echo json_encode([
            'success'       => true,
            'id'            => $node['id'],
            'full_name'     => $node['full_name'],
            'email'         => $node['email'],
            'phone'         => $node['phone'] ?? 'UNREGISTERED',
            'role'          => strtoupper($node['role']),
            'status'        => strtoupper($node['status']),
            'county'        => $node['county'] ?? 'NOT SET',
            'area'          => $node['area'] ?? 'NOT SET',
            'joined_on'     => date('M d, Y', strtotime($node['created_at'])),
            
            // Professional Metadata (Only present if role is 'worker')
            'is_worker'     => ($node['role'] === 'worker'),
            'bio'           => $node['bio'] ?? 'No professional bio provided by node.',
            'hourly_rate'   => number_format((float)($node['hourly_rate'] ?? 0), 0),
            'is_verified'   => (bool)($node['is_verified'] ?? 0),
            'industry'      => $node['industry'] ?? 'GENERAL_CLIENT',
            'photo'         => $node['profile_photo'] ?? 'default.png'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Node ID not found in Registry']);
    }

} catch (Exception $e) {
    // Log error for the architect but return a clean system error to the UI
    error_log("AJAX Intel Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Internal System Protocol Error']);
}