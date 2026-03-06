<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: client/dashboard.php
 * PURPOSE: Elite Client Command Center - Active Task Engine & High-Fidelity UI
 */

session_start();

// 1. Security Guard
if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'client') {
    header("Location: ../auth/login.php");
    exit();
}

// 2. System Initialization
$base_dir = dirname(__DIR__);
require_once $base_dir . '/includes/config.php';

// 3. Data Retrieval (The Heartbeat)
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];

// Fetch Active Tasks
$active_tasks_query = $conn->query("SELECT t.*, c.cat_name 
                                    FROM tasks t 
                                    JOIN categories c ON t.category_id = c.id 
                                    WHERE t.client_id = '$user_id' 
                                    AND t.status IN ('open', 'assigned', 'in_progress')
                                    ORDER BY t.created_at DESC");

// Fetch Top Pros (Nationwide Context)
$pros_query = $conn->query("SELECT wp.*, u.full_name, c.cat_name 
                            FROM worker_profiles wp 
                            JOIN users u ON wp.user_id = u.id 
                            JOIN categories c ON wp.category_id = c.id 
                            WHERE wp.is_live = 1 
                            LIMIT 3");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Command Center | SkillLink Client</title>
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
        }
        
        /* PULSING STATUS ANIMATION */
        @keyframes status-pulse { 0% { opacity: 0.4; } 50% { opacity: 1; } 100% { opacity: 0.4; } }
        .status-searching { animation: status-pulse 2s infinite; }
        
        .hero-glass {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-[#f8fafc] font-sans antialiased text-slate-900 overflow-hidden">

    <div class="flex h-screen overflow-hidden">
        
        <aside class="w-72 glass-sidebar border-r border-slate-200 flex flex-col z-50">
            <div class="p-8">
                <div class="flex items-center gap-3 mb-10">
                    <div class="w-12 h-12 bg-slate-900 text-white rounded-2xl flex items-center justify-center shadow-xl">
                        <i class="fas fa-screwdriver-wrench text-lg"></i>
                    </div>
                    <span class="text-2xl font-black tracking-tighter uppercase">Skill<span class="text-skill-blue">Link</span></span>
                </div>

                <nav class="space-y-2">
                    <a href="dashboard.php" class="bg-white elite-card text-skill-blue flex items-center gap-4 px-6 py-4 rounded-2xl font-black text-sm">
                        <i class="fas fa-th-large text-xs"></i> Dashboard
                    </a>
                    <a href="post-job.php" class="flex items-center gap-4 px-6 py-4 text-slate-400 hover:text-slate-900 transition-all rounded-2xl font-bold text-sm">
                        <i class="fas fa-plus-circle text-xs"></i> Launch Project
                    </a>
                    <a href="#" class="flex items-center gap-4 px-6 py-4 text-slate-400 hover:text-slate-900 transition-all rounded-2xl font-bold text-sm">
                        <i class="fas fa-calendar-check text-xs"></i> My Bookings
                    </a>
                    <a href="#" class="flex items-center gap-4 px-6 py-4 text-slate-400 hover:text-slate-900 transition-all rounded-2xl font-bold text-sm">
                        <i class="fas fa-history text-xs"></i> Activity Log
                    </a>
                </nav>
            </div>
            <div class="mt-auto p-8 border-t border-slate-100">
                <a href="../auth/logout.php" class="flex items-center gap-4 px-6 py-4 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-2xl font-bold text-sm transition-all">
                    <i class="fas fa-power-off text-xs"></i> Sign Out
                </a>
            </div>
        </aside>

        <main class="flex-1 flex flex-col overflow-hidden relative">
            
            <header class="h-24 flex items-center justify-between px-12 bg-white border-b border-slate-100 relative z-20">
                <div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight leading-none">Jambo, <?php echo explode(' ', $user_name)[0]; ?>!</h1>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-2">Elite Client Environment</p>
                </div>

                <div class="flex items-center gap-6">
                    <button class="w-12 h-12 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-slate-400 hover:text-skill-blue hover:border-skill-blue transition-all relative">
                        <i class="fas fa-bell text-sm"></i>
                        <span class="absolute top-3 right-3 w-2.5 h-2.5 bg-red-500 border-4 border-white rounded-full"></span>
                    </button>
                    <div class="w-14 h-14 bg-slate-900 rounded-[1.25rem] overflow-hidden border-4 border-white shadow-2xl">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=0f172a&color=fff" alt="User">
                    </div>
                </div>
            </header>

            <section class="flex-1 overflow-y-auto p-12 custom-scroll bg-[#f1f5f9]/50 relative">
                
                <div class="hero-glass rounded-[3.5rem] p-12 mb-14 relative overflow-hidden shadow-2xl">
                    <div class="absolute top-0 right-0 w-96 h-96 bg-skill-blue/10 rounded-full blur-[100px]"></div>
                    <div class="relative z-10 flex flex-col lg:flex-row items-center gap-12">
                        <div class="flex-1 space-y-4">
                            <h2 class="text-4xl font-black text-white tracking-tight leading-tight">What expert do<br>you need today?</h2>
                            <p class="text-slate-400 font-bold text-sm tracking-wide">Match with verified professionals in your territory instantly.</p>
                        </div>
                        <div class="flex-1 w-full group">
                            <div class="relative">
                                <i class="fas fa-magnifying-glass absolute left-8 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-skill-blue transition-colors"></i>
                                <input type="text" placeholder="Search for Electricians, Plumbers..." 
                                       class="w-full bg-white/5 border border-white/10 rounded-[2rem] py-7 pl-20 pr-10 text-white font-bold placeholder:text-slate-600 focus:bg-white/10 focus:ring-4 focus:ring-skill-blue/20 outline-none transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                    
                    <div class="lg:col-span-2 space-y-8">
                        <div class="flex items-center justify-between px-4">
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em]">Active Bookings</h3>
                            <a href="#" class="text-[10px] font-black text-skill-blue uppercase tracking-widest border-b-2 border-skill-blue/20 hover:border-skill-blue transition-all pb-1">View All Activity</a>
                        </div>
                        
                        <div class="grid grid-cols-1 gap-6">
                            <?php if($active_tasks_query->num_rows > 0): ?>
                                <?php while($task = $active_tasks_query->fetch_assoc()): ?>
                                <div class="elite-card p-8 rounded-[3rem] flex items-center justify-between group hover:border-skill-blue transition-all">
                                    <div class="flex items-center gap-6">
                                        <div class="w-16 h-16 bg-slate-50 rounded-[1.5rem] flex items-center justify-center text-slate-300 group-hover:text-skill-blue transition-colors">
                                            <i class="fas fa-screwdriver-wrench text-2xl"></i>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-3 mb-1">
                                                <span class="px-3 py-1 bg-skill-blue/10 text-skill-blue text-[9px] font-black uppercase rounded-full tracking-tighter"><?php echo $task['cat_name']; ?></span>
                                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">• <?php echo date('M d', strtotime($task['created_at'])); ?></span>
                                            </div>
                                            <h4 class="text-lg font-black text-slate-900 tracking-tight"><?php echo $task['title']; ?></h4>
                                            <p class="text-xs text-slate-400 font-bold mt-1 uppercase tracking-widest"><i class="fas fa-map-pin mr-1 text-skill-blue"></i> <?php echo $task['location_name']; ?></p>
                                        </div>
                                    </div>
                                    
                                    <div class="text-right flex items-center gap-8">
                                        <div class="hidden md:block">
                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Current Status</p>
                                            <div class="flex items-center gap-2">
                                                <div class="w-2 h-2 bg-skill-blue rounded-full status-searching"></div>
                                                <span class="text-[10px] font-black text-slate-900 uppercase tracking-widest"><?php echo strtoupper($task['status']); ?></span>
                                            </div>
                                        </div>
                                        <button class="w-12 h-12 bg-slate-900 text-white rounded-2xl flex items-center justify-center shadow-xl hover:bg-skill-blue transition-all">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="bg-white border-4 border-dashed border-slate-100 rounded-[4rem] p-20 text-center space-y-8 group hover:border-skill-blue/20 transition-all">
                                    <div class="w-24 h-24 bg-slate-50 text-slate-200 rounded-full flex items-center justify-center mx-auto transition-transform group-hover:scale-110">
                                        <i class="fas fa-rocket text-4xl"></i>
                                    </div>
                                    <div class="space-y-2">
                                        <h4 class="text-xl font-black text-slate-900">No Projects Launched</h4>
                                        <p class="text-sm text-slate-400 font-bold tracking-tight max-w-xs mx-auto">Engineer your first professional request and reach local pros across Kenya.</p>
                                    </div>
                                    <a href="post-job.php" class="inline-flex items-center gap-4 px-10 py-5 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-skill-blue transition-all shadow-2xl">
                                        Launch New Project <i class="fas fa-plus"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em] px-4">Top Rated Pros</h3>
                        
                        <div class="space-y-5">
                            <?php while($pro = $pros_query->fetch_assoc()): ?>
                            <div class="bg-white elite-card p-6 rounded-[2.5rem] group hover:-translate-y-2 transition-all duration-500">
                                <div class="flex items-center gap-5 mb-6">
                                    <div class="w-16 h-16 rounded-[1.5rem] overflow-hidden border-4 border-slate-50 shadow-lg">
                                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($pro['full_name']); ?>&background=random" alt="Pro">
                                    </div>
                                    <div>
                                        <h5 class="text-base font-black text-slate-900 group-hover:text-skill-blue transition-colors"><?php echo $pro['full_name']; ?></h5>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="bg-green-50 text-green-600 text-[9px] font-black px-2 py-0.5 rounded uppercase tracking-tighter"><?php echo $pro['cat_name']; ?></span>
                                            <span class="text-[10px] font-black text-slate-400"><i class="fas fa-star text-amber-400"></i> 5.0</span>
                                        </div>
                                    </div>
                                </div>
                                <button class="w-full py-4 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-skill-blue transition-all shadow-xl active:scale-95">
                                    Consult Expert
                                </button>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>

                </div>
            </section>
        </main>
    </div>
</body>
</html>