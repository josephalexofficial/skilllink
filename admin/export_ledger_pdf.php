<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: admin/export_ledger_pdf.php
 * VERSION: 1.0 "The Sovereign Audit Ledger"
 * FOCUS: Generating high-fidelity, print-ready PDF financial reports.
 */

// 1. THE STEEL VAULT: Session Guard
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'admin') {
    die("UNAUTHORIZED_ACCESS_DENIED");
}

// 2. System Initialization & Dompdf Load
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'config.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * 3. THE ANALYST: Core Data Aggregation
 */
try {
    // Marketplace Health HUD Stats
    $rev = $conn->query("SELECT SUM(budget) FROM tasks WHERE status IN ('completed', 'finalized')")->fetch_row()[0] ?? 0;
    $total_t = $conn->query("SELECT COUNT(*) FROM tasks")->fetch_row()[0] ?? 0;
    $final_t = $conn->query("SELECT COUNT(*) FROM tasks WHERE status = 'finalized'")->fetch_row()[0] ?? 0;
    $rating  = $conn->query("SELECT AVG(rating) FROM reviews")->fetch_row()[0] ?? 0;
    $success = $total_t > 0 ? round(($final_t / $total_t) * 100, 1) : 0;

    // Category Performance Ledger
    $ledger_query = "
        SELECT 
            c.cat_name, 
            COUNT(t.id) as task_count,
            COALESCE(SUM(t.budget), 0) as total_value,
            COALESCE(AVG(r.rating), 0) as avg_rating
        FROM categories c
        LEFT JOIN tasks t ON c.id = t.category_id
        LEFT JOIN reviews r ON t.id = r.task_id
        GROUP BY c.id
        ORDER BY total_value DESC";
    $ledger = $conn->query($ledger_query);

} catch (Exception $e) {
    die("AUDIT_FAILED: " . $e->getMessage());
}

/**
 * 4. THE DESIGNER: Constructing the HTML Blueprint
 */
$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        @page { margin: 100px 50px; }
        body { font-family: "Helvetica", sans-serif; color: #1e293b; font-size: 12px; line-height: 1.5; }
        
        /* Header Protocol */
        .header { position: fixed; top: -60px; left: 0; right: 0; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; }
        .logo { font-size: 24px; font-weight: bold; color: #0f172a; text-transform: uppercase; letter-spacing: -1px; }
        .logo span { color: #3b82f6; }
        .report-title { float: right; text-align: right; color: #64748b; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; }

        /* HUD Stats */
        .summary-hud { margin-top: 20px; width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        .hud-box { background: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; text-align: center; width: 25%; }
        .hud-label { font-size: 8px; font-weight: bold; color: #94a3b8; text-transform: uppercase; margin-bottom: 5px; }
        .hud-value { font-size: 16px; font-weight: bold; color: #0f172a; }

        /* Main Ledger Table */
        table.main-ledger { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table.main-ledger th { background: #0f172a; color: white; padding: 12px 10px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 1px; }
        table.main-ledger td { padding: 12px 10px; border-bottom: 1px solid #f1f5f9; }
        table.main-ledger tr:nth-child(even) { background-color: #fcfcfc; }
        
        .currency { text-align: right; font-weight: bold; }
        .text-blue { color: #3b82f6; }
        
        /* Footer Protocol */
        .footer { position: fixed; bottom: -60px; left: 0; right: 0; font-size: 8px; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="report-title">Official Strategic Ledger<br>Ref: SKL-'.date('Ymd-Hi').'</div>
        <div class="logo">Skill<span>Link</span></div>
    </div>

    <div class="footer">
        SKILLLINK_INFRASTRUCTURE_NODE >> SYSTEM_AUDIT_LOG >> GENERATED_ON: '.date('Y-m-d H:i:s').' >> PAGE_01
    </div>

    <div style="margin-bottom: 20px;">
        <h2 style="font-size: 18px; font-weight: bold; margin-bottom: 5px;">Marketplace Performance Audit</h2>
        <p style="color: #64748b; margin: 0;">Comprehensive economic analysis and node density report.</p>
    </div>

    <table class="summary-hud">
        <tr>
            <td class="hud-box">
                <div class="hud-label">Total Throughput</div>
                <div class="hud-value">KES '.number_format($rev, 0).'</div>
            </td>
            <td class="hud-box">
                <div class="hud-label">Success Rate</div>
                <div class="hud-value" style="color: #3b82f6;">'.$success.'%</div>
            </td>
            <td class="hud-box">
                <div class="hud-label">Trust Index</div>
                <div class="hud-value" style="color: #f59e0b;">'.number_format($rating, 1).' ★</div>
            </td>
            <td class="hud-box">
                <div class="hud-label">Active Tasks</div>
                <div class="hud-value">'.$total_t.'</div>
            </td>
        </tr>
    </table>

    <table class="main-ledger">
        <thead>
            <tr>
                <th width="40%">Industry Node</th>
                <th width="20%" style="text-align: center;">Task Density</th>
                <th width="20%" style="text-align: center;">Avg Rating</th>
                <th width="20%" style="text-align: right;">Economic Value</th>
            </tr>
        </thead>
        <tbody>';

        while($row = $ledger->fetch_assoc()) {
            $html .= '
            <tr>
                <td><strong>' . htmlspecialchars($row['cat_name']) . '</strong><br><span style="font-size: 8px; color: #94a3b8;">PROTOCOL_ACTIVE</span></td>
                <td style="text-align: center;">' . (int)$row['task_count'] . '</td>
                <td style="text-align: center; color: #f59e0b;">' . number_format($row['avg_rating'], 1) . ' ★</td>
                <td class="currency">KES ' . number_format($row['total_value'], 0) . '</td>
            </tr>';
        }

$html .= '
        </tbody>
    </table>

    <div style="margin-top: 50px; padding: 20px; border: 1px dashed #cbd5e1; border-radius: 10px; background: #f8fafc;">
        <p style="font-size: 9px; margin: 0; font-weight: bold; color: #64748b; text-transform: uppercase;">Security Certification</p>
        <p style="font-size: 8px; color: #94a3b8; margin-top: 5px;">This document is a system-generated Personnel and Financial Audit for the SkillLink Platform. It serves as an official record of node performance and economic throughput. Any tampering with this digital ledger is a breach of security protocol.</p>
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

// Set Paper Size to A4 (Standard Audit Grade)
$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

// Direct Stream to Browser for Download
$dompdf->stream("SkillLink_Audit_Ledger_".date('Y-m-d').".pdf", array("Attachment" => 1));