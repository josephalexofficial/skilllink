<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: client/activity-log.php
 * PURPOSE: Elite Activity Log - Permanent Mission Ledger & Re-Discovery Hub
 */

session_start();

// 1. Security Guard: Role-Gate
if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'client') {
    header("Location: ../auth/login.php");
    exit();
}

// 2. System Initialization
$base_dir = dirname(__DIR__);
require_once $base_dir . '/includes/config.php';

$client_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];

// 3. Surgical History Logic
// Fetching Lifetime Investment & Mission Count
$stats_query = $conn->query("SELECT 
                                IFNULL(SUM(budget), 0) AS lifetime_spend,
                                COUNT(id) AS finalized_count
                             FROM tasks 
                             WHERE client_id = '$client_id' 
                             AND status = 'finalized'");
$stats = $stats_query->fetch_assoc();

// Fetching The Archive (Joined with Worker Info & Reviews)
$history_query = $conn->query("SELECT t.*, u.full_name as worker_name, c.cat_name, r.rating, r.comment as review_note
                               FROM tasks t 
                               JOIN users u ON t.worker_id = u.id 
                               JOIN categories c ON t.category_id = c.id 
                               LEFT JOIN reviews r ON t.id = r.task_id
                               WHERE t.client_id = '$client_id' 
                               AND t.status = 'finalized'
                               ORDER BY t.updated_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log | SkillLink Archive</title>
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
        
        /* ELITE RECESSED SYSTEM */
        .recessed-card { 
            background: #fdfdfd; 
            border: 1px solid rgba(0, 0, 0, 0.03); 
            box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.02), 0 10px 15px -3px rgba(0, 0, 0, 0.02);
            transition: all 0.3s ease;
        }
        .recessed-card:hover { transform: translateY(-2px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05); }

        /* SIDEBAR ANCHOR */
        .active-nav { background: white; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); color: skill-blue !important; border-radius: 1.25rem; }

        .vault-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border: 1px solid rgba(255, 255, 255, 0.05);
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
                        <i class="fas fa-th-large text-xs"></i> Dashboard
                    </a>
                    <a href="post-job.php" class="flex items-center gap-4 px-6 py-4 text-slate-400 hover:text-skill-blue transition-all rounded-2xl font-bold text-sm">
                        <i class="fas fa-circle-plus text-xs"></i> Launch Project
                    </a>
                    <a href="my-bookings.php" class="flex items-center gap-4 px-6 py-4 text-slate-400 hover:text-skill-blue transition-all rounded-2xl font-bold text-sm">
                        <i class="fas fa-calendar-check text-xs"></i> My Bookings
                    </a>
                    <a href="activity-log.php" class="active-nav flex items-center gap-4 px-6 py-4 rounded-2xl font-black text-sm transition-all text-skill-blue">
                        <i class="fas fa-clock-rotate-left text-xs"></i> Activity Log
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
                    <h1 class="text-xl font-black text-slate-900 leading-none">Activity Log</h1>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-2">Permanent Mission Archive</p>
                </div>

                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Audit Mode</p>
                        <p class="font-black text-slate-900"><?php echo $user_name; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-slate-900 rounded-2xl overflow-hidden border-2 border-white shadow-xl">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=0f172a&color=fff" alt="User">
                    </div>
                </div>
            </header>

            <section class="flex-1 overflow-y-auto p-12 custom-scroll bg-[#f1f5f9]/30">
                <div class="max-w-6xl mx-auto space-y-12 pb-24">
                    
                    <div class="vault-hero rounded-[3rem] p-10 text-white shadow-2xl flex flex-col md:flex-row justify-between items-center gap-8 relative overflow-hidden">
                        <div class="absolute -top-10 -left-10 w-40 h-40 bg-skill-blue/10 rounded-full blur-3xl"></div>
                        <div class="space-y-1 relative z-10 text-center md:text-left">
                            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-skill-blue">Total Value Generated</p>
                            <h2 class="text-4xl font-black tracking-tighter italic">Ksh <?php echo number_format($stats['lifetime_spend'], 2); ?></h2>
                        </div>
                        <div class="flex gap-12 relative z-10">
                            <div class="text-center">
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Missions</p>
                                <p class="text-2xl font-black"><?php echo $stats['finalized_count']; ?></p>
                            </div>
                            <div class="text-center border-l border-white/10 pl-12">
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Status</p>
                                <p class="text-2xl font-black text-green-400">Audited</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="flex items-center justify-between px-4">
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em]">Historical Registry</h3>
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">End-to-End Solutions</span>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            <?php if($history_query->num_rows > 0): ?>
                                <?php while($entry = $history_query->fetch_assoc()): ?>
                                <div class="recessed-card p-8 rounded-[2.5rem] flex flex-col md:flex-row items-center justify-between gap-8 group">
                                    <div class="flex items-center gap-8 flex-1">
                                        <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-300 shadow-inner group-hover:text-skill-blue transition-colors">
                                            <i class="fas fa-archive text-xl"></i>
                                        </div>
                                        <div class="space-y-1">
                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1"><?php echo date('M d, Y', strtotime($entry['updated_at'])); ?> • <?php echo $entry['cat_name']; ?></p>
                                            <h4 class="text-lg font-black text-slate-900 tracking-tight leading-tight"><?php echo $entry['title']; ?></h4>
                                            
                                            <?php if($entry['rating']): ?>
                                            <div class="flex items-center gap-3 pt-2">
                                                <div class="flex text-amber-400 text-[10px]">
                                                    <?php for($i=0; $i<$entry['rating']; $i++): ?> <i class="fas fa-star"></i> <?php endfor; ?>
                                                </div>
                                                <p class="text-[10px] text-slate-400 font-medium italic">"<?php echo $entry['review_note']; ?>"</p>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-10 w-full md:w-auto justify-between md:justify-end border-t md:border-t-0 pt-6 md:pt-0">
                                        <div class="text-left md:text-right">
                                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Final Handshake</p>
                                            <p class="text-xl font-black text-slate-900 leading-none">Ksh <?php echo number_format($entry['budget'], 2); ?></p>
                                        </div>
                                        <div class="flex gap-2">
                                            <button title="Clone Blueprint" class="w-10 h-10 rounded-xl border border-slate-100 flex items-center justify-center text-slate-400 hover:bg-skill-blue hover:text-white hover:border-skill-blue transition-all">
                                                <i class="fas fa-copy text-xs"></i>
                                            </button>
                                            <button title="Re-Hire Pro" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest hover:bg-skill-blue transition-all">
                                                Book Again
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="bg-white rounded-[4rem] p-24 text-center border-4 border-dashed border-slate-100 group transition-all">
                                    <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner">
                                        <i class="fas fa-folder-open text-slate-200 text-4xl animate-pulse"></i>
                                    </div>
                                    <h4 class="text-2xl font-black text-slate-900 tracking-tight italic">Registry is Empty</h4>
                                    <p class="text-sm text-slate-400 font-medium max-w-sm mx-auto mt-4 leading-relaxed">Once you finalize your first professional handshake in <strong>My Bookings</strong>, it will be securely archived here.</p>
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