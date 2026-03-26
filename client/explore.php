<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: client/explore.php
 * VERSION: 1.0 "The Sector Discovery Terminal"
 */

session_start();

// 1. THE STEEL VAULT: Security Guard
if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'client') {
    header("Location: ../auth/login.php");
    exit();
}

// 2. System Initialization
$base_dir = dirname(__DIR__);
require_once $base_dir . '/includes/config.php';

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];

// 3. Category Intelligence Fetch
$cat_id = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;

if ($cat_id <= 0) {
    header("Location: dashboard.php?error=invalid_sector");
    exit();
}

// Fetch Category Details
$cat_query = $conn->query("SELECT * FROM categories WHERE id = $cat_id LIMIT 1");
$category = $cat_query->fetch_assoc();

if (!$category) {
    header("Location: dashboard.php?error=sector_not_found");
    exit();
}

// Surgical Icon Logic
$display_icon = str_contains(strtolower($category['cat_name']), 'clean') ? 'fa-broom-ball' : ($category['cat_icon'] ?: 'fa-cube');

/**
 * 4. THE ANALYST: Fetch Verified Expert Nodes
 */
$experts_query = $conn->query("
    SELECT wp.*, u.full_name, u.status as user_status,
    IFNULL((SELECT AVG(rating) FROM reviews WHERE worker_id = wp.user_id), 0) as avg_rating,
    IFNULL((SELECT COUNT(*) FROM reviews WHERE worker_id = wp.user_id), 0) as review_count
    FROM worker_profiles wp 
    JOIN users u ON wp.user_id = u.id 
    WHERE wp.category_id = $cat_id 
    AND wp.is_live = 1 
    AND u.status = 'active'
    ORDER BY avg_rating DESC
");

// Helper for Slate Gradients (UI Consistency)
function getSlateGradient($id) {
    $gradients = ['from-slate-800 to-slate-900', 'from-blue-900 to-slate-900', 'from-indigo-950 to-slate-900', 'from-slate-700 to-slate-800'];
    return $gradients[$id % count($gradients)];
}
?>

<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore <?= $category['cat_name'] ?> | SkillLink</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: { 'skill-blue': '#3b82f6' }
                }
            }
        }
    </script>
    <style>
        .blueprint-bg { background-image: radial-gradient(#e2e8f0 1.1px, transparent 1.1px); background-size: 30px 30px; }
        .glass-sidebar { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(20px); }
        .custom-scroll::-webkit-scrollbar { width: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        
        .elite-card { background: white; border: 1px solid rgba(226, 232, 240, 0.8); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .elite-card:hover { transform: translateY(-3px); border-color: #3b82f6; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05); }
        
        .hero-glass { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-900 overflow-hidden blueprint-bg">

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
                    <a href="dashboard.php" class="flex items-center gap-4 px-6 py-4 text-slate-400 hover:text-skill-blue transition-all rounded-2xl font-bold text-sm">
                        <i class="fas fa-th-large text-xs"></i> Dashboard
                    </a>
                    <a href="post-job.php" class="flex items-center gap-4 px-6 py-4 text-slate-400 hover:text-slate-900 transition-all rounded-2xl font-bold text-sm">
                        <i class="fas fa-plus-circle text-xs"></i> Launch Project
                    </a>
                    <a href="my-bookings.php" class="flex items-center gap-4 px-6 py-4 text-slate-400 hover:text-slate-900 transition-all rounded-2xl font-bold text-sm">
                        <i class="fas fa-calendar-check text-xs"></i> My Bookings
                    </a>
                    <a href="activity-log.php" class="flex items-center gap-4 px-6 py-4 text-slate-400 hover:text-slate-900 transition-all rounded-2xl font-bold text-sm">
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
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight leading-none uppercase">Sector Discovery</h1>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-2">Registry / Explore / <?= $category['cat_name'] ?></p>
                </div>

                <div class="flex items-center gap-6">
                    <div class="w-14 h-14 bg-slate-900 rounded-[1.25rem] overflow-hidden border-4 border-white shadow-2xl">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=0f172a&color=fff" alt="User">
                    </div>
                </div>
            </header>

            <section class="flex-1 overflow-y-auto p-12 custom-scroll">
                <div class="max-w-7xl mx-auto space-y-12 pb-24">
                    
                    <div class="hero-glass rounded-[3.5rem] p-12 relative overflow-hidden shadow-2xl">
                        <div class="absolute -top-20 -right-20 w-80 h-80 bg-skill-blue/20 rounded-full blur-[100px]"></div>
                        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                            <div class="flex items-center gap-8">
                                <div class="w-20 h-20 bg-white/10 rounded-[2rem] flex items-center justify-center text-white text-3xl border border-white/20 backdrop-blur-md">
                                    <i class="fas <?= $display_icon ?>"></i>
                                </div>
                                <div>
                                    <h2 class="text-4xl font-black text-white tracking-tighter uppercase"><?= $category['cat_name'] ?></h2>
                                    <p class="text-slate-400 font-bold text-xs uppercase tracking-[0.3em] mt-2">Verified Professional Registry</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <span class="px-5 py-2 bg-white/5 border border-white/10 rounded-xl text-[10px] font-black text-white uppercase tracking-widest">
                                    Nodes: <?= $experts_query->num_rows ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <?php if($experts_query->num_rows > 0): ?>
                            <?php while($pro = $experts_query->fetch_assoc()): 
                                $p_initials = strtoupper(substr($pro['full_name'], 0, 1) . substr(explode(' ', $pro['full_name'])[1] ?? '', 0, 1));
                                $p_gradient = getSlateGradient($pro['user_id']);
                                $has_photo = !empty($pro['profile_photo']) && file_exists("../uploads/profiles/".$pro['profile_photo']);
                            ?>
                            <div class="elite-card p-8 rounded-[3rem] group">
                                <div class="flex flex-col items-center text-center space-y-5">
                                    <div class="relative">
                                        <?php if($has_photo): ?>
                                            <img src="../uploads/profiles/<?= $pro['profile_photo'] ?>" class="w-24 h-24 rounded-[2rem] object-cover border-4 border-slate-50 shadow-xl group-hover:border-skill-blue transition-all">
                                        <?php else: ?>
                                            <div class="w-24 h-24 rounded-[2.5rem] bg-gradient-to-br <?= $p_gradient ?> flex items-center justify-center text-white text-xl font-black shadow-xl border-4 border-white group-hover:border-skill-blue transition-all">
                                                <?= $p_initials ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="absolute -bottom-2 -right-2 bg-white p-2 rounded-full shadow-lg">
                                            <i class="fas fa-shield-check text-blue-500 text-sm"></i>
                                        </div>
                                    </div>

                                    <div>
                                        <h4 class="text-xl font-black text-slate-900 tracking-tight group-hover:text-skill-blue transition-colors"><?= htmlspecialchars($pro['full_name']) ?></h4>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1 italic"><?= $pro['location_name'] ?? 'Territory: Maseno' ?></p>
                                    </div>

                                    <div class="flex items-center gap-4 py-2 border-y border-slate-50 w-full justify-center">
                                        <div class="text-center px-4">
                                            <p class="text-[8px] font-black text-slate-400 uppercase">Rating</p>
                                            <p class="text-xs font-black text-amber-500"><i class="fas fa-star mr-1"></i><?= number_format($pro['avg_rating'], 1) ?></p>
                                        </div>
                                        <div class="h-6 w-[1px] bg-slate-100"></div>
                                        <div class="text-center px-4">
                                            <p class="text-[8px] font-black text-slate-400 uppercase">Experience</p>
                                            <p class="text-xs font-black text-slate-900"><?= $pro['review_count'] ?> Tasks</p>
                                        </div>
                                    </div>

                                    <p class="text-xs text-slate-500 font-medium leading-relaxed line-clamp-2">
                                        <?= htmlspecialchars($pro['bio'] ?: 'A verified professional expert in the '.$category['cat_name'].' sector, serving local territory needs.') ?>
                                    </p>

                                    <a href="view-worker.php?id=<?= $pro['user_id'] ?>" class="w-full py-5 bg-slate-900 text-white rounded-[1.5rem] font-black text-[10px] uppercase tracking-[0.2em] shadow-xl hover:bg-skill-blue transition-all active:scale-95 group-hover:shadow-blue-500/20">
                                        Inspect Pro Dossier
                                    </a>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-span-full bg-white border-4 border-dashed border-slate-100 rounded-[4rem] p-24 text-center">
                                <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner">
                                    <i class="fas fa-satellite-dish text-slate-200 text-4xl"></i>
                                </div>
                                <h4 class="text-2xl font-black text-slate-900 tracking-tight">Sector Node Inactive</h4>
                                <p class="text-sm text-slate-400 font-bold uppercase tracking-widest mt-4 max-w-md mx-auto">
                                    No verified professionals found in the <span class="text-skill-blue"><?= $category['cat_name'] ?></span> registry. 
                                    Broadcast a mission request to notify nearby experts.
                                </p>
                                <a href="post-job.php?cat=<?= $cat_id ?>" class="mt-10 inline-flex items-center gap-4 px-10 py-5 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-skill-blue transition-all shadow-2xl">
                                    Broadcast Mission Request <i class="fas fa-paper-plane"></i>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </main>
    </div>

</body>
</html>