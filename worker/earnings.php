<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: worker/earnings.php
 * PURPOSE: Elite Revenue Registry - Proof of Work & Financial Tracking
 */

session_start();

// 1. Security Guard
if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'worker') {
    header("Location: ../auth/login.php");
    exit();
}

// 2. System Initialization
$base_dir = dirname(__DIR__);
require_once $base_dir . '/includes/config.php';

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];

// 3. Surgical Financial Logic
// Fetching Lifetime Revenue (The Ledger Sum)
$ledger_query = $conn->query("SELECT 
                                IFNULL(SUM(budget), 0) AS total_revenue,
                                COUNT(id) AS job_count,
                                IFNULL(AVG(budget), 0) AS avg_ticket
                             FROM tasks 
                             WHERE worker_id = '$user_id' 
                             AND status = 'completed'");
$ledger = $ledger_query->fetch_assoc();

// Fetching Recent Transactions (The Surgical Registry)
$transactions = $conn->query("SELECT t.*, u.full_name as client_name 
                              FROM tasks t 
                              JOIN users u ON t.client_id = u.id 
                              WHERE t.worker_id = '$user_id' 
                              AND t.status = 'completed' 
                              ORDER BY t.updated_at DESC");

// Active Missions Volume
$active_volume_query = $conn->query("SELECT IFNULL(SUM(budget), 0) as pending_volume 
                                     FROM tasks 
                                     WHERE worker_id = '$user_id' 
                                     AND status IN ('assigned', 'in_progress')");
$active_volume = $active_volume_query->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revenue Registry | SkillLink Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { 'skill-blue': '<?php echo defined("BRAND_COLOR") ? BRAND_COLOR : "#3b82f6"; ?>' }
                }
            }
        }
    </script>
    <style>
        .glass-sidebar { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(20px); }
        .custom-scroll::-webkit-scrollbar { width: 6px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        
        .elite-card { 
            background: white; 
            border: 1px solid rgba(255, 255, 255, 0.6); 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 20px 25px -5px rgba(0, 0, 0, 0.04); 
        }
        
        .active-nav { background: white; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); color: skill-blue !important; border-radius: 1.25rem; }

        .vault-glass {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.98) 100%);
            backdrop-filter: blur(40px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .badge-certified {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }
    </style>
</head>
<body class="bg-[#f8fafc] font-sans antialiased text-slate-900 overflow-hidden">

    <div class="flex h-screen overflow-hidden">
        
        <aside class="w-72 glass-sidebar border-r border-slate-200 flex flex-col z-50">
            <div class="p-8">
                <div class="flex items-center gap-3 mb-10">
                    <div class="w-10 h-10 bg-slate-900 text-white rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-screwdriver-wrench"></i>
                    </div>
                    <span class="text-xl font-black tracking-tighter uppercase">Skill<span class="text-skill-blue">Link</span></span>
                </div>

                <nav class="space-y-2">
                    <a href="dashboard.php" class="flex items-center gap-4 px-6 py-4 text-slate-400 hover:text-skill-blue transition-all rounded-2xl font-bold text-sm">
                        <i class="fas fa-chart-line text-xs"></i> Overview
                    </a>
                    <a href="manage-tasks.php" class="flex items-center gap-4 px-6 py-4 text-slate-400 hover:text-skill-blue transition-all rounded-2xl font-bold text-sm">
                        <i class="fas fa-list-check text-xs"></i> Active Tasks
                    </a>
                    <a href="earnings.php" class="active-nav flex items-center gap-4 px-6 py-4 rounded-2xl font-black text-sm transition-all text-skill-blue">
                        <i class="fas fa-wallet text-xs"></i> Earnings
                    </a>
                    <a href="edit-profile.php" class="flex items-center gap-4 px-6 py-4 text-slate-400 hover:text-skill-blue transition-all rounded-2xl font-bold text-sm">
                        <i class="fas fa-user-gear text-xs"></i> Profile Settings
                    </a>
                </nav>
            </div>
            <div class="mt-auto p-8 border-t border-slate-100">
                <a href="../auth/logout.php" class="flex items-center gap-4 px-6 py-4 text-red-500 hover:bg-red-50 rounded-2xl font-bold text-sm transition-all">
                    <i class="fas fa-power-off text-xs"></i> Sign Out
                </a>
            </div>
        </aside>

        <main class="flex-1 flex flex-col overflow-hidden relative">
            
            <header class="h-24 flex items-center justify-between px-12 bg-white/50 backdrop-blur-md border-b border-slate-100 relative z-20">
                <div>
                    <h1 class="text-xl font-black text-slate-900 leading-none">Revenue Registry</h1>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-2">Professional Proof of Work</p>
                </div>

                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Authenticated Pro</p>
                        <p class="font-black text-slate-900"><?php echo $user_name; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-slate-900 rounded-2xl overflow-hidden border-2 border-white shadow-xl">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=0f172a&color=fff" alt="User">
                    </div>
                </div>
            </header>

            <section class="flex-1 overflow-y-auto p-12 custom-scroll bg-[#f1f5f9]/30">
                <div class="max-w-6xl mx-auto space-y-12 pb-24">
                    
                    <div class="vault-glass rounded-[4rem] p-12 text-white relative overflow-hidden shadow-2xl">
                        <div class="absolute -top-20 -right-20 w-80 h-80 bg-skill-blue/20 rounded-full blur-[100px]"></div>
                        <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-12">
                            <div class="space-y-4 text-center md:text-left">
                                <p class="text-xs font-black uppercase tracking-[0.3em] text-skill-blue">Lifetime Professional Revenue</p>
                                <h2 class="text-6xl font-black tracking-tighter italic">Ksh <?php echo number_format($ledger['total_revenue'], 2); ?></h2>
                                <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mt-4">
                                    <span class="px-4 py-1.5 bg-skill-blue/20 text-skill-blue text-[10px] font-black uppercase rounded-full border border-skill-blue/30">Verified Handshake Records</span>
                                    <span class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">• Nationwide Track Record</span>
                                </div>
                            </div>
                            
                            <button class="px-10 py-6 bg-white text-slate-900 rounded-[2rem] font-black text-[10px] uppercase tracking-[0.2em] shadow-xl hover:bg-skill-blue hover:text-white transition-all active:scale-95 group">
                                Export Work History <i class="fas fa-file-invoice ml-3 group-hover:animate-pulse"></i>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-16 pt-12 border-t border-white/5 relative z-10 text-center md:text-left">
                            <div class="space-y-2">
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest leading-none mb-1">Missions Accomplished</p>
                                <p class="text-2xl font-black leading-none"><?php echo $ledger['job_count']; ?> Projects</p>
                            </div>
                            <div class="space-y-2 border-l border-white/5 md:pl-8">
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest leading-none mb-1">Average Ticket Size</p>
                                <p class="text-2xl font-black leading-none">Ksh <?php echo number_format($ledger['avg_ticket'], 2); ?></p>
                            </div>
                            <div class="space-y-2 border-l border-white/5 md:pl-8">
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest leading-none mb-1">Active Mission Volume</p>
                                <p class="text-2xl font-black text-amber-400 leading-none">Ksh <?php echo number_format($active_volume['pending_volume'], 2); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div class="flex items-center justify-between px-6">
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em]">Verified Mission Ledger</h3>
                            <div class="flex items-center gap-3">
                                <i class="fas fa-certificate text-skill-blue"></i>
                                <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 italic">Certified Proof of Work</span>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <?php if($transactions->num_rows > 0): ?>
                                <?php while($row = $transactions->fetch_assoc()): ?>
                                <div class="elite-card p-10 rounded-[3rem] flex flex-col md:flex-row items-center justify-between group hover:border-skill-blue/40 transition-all gap-8">
                                    <div class="flex items-center gap-8 w-full md:w-auto">
                                        <div class="w-16 h-16 bg-slate-900 text-white rounded-[1.5rem] flex items-center justify-center shadow-xl group-hover:bg-skill-blue transition-colors">
                                            <i class="fas fa-check-double text-xl"></i>
                                        </div>
                                        <div class="space-y-1">
                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest"><?php echo date('F d, Y', strtotime($row['updated_at'])); ?> • REG_<?php echo $row['id']; ?></p>
                                            <h4 class="text-xl font-black text-slate-900 tracking-tight"><?php echo $row['title']; ?></h4>
                                            <div class="flex items-center gap-3">
                                                <div class="w-5 h-5 rounded-full overflow-hidden">
                                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($row['client_name']); ?>&background=0f172a&color=fff" class="w-full h-full">
                                                </div>
                                                <p class="text-[11px] font-bold text-slate-500">Service rendered to <span class="text-slate-900"><?php echo $row['client_name']; ?></span></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-10 w-full md:w-auto justify-between border-t md:border-t-0 pt-6 md:pt-0">
                                        <div class="text-left md:text-right">
                                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 leading-none">Revenue Pulled</p>
                                            <p class="text-2xl font-black text-slate-900 leading-none">Ksh <?php echo number_format($row['budget'], 2); ?></p>
                                        </div>
                                        <div class="badge-certified px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest flex items-center gap-2">
                                            Certified <i class="fas fa-shield-check"></i>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="bg-white rounded-[4rem] p-24 text-center border-4 border-dashed border-slate-100 group transition-all">
                                    <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner">
                                        <i class="fas fa-book-sparkles text-slate-200 text-4xl animate-pulse"></i>
                                    </div>
                                    <h4 class="text-2xl font-black text-slate-900 tracking-tight italic">No Missions Logged</h4>
                                    <p class="text-sm text-slate-400 font-medium max-w-sm mx-auto mt-4 leading-relaxed">Your professional work registry will expand as you complete projects across the network.</p>
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