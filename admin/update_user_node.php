<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: admin/update_user_node.php
 * VERSION: 1.0 "The Executive Handler"
 * FOCUS: Surgical database updates for User Metadata and Operational Status.
 */

// 1. THE STEEL VAULT: Session & Security Guard
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'UNAUTHORIZED_ACCESS_DENIED']);
    exit();
}

// 2. System Initialization
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'config.php';

/**
 * 3. THE COMMAND ANALYST: Input Validation
 */
// Accepting JSON payload or Standard POST
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

if (!isset($input['user_id']) || !isset($input['action'])) {
    echo json_encode(['success' => false, 'message' => 'INVALID_PROTOCOL_PARAMETERS']);
    exit();
}

$user_id = intval($input['user_id']);
$action  = $input['action'];

try {
    /**
     * 4. COMMAND: UPDATE_METADATA
     * Updates: full_name, phone, county, area.
     * Note: Email remains locked for audit integrity.
     */
    if ($action === 'UPDATE_METADATA') {
        $full_name = trim($input['full_name']);
        $phone     = trim($input['phone']);
        $county    = trim($input['county']);
        $area      = trim($input['area']);

        if (empty($full_name)) {
            throw new Exception("IDENTITY_NODE_NAME_REQUIRED");
        }

        $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone = ?, county = ?, area = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $full_name, $phone, $county, $area, $user_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'METADATA_SYNC_COMPLETE']);
        } else {
            throw new Exception("DATABASE_SYNC_FAILURE");
        }
    }

    /**
     * 5. COMMAND: TOGGLE_STATUS
     * Switches status between 'active' and 'suspended'.
     */
    elseif ($action === 'TOGGLE_STATUS') {
        // First, fetch current state to flip it surgically
        $check = $conn->query("SELECT status FROM users WHERE id = $user_id");
        $current_node = $check->fetch_assoc();
        
        if (!$current_node) throw new Exception("NODE_NOT_FOUND");

        $new_status = ($current_node['status'] === 'active') ? 'suspended' : 'active';

        $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $user_id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true, 
                'message' => 'PROTOCOL_STATE_CHANGED', 
                'new_status' => strtoupper($new_status)
            ]);
        } else {
            throw new Exception("STATE_TRANSITION_FAILURE");
        }
    }

    else {
        throw new Exception("UNKNOWN_COMMAND_PROTOCOL");
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}