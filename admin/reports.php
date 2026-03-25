<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: admin/reports.php
 * VERSION: 2.0 "The Strategic Architect"
 * FOCUS: Marketplace health monitoring and surgical PDF audit exports.
 */

session_start();

// 1. THE STEEL VAULT
if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../auth/login.php?error=unauthorized");
    exit();
}

// 2. System Initialization
$base_path = dirname(__DIR__) . DIRECTORY_SEPARATOR;
require_once $base_path . 'includes' . DIRECTORY_SEPARATOR . 'config.php';

$page_title = "Strategic Intelligence";
$stats = ['total_revenue' => 0, 'success_rate' => 0, 'avg_rating' => 0, 'active_tasks' => 0];

/**
 * 3. THE ANALYST: Complex Data Aggregation
 */
try {
    // TIER 1: Marketplace Health HUD
    $revenue_query = $conn->query("SELECT SUM(budget) FROM tasks WHERE status IN ('completed', 'finalized')");
    $stats['total_revenue'] = (float)($revenue_query->fetch_row()[0] ?? 0);

    $total_tasks = (int)($conn->query("SELECT COUNT(*) FROM tasks")->fetch_row()[0] ?? 0);
    $finalized_tasks = (int)($conn->query("SELECT COUNT(*) FROM tasks WHERE status = 'finalized'")->fetch_row()[0] ?? 0);
    $stats['success_rate'] = $total_tasks > 0 ? round(($finalized_tasks / $total_tasks) * 100, 1) : 0;

    $stats['avg_rating'] = (float)($conn->query("SELECT AVG(rating) FROM reviews")->fetch_row()[0] ?? 0);
    $stats['active_tasks'] = (int)($conn->query("SELECT COUNT(*) FROM tasks WHERE status IN ('open', 'assigned', 'in_progress')")->fetch_row()[0] ?? 0);

    // TIER 2: Category Intelligence (Ranked by Economic Volume)
    $category_report = $conn->query("
        SELECT 
            c.cat_name, c.cat_icon,
            COUNT(t.id) as task_density,
            COALESCE(SUM(t.budget), 0) as total_value,
            COALESCE(AVG(r.rating), 0) as avg_cat_rating
        FROM categories c
        LEFT JOIN tasks t ON c.id = t.category_id
        LEFT JOIN reviews r ON t.id = r.task_id
        GROUP BY c.id
        ORDER BY total_value DESC
    ");

    // TIER 3: Friction Log
    $friction_log = $conn->query("
        SELECT r.rating, r.comment, u.full_name as client_name, w.full_name as worker_name, t.title
        FROM reviews r
        JOIN tasks t ON r.task_id = t.id
        JOIN users u ON r.client_id = u.id
        JOIN users w ON r.worker_id = w.id
        WHERE r.rating <= 3
        ORDER BY r.created_at DESC LIMIT 5
    ");

} catch (Exception $e) {
    error_log("Strategic Report Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillLink | Strategic Intelligence</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .blueprint-bg {
            background-image: radial-gradient(#e2e8f0 1.1px, transparent 1.1px);
            background-size: 30px 30px;
        }
        .hud-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.3s ease;
        }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="h-screen overflow-hidden flex flex-col blueprint-bg">

    <header class="h-16 shrink-0 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-8 z-50">
        <div class="flex items-center gap-4">
            <span class="text-[10px] font-black bg-slate-900 text-white px-2 py-0.5 rounded tracking-widest uppercase shadow-lg shadow-blue-500/20">Strategic Node</span>
            <div class="h-4 w-[1px] bg-slate-200"></div>
            <h1 class="text-sm font-bold text-slate-500 uppercase tracking-widest">Command Center / <span class="text-slate-900">Intelligence Suite</span></h1>
        </div>
        <a href="export_ledger_pdf.php" target="_blank" class="bg-blue-600 text-white text-[10px] font-black px-6 py-2.5 rounded-xl uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">
            <i class="fas fa-file-export mr-2"></i> Export Ledger Report
        </a>
    </header>

    <div class="flex flex-1 overflow-hidden">
        <aside class="w-72 bg-slate-900 text-white flex flex-col h-full flex-none shadow-2xl z-40 relative">
            <div class="p-8 border-b border-slate-800/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-chart-line text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black tracking-tighter uppercase leading-none">Skill<span class="text-blue-500">Link</span></h2>
                        <p class="text-[9px] uppercase tracking-[0.3em] text-slate-500 font-bold mt-1">Intelligence</p>
                    </div>
                </div>
            </div>
            <nav class="flex-1 p-6 space-y-2 overflow-y-auto custom-scrollbar">
                <a href="dashboard.php" class="flex items-center gap-4 px-5 py-3.5 text-slate-400 hover:text-white hover:bg-slate-800/50 rounded-xl font-bold transition-all group">
                    <i class="fas fa-grid-2 text-sm"></i> Dashboard
                </a>
                <a href="verifications.php" class="flex items-center gap-4 px-5 py-3.5 text-slate-400 hover:text-white hover:bg-slate-800/50 rounded-xl font-bold transition-all group">
                    <i class="fas fa-user-check text-sm"></i> Verification
                </a>
                <a href="skills.php" class="flex items-center gap-4 px-5 py-3.5 text-slate-400 hover:text-white hover:bg-slate-800/50 rounded-xl font-bold transition-all group">
                    <i class="fas fa-layer-group text-sm"></i> Skill Tree
                </a>
                <a href="reports.php" class="flex items-center gap-4 px-5 py-3.5 bg-blue-600/10 text-blue-500 rounded-xl font-bold transition-all border border-blue-600/20">
                    <i class="fas fa-flag text-sm"></i> Reports
                </a>
            </nav>
            <div class="p-6 border-t border-slate-800/50 bg-slate-900/50">
                <a href="../auth/logout.php" class="flex items-center justify-center gap-3 w-full py-3.5 bg-red-500/5 text-red-500 rounded-xl font-bold hover:bg-red-500 hover:text-white transition-all border border-red-500/20">
                    <i class="fas fa-power-off text-sm"></i> Logout
                </a>
            </div>
        </aside>

        <main class="flex-1 overflow-y-auto custom-scrollbar p-10 space-y-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="hud-card p-6 rounded-[2rem]">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Throughput</p>
                    <p class="text-2xl font-black text-slate-900 font-mono">KES <?= number_format($stats['total_revenue'], 0) ?></p>
                </div>
                <div class="hud-card p-6 rounded-[2rem]">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Success Rate</p>
                    <p class="text-2xl font-black text-blue-600 font-mono"><?= $stats['success_rate'] ?>%</p>
                </div>
                <div class="hud-card p-6 rounded-[2rem]">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Trust Index</p>
                    <p class="text-2xl font-black text-amber-500 font-mono"><?= number_format($stats['avg_rating'], 1) ?> <span class="text-xs text-slate-300">/ 5.0</span></p>
                </div>
                <div class="hud-card p-6 rounded-[2rem]">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Active Liquidity</p>
                    <p class="text-2xl font-black text-slate-700 font-mono"><?= $stats['active_tasks'] ?> <span class="text-xs text-slate-300">TASKS</span></p>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200/60 overflow-hidden">
                <div class="px-10 py-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/30">
                    <div>
                        <h2 class="text-lg font-black text-slate-900 tracking-tight uppercase">Skill Performance Ledger</h2>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-0.5">Economic Volume per Industry</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[9px] uppercase tracking-[0.25em] text-slate-400 font-black bg-slate-50/50">
                                <th class="px-10 py-5">Industry Node</th>
                                <th class="px-10 py-5 text-center">Task Density</th>
                                <th class="px-10 py-5 text-center">Economic Value</th>
                                <th class="px-10 py-5 text-right">Node Rating</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php if ($category_report): ?>
                                <?php while($row = $category_report->fetch_assoc()): 
                                    // ICON FALLBACK PROTOCOL
                                    $icon = !empty($row['cat_icon']) ? $row['cat_icon'] : (str_contains(strtolower($row['cat_name']), 'clean') ? 'fa-broom-ball' : 'fa-microchip');
                                ?>
                                <tr class="hover:bg-blue-50/30 transition-all">
                                    <td class="px-10 py-5 flex items-center gap-4">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-blue-600 shadow-sm border border-slate-200">
                                            <i class="fas <?= htmlspecialchars($icon) ?> text-[10px]"></i>
                                        </div>
                                        <span class="text-sm font-extrabold text-slate-900"><?= htmlspecialchars($row['cat_name']) ?></span>
                                    </td>
                                    <td class="px-10 py-5 text-center text-[11px] font-bold text-slate-500 font-mono"><?= (int)$row['task_density'] ?></td>
                                    <td class="px-10 py-5 text-center text-[11px] font-bold text-slate-900 font-mono">KES <?= number_format((float)$row['total_value'], 0) ?></td>
                                    <td class="px-10 py-5 text-right">
                                        <span class="text-[10px] font-black text-amber-500"><?= number_format((float)$row['avg_cat_rating'], 1) ?> ★</span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="hud-card p-10 rounded-[3rem] border-red-100 bg-red-50/10 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-10 opacity-[0.05]"><i class="fas fa-triangle-exclamation text-8xl text-red-600"></i></div>
                <h3 class="text-sm font-black text-red-600 uppercase tracking-widest mb-8 border-b border-red-100 pb-4">Critical Audit Log (Low Ratings)</h3>
                <div class="space-y-6">
                    <?php if($friction_log && $friction_log->num_rows > 0): ?>
                        <?php while($f = $friction_log->fetch_assoc()): ?>
                        <div class="flex gap-6 items-start">
                            <div class="w-1.5 h-12 bg-red-500 rounded-full shrink-0 mt-1"></div>
                            <div>
                                <p class="text-[10px] font-black text-red-600 uppercase mb-1">CRITICAL Review: <?= (int)$f['rating'] ?> ★</p>
                                <p class="text-sm font-bold text-slate-900 leading-none mb-1"><?= htmlspecialchars($f['title']) ?></p>
                                <p class="text-xs text-slate-500 font-medium italic">"<?= htmlspecialchars($f['comment']) ?>"</p>
                                <p class="text-[9px] font-bold text-slate-400 mt-2 uppercase tracking-tighter">Client: <?= htmlspecialchars($f['client_name']) ?> | Worker: <?= htmlspecialchars($f['worker_name']) ?></p>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest italic text-center py-10">No Friction Nodes Identified. Quality Control Nominal.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <footer class="h-10 shrink-0 bg-slate-950 border-t border-slate-900 flex items-center px-8 z-50">
        <div class="flex items-center gap-4">
            <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.4em] whitespace-nowrap">
                REPORT_SYNC >> ANALYZING TASKS AND REVIEWS >> ALL NODES NOMINAL >> ECOSYSTEM UPTIME: 99.9%
            </p>
        </div>
    </footer>
</body>
</html>