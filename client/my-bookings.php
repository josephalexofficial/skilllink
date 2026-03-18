<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: client/my-bookings.php
 * PURPOSE: Elite Mission Tracker - Project Management & Verification Hub
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

// 3. Data Retrieval: The Solution Ledger
// Fetch all active/completed/open bookings for this client
$bookings_query = $conn->query("SELECT t.*, u.full_name as worker_name, c.cat_name 
                                FROM tasks t 
                                LEFT JOIN users u ON t.worker_id = u.id 
                                JOIN categories c ON t.category_id = c.id 
                                WHERE t.client_id = '$client_id' 
                                AND t.status IN ('open', 'assigned', 'in_progress', 'completed')
                                ORDER BY t.updated_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings | SkillLink Command</title>
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

        /* PULSE ANIMATIONS */
        .pulse-blue { animation: pulse-blue 2s infinite; }
        @keyframes pulse-blue { 0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); } 70% { box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); } 100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); } }
        
        .verify-glow { border: 2px solid #22c55e; box-shadow: 0 0 20px rgba(34, 197, 94, 0.2); }
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
                    <a href="my-bookings.php" class="active-nav flex items-center gap-4 px-6 py-4 rounded-2xl font-black text-sm transition-all text-skill-blue">
                        <i class="fas fa-calendar-check text-xs"></i> My Bookings
                    </a>
                    <a href="activity-log.php" class="flex items-center gap-4 px-6 py-4 text-slate-400 hover:text-skill-blue transition-all rounded-2xl font-bold text-sm">
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
                    <h1 class="text-xl font-black text-slate-900 leading-none">My Bookings</h1>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-2">Active Mission Control</p>
                </div>

                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Client Account</p>
                        <p class="font-black text-slate-900"><?php echo $user_name; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-slate-900 rounded-2xl overflow-hidden border-2 border-white shadow-xl">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=0f172a&color=fff" alt="User">
                    </div>
                </div>
            </header>

            <section class="flex-1 overflow-y-auto p-12 custom-scroll bg-[#f1f5f9]/30">
                <div class="max-w-6xl mx-auto space-y-12">
                    
                    <?php if($bookings_query->num_rows > 0): ?>
                        <div class="grid grid-cols-1 gap-8 pb-20">
                            <?php while($booking = $bookings_query->fetch_assoc()): ?>
                                
                                <div class="elite-card rounded-[3.5rem] overflow-hidden <?php echo ($booking['status'] == 'completed') ? 'verify-glow' : ''; ?>">
                                    <div class="flex flex-col md:flex-row">
                                        
                                        <div class="flex-1 p-10 space-y-6">
                                            <div class="flex items-center gap-4">
                                                <span class="px-3 py-1 bg-slate-100 text-slate-500 text-[9px] font-black uppercase rounded-md tracking-widest"><?php echo $booking['cat_name']; ?></span>
                                                
                                                <?php if($booking['status'] == 'completed'): ?>
                                                    <span class="px-3 py-1 bg-green-100 text-green-600 text-[9px] font-black uppercase rounded-md tracking-widest flex items-center gap-2">
                                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-ping"></span> Verification Needed
                                                    </span>
                                                <?php elseif($booking['status'] == 'in_progress'): ?>
                                                    <span class="px-3 py-1 bg-amber-50 text-amber-600 text-[9px] font-black uppercase rounded-md tracking-widest">In Progress</span>
                                                <?php elseif($booking['status'] == 'assigned'): ?>
                                                    <span class="px-3 py-1 bg-blue-50 text-skill-blue text-[9px] font-black uppercase rounded-md tracking-widest">Pro En Route</span>
                                                <?php else: ?>
                                                    <span class="px-3 py-1 bg-slate-50 text-slate-400 text-[9px] font-black uppercase rounded-md tracking-widest">Searching for Pros</span>
                                                <?php endif; ?>
                                            </div>

                                            <h2 class="text-2xl font-black text-slate-900 tracking-tight"><?php echo $booking['title']; ?></h2>
                                            
                                            <div class="flex items-center gap-8 pt-2">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 bg-skill-blue/10 text-skill-blue rounded-lg flex items-center justify-center text-xs"><i class="fas fa-map-pin"></i></div>
                                                    <p class="text-[11px] font-black text-slate-900 uppercase tracking-tight"><?php echo $booking['location_name']; ?></p>
                                                </div>
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 bg-green-50 text-green-600 rounded-lg flex items-center justify-center text-xs"><i class="fas fa-wallet"></i></div>
                                                    <p class="text-[11px] font-black text-slate-900 uppercase tracking-tight">Ksh <?php echo number_format($booking['budget'], 2); ?></p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="w-full md:w-96 bg-slate-50 border-l border-slate-100 p-10 flex flex-col justify-center">
                                            <?php if($booking['worker_id']): ?>
                                                <div class="flex items-center gap-4 mb-8">
                                                    <div class="w-12 h-12 rounded-2xl overflow-hidden border-2 border-white shadow-lg">
                                                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($booking['worker_name']); ?>&background=0f172a&color=fff" class="w-full h-full object-cover">
                                                    </div>
                                                    <div>
                                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Assigned Pro</p>
                                                        <p class="text-sm font-black text-slate-900"><?php echo $booking['worker_name']; ?></p>
                                                    </div>
                                                </div>

                                                <div class="space-y-3">
                                                    <?php if($booking['status'] == 'completed'): ?>
                                                        <button onclick="openReviewModal(<?php echo $booking['id']; ?>, '<?php echo $booking['worker_name']; ?>')" 
                                                                class="block w-full py-4 bg-green-600 text-white rounded-2xl text-center font-black text-[10px] uppercase tracking-widest hover:bg-green-700 transition-all shadow-xl">
                                                            Finalize & Rate <i class="fas fa-star ml-2"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <a href="https://wa.me/254123456789" target="_blank" class="block w-full py-4 bg-slate-900 text-white rounded-2xl text-center font-black text-[10px] uppercase tracking-widest hover:bg-skill-blue transition-all shadow-xl">
                                                            Contact via WhatsApp <i class="fab fa-whatsapp ml-2"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="text-center py-4">
                                                    <i class="fas fa-satellite-dish text-slate-200 text-3xl mb-4 animate-pulse"></i>
                                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Scanning for available pros...</p>
                                                    <button class="mt-4 text-[10px] font-black text-red-400 uppercase tracking-widest hover:underline">Cancel Request</button>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="bg-white rounded-[4rem] p-24 text-center border-4 border-dashed border-slate-100 group transition-all">
                            <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner">
                                <i class="fas fa-calendar-xmark text-slate-200 text-4xl animate-pulse"></i>
                            </div>
                            <h4 class="text-2xl font-black text-slate-900 tracking-tight">No Active Missions</h4>
                            <p class="text-sm text-slate-400 font-medium max-w-sm mx-auto mt-4 leading-relaxed">You haven't booked any professionals yet. Launch a new project to solve your next problem.</p>
                            <a href="launch-project.php" class="inline-flex mt-8 px-10 py-5 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-skill-blue shadow-2xl transition-all">
                                Launch Project <i class="fas fa-arrow-right ml-3"></i>
                            </a>
                        </div>
                    <?php endif; ?>

                </div>
            </section>
        </main>
    </div>

    <div id="review-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/60 backdrop-blur-md px-6">
        <div class="bg-white p-14 rounded-[4rem] shadow-2xl text-center max-w-md w-full elite-card">
            <h2 class="text-3xl font-black text-slate-900 tracking-tight leading-none">Mission Complete!</h2>
            <p class="text-slate-400 font-medium mt-4 leading-relaxed italic">"Please rate <span id="modal-worker-name" class="font-black text-slate-900"></span>'s professional service."</p>
            
            <form action="finalize-logic.php" method="POST" class="mt-10 space-y-8">
                <input type="hidden" name="task_id" id="modal-task-id">
                
                <div class="flex justify-center gap-4 text-3xl text-slate-200">
                    <button type="button" onclick="setRating(1)" class="star-btn hover:scale-125 transition-transform"><i class="fas fa-star"></i></button>
                    <button type="button" onclick="setRating(2)" class="star-btn hover:scale-125 transition-transform"><i class="fas fa-star"></i></button>
                    <button type="button" onclick="setRating(3)" class="star-btn hover:scale-125 transition-transform"><i class="fas fa-star"></i></button>
                    <button type="button" onclick="setRating(4)" class="star-btn hover:scale-125 transition-transform"><i class="fas fa-star"></i></button>
                    <button type="button" onclick="setRating(5)" class="star-btn hover:scale-125 transition-transform"><i class="fas fa-star"></i></button>
                    <input type="hidden" name="rating" id="rating-input" value="0">
                </div>

                <textarea name="comment" placeholder="Leave a professional note (optional)..." class="w-full p-6 bg-slate-50 border border-slate-100 rounded-[1.5rem] text-sm focus:outline-none focus:ring-2 focus:ring-skill-blue h-32 custom-scroll"></textarea>

                <div class="flex gap-4">
                    <button type="button" onclick="closeReviewModal()" class="flex-1 py-6 bg-slate-100 text-slate-500 rounded-[1.75rem] font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition-all">Cancel</button>
                    <button type="submit" class="flex-1 py-6 bg-skill-blue text-white rounded-[1.75rem] font-black text-xs uppercase tracking-widest hover:bg-slate-900 transition-all shadow-xl">Finalize</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openReviewModal(taskId, workerName) {
            document.getElementById('modal-task-id').value = taskId;
            document.getElementById('modal-worker-name').textContent = workerName;
            document.getElementById('review-modal').classList.remove('hidden');
            document.getElementById('review-modal').classList.add('flex');
        }

        function closeReviewModal() {
            document.getElementById('review-modal').classList.add('hidden');
            document.getElementById('review-modal').classList.remove('flex');
        }

        function setRating(val) {
            document.getElementById('rating-input').value = val;
            const stars = document.querySelectorAll('.star-btn');
            stars.forEach((star, index) => {
                if (index < val) star.classList.add('text-amber-400');
                else star.classList.remove('text-amber-400');
            });
        }
    </script>

</body>
</html>