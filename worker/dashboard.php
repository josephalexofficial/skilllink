<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: worker/dashboard.php
 * PURPOSE: High-Performance Professional Business Suite
 * REFINEMENTS: Role-Gate Security, Earnings Analytics, and Live Opportunity Feed.
 */

// 1. Security Guard: The Access Control Node
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'worker') {
    header("Location: ../auth/login.php");
    exit();
}

// 2. System Initialization
$base_dir = dirname(__DIR__);
require_once $base_dir . '/includes/config.php';

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
                    colors: { 'skill-blue': '<?php echo BRAND_COLOR; ?>' }
                }
            }
        }
    </script>
    <style>
        .glass-sidebar { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(20px); }
        .custom-scroll::-webkit-scrollbar { width: 6px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .status-online { color: #10b981; }
        .active-nav { background: #f8fafc; color: <?php echo BRAND_COLOR; ?>; border-right: 4px solid <?php echo BRAND_COLOR; ?>; }
    </style>
</head>
<body class="bg-[#f1f5f9] font-sans antialiased text-slate-900 overflow-hidden">

    <div class="flex h-screen overflow-hidden">
        
        <aside class="w-72 glass-sidebar border-r border-slate-200 flex flex-col z-50">
            <div class="p-8">
                <div class="flex items-center gap-3 mb-10">
                    <div class="w-10 h-10 bg-slate-900 text-white rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <span class="text-xl font-black tracking-tighter">Skill<span class="text-skill-blue">Link</span></span>
                </div>

                <nav class="space-y-2">
                    <a href="#" class="active-nav flex items-center gap-4 px-6 py-4 rounded-2xl font-bold text-sm transition-all">
                        <i class="fas fa-chart-line"></i> Overview
                    </a>
                    <a href="#" class="flex items-center gap-4 px-6 py-4 text-slate-500 hover:text-skill-blue hover:bg-slate-50 rounded-2xl font-bold text-sm transition-all">
                        <i class="fas fa-list-check"></i> Active Tasks
                    </a>
                    <a href="#" class="flex items-center gap-4 px-6 py-4 text-slate-500 hover:text-skill-blue hover:bg-slate-50 rounded-2xl font-bold text-sm transition-all">
                        <i class="fas fa-wallet"></i> Earnings
                    </a>
                    <a href="#" class="flex items-center gap-4 px-6 py-4 text-slate-500 hover:text-skill-blue hover:bg-slate-50 rounded-2xl font-bold text-sm transition-all">
                        <i class="fas fa-user-gear"></i> Profile Setup
                    </a>
                </nav>
            </div>

            <div class="mt-auto p-8 border-t border-slate-100">
                <a href="../auth/logout.php" class="flex items-center gap-4 px-6 py-4 text-red-500 hover:bg-red-50 rounded-2xl font-bold text-sm transition-all">
                    <i class="fas fa-power-off"></i> Sign Out
                </a>
            </div>
        </aside>

        <main class="flex-1 flex flex-col overflow-hidden relative">
            
            <header class="h-24 flex items-center justify-between px-12 bg-white border-b border-slate-100">
                <div class="flex items-center gap-4">
                    <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                    <div>
                        <h1 class="text-xl font-black text-slate-900">Live Status: <span class="text-green-500">Online</span></h1>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Receiving jobs in your area</p>
                    </div>
                </div>

                <div class="flex items-center gap-8">
                    <div class="text-right hidden md:block">
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Master Pro</p>
                        <p class="font-black text-slate-900"><?php echo $user_name; ?></p>
                    </div>
                    <div class="w-14 h-14 bg-slate-100 rounded-2xl overflow-hidden border-2 border-white shadow-xl">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=3b82f6&color=fff" alt="User">
                    </div>
                </div>
            </header>

            <section class="flex-1 overflow-y-auto p-12 custom-scroll">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Total Revenue</p>
                        <h3 class="text-3xl font-[900] text-slate-900">Ksh 0.00</h3>
                        <div class="mt-4 flex items-center gap-2 text-green-500 font-bold text-xs">
                            <i class="fas fa-arrow-up"></i> 0% this month
                        </div>
                    </div>
                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Jobs Completed</p>
                        <h3 class="text-3xl font-[900] text-slate-900">0</h3>
                        <p class="mt-4 text-slate-400 font-bold text-xs italic">Start your first task today!</p>
                    </div>
                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-900 bg-slate-900 text-white shadow-2xl">
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Pro Rating</p>
                        <h3 class="text-3xl font-[900] flex items-center gap-3">5.0 <i class="fas fa-star text-amber-400 text-xl"></i></h3>
                        <p class="mt-4 text-slate-500 font-bold text-xs uppercase tracking-widest">New Profile</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    
                    <div class="lg:col-span-2 space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-black text-slate-900">Opportunity Feed</h3>
                            <span class="bg-skill-blue/10 text-skill-blue text-[10px] font-black px-3 py-1 rounded-full uppercase">Real-time</span>
                        </div>
                        
                        <div class="bg-white rounded-[3rem] p-16 text-center border-2 border-dashed border-slate-200">
                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-satellite-dish text-slate-300 text-3xl animate-pulse"></i>
                            </div>
                            <h4 class="text-xl font-black text-slate-900">Scanning for requests...</h4>
                            <p class="text-slate-400 font-medium max-w-xs mx-auto mt-2">New jobs in your area will appear here the moment they are posted.</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <h3 class="text-xl font-black text-slate-900">Profile Completion</h3>
                        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                            <div class="relative w-32 h-32 mx-auto mb-6">
                                <svg class="w-full h-full transform -rotate-90">
                                    <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="8" fill="transparent" class="text-slate-100" />
                                    <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="8" fill="transparent" class="text-skill-blue" stroke-dasharray="364.4" stroke-dashoffset="273.3" stroke-linecap="round" />
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-xl font-black">25%</span>
                                </div>
                            </div>
                            <ul class="space-y-4">
                                <li class="flex items-center gap-3 text-sm font-bold text-green-500">
                                    <i class="fas fa-check-circle"></i> Account Created
                                </li>
                                <li class="flex items-center gap-3 text-sm font-bold text-slate-300">
                                    <i class="far fa-circle"></i> Add Bio & Portfolio
                                </li>
                                <li class="flex items-center gap-3 text-sm font-bold text-slate-300">
                                    <i class="far fa-circle"></i> Upload Identity Docs
                                </li>
                            </ul>
                            <button class="w-full mt-8 py-4 bg-slate-900 text-white rounded-2xl font-black text-sm hover:bg-skill-blue transition-all">
                                Complete Profile
                            </button>
                        </div>
                    </div>

                </div>
            </section>
        </main>
    </div>
</body>
</html>