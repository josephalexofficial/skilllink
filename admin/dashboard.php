<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: admin/dashboard.php
 * VERSION: 4.1 "The Operational Architect"
 * FIXES: Schema alignment (tasks vs bookings), Variable initialization.
 */

session_start();

// 1. THE STEEL VAULT: Session Guard
if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../auth/login.php?error=unauthorized");
    exit();
}

// 2. System Initialization
$base_path = dirname(__DIR__) . DIRECTORY_SEPARATOR;
require_once $base_path . 'includes' . DIRECTORY_SEPARATOR . 'config.php';

$page_title = "Command Center";

// PRE-INITIALIZATION: Prevents "Undefined variable" errors if queries fail
$recent_users = null;
$stats = ['users' => 0, 'workers' => 0, 'pending' => 0, 'gtv' => 0];

/**
 * 3. THE ANALYST: Pulse Data Logic (Aligned with skilllink_db.sql)
 */
try {
    // Basic User Stats - Matches 'users' table
    $stats['users'] = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0] ?? 0;
    
    // Active Pros - Matches 'role' and 'status' columns
    $stats['workers'] = $conn->query("SELECT COUNT(*) FROM users WHERE role='worker' AND status='active'")->fetch_row()[0] ?? 0;
    
    // Pending Trust - Points to 'worker_profiles' verification status
    $stats['pending'] = $conn->query("SELECT COUNT(*) FROM worker_profiles WHERE is_verified = 0")->fetch_row()[0] ?? 0;

    // GTV: Points to 'tasks' table and 'budget' column
    $gtv_query = $conn->query("SELECT SUM(budget) FROM tasks WHERE status IN ('completed', 'finalized')");
    $stats['gtv'] = $gtv_query ? ($gtv_query->fetch_row()[0] ?? 0) : 0;

    // 4. THE JUDGE: Fetch Moderation Queue
    $recent_users = $conn->query("SELECT id, full_name, email, role, status, created_at FROM users ORDER BY created_at DESC LIMIT 8");

} catch (Exception $e) {
    error_log("Dashboard Error: " . $e->getMessage());
    // Stats already initialized to 0 above
}
?>

<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillLink | Architect Command Center</title>
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
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .hud-card:hover {
            transform: translateY(-2px);
            border-color: #3b82f6;
            box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.05);
        }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="h-screen overflow-hidden flex flex-col blueprint-bg">

    <header class="h-16 shrink-0 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-8 z-50">
        <div class="flex items-center gap-4">
            <span class="text-[10px] font-black bg-slate-900 text-white px-2 py-0.5 rounded tracking-widest uppercase">Root Access</span>
            <div class="h-4 w-[1px] bg-slate-200"></div>
            <h1 class="text-sm font-bold text-slate-500 uppercase tracking-widest">Command Center / <span class="text-slate-900">Oversight</span></h1>
        </div>
        <div class="flex items-center gap-6">
            <div class="text-right hidden sm:block">
                <p class="text-[9px] font-black text-slate-400 uppercase leading-none mb-1">Node: Maseno-Main</p>
                <div class="flex items-center justify-end gap-2">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                    <span class="text-[10px] font-bold text-green-600">OPERATIONAL</span>
                </div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center font-black text-slate-600">AJ</div>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">
        <aside class="w-72 bg-slate-900 text-white flex flex-col h-full flex-none shadow-2xl z-40 relative">
            <div class="p-8 border-b border-slate-800/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                        <i class="fas fa-microchip text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black tracking-tighter uppercase leading-none">Skill<span class="text-blue-500">Link</span></h2>
                        <p class="text-[9px] uppercase tracking-[0.3em] text-slate-500 font-bold mt-1">Admin Console</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 p-6 space-y-2 overflow-y-auto custom-scrollbar">
                <a href="dashboard.php" class="flex items-center gap-4 px-5 py-3.5 bg-blue-600/10 text-blue-500 rounded-xl font-bold transition-all border border-blue-600/20">
                    <i class="fas fa-chart-pie text-sm"></i> Dashboard
                </a>
                <a href="verifications.php" class="flex items-center gap-4 px-5 py-3.5 text-slate-400 hover:text-white hover:bg-slate-800/50 rounded-xl font-bold transition-all group">
                    <i class="fas fa-user-check group-hover:text-blue-500 text-sm"></i> Verification
                </a>
                <a href="skills.php" class="flex items-center gap-4 px-5 py-3.5 text-slate-400 hover:text-white hover:bg-slate-800/50 rounded-xl font-bold transition-all group">
                    <i class="fas fa-layer-group group-hover:text-blue-500 text-sm"></i> Skill Tree
                </a>
                <a href="reports.php" class="flex items-center gap-4 px-5 py-3.5 text-slate-400 hover:text-white hover:bg-slate-800/50 rounded-xl font-bold transition-all group">
                    <i class="fas fa-flag group-hover:text-blue-500 text-sm"></i> Reports
                </a>
            </nav>

            <div class="p-6 border-t border-slate-800/50 bg-slate-900/50">
                <p class="text-[8px] text-slate-600 uppercase tracking-widest mb-4">SkillLink OS v4.1</p>
                <a href="../auth/logout.php" class="flex items-center justify-center gap-3 w-full py-3.5 bg-red-500/5 text-red-500 rounded-xl font-bold hover:bg-red-500 hover:text-white transition-all duration-300 border border-red-500/20">
                    <i class="fas fa-power-off text-sm"></i> Logout
                </a>
            </div>
        </aside>

        <section class="flex-1 h-full overflow-y-auto custom-scrollbar scroll-smooth">
            <div class="p-8 space-y-8 pb-12"> 
                
                <div class="hud-card p-8 rounded-[2.5rem] border-blue-100 flex justify-between items-center">
                    <div>
                        <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.2em]">Platform Architect</span>
                        <h1 class="text-3xl font-black tracking-tight mt-1 text-slate-900 uppercase">Gross Transactional Value</h1>
                    </div>
                    <div class="text-right">
                        <p class="text-4xl font-black text-slate-900 font-mono tracking-tighter">KES <?= number_format($stats['gtv'], 0); ?></p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Total Marketplace Liquidity</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="hud-card p-6 rounded-3xl relative overflow-hidden group">
                        <p class="text-[10px] uppercase tracking-widest text-slate-400 font-black mb-3">Total Users</p>
                        <h3 class="text-3xl font-black text-slate-900"><?= number_format($stats['users']); ?></h3>
                        <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.07] transition-opacity text-slate-900"><i class="fas fa-users text-7xl"></i></div>
                    </div>

                    <div class="hud-card p-6 rounded-3xl relative overflow-hidden group">
                        <p class="text-[10px] uppercase tracking-widest text-slate-400 font-black mb-3">Active Pros</p>
                        <h3 class="text-3xl font-black text-blue-600"><?= number_format($stats['workers']); ?></h3>
                        <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.07] transition-opacity text-blue-600"><i class="fas fa-user-gear text-7xl"></i></div>
                    </div>

                    <div class="hud-card p-6 rounded-3xl relative overflow-hidden group">
                        <p class="text-[10px] uppercase tracking-widest text-slate-400 font-black mb-3">Pending Trust</p>
                        <h3 class="text-3xl font-black text-amber-500"><?= number_format($stats['pending']); ?></h3>
                        <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.07] transition-opacity text-amber-500"><i class="fas fa-shield-halved text-7xl"></i></div>
                    </div>

                    <div class="bg-slate-900 p-6 rounded-3xl shadow-xl shadow-slate-900/10 text-white flex flex-col justify-between overflow-hidden relative">
                        <p class="text-[10px] uppercase tracking-widest text-slate-500 font-black">System Load</p>
                        <div class="flex justify-center gap-1.5 h-6 items-end mb-1">
                            <div class="w-1 bg-blue-500 rounded-full animate-[bounce_1.2s_infinite_100ms] h-[40%]"></div>
                            <div class="w-1 bg-blue-500 rounded-full animate-[bounce_1.2s_infinite_200ms] h-[70%]"></div>
                            <div class="w-1 bg-blue-500 rounded-full animate-[bounce_1.2s_infinite_300ms] h-[50%]"></div>
                            <div class="w-1 bg-blue-400 rounded-full animate-[bounce_1.2s_infinite_400ms] h-[90%]"></div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200/60 overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                        <div>
                            <h2 class="text-lg font-black text-slate-900">Audit Ledger</h2>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Real-time Verification Need</p>
                        </div>
                        <a href="users_registry.php" class="text-[10px] font-black bg-blue-600 text-white px-5 py-2 rounded-xl uppercase tracking-widest shadow-lg shadow-blue-500/20 hover:bg-blue-700 transition-colors">
                            View All Users
                        </a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-[10px] uppercase tracking-widest text-slate-400 font-black bg-slate-50/30">
                                    <th class="px-10 py-5">Identity Node</th>
                                    <th class="px-10 py-5 text-center">Protocol Role</th>
                                    <th class="px-10 py-5 text-center">Status</th>
                                    <th class="px-10 py-5 text-right">Moderation</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php if($recent_users && $recent_users->num_rows > 0): ?>
                                    <?php while($row = $recent_users->fetch_assoc()): ?>
                                    <tr class="hover:bg-blue-50/30 transition-all group">
                                        <td class="px-10 py-5">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center font-black text-slate-500 group-hover:from-blue-600 group-hover:to-blue-500 group-hover:text-white transition-all duration-500 shadow-sm border border-slate-200">
                                                    <?= strtoupper(substr($row['full_name'], 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-extrabold text-slate-900 leading-none mb-1"><?= htmlspecialchars($row['full_name']); ?></p>
                                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter"><?= htmlspecialchars($row['email']); ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-10 py-5 text-center">
                                            <span class="text-[10px] font-black uppercase text-slate-500 border border-slate-200 px-2 py-0.5 rounded">
                                                <?= $row['role']; ?>
                                            </span>
                                        </td>
                                        <td class="px-10 py-5 text-center">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest 
                                                <?= $row['status'] === 'active' ? 'bg-green-100 text-green-600' : 'bg-amber-100 text-amber-600'; ?>">
                                                <span class="w-1 h-1 rounded-full bg-current animate-pulse"></span>
                                                <?= $row['status']; ?>
                                            </span>
                                        </td>
                                        <td class="px-10 py-5 text-right">
                                            <div class="flex justify-end gap-2">
                                                <button title="Examine Node" class="w-8 h-8 rounded-lg bg-slate-50 text-slate-400 hover:bg-slate-900 hover:text-white transition-all"><i class="fas fa-eye text-[10px]"></i></button>
                                                <button title="Verify Protocol" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all"><i class="fas fa-check text-[10px]"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="p-10 text-center text-slate-400 text-sm font-medium">No recent registrations found. System standing by...</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <footer class="h-8 shrink-0 bg-slate-950 border-t border-slate-800 flex items-center px-8 overflow-hidden">
                <div class="flex items-center gap-4">
                    <span class="w-2 h-2 bg-blue-500 rounded-full animate-ping"></span>
                    <p class="text-[9px] font-bold text-slate-500 uppercase tracking-[0.4em] whitespace-nowrap">
                        <span class="text-blue-500">SYSTEM LOG:</span> BROADCASTING FROM NODE [MASENO-MAIN] ... DATABASE SYNCED ... MONITORING LIVE REGISTRY ENTRIES
                    </p>
                </div>
            </footer>
        </section>
    </div>
</body>
</html>