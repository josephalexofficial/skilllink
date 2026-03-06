<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: worker/view-task.php
 * PURPOSE: High-Fidelity Task Inspection & Booking Interface
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

// 3. Logic Gate: Task Retrieval
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$task_id = mysqli_real_escape_string($conn, $_GET['id']);
$user_id = $_SESSION['user_id'];

// Fetch surgical task details with Client and Category joins
$task_query = $conn->query("SELECT t.*, u.full_name as client_name, c.cat_name 
                            FROM tasks t 
                            JOIN users u ON t.client_id = u.id 
                            JOIN categories c ON t.category_id = c.id 
                            WHERE t.id = '$task_id' AND t.status = 'open'");

if ($task_query->num_rows == 0) {
    // Task might be already assigned or cancelled
    header("Location: dashboard.php?status=error");
    exit();
}

$task = $task_query->fetch_assoc();
$user_name = $_SESSION['full_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inspect Task | SkillLink Pro</title>
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
        
        /* SIDEBAR ANCHOR CONSISTENCY */
        .active-nav { background: white; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); color: skill-blue !important; border-radius: 1.25rem; }

        .blueprint-bg {
            background-image: radial-gradient(#e2e8f0 0.5px, transparent 0.5px);
            background-size: 24px 24px;
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
                    <a href="dashboard.php" class="active-nav flex items-center gap-4 px-6 py-4 rounded-2xl font-black text-sm transition-all text-skill-blue">
                        <i class="fas fa-chart-line text-xs"></i> Overview
                    </a>
                    <a href="manage-tasks.php" class="flex items-center gap-4 px-6 py-4 text-slate-500 hover:text-skill-blue transition-all rounded-2xl font-bold text-sm">
                        <i class="fas fa-list-check text-xs"></i> Active Tasks
                    </a>
                    <a href="#" class="flex items-center gap-4 px-6 py-4 text-slate-500 hover:text-skill-blue transition-all rounded-2xl font-bold text-sm">
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
                <div class="flex items-center gap-6">
                    <a href="dashboard.php" class="w-10 h-10 bg-slate-50 text-slate-400 rounded-xl flex items-center justify-center hover:bg-slate-900 hover:text-white transition-all shadow-sm">
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                    <div>
                        <h1 class="text-lg font-black text-slate-900 leading-none">Task Inspection</h1>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-1">Review blueprint before commitment</p>
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Authenticated Pro</p>
                        <p class="font-black text-slate-900"><?php echo $user_name; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-slate-900 rounded-2xl overflow-hidden border-2 border-white shadow-xl">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=0f172a&color=fff" alt="User">
                    </div>
                </div>
            </header>

            <section class="flex-1 overflow-y-auto p-12 custom-scroll bg-[#f1f5f9]/30">
                
                <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-12">
                    
                    <div class="lg:col-span-2 space-y-8">
                        <div class="elite-card rounded-[3.5rem] overflow-hidden blueprint-bg">
                            <div class="bg-slate-900 p-10 text-white flex items-center justify-between">
                                <div class="space-y-2">
                                    <span class="px-4 py-1.5 bg-skill-blue rounded-full text-[10px] font-black uppercase tracking-widest"><?php echo $task['cat_name']; ?> Specialist Required</span>
                                    <h2 class="text-3xl font-black tracking-tight"><?php echo $task['title']; ?></h2>
                                </div>
                                <div class="text-right">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Posted</p>
                                    <p class="text-sm font-bold"><?php echo date('M d, Y', strtotime($task['created_at'])); ?></p>
                                </div>
                            </div>

                            <div class="p-12 space-y-12">
                                <div>
                                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em] mb-6 flex items-center gap-3">
                                        <i class="fas fa-align-left text-skill-blue"></i> Detailed Scope
                                    </h3>
                                    <p class="text-slate-600 leading-relaxed font-medium text-lg italic">
                                        "<?php echo nl2br($task['description']); ?>"
                                    </p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-10 border-t border-slate-100">
                                    <div class="flex items-start gap-5">
                                        <div class="w-12 h-12 bg-slate-50 text-skill-blue rounded-2xl flex items-center justify-center shadow-inner">
                                            <i class="fas fa-map-location-dot text-lg"></i>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Service Territory</p>
                                            <p class="text-base font-black text-slate-900"><?php echo $task['location_name']; ?></p>
                                            <p class="text-[10px] font-bold text-green-500 mt-1 uppercase">✓ Within Your Radius</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-5">
                                        <div class="w-12 h-12 bg-slate-50 text-amber-500 rounded-2xl flex items-center justify-center shadow-inner">
                                            <i class="fas fa-wallet text-lg"></i>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Financial Offer</p>
                                            <p class="text-base font-black text-slate-900">Ksh <?php echo number_format($task['budget'], 2); ?></p>
                                            <span class="inline-block mt-1 px-3 py-0.5 bg-slate-100 rounded-md text-[9px] font-black uppercase text-slate-500"><?php echo $task['budget_type']; ?> Budget</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div class="bg-white/80 backdrop-blur-xl elite-card p-10 rounded-[4rem] text-center relative overflow-hidden border-white">
                            <div class="absolute top-0 right-0 p-8 opacity-5 text-skill-blue"><i class="fas fa-handshake-simple text-7xl"></i></div>
                            
                            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-8">Contracting Client</h3>
                            <div class="w-24 h-24 bg-slate-900 rounded-[2.5rem] mx-auto mb-6 border-4 border-slate-50 shadow-2xl overflow-hidden">
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($task['client_name']); ?>&background=0f172a&color=fff" class="w-full h-full object-cover">
                            </div>
                            <h4 class="text-xl font-black text-slate-900 tracking-tight"><?php echo $task['client_name']; ?></h4>
                            <div class="inline-block mt-3 px-6 py-2 bg-green-50 rounded-full border border-green-100">
                                <span class="text-[10px] text-green-600 font-black uppercase tracking-widest">Verified Requester</span>
                            </div>

                            <div class="mt-12 pt-8 border-t border-slate-50 space-y-6">
                                <p class="text-xs text-slate-400 font-medium leading-relaxed">By accepting, you commit to providing professional services as described in the blueprint across Kenya.</p>
                                
                                <form action="accept-logic.php" method="POST">
                                    <input type="hidden" name="task_id" value="<?php echo $task_id; ?>">
                                    <button type="submit" class="w-full py-6 bg-slate-900 text-white rounded-[1.75rem] font-black text-xs uppercase tracking-[0.2em] hover:bg-skill-blue transition-all shadow-2xl active:scale-95 group">
                                        Commit to Task <i class="fas fa-bolt-lightning ml-3 group-hover:text-amber-400"></i>
                                    </button>
                                </form>
                                
                                <button class="w-full py-4 bg-transparent text-slate-400 font-black text-[10px] uppercase tracking-widest hover:text-slate-900 transition-colors">
                                    Request Clarification
                                </button>
                            </div>
                        </div>

                        <div class="bg-amber-50/50 border border-amber-100 p-8 rounded-[3rem] flex items-start gap-4">
                            <i class="fas fa-shield-halved text-amber-500 mt-1"></i>
                            <div>
                                <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-1">Safety Protocol</p>
                                <p class="text-[11px] font-bold text-amber-700 leading-tight">Always verify work details and safety conditions on-site before starting specialized crafts.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </section>
        </main>
    </div>
</body>
</html>