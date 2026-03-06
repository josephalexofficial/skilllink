<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: worker/manage-tasks.php
 * PURPOSE: Elite Mission Control - Active Task Tracking & Success Pulse
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

$worker_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];

// 3. Data Retrieval: The Operational Ledger
// Fetch In-Progress/Assigned Tasks (The "Hero" Zone)
$active_tasks = $conn->query("SELECT t.*, u.full_name as client_name, c.cat_name 
                              FROM tasks t 
                              JOIN users u ON t.client_id = u.id 
                              JOIN categories c ON t.category_id = c.id 
                              WHERE t.worker_id = '$worker_id' 
                              AND t.status IN ('assigned', 'in_progress')
                              ORDER BY t.updated_at DESC");

// Fetch Completed Tasks (The "History Vault")
$history_tasks = $conn->query("SELECT t.*, u.full_name as client_name, c.cat_name 
                               FROM tasks t 
                               JOIN users u ON t.client_id = u.id 
                               JOIN categories c ON t.category_id = c.id 
                               WHERE t.worker_id = '$worker_id' 
                               AND t.status = 'completed' 
                               ORDER BY t.updated_at DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Missions | SkillLink Pro</title>
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
        
        /* ELITE MATERIAL SYSTEM */
        .elite-card { 
            background: white; 
            border: 1px solid rgba(255, 255, 255, 0.6); 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 20px 25px -5px rgba(0, 0, 0, 0.04); 
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* SIDEBAR ANCHOR */
        .active-nav { background: white; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); color: skill-blue !important; border-radius: 1.25rem; }

        /* STATUS CAPSULES */
        .badge-assigned { background: #eff6ff; color: #3b82f6; border: 1px solid #dbeafe; }
        .badge-progress { background: #fffbeb; color: #d97706; border: 1px solid #fef3c7; animation: pulse 2s infinite; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.7; } 100% { opacity: 1; } }

        /* TOAST ANIMATION */
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
                    <a href="dashboard.php" class="flex items-center gap-4 px-6 py-4 text-slate-400 hover:text-skill-blue transition-all rounded-2xl font-bold text-sm">
                        <i class="fas fa-chart-line text-xs"></i> Overview
                    </a>
                    <a href="manage-tasks.php" class="active-nav flex items-center gap-4 px-6 py-4 rounded-2xl font-black text-sm transition-all text-skill-blue">
                        <i class="fas fa-list-check text-xs"></i> Active Tasks
                    </a>
                    <a href="earnings.php" class="flex items-center gap-4 px-6 py-4 text-slate-400 hover:text-skill-blue transition-all rounded-2xl font-bold text-sm">
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
                    <h1 class="text-xl font-black text-slate-900 leading-none">Manage Missions</h1>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-2">Operational Command Center</p>
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
                
                <div class="max-w-6xl mx-auto space-y-14">
                    
                    <div class="space-y-8">
                        <div class="flex items-center justify-between px-4">
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em]">In-Progress Operations</h3>
                            <span class="px-4 py-1.5 bg-skill-blue/10 text-skill-blue text-[9px] font-black uppercase rounded-full"><?php echo $active_tasks->num_rows; ?> Active Commitments</span>
                        </div>

                        <div class="grid grid-cols-1 gap-8">
                            <?php if($active_tasks->num_rows > 0): ?>
                                <?php while($task = $active_tasks->fetch_assoc()): ?>
                                <div class="elite-card rounded-[3.5rem] overflow-hidden group hover:border-skill-blue/40">
                                    <div class="flex flex-col md:flex-row">
                                        <div class="flex-1 p-10 space-y-6">
                                            <div class="flex items-center gap-4">
                                                <span class="px-3 py-1 bg-slate-100 text-slate-500 text-[9px] font-black uppercase rounded-md tracking-widest"><?php echo $task['cat_name']; ?></span>
                                                <span class="badge-<?php echo ($task['status'] == 'assigned') ? 'assigned' : 'progress'; ?> px-3 py-1 text-[9px] font-black uppercase rounded-md tracking-widest">
                                                    <?php echo str_replace('_', ' ', $task['status']); ?>
                                                </span>
                                            </div>
                                            <h2 class="text-2xl font-black text-slate-900 tracking-tight"><?php echo $task['title']; ?></h2>
                                            <p class="text-sm text-slate-500 font-medium leading-relaxed line-clamp-2 italic">"<?php echo $task['description']; ?>"</p>
                                            
                                            <div class="flex items-center gap-8 pt-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 bg-skill-blue/10 text-skill-blue rounded-lg flex items-center justify-center text-xs"><i class="fas fa-map-pin"></i></div>
                                                    <p class="text-[11px] font-black text-slate-900 uppercase tracking-tight"><?php echo $task['location_name']; ?></p>
                                                </div>
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 bg-green-50 text-green-600 rounded-lg flex items-center justify-center text-xs"><i class="fas fa-wallet"></i></div>
                                                    <p class="text-[11px] font-black text-slate-900 uppercase tracking-tight">Ksh <?php echo number_format($task['budget'], 2); ?></p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="w-full md:w-96 bg-slate-50 border-l border-slate-100 p-10 flex flex-col justify-between">
                                            <div class="flex items-center gap-4 mb-8">
                                                <div class="w-12 h-12 rounded-2xl overflow-hidden border-2 border-white shadow-lg">
                                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($task['client_name']); ?>&background=random" class="w-full h-full object-cover">
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Contracting Client</p>
                                                    <p class="text-sm font-black text-slate-900"><?php echo $task['client_name']; ?></p>
                                                </div>
                                            </div>

                                            <div class="space-y-3">
                                                <?php if($task['status'] == 'assigned'): ?>
                                                    <a href="update-status.php?id=<?php echo $task['id']; ?>&to=in_progress" 
                                                       class="block w-full py-4 bg-slate-900 text-white rounded-2xl text-center font-black text-[10px] uppercase tracking-widest hover:bg-skill-blue transition-all shadow-xl">
                                                        Deploy to Site <i class="fas fa-person-running ml-2"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="update-status.php?id=<?php echo $task['id']; ?>&to=completed" 
                                                       class="block w-full py-4 bg-green-600 text-white rounded-2xl text-center font-black text-[10px] uppercase tracking-widest hover:bg-green-700 transition-all shadow-xl">
                                                        Mark as Finished <i class="fas fa-check-double ml-2"></i>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <a href="https://wa.me/254123456789" target="_blank" class="block w-full py-4 border border-slate-200 text-slate-600 rounded-2xl text-center font-black text-[10px] uppercase tracking-widest hover:bg-white hover:text-skill-blue transition-all">
                                                    Contact via WhatsApp <i class="fab fa-whatsapp ml-2"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="bg-white rounded-[4rem] p-24 text-center border-4 border-dashed border-slate-100 group transition-all">
                                    <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner">
                                        <i class="fas fa-radar text-slate-200 text-4xl animate-pulse"></i>
                                    </div>
                                    <h4 class="text-2xl font-black text-slate-900 tracking-tight">No Active Missions</h4>
                                    <p class="text-sm text-slate-400 font-medium max-w-sm mx-auto mt-4 leading-relaxed">Visit the Opportunity Feed to claim your next professional project in the region.</p>
                                    <a href="dashboard.php" class="inline-flex mt-8 px-10 py-5 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-skill-blue shadow-2xl transition-all">
                                        Browse Opportunities <i class="fas fa-arrow-right ml-3"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if($history_tasks->num_rows > 0): ?>
                    <div class="space-y-8 pb-20 opacity-60 hover:opacity-100 transition-opacity">
                        <div class="flex items-center justify-between px-4 border-t border-slate-200 pt-14">
                            <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Operational History</h3>
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Completed Legacy</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <?php while($history = $history_tasks->fetch_assoc()): ?>
                            <div class="elite-card p-8 rounded-[2.5rem] flex items-center justify-between grayscale hover:grayscale-0 transition-all">
                                <div class="flex items-center gap-6">
                                    <div class="w-12 h-12 bg-green-50 text-green-500 rounded-xl flex items-center justify-center"><i class="fas fa-check-circle"></i></div>
                                    <div>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest"><?php echo date('M d, Y', strtotime($history['updated_at'])); ?></p>
                                        <h4 class="text-sm font-black text-slate-900"><?php echo $history['title']; ?></h4>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-black text-slate-900 tracking-tight">Ksh <?php echo number_format($history['budget'], 2); ?></p>
                                    <span class="text-[8px] font-black uppercase text-green-500">Paid Out</span>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </section>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            
            // Detect Status and State Parameters
            if (urlParams.has('status')) {
                const status = urlParams.get('status');
                const state = urlParams.get('state');

                if (status === 'synced') {
                    if (state === 'in_progress') {
                        showToast("Deployment Active: En Route to Client Site");
                    } else if (state === 'completed') {
                        showToast("Mission Accomplished: Earnings Logged to History");
                    } else {
                        showToast("Operation Synchronized Successfully");
                    }
                }

                // URL Cleanup Protocol
                const newUrl = window.location.pathname;
                window.history.replaceState({}, document.title, newUrl);
            }
        });

        function showToast(msg) {
            const toast = document.getElementById('success-toast');
            const message = document.getElementById('toast-message');
            
            message.textContent = msg;
            toast.classList.remove('hidden');
            toast.classList.add('toast-active');

            // Auto-Dismiss Timer (5 Seconds)
            setTimeout(() => {
                closeToast();
            }, 5000);
        }

        function closeToast() {
            const toast = document.getElementById('success-toast');
            toast.classList.remove('toast-active');
            toast.classList.add('toast-closing');
            
            setTimeout(() => {
                toast.classList.add('hidden');
                toast.classList.remove('toast-closing');
            }, 600);
        }
    </script>
</body>
</html>