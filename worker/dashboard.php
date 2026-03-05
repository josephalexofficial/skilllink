<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: worker/dashboard.php
 * REFINEMENTS: Logic-Gate Integration & Dynamic Profile Guard
 */

session_start();

// 1. Security Guard: The Access Control Node
if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'worker') {
    header("Location: ../auth/login.php");
    exit();
}

// 2. System Initialization
$base_dir = dirname(__DIR__);
require_once $base_dir . '/includes/config.php';

// --- NEW: THE LOGIC GATE ---
// Check if the worker has a profile. If not, bounce to setup.
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
// ---------------------------

// 3. Page Identity
$user_name = $_SESSION['full_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pro Dashboard | SkillLink</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        .custom-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .active-nav { background: #f8fafc; color: <?php echo defined("BRAND_COLOR") ? BRAND_COLOR : "#3b82f6"; ?>; border-right: 4px solid currentColor; }
    </style>
</head>
<body class="bg-[#f1f5f9] font-sans antialiased text-slate-900 overflow-hidden">

    <div class="flex h-screen overflow-hidden">
        
        <aside class="w-72 glass-sidebar border-r border-slate-200 flex flex-col z-50">
            <div class="p-8">
                <div class="flex items-center gap-3 mb-10">
                    <div class="w-10 h-10 bg-slate-900 text-white rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-briefcase text-sm"></i>
                    </div>
                    <span class="text-xl font-black tracking-tighter uppercase">Skill<span class="text-skill-blue">Link</span></span>
                </div>

                <nav class="space-y-2">
                    <a href="dashboard.php" class="active-nav flex items-center gap-4 px-6 py-4 rounded-2xl font-bold text-sm transition-all">
                        <i class="fas fa-chart-line text-xs"></i> Overview
                    </a>
                    <a href="manage-tasks.php" class="flex items-center gap-4 px-6 py-4 text-slate-500 hover:text-skill-blue hover:bg-slate-50 rounded-2xl font-bold text-sm transition-all">
                        <i class="fas fa-list-check text-xs"></i> Active Tasks
                    </a>
                    <a href="#" class="flex items-center gap-4 px-6 py-4 text-slate-500 hover:text-skill-blue hover:bg-slate-50 rounded-2xl font-bold text-sm transition-all">
                        <i class="fas fa-wallet text-xs"></i> Earnings
                    </a>
                    <a href="edit-profile.php" class="flex items-center gap-4 px-6 py-4 text-slate-500 hover:text-skill-blue hover:bg-slate-50 rounded-2xl font-bold text-sm transition-all">
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
            
            <header class="h-24 flex items-center justify-between px-12 bg-white border-b border-slate-100">
                <div class="flex items-center gap-4">
                    <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
                    <div>
                        <h1 class="text-lg font-black text-slate-900 leading-none">Status: <span class="text-green-500">Live</span></h1>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1">Ready for <?php echo $worker_data['cat_name']; ?> jobs</p>
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    <div class="text-right hidden md:block">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Professional</p>
                        <p class="font-black text-slate-900"><?php echo $user_name; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-slate-100 rounded-2xl overflow-hidden border-2 border-white shadow-xl">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=0f172a&color=fff" alt="User">
                    </div>
                </div>
            </header>

            <section class="flex-1 overflow-y-auto p-12 custom-scroll bg-[#f8fafc]/50">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-500">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Total Earnings</p>
                        <h3 class="text-3xl font-black text-slate-900">Ksh 0.00</h3>
                        <div class="mt-4 flex items-center gap-2 text-green-500 font-bold text-[10px] uppercase">
                            <i class="fas fa-arrow-up"></i> 0% growth
                        </div>
                    </div>
                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-500">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Tasks Finished</p>
                        <h3 class="text-3xl font-black text-slate-900">0</h3>
                        <p class="mt-4 text-slate-400 font-bold text-[10px] uppercase tracking-tighter">Awaiting first assignment</p>
                    </div>
                    <div class="bg-slate-900 p-8 rounded-[2.5rem] text-white shadow-2xl relative overflow-hidden group">
                        <div class="relative z-10">
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">Trust Score</p>
                            <h3 class="text-3xl font-black flex items-center gap-3 italic">5.0 <i class="fas fa-star text-amber-400 text-xl not-italic"></i></h3>
                            <p class="mt-4 text-skill-blue font-black text-[10px] uppercase tracking-widest">Certified Pro</p>
                        </div>
                        <i class="fas fa-shield-check absolute -right-4 -bottom-4 text-8xl opacity-10 group-hover:scale-110 transition-transform"></i>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    
                    <div class="lg:col-span-2 space-y-6">
                        <div class="flex items-center justify-between px-4">
                            <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Active Opportunity Feed</h3>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-skill-blue rounded-full animate-ping"></span>
                                <span class="text-skill-blue text-[9px] font-black uppercase tracking-widest">Scanning Live</span>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-[3rem] p-20 text-center border-2 border-dashed border-slate-200 hover:border-skill-blue/30 transition-colors">
                            <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner">
                                <i class="fas fa-satellite-dish text-slate-300 text-4xl animate-pulse"></i>
                            </div>
                            <h4 class="text-2xl font-black text-slate-900 tracking-tight">Listening for Requests...</h4>
                            <p class="text-slate-400 font-medium max-w-sm mx-auto mt-4 leading-relaxed">Jobs matching your <strong><?php echo $worker_data['cat_name']; ?></strong> skill will appear here automatically.</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight px-4">Pro verification</h3>
                        <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm text-center">
                            <div class="relative w-36 h-36 mx-auto mb-8">
                                <svg class="w-full h-full transform -rotate-90">
                                    <circle cx="72" cy="72" r="64" stroke="currentColor" stroke-width="10" fill="transparent" class="text-slate-100" />
                                    <circle cx="72" cy="72" r="64" stroke="currentColor" stroke-width="10" fill="transparent" class="text-skill-blue" stroke-dasharray="402" stroke-dashoffset="100" stroke-linecap="round" />
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-2xl font-black text-slate-900 leading-none">75%</span>
                                    <span class="text-[8px] font-black text-slate-400 uppercase mt-1">Ready</span>
                                </div>
                            </div>
                            <ul class="space-y-4 text-left mb-8">
                                <li class="flex items-center gap-4 text-[11px] font-black text-green-500 uppercase tracking-wider">
                                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center"><i class="fas fa-check"></i></div>
                                    Skill Identity Set
                                </li>
                                <li class="flex items-center gap-4 text-[11px] font-black text-green-500 uppercase tracking-wider">
                                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center"><i class="fas fa-check"></i></div>
                                    Basic Bio Complete
                                </li>
                                <li class="flex items-center gap-4 text-[11px] font-black text-slate-300 uppercase tracking-wider">
                                    <div class="w-6 h-6 bg-slate-50 rounded-full flex items-center justify-center border-2 border-dashed border-slate-200"><i class="fas fa-id-card"></i></div>
                                    Verify ID Docs
                                </li>
                            </ul>
                            <a href="edit-profile.php" class="block w-full py-5 bg-slate-900 text-white rounded-[1.5rem] font-black text-xs uppercase tracking-widest hover:bg-skill-blue hover:-translate-y-1 transition-all duration-300 shadow-xl shadow-slate-900/10">
                                Upgrade Profile
                            </a>
                        </div>
                    </div>

                </div>
            </section>
        </main>
    </div>

    <?php if(isset($_GET['status']) && $_GET['status'] === 'welcome'): ?>
    <div id="welcome-modal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-md transition-all duration-700 px-6">
        <div class="bg-white p-10 md:p-14 rounded-[3.5rem] shadow-2xl text-center max-w-sm transform scale-100">
            <div class="w-24 h-24 bg-green-100 text-green-500 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner">
                <i class="fas fa-certificate text-4xl"></i>
            </div>
            <h2 class="text-3xl font-[900] text-slate-900 tracking-tight">You're Live!</h2>
            <p class="text-slate-400 font-medium mt-4 leading-relaxed">Your professional profile is active. You are now visible to clients in your region.</p>
            <button onclick="document.getElementById('welcome-modal').remove()" class="w-full mt-10 py-5 bg-skill-blue text-white rounded-[1.5rem] font-black text-xs uppercase tracking-widest hover:bg-slate-900 transition-all shadow-xl">
                Launch Dashboard
            </button>
        </div>
    </div>
    <?php endif; ?>

</body>
</html>