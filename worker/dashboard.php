<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: worker/dashboard.php
 * PURPOSE: Elite Worker Command Center - Live Opportunity Feed, Success Pulse & Dynamic Ledger
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

// 3. Logic Gate: Profile Verification
$user_id = $_SESSION['user_id'];
$profile_query = $conn->query("SELECT wp.*, c.cat_name 
                               FROM worker_profiles wp 
                               JOIN categories c ON wp.category_id = c.id 
                               WHERE wp.user_id = '$user_id'");

if ($profile_query->num_rows == 0) {
    header("Location: setup-wizard.php");
    exit();
}
$worker_data = $profile_query->fetch_assoc();
$worker_category_id = $worker_data['category_id'];

// 4. Data Retrieval: Live Opportunity Engine
$opportunities_query = $conn->query("SELECT t.*, c.cat_name 
                                     FROM tasks t 
                                     JOIN categories c ON t.category_id = c.id 
                                     WHERE t.category_id = '$worker_category_id' 
                                     AND t.status = 'open' 
                                     ORDER BY t.created_at DESC 
                                     LIMIT 5");

// 5. NEW: The Financial Pulse (Dynamic Ledger)
// Surgical fetch of completed missions and total withdrawable earnings
$stats_query = $conn->query("SELECT 
                                COUNT(id) AS total_tasks_finished, 
                                IFNULL(SUM(budget), 0) AS total_earnings 
                             FROM tasks 
                             WHERE worker_id = '$user_id' 
                             AND status = 'completed'");
$stats = $stats_query->fetch_assoc();

$user_name = $_SESSION['full_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pro Dashboard | SkillLink</title>
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
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .active-nav { 
            background: white; 
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); 
            color: skill-blue !important; 
            border-radius: 1.25rem;
        }

        @keyframes toast-slide-in {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes toast-slide-out {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
        .toast-active { animation: toast-slide-in 0.6s cubic-bezier(0.23, 1, 0.32, 1) forwards; }
        .toast-closing { animation: toast-slide-out 0.6s cubic-bezier(0.23, 1, 0.32, 1) forwards; }
        
        @keyframes subtle-glow { 0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); } 50% { box-shadow: 0 0 15px 2px rgba(59, 130, 246, 0.1); } 100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); } }
        .new-job-glow { animation: subtle-glow 3s infinite; }
    </style>
</head>
<body class="bg-[#f8fafc] font-sans antialiased text-slate-900 overflow-hidden">

    <div id="success-toast" class="fixed top-8 right-8 z-[200] hidden">
        <div class="bg-white/90 backdrop-blur-xl border border-white/50 shadow-2xl rounded-[1.5rem] p-6 flex items-center gap-5 min-w-[320px] elite-card">
            <div class="w-12 h-12 bg-green-500 text-white rounded-full flex items-center justify-center shadow-lg shadow-green-500/20">
                <i class="fas fa-check"></i>
            </div>
            <div class="flex-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">System Pulse</p>
                <p id="toast-message" class="text-sm font-black text-slate-900 leading-tight">Handshake Synchronized</p>
            </div>
            <button onclick="closeToast()" class="text-slate-300 hover:text-slate-900 transition-colors">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    </div>

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
                    <a href="dashboard.php" class="active-nav flex items-center gap-4 px-6 py-4 rounded-2xl font-black text-sm transition-all text-skill-blue">
                        <i class="fas fa-chart-line text-xs"></i> Overview
                    </a>
                    <a href="manage-tasks.php" class="flex items-center gap-4 px-6 py-4 text-slate-500 hover:text-skill-blue transition-all rounded-2xl font-bold text-sm">
                        <i class="fas fa-list-check text-xs"></i> Active Tasks
                    </a>
                    <a href="earnings.php" class="flex items-center gap-4 px-6 py-4 text-slate-500 hover:text-skill-blue transition-all rounded-2xl font-bold text-sm">
                        <i class="fas fa-wallet text-xs"></i> Earnings
                    </a>
                    <a href="edit-profile.php" class="flex items-center gap-4 px-6 py-4 text-slate-500 hover:text-skill-blue transition-all rounded-2xl font-bold text-sm">
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
                <div class="flex items-center gap-4">
                    <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
                    <div>
                        <h1 class="text-lg font-black text-slate-900 leading-none">Status: <span class="text-green-500 uppercase">Live</span></h1>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-1">Accepting <?php echo $worker_data['cat_name']; ?> jobs nationwide</p>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <div class="text-right hidden md:block">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Professional Pro</p>
                        <p class="font-black text-slate-900"><?php echo $user_name; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-slate-900 rounded-2xl overflow-hidden border-2 border-white shadow-xl">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=0f172a&color=fff" alt="User">
                    </div>
                </div>
            </header>

            <section class="flex-1 overflow-y-auto p-12 custom-scroll bg-[#f1f5f9]/30">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                    <div class="elite-card p-8 rounded-[2.5rem] group hover:-translate-y-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Total Earnings</p>
                        <h3 class="text-3xl font-black text-slate-900 tracking-tight">Ksh <?php echo number_format($stats['total_earnings'], 2); ?></h3>
                        <div class="mt-4 inline-flex items-center gap-2 text-green-500 bg-green-50 px-3 py-1 rounded-full text-[9px] font-black uppercase">
                            <i class="fas fa-arrow-trend-up"></i> <?php echo ($stats['total_tasks_finished'] > 0) ? "Growth Active" : "0% Growth"; ?>
                        </div>
                    </div>
                    <div class="elite-card p-8 rounded-[2.5rem] group hover:-translate-y-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Tasks Finished</p>
                        <h3 class="text-3xl font-black text-slate-900 tracking-tight"><?php echo $stats['total_tasks_finished']; ?></h3>
                        <p class="mt-4 text-slate-400 font-bold text-[9px] uppercase tracking-widest italic">
                            <?php echo ($stats['total_tasks_finished'] > 0) ? "Consistent Performer" : "Awaiting First Handshake"; ?>
                        </p>
                    </div>
                    <div class="bg-slate-900 p-8 rounded-[2.5rem] text-white shadow-2xl relative overflow-hidden group">
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-skill-blue/10 rounded-full blur-2xl"></div>
                        <div class="relative z-10">
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">Professional Score</p>
                            <h3 class="text-3xl font-black flex items-center gap-3">5.0 <i class="fas fa-star text-amber-400 text-xl"></i></h3>
                            <p class="mt-4 text-skill-blue font-black text-[9px] uppercase tracking-[0.2em]">Certified SkillLink Pro</p>
                        </div>
                        <i class="fas fa-shield-check absolute -right-6 -bottom-6 text-9xl opacity-5 group-hover:scale-110 transition-transform duration-700"></i>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                    <div class="lg:col-span-2 space-y-8">
                        <div class="flex items-center justify-between px-4">
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em]">Opportunity Engine</h3>
                            <div class="flex items-center gap-3"><span class="w-2 h-2 bg-skill-blue rounded-full animate-ping"></span><span class="text-skill-blue text-[9px] font-black uppercase tracking-widest">Scanning Local Leads</span></div>
                        </div>
                        <div class="space-y-6">
                            <?php if($opportunities_query->num_rows > 0): ?>
                                <?php while($job = $opportunities_query->fetch_assoc()): ?>
                                <div class="elite-card p-8 rounded-[3rem] flex items-center justify-between group new-job-glow hover:border-skill-blue/50 transition-all">
                                    <div class="flex items-center gap-8">
                                        <div class="w-16 h-16 bg-slate-50 rounded-[1.75rem] flex items-center justify-center text-slate-300 group-hover:text-skill-blue transition-colors"><i class="fas fa-bolt-lightning text-2xl"></i></div>
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-3">
                                                <span class="px-3 py-1 bg-skill-blue/10 text-skill-blue text-[9px] font-black uppercase rounded-full tracking-tighter"><?php echo $job['cat_name']; ?></span>
                                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">• <?php echo date('h:i A', strtotime($job['created_at'])); ?></span>
                                            </div>
                                            <h4 class="text-lg font-black text-slate-900 tracking-tight leading-tight"><?php echo $job['title']; ?></h4>
                                            <div class="flex items-center gap-4 pt-1">
                                                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest"><i class="fas fa-map-pin mr-1.5 text-skill-blue"></i> <?php echo $job['location_name']; ?></p>
                                                <p class="text-xs text-slate-900 font-black uppercase tracking-widest"><i class="fas fa-wallet mr-1.5 text-skill-blue"></i> Ksh <?php echo number_format($job['budget'], 2); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="view-task.php?id=<?php echo $job['id']; ?>" class="px-8 py-5 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-skill-blue transition-all shadow-xl active:scale-95 inline-flex items-center justify-center">
                                        Inspect Task <i class="fas fa-arrow-right ml-3"></i>
                                    </a>
                                </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="bg-white rounded-[4rem] p-24 text-center border-4 border-dashed border-slate-100 group transition-all">
                                    <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner"><i class="fas fa-satellite-dish text-slate-200 text-4xl animate-pulse"></i></div>
                                    <h4 class="text-2xl font-black text-slate-900 tracking-tight">Listening for Requests...</h4>
                                    <p class="text-sm text-slate-400 font-medium max-w-sm mx-auto mt-4 leading-relaxed">Jobs matching your <strong><?php echo $worker_data['cat_name']; ?></strong> skill in your region will appear here instantly.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em] px-4">Readiness Protocol</h3>
                        <div class="bg-white p-10 rounded-[3.5rem] elite-card text-center">
                            <div class="relative w-40 h-40 mx-auto mb-10 group">
                                <svg class="w-full h-full transform -rotate-90">
                                    <circle cx="80" cy="80" r="72" stroke="currentColor" stroke-width="12" fill="transparent" class="text-slate-50" />
                                    <circle cx="80" cy="80" r="72" stroke="currentColor" stroke-width="12" fill="transparent" class="text-skill-blue" stroke-dasharray="452" stroke-dashoffset="113" stroke-linecap="round" />
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-3xl font-black text-slate-900 leading-none">75%</span>
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-2">Verified</span>
                                </div>
                            </div>
                            <div class="space-y-5 mb-10 text-left">
                                <div class="flex items-center gap-4 bg-green-50/50 p-4 rounded-2xl border border-green-100"><div class="w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-[8px]"><i class="fas fa-check"></i></div><span class="text-[10px] font-black text-green-600 uppercase tracking-widest">Skill Identity Set</span></div>
                                <div class="flex items-center gap-4 bg-green-50/50 p-4 rounded-2xl border border-green-100"><div class="w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-[8px]"><i class="fas fa-check"></i></div><span class="text-[10px] font-black text-green-600 uppercase tracking-widest">Basic Bio Complete</span></div>
                                <div class="flex items-center gap-4 p-4 rounded-2xl border-2 border-dashed border-slate-100 opacity-60"><div class="w-6 h-6 bg-slate-100 rounded-full flex items-center justify-center text-[8px]"><i class="fas fa-id-card"></i></div><span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Verify National ID</span></div>
                            </div>
                            <a href="edit-profile.php" class="block w-full py-5 bg-slate-900 text-white rounded-[1.5rem] font-black text-xs uppercase tracking-[0.2em] hover:bg-skill-blue shadow-xl transition-all active:scale-95">Finalize Verification <i class="fas fa-shield-halved ml-2"></i></a>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('status')) {
                const status = urlParams.get('status');
                if (status === 'synced') showToast("Operation Synchronized Successfully");
                else if (status === 'committed') showToast("New Project Successfully Claimed!");
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });

        function showToast(msg) {
            const toast = document.getElementById('success-toast');
            document.getElementById('toast-message').textContent = msg;
            toast.classList.remove('hidden');
            toast.classList.add('toast-active');
            setTimeout(() => closeToast(), 5000);
        }

        function closeToast() {
            const toast = document.getElementById('success-toast');
            toast.classList.remove('toast-active');
            toast.classList.add('toast-closing');
            setTimeout(() => { toast.classList.add('hidden'); toast.classList.remove('toast-closing'); }, 600);
        }
    </script>
</body>
</html>