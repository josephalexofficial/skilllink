<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: client/dashboard.php
 * PURPOSE: High-Fidelity Client Command Center
 * REFINEMENTS: Zero-Scroll Architecture, Smart Search, and Role-Gate Security.
 */

// 1. Security Guard: The Access Control Node
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'client') {
    header("Location: ../auth/login.php");
    exit();
}

// 2. System Initialization
$base_dir = dirname(__DIR__);
require_once $base_dir . '/includes/config.php';

// 3. Page Identity
$current_page = 'dashboard';
$user_name = $_SESSION['full_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard | SkillLink</title>
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
        .glass-sidebar { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(20px); }
        .custom-scroll::-webkit-scrollbar { width: 5px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .active-nav { background: #f1f5f9; color: <?php echo BRAND_COLOR; ?>; border-right: 4px solid <?php echo BRAND_COLOR; ?>; }
    </style>
</head>
<body class="bg-[#f8fafc] font-sans antialiased text-slate-900 overflow-hidden">

    <div class="flex h-screen overflow-hidden">
        
        <aside class="w-72 glass-sidebar border-r border-slate-200 flex flex-col z-50">
            <div class="p-8">
                <div class="flex items-center gap-3 mb-10">
                    <div class="w-10 h-10 bg-skill-blue text-white rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                        <i class="fas fa-screwdriver-wrench"></i>
                    </div>
                    <span class="text-xl font-black tracking-tighter">Skill<span class="text-skill-blue">Link</span></span>
                </div>

                <nav class="space-y-2">
                    <a href="#" class="active-nav flex items-center gap-4 px-6 py-4 rounded-2xl font-bold text-sm transition-all">
                        <i class="fas fa-grid-2"></i> Dashboard
                    </a>
                    <a href="#" class="flex items-center gap-4 px-6 py-4 text-slate-500 hover:text-skill-blue hover:bg-slate-50 rounded-2xl font-bold text-sm transition-all">
                        <i class="fas fa-magnifying-glass"></i> Find Pros
                    </a>
                    <a href="#" class="flex items-center gap-4 px-6 py-4 text-slate-500 hover:text-skill-blue hover:bg-slate-50 rounded-2xl font-bold text-sm transition-all">
                        <i class="fas fa-calendar-check"></i> My Bookings
                    </a>
                    <a href="#" class="flex items-center gap-4 px-6 py-4 text-slate-500 hover:text-skill-blue hover:bg-slate-50 rounded-2xl font-bold text-sm transition-all">
                        <i class="fas fa-clock-rotate-left"></i> History
                    </a>
                </nav>
            </div>

            <div class="mt-auto p-8 border-t border-slate-100">
                <a href="../auth/logout.php" class="flex items-center gap-4 px-6 py-4 text-red-500 hover:bg-red-50 rounded-2xl font-bold text-sm transition-all">
                    <i class="fas fa-right-from-bracket"></i> Logout
                </a>
            </div>
        </aside>

        <main class="flex-1 flex flex-col overflow-hidden relative">
            
            <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-skill-blue/5 rounded-full blur-[120px] -z-10"></div>
            
            <header class="h-24 flex items-center justify-between px-12 bg-white/50 backdrop-blur-md border-b border-slate-100">
                <div>
                    <h1 class="text-2xl font-[900] tracking-tight text-slate-900">Jambo, <?php echo explode(' ', $user_name)[0]; ?>!</h1>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Ready to solve a project today?</p>
                </div>

                <div class="flex items-center gap-6">
                    <div class="relative group cursor-pointer">
                        <div class="w-12 h-12 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-slate-600 shadow-sm group-hover:border-skill-blue transition-all">
                            <i class="fas fa-bell"></i>
                        </div>
                        <span class="absolute top-3 right-3 w-2.5 h-2.5 bg-red-500 border-2 border-white rounded-full"></span>
                    </div>
                    <div class="w-12 h-12 bg-slate-900 rounded-2xl overflow-hidden shadow-xl border-2 border-white">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=0f172a&color=fff" alt="User">
                    </div>
                </div>
            </header>

            <section class="flex-1 overflow-y-auto p-12 custom-scroll">
                
                <div class="bg-slate-900 rounded-[3rem] p-10 mb-12 relative overflow-hidden shadow-2xl shadow-slate-900/20">
                    <div class="relative z-10 flex flex-col md:flex-row items-center gap-8">
                        <div class="flex-1 space-y-2">
                            <h2 class="text-3xl font-black text-white leading-tight">What expert do you need?</h2>
                            <p class="text-slate-400 font-medium">Instantly match with verified pros in your area.</p>
                        </div>
                        <div class="flex-1 w-full relative">
                            <i class="fas fa-magnifying-glass absolute left-6 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" placeholder="Search for Plumbers, Electricians..." 
                                   class="w-full bg-white/10 border border-white/10 rounded-3xl py-6 pl-16 pr-8 text-white font-bold placeholder:text-slate-500 focus:outline-none focus:ring-4 focus:ring-skill-blue/20 transition-all">
                        </div>
                    </div>
                    <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-skill-blue/20 rounded-full blur-3xl"></div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    
                    <div class="lg:col-span-2 space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-black text-slate-900">Active Bookings</h3>
                            <a href="#" class="text-xs font-black text-skill-blue uppercase tracking-widest hover:underline">View All</a>
                        </div>
                        
                        <div class="bg-white border-2 border-dashed border-slate-200 rounded-[2.5rem] p-12 text-center space-y-4">
                            <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto text-2xl">
                                <i class="fas fa-calendar-plus"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-slate-900">No Active Requests</h4>
                                <p class="text-sm text-slate-400 font-medium">Your project requests will appear here once submitted.</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-black text-slate-900">Top Pros Near You</h3>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-14 h-14 rounded-2xl overflow-hidden bg-slate-100">
                                        <img src="https://i.pravatar.cc/150?u=tech" alt="Pro">
                                    </div>
                                    <div>
                                        <h5 class="font-black text-slate-900 group-hover:text-skill-blue transition-colors">Brian Ouma</h5>
                                        <div class="flex items-center gap-2">
                                            <span class="bg-green-100 text-green-600 text-[10px] font-black px-2 py-0.5 rounded-md uppercase">Electrician</span>
                                            <span class="text-xs text-slate-400 font-bold"><i class="fas fa-star text-amber-400"></i> 4.9</span>
                                        </div>
                                    </div>
                                </div>
                                <button class="w-full py-4 bg-slate-50 text-slate-900 rounded-2xl font-black text-sm group-hover:bg-slate-900 group-hover:text-white transition-all">
                                    View Profile
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </section>
        </main>
    </div>
</body>
</html>