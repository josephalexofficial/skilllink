<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: worker/earnings.php
 * VERSION: 2.0 "The Sovereign Ledger"
 * PURPOSE: Elite Revenue Registry - Proof of Work & Financial Tracking
 */

session_start();

// 1. THE STEEL VAULT: Security Guard
if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'worker') {
    header("Location: ../auth/login.php");
    exit();
}

// 2. System Initialization
$base_dir = dirname(__DIR__);
require_once $base_dir . '/includes/config.php';

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];

/**
 * 3. THE ANALYST: Surgical Financial Logic
 */
try {
    // TIER 1: Lifetime Revenue & Core Metrics
    $ledger_query = $conn->query("
        SELECT 
            IFNULL(SUM(budget), 0) AS total_revenue,
            COUNT(id) AS job_count,
            IFNULL(AVG(budget), 0) AS avg_ticket
        FROM tasks 
        WHERE worker_id = '$user_id' 
        AND status = 'completed'
    ");
    $ledger = $ledger_query->fetch_assoc();

    // TIER 2: Monthly Momentum (Current Month Earnings)
    $current_month_query = $conn->query("
        SELECT IFNULL(SUM(budget), 0) as monthly_vol 
        FROM tasks 
        WHERE worker_id = '$user_id' 
        AND status = 'completed' 
        AND MONTH(updated_at) = MONTH(CURRENT_DATE())
        AND YEAR(updated_at) = YEAR(CURRENT_DATE())
    ");
    $monthly_momentum = $current_month_query->fetch_assoc()['monthly_vol'];

    // TIER 3: The Surgical Registry (JOINing Categories for Node Insight)
    $transactions = $conn->query("
        SELECT t.*, u.full_name as client_name, c.cat_name, c.cat_icon 
        FROM tasks t 
        JOIN users u ON t.client_id = u.id 
        LEFT JOIN categories c ON t.category_id = c.id
        WHERE t.worker_id = '$user_id' 
        AND t.status = 'completed' 
        ORDER BY t.updated_at DESC
    ");

    // TIER 4: Active Missions Volume (Pending Liquidity)
    $active_volume_query = $conn->query("
        SELECT IFNULL(SUM(budget), 0) as pending_volume 
        FROM tasks 
        WHERE worker_id = '$user_id' 
        AND status IN ('assigned', 'in_progress')
    ");
    $active_volume = $active_volume_query->fetch_assoc()['pending_volume'];

} catch (Exception $e) {
    error_log("Financial Registry Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revenue Registry | SkillLink Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace']
                    },
                    colors: { 'skill-blue': '#3b82f6' }
                }
            }
        }
    </script>
    <style>
        .blueprint-bg { background-image: radial-gradient(#e2e8f0 1.1px, transparent 1.1px); background-size: 30px 30px; }
        .glass-sidebar { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(20px); }
        .custom-scroll::-webkit-scrollbar { width: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        
        .elite-card { background: white; border: 1px solid rgba(226, 232, 240, 0.8); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .elite-card:hover { transform: translateY(-2px); border-color: #3b82f6; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05); }
        
        .vault-glass { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border: 1px solid rgba(255, 255, 255, 0.1); }
        .active-nav { background: #f1f5f9; color: #3b82f6 !important; border-radius: 1rem; }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-900 overflow-hidden blueprint-bg">

    <div class="flex h-screen overflow-hidden">
        
        <aside class="w-72 glass-sidebar border-r border-slate-200 flex flex-col z-50">
            <div class="p-8">
                <div class="flex items-center gap-3 mb-10">
                    <div class="w-10 h-10 bg-slate-900 text-white rounded-xl flex items-center justify-center shadow-lg shadow-slate-900/20">
                        <i class="fas fa-screwdriver-wrench"></i>
                    </div>
                    <span class="text-xl font-black tracking-tighter uppercase">Skill<span class="text-skill-blue">Link</span></span>
                </div>

                <nav class="space-y-1">
                    <a href="dashboard.php" class="flex items-center gap-4 px-5 py-3.5 text-slate-400 hover:text-skill-blue transition-all font-bold text-sm">
                        <i class="fas fa-grid-2 text-xs"></i> Overview
                    </a>
                    <a href="manage-tasks.php" class="flex items-center gap-4 px-5 py-3.5 text-slate-400 hover:text-skill-blue transition-all font-bold text-sm">
                        <i class="fas fa-list-check text-xs"></i> Active Tasks
                    </a>
                    <a href="earnings.php" class="active-nav flex items-center gap-4 px-5 py-3.5 font-black text-sm text-skill-blue">
                        <i class="fas fa-wallet text-xs"></i> Earnings
                    </a>
                    <a href="edit-profile.php" class="flex items-center gap-4 px-5 py-3.5 text-slate-400 hover:text-skill-blue transition-all font-bold text-sm">
                        <i class="fas fa-user-gear text-xs"></i> Profile
                    </a>
                </nav>
            </div>
            <div class="mt-auto p-8 border-t border-slate-100">
                <a href="../auth/logout.php" class="flex items-center gap-4 px-5 py-3.5 text-red-500 hover:bg-red-50 rounded-xl font-bold text-sm transition-all">
                    <i class="fas fa-power-off text-xs"></i> Sign Out
                </a>
            </div>
        </aside>

        <main class="flex-1 flex flex-col overflow-hidden relative">
            
            <header class="h-20 flex items-center justify-between px-12 bg-white/60 backdrop-blur-md border-b border-slate-200 z-20">
                <div>
                    <h1 class="text-lg font-black text-slate-900 tracking-tight uppercase">Revenue Registry</h1>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Authenticated Financial Node: <?= $user_id ?></p>
                </div>

                <div class="flex items-center gap-5">
                    <div class="text-right hidden md:block">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Worker Access</p>
                        <p class="font-black text-slate-900 text-sm"><?= $user_name; ?></p>
                    </div>
                    <div class="w-10 h-10 bg-slate-900 rounded-xl overflow-hidden border-2 border-white shadow-lg">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($user_name); ?>&background=0f172a&color=fff" alt="User">
                    </div>
                </div>
            </header>

            <section class="flex-1 overflow-y-auto p-12 custom-scroll">
                <div class="max-w-6xl mx-auto space-y-10 pb-20">
                    
                    <div class="vault-glass rounded-[3rem] p-12 text-white relative overflow-hidden shadow-2xl">
                        <div class="absolute -top-20 -right-20 w-80 h-80 bg-skill-blue/20 rounded-full blur-[100px]"></div>
                        
                        <div class="relative z-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-10">
                            <div class="space-y-3">
                                <p class="text-[10px] font-black uppercase tracking-[0.4em] text-skill-blue">Lifetime Professional Revenue</p>
                                <h2 class="text-6xl font-black tracking-tighter font-mono">KES <?= number_format($ledger['total_revenue'], 2); ?></h2>
                                <div class="flex items-center gap-3 mt-4">
                                    <span class="px-3 py-1 bg-white/10 text-white text-[9px] font-black uppercase rounded-lg border border-white/10">Certified Ledger</span>
                                    <span class="text-slate-400 text-[9px] font-bold uppercase tracking-widest italic">• This month: KES <?= number_format($monthly_momentum, 0) ?></span>
                                </div>
                            </div>
                            
                            <a href="export_history_pdf.php" target="_blank" class="px-8 py-5 bg-white text-slate-900 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-xl hover:bg-blue-600 hover:text-white transition-all active:scale-95 group">
                                <i class="fas fa-file-invoice mr-3"></i> Export Work History
                            </a>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mt-16 pt-10 border-t border-white/5 relative z-10">
                            <div>
                                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2">Missions Accomplished</p>
                                <p class="text-2xl font-black font-mono"><?= $ledger['job_count']; ?> <span class="text-xs text-slate-600">NODES</span></p>
                            </div>
                            <div class="border-l border-white/5 md:pl-10">
                                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2">Avg Ticket Size</p>
                                <p class="text-2xl font-black font-mono text-skill-blue">KES <?= number_format($ledger['avg_ticket'], 0); ?></p>
                            </div>
                            <div class="border-l border-white/5 md:pl-10">
                                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2">Pending Liquidity</p>
                                <p class="text-2xl font-black font-mono text-amber-400">KES <?= number_format($active_volume, 0); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="flex items-center justify-between px-4">
                            <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-[0.3em]">Verified Service Transcript</h3>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest italic">Sorted by Recency</span>
                        </div>

                        <div class="space-y-4">
                            <?php if($transactions->num_rows > 0): ?>
                                <?php while($row = $transactions->fetch_assoc()): 
                                    $cat_icon = $row['cat_icon'] ?? 'fa-check-double';
                                ?>
                                <div class="elite-card p-8 rounded-[2.5rem] flex flex-col md:flex-row items-center justify-between group gap-6">
                                    <div class="flex items-center gap-6 w-full md:w-auto">
                                        <div class="w-14 h-14 bg-slate-950 text-white rounded-2xl flex items-center justify-center shadow-lg group-hover:bg-skill-blue transition-all duration-500">
                                            <i class="fas <?= $cat_icon ?> text-lg"></i>
                                        </div>
                                        <div>
                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">
                                                <?= date('M d, Y', strtotime($row['updated_at'])); ?> • NODE_<?= $row['id']; ?>
                                            </p>
                                            <h4 class="text-lg font-black text-slate-900 tracking-tight leading-none mb-2"><?= htmlspecialchars($row['title']); ?></h4>
                                            <div class="flex items-center gap-2">
                                                <span class="text-[10px] font-bold text-slate-500 italic">Client: <?= htmlspecialchars($row['client_name']); ?></span>
                                                <span class="text-slate-300">•</span>
                                                <span class="text-[10px] font-black text-blue-500 uppercase"><?= $row['cat_name'] ?? 'General Service' ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-8 w-full md:w-auto justify-between md:justify-end">
                                        <div class="text-right">
                                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1 leading-none">Net Revenue</p>
                                            <p class="text-xl font-black text-slate-900 font-mono leading-none">KES <?= number_format($row['budget'], 2); ?></p>
                                        </div>
                                        <div class="bg-green-50 text-green-600 px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest border border-green-100 flex items-center gap-2">
                                            Released <i class="fas fa-circle-check text-[7px]"></i>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="bg-white rounded-[3rem] p-20 text-center border-2 border-dashed border-slate-200">
                                    <i class="fas fa-wallet text-slate-200 text-5xl mb-6"></i>
                                    <h4 class="text-xl font-black text-slate-900 uppercase">No Revenue Logged</h4>
                                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-2">Activate task nodes to begin generating throughput.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

</body>
</html>