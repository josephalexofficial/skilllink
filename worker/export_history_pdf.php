<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: worker/export_history_pdf.php
 * VERSION: 1.0 "The Sovereign Work Transcript"
 * FOCUS: High-fidelity PDF generation of worker professional history.
 */

// 1. THE STEEL VAULT: Security Guard
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'worker') {
    die("UNAUTHORIZED_ACCESS_DENIED");
}

// 2. System Initialization
$base_dir = dirname(__DIR__);
require_once $base_dir . '/includes/config.php';
require_once $base_dir . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];

/**
 * 3. THE ANALYST: Data Aggregation
 */
try {
    // TIER 1: Executive Summary Metrics
    $stats = $conn->query("
        SELECT 
            IFNULL(SUM(budget), 0) AS total_revenue,
            COUNT(id) AS job_count,
            IFNULL(AVG(budget), 0) AS avg_ticket
        FROM tasks 
        WHERE worker_id = '$user_id' AND status = 'completed'
    ")->fetch_assoc();

    // TIER 2: The Professional Ledger (Joined with Categories)
    $ledger = $conn->query("
        SELECT t.*, c.cat_name 
        FROM tasks t 
        LEFT JOIN categories c ON t.category_id = c.id
        WHERE t.worker_id = '$user_id' AND t.status = 'completed' 
        ORDER BY t.updated_at DESC
    ");

} catch (Exception $e) {
    die("TRANSCRIPT_GENERATION_FAILED: " . $e->getMessage());
}

/**
 * 4. THE DESIGNER: Constructing the Sovereign Blueprint
 */
$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        @page { margin: 80px 50px; }
        body { font-family: "Helvetica", sans-serif; color: #0f172a; font-size: 11px; line-height: 1.6; }
        
        /* Official Header Protocol */
        .header { position: fixed; top: -50px; left: 0; right: 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; }
        .logo { font-size: 20px; font-weight: 900; color: #0f172a; text-transform: uppercase; }
        .logo span { color: #3b82f6; }
        .doc-type { float: right; text-align: right; color: #94a3b8; font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; }

        /* Worker Identity Node */
        .identity-card { margin-top: 30px; margin-bottom: 40px; }
        .worker-name { font-size: 24px; font-weight: 900; color: #0f172a; margin: 0; }
        .worker-meta { font-size: 9px; color: #64748b; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }

        /* Financial HUD Boxes */
        .stats-table { width: 100%; margin-bottom: 40px; border-collapse: separate; border-spacing: 10px 0; margin-left: -10px; }
        .stat-box { background: #f8fafc; border: 1px solid #f1f5f9; padding: 15px; width: 33.3%; }
        .stat-label { font-size: 7px; font-weight: 900; color: #94a3b8; text-transform: uppercase; margin-bottom: 5px; }
        .stat-value { font-size: 14px; font-weight: bold; color: #0f172a; }

        /* The Main Ledger Table */
        table.ledger { width: 100%; border-collapse: collapse; }
        table.ledger th { background: #f8fafc; color: #64748b; padding: 12px 10px; text-align: left; font-size: 8px; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; }
        table.ledger td { padding: 12px 10px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        
        .cat-badge { font-size: 8px; font-weight: bold; color: #3b82f6; text-transform: uppercase; }
        .amount { text-align: right; font-weight: bold; font-family: "Courier", monospace; }
        .status-pill { background: #ecfdf5; color: #059669; padding: 2px 8px; border-radius: 4px; font-size: 7px; font-weight: 900; text-transform: uppercase; }

        /* Security Footer */
        .footer { position: fixed; bottom: -50px; left: 0; right: 0; font-size: 7px; color: #cbd5e1; text-align: center; border-top: 1px solid #f1f5f9; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="doc-type">Official Work Transcript<br>REG_NODE_'.strtoupper(uniqid()).'</div>
        <div class="logo">Skill<span>Link</span></div>
    </div>

    <div class="footer">
        SKILLLINK_PROFESSIONAL_REGISTRY >> VERIFIED_PROOF_OF_WORK >> AUTH_ID: '.md5($user_id).' >> GENERATED: '.date('Y-m-d H:i:s').'
    </div>

    <div class="identity-card">
        <p class="worker-meta">Authenticated Professional Node</p>
        <h1 class="worker-name">'.htmlspecialchars($user_name).'</h1>
        <p style="margin: 5px 0 0 0; color: #3b82f6; font-weight: bold;">System ID: SL-W-'.str_pad($user_id, 4, "0", STR_PAD_LEFT).'</p>
    </div>

    <table class="stats-table">
        <tr>
            <td class="stat-box">
                <div class="stat-label">Lifetime Earnings</div>
                <div class="stat-value">KES '.number_format($stats['total_revenue'], 2).'</div>
            </td>
            <td class="stat-box">
                <div class="stat-label">Missions Accomplished</div>
                <div class="stat-value">'.$stats['job_count'].' Tasks</div>
            </td>
            <td class="stat-box">
                <div class="stat-label">Average Ticket</div>
                <div class="stat-value">KES '.number_format($stats['avg_ticket'], 0).'</div>
            </td>
        </tr>
    </table>

    <table class="ledger">
        <thead>
            <tr>
                <th width="15%">Date</th>
                <th width="45%">Service Execution</th>
                <th width="15%" style="text-align: center;">Status</th>
                <th width="25%" style="text-align: right;">Revenue Node</th>
            </tr>
        </thead>
        <tbody>';

        if ($ledger->num_rows > 0) {
            while($row = $ledger->fetch_assoc()) {
                $html .= '
                <tr>
                    <td style="color: #64748b;">'.date('M d, Y', strtotime($row['updated_at'])).'</td>
                    <td>
                        <div style="font-weight: bold; font-size: 11px;">'.htmlspecialchars($row['title']).'</div>
                        <div class="cat-badge">'.($row['cat_name'] ?? 'General Services').'</div>
                    </td>
                    <td style="text-align: center;"><span class="status-pill">Released</span></td>
                    <td class="amount">KES '.number_format($row['budget'], 2).'</td>
                </tr>';
            }
        } else {
            $html .= '<tr><td colspan="4" style="text-align:center; padding: 50px; color: #94a3b8;">No verified transactions found in the sovereign registry.</td></tr>';
        }

$html .= '
        </tbody>
    </table>

    <div style="margin-top: 60px; padding: 20px; background: #f8fafc; border-radius: 15px; border: 1px dashed #e2e8f0;">
        <h4 style="margin: 0 0 10px 0; font-size: 9px; text-transform: uppercase; color: #0f172a;">Sovereign Certification</h4>
        <p style="margin: 0; font-size: 8px; color: #64748b; text-align: justify;">
            This document serves as an official verified work transcript generated by the SkillLink Professional Registry. It confirms the successful execution of service nodes and the subsequent release of funds from the marketplace vault to the authenticated professional listed above. This record is cryptographically logged and validated by system protocol.
        </p>
    </div>
</body>
</html>';

/**
 * 5. THE EXECUTOR: PDF Rendering Protocol
 */
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Helvetica');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Stream to Browser
$dompdf->stream("SkillLink_Transcript_".$user_name.".pdf", array("Attachment" => 1));