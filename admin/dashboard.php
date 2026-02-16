<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: admin/dashboard.php
 * PURPOSE: Zero-Scroll "App-Feel" Layout with Internal Scroll Pockets
 */

session_start();

// 1. THE STEEL VAULT: Session Guard
if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../auth/login.php?error=unauthorized");
    exit();
}

// 2. System Initialization
$include_path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;
require_once $include_path . 'config.php';

// Setting HUD context for the admin/header.php
$page_title = "Command Center";
include 'header.php'; 

// 3. THE ANALYST: Fetch Pulse Data
$stats = [
    'users'    => $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0],
    'workers'  => $conn->query("SELECT COUNT(*) FROM users WHERE role='worker'")->fetch_row()[0],
    'pending'  => $conn->query("SELECT COUNT(*) FROM users WHERE status='pending'")->fetch_row()[0],
    'revenue'  => 0 
];

// 4. THE JUDGE: Fetch Moderation Queue
$recent_users = $conn->query("SELECT id, full_name, email, role, status, created_at FROM users ORDER BY created_at DESC LIMIT 15");
?>

<main class="flex h-[calc(100vh-80px)] overflow-hidden bg-[#F8FAFC]">

    <aside class="w-80 bg-slate-900 text-white flex flex-col h-full flex-none shadow-2xl z-40">
        <div class="p-10 border-b border-slate-800/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-skill-blue rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                    <i class="fas fa-screwdriver-wrench text-white"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black tracking-tighter uppercase leading-none">Skill<span class="text-skill-blue">Link</span></h2>
                    <p class="text-[9px] uppercase tracking-[0.3em] text-slate-500 font-bold mt-1">Admin Console</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 p-8 space-y-3 overflow-y-auto">
            <a href="dashboard.php" class="flex items-center gap-4 px-6 py-4 bg-skill-blue/10 text-skill-blue rounded-2xl font-bold transition-all border border-skill-blue/20">
                <i class="fas fa-grid-2 text-sm"></i> Dashboard
            </a>
            <a href="#" class="flex items-center gap-4 px-6 py-4 text-slate-400 hover:text-white hover:bg-slate-800/50 rounded-2xl font-bold transition-all group">
                <i class="fas fa-user-check group-hover:text-skill-blue text-sm"></i> Verification
            </a>
            <a href="#" class="flex items-center gap-4 px-6 py-4 text-slate-400 hover:text-white hover:bg-slate-800/50 rounded-2xl font-bold transition-all group">
                <i class="fas fa-layer-group group-hover:text-skill-blue text-sm"></i> Skill Tree
            </a>
            <a href="#" class="flex items-center gap-4 px-6 py-4 text-slate-400 hover:text-white hover:bg-slate-800/50 rounded-2xl font-bold transition-all group">
                <i class="fas fa-chart-line group-hover:text-skill-blue text-sm"></i> Reports
            </a>
        </nav>

        <div class="p-8 border-t border-slate-800/50 bg-slate-900/50">
            <a href="../auth/logout.php" class="flex items-center justify-center gap-3 w-full py-4 bg-red-500/10 text-red-500 rounded-2xl font-bold hover:bg-red-500 hover:text-white transition-all duration-300">
                <i class="fas fa-power-off"></i> Logout
            </a>
        </div>
    </aside>

    <section class="flex-1 h-full overflow-y-auto scroll-smooth">
        
        <div class="p-10 space-y-10 pb-20"> <div class="flex justify-between items-end">
                <div>
                    <span class="text-xs font-black text-skill-blue uppercase tracking-widest">Platform Architect</span>
                    <h1 class="text-4xl font-black tracking-tight mt-1 text-slate-900">System Oversight</h1>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Node Status</p>
                    <div class="flex items-center justify-end gap-2 mt-1">
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-ping"></span>
                        <span class="text-xs font-black text-green-600">OPERATIONAL</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 relative overflow-hidden group hover:shadow-xl transition-all duration-500">
                    <p class="text-[10px] uppercase tracking-[0.2em] text-slate-400 font-black mb-4">Total Users</p>
                    <h3 class="text-4xl font-black text-slate-900"><?php echo $stats['users']; ?></h3>
                    <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity"><i class="fas fa-users text-9xl"></i></div>
                </div>

                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 relative overflow-hidden group hover:shadow-xl transition-all duration-500">
                    <p class="text-[10px] uppercase tracking-[0.2em] text-slate-400 font-black mb-4">Active Pros</p>
                    <h3 class="text-4xl font-black text-skill-blue"><?php echo $stats['workers']; ?></h3>
                    <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity text-skill-blue"><i class="fas fa-user-gear text-9xl"></i></div>
                </div>

                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 relative overflow-hidden group hover:shadow-xl transition-all duration-500">
                    <p class="text-[10px] uppercase tracking-[0.2em] text-slate-400 font-black mb-4">Pending Trust</p>
                    <h3 class="text-4xl font-black text-amber-500"><?php echo $stats['pending']; ?></h3>
                    <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity text-amber-500"><i class="fas fa-shield-halved text-9xl"></i></div>
                </div>

                <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-2xl shadow-slate-900/20 text-white flex flex-col justify-center">
                    <p class="text-[10px] uppercase tracking-[0.2em] text-slate-500 font-black mb-2 text-center">Live Pulse</p>
                    <div class="flex justify-center gap-1.5 h-8 items-end">
                        <div class="w-1.5 h-4 bg-skill-blue rounded-full animate-[bounce_1s_infinite_100ms]"></div>
                        <div class="w-1.5 h-6 bg-skill-blue rounded-full animate-[bounce_1s_infinite_200ms]"></div>
                        <div class="w-1.5 h-5 bg-skill-blue rounded-full animate-[bounce_1s_infinite_300ms]"></div>
                        <div class="w-1.5 h-8 bg-skill-blue rounded-full animate-[bounce_1s_infinite_400ms]"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[3.5rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-10 py-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
                    <div>
                        <h2 class="text-xl font-black text-slate-900">Recent registrations</h2>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Verification Queue</p>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] uppercase tracking-[0.3em] text-slate-400 font-black bg-slate-50/20">
                                <th class="px-10 py-6">User Identity</th>
                                <th class="px-10 py-6">Classification</th>
                                <th class="px-10 py-6">Status</th>
                                <th class="px-10 py-6 text-right">Moderation</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php while($row = $recent_users->fetch_assoc()): ?>
                            <tr class="hover:bg-slate-50/50 transition-all group">
                                <td class="px-10 py-7">
                                    <div class="flex items-center gap-5">
                                        <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center font-black text-slate-400 group-hover:bg-skill-blue group-hover:text-white transition-all duration-300">
                                            <?php echo strtoupper(substr($row['full_name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 leading-none mb-1"><?php echo $row['full_name']; ?></p>
                                            <p class="text-xs text-slate-400 font-medium"><?php echo $row['email']; ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-10 py-7">
                                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-slate-100 rounded-lg">
                                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-600"><?php echo $row['role']; ?></span>
                                    </div>
                                </td>
                                <td class="px-10 py-7">
                                    <span class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-[0.15em] 
                                        <?php echo $row['status'] === 'active' ? 'bg-green-100 text-green-600' : 'bg-amber-100 text-amber-600'; ?>">
                                        ● <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td class="px-10 py-7 text-right">
                                    <button class="w-10 h-10 bg-slate-50 rounded-xl text-slate-400 hover:bg-skill-blue hover:text-white transition-all"><i class="fas fa-eye text-xs"></i></button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
<?php include 'footer.php'; ?>