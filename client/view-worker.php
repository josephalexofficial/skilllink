<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: client/view-worker.php
 * PURPOSE: Elite Worker Profile & Verification Engine - Trust & Social Proof
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

// 3. Surgical Target Identification
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$worker_user_id = mysqli_real_escape_string($conn, $_GET['id']);
$user_name = $_SESSION['full_name'];

// 4. Data Retrieval: The Professional Blueprint
// Fetching Worker Profile & Identity
$worker_query = $conn->query("SELECT wp.*, u.full_name, u.phone, c.cat_name 
                              FROM worker_profiles wp 
                              JOIN users u ON wp.user_id = u.id 
                              JOIN categories c ON wp.category_id = c.id 
                              WHERE wp.user_id = '$worker_user_id'");

if ($worker_query->num_rows === 0) {
    die("Professional identity not found.");
}
$worker = $worker_query->fetch_assoc();

// 5. The Reputation Engine: Live Stats
$stats_query = $conn->query("SELECT 
                                COUNT(id) AS total_reviews, 
                                IFNULL(AVG(rating), 0) AS avg_rating 
                             FROM reviews 
                             WHERE worker_id = '$worker_user_id'");
$stats = $stats_query->fetch_assoc();

// Count finalized missions for extra trust
$mission_query = $conn->query("SELECT COUNT(id) as success_count FROM tasks WHERE worker_id = '$worker_user_id' AND status = 'finalized'");
$missions = $mission_query->fetch_assoc();

// 6. The Review Registry: Social Proof Feed
$reviews_feed = $conn->query("SELECT r.*, u.full_name as reviewer_name 
                              FROM reviews r 
                              JOIN users u ON r.client_id = u.id 
                              WHERE r.worker_id = '$worker_user_id' 
                              ORDER BY r.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $worker['full_name']; ?> | SkillLink Pro</title>
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
        
        .vault-glass {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.98) 100%);
            backdrop-filter: blur(40px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .recessed-row { 
            background: #fdfdfd; 
            border: 1px solid rgba(0, 0, 0, 0.03); 
            box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.01);
        }
        
        /* SIDEBAR ANCHOR */
        .active-nav { background: white; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); color: skill-blue !important; border-radius: 1.25rem; }
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
                <div class="flex items-center gap-4">
                    <a href="javascript:history.back()" class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-slate-900 hover:text-white transition-all">
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                    <div>
                        <h1 class="text-xl font-black text-slate-900 leading-none">Professional Profile</h1>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-2">Verified SkillLink Identity</p>
                    </div>
                </div>
            </header>

            <section class="flex-1 overflow-y-auto p-12 custom-scroll bg-[#f1f5f9]/30">
                <div class="max-w-6xl mx-auto space-y-12 pb-24">
                    
                    <div class="vault-glass rounded-[4rem] p-12 text-white relative overflow-hidden shadow-2xl">
                        <div class="absolute -top-20 -right-20 w-80 h-80 bg-skill-blue/20 rounded-full blur-[100px]"></div>
                        <div class="relative z-10 flex flex-col md:flex-row items-center gap-10">
                            <div class="w-40 h-40 bg-slate-900 rounded-[3rem] border-4 border-white/10 overflow-hidden shadow-2xl relative group">
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($worker['full_name']); ?>&background=0f172a&color=fff&size=200" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-skill-blue/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </div>

                            <div class="flex-1 space-y-4 text-center md:text-left">
                                <div class="flex flex-wrap justify-center md:justify-start items-center gap-3">
                                    <h2 class="text-4xl font-black tracking-tighter"><?php echo $worker['full_name']; ?></h2>
                                    <span class="px-4 py-1.5 bg-skill-blue text-[10px] font-black uppercase rounded-full tracking-widest flex items-center gap-2">
                                        <i class="fas fa-shield-check"></i> Verified Elite
                                    </span>
                                </div>
                                <p class="text-slate-400 font-medium text-lg leading-relaxed max-w-2xl mx-auto md:mx-0">
                                    "<?php echo $worker['bio']; ?>"
                                </p>
                                <div class="flex items-center justify-center md:justify-start gap-4">
                                    <span class="text-skill-blue font-black text-xs uppercase tracking-[0.2em]"><?php echo $worker['cat_name']; ?> Expert</span>
                                    <span class="text-slate-500 text-[10px] font-black">• <?php echo $missions['success_count']; ?> Missions Settled</span>
                                </div>
                            </div>

                            <div class="flex flex-col gap-4 w-full md:w-auto">
                                <a href="https://wa.me/<?php echo $worker['phone']; ?>" target="_blank" class="px-10 py-6 bg-green-600 text-white rounded-[2rem] font-black text-xs uppercase tracking-[0.2em] shadow-xl hover:bg-green-700 text-center transition-all">
                                    Direct Consult <i class="fab fa-whatsapp ml-3"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="elite-card p-10 rounded-[3rem] text-center">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 leading-none">Professional Score</p>
                            <div class="flex items-center justify-center gap-3">
                                <span class="text-5xl font-black text-slate-900"><?php echo number_format($stats['avg_rating'], 1); ?></span>
                                <div class="text-amber-400 text-2xl flex"><i class="fas fa-star"></i></div>
                            </div>
                            <p class="mt-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Calculated from <?php echo $stats['total_reviews']; ?> Reviews</p>
                        </div>
                        
                        <div class="elite-card p-10 rounded-[3rem] text-center">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 leading-none">Mission Reliability</p>
                            <h3 class="text-5xl font-black text-slate-900"><?php echo ($missions['success_count'] > 0) ? "100%" : "New"; ?></h3>
                            <p class="mt-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Success Rate Nationwide</p>
                        </div>

                        <div class="elite-card p-10 rounded-[3rem] text-center">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 leading-none">Experience Level</p>
                            <h3 class="text-3xl font-black text-slate-900 uppercase tracking-tighter">Certified</h3>
                            <p class="mt-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">SkillLink Registered Pro</p>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div class="flex items-center justify-between px-6">
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em]">Verified Feedback Registry</h3>
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">Audited Project History</span>
                        </div>

                        <div class="space-y-4">
                            <?php if($reviews_feed->num_rows > 0): ?>
                                <?php while($review = $reviews_feed->fetch_assoc()): ?>
                                <div class="recessed-row p-10 rounded-[2.5rem] flex flex-col md:flex-row items-start justify-between gap-8 group">
                                    <div class="flex-1 space-y-4">
                                        <div class="flex items-center gap-4">
                                            <div class="flex text-amber-400 text-sm">
                                                <?php for($i=0; $i<$review['rating']; $i++): ?> <i class="fas fa-star"></i> <?php endfor; ?>
                                            </div>
                                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></span>
                                        </div>
                                        <p class="text-slate-900 font-semibold leading-relaxed">
                                            "<?php echo $review['comment']; ?>"
                                        </p>
                                        <div class="flex items-center gap-3">
                                            <div class="w-6 h-6 rounded-full overflow-hidden border border-slate-200">
                                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($review['reviewer_name']); ?>&background=random" class="w-full h-full">
                                            </div>
                                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-tighter">Reviewed by <span class="text-slate-900"><?php echo $review['reviewer_name']; ?></span></p>
                                        </div>
                                    </div>
                                    <div class="px-6 py-3 bg-white border border-slate-100 rounded-2xl flex items-center gap-2">
                                        <i class="fas fa-check-double text-green-500 text-[10px]"></i>
                                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Verified Mission</span>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="bg-white rounded-[4rem] p-24 text-center border-4 border-dashed border-slate-100">
                                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                                        <i class="fas fa-star text-slate-200 text-3xl animate-pulse"></i>
                                    </div>
                                    <h4 class="text-xl font-black text-slate-900">Awaiting First Review</h4>
                                    <p class="text-sm text-slate-400 font-medium max-w-xs mx-auto mt-2">This professional identity is new to the marketplace. Be the first to verify their excellence.</p>
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