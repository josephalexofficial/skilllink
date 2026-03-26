<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: services.php
 * VERSION: 2.1 "The Compact Discovery Node"
 */

// 1. System Initialization
$include_path = __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;
require_once $include_path . 'config.php';

// 2. Navigation State
$current_page = basename($_SERVER['PHP_SELF']);
include $include_path . 'header.php';

/**
 * 3. THE ANALYST: Logic for Live Data
 */
$search = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';
$cat_filter = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;

// Fetch Dynamic Categories for Sidebar
$sidebar_cats = $conn->query("SELECT * FROM categories WHERE status = 'active' ORDER BY cat_name ASC");

// Fetch Live Worker Nodes (Including base_rate check)
$sql = "SELECT wp.*, u.full_name, c.cat_name, c.cat_icon,
        IFNULL((SELECT AVG(rating) FROM reviews WHERE worker_id = wp.user_id), 0) as avg_rating,
        IFNULL((SELECT COUNT(*) FROM reviews WHERE worker_id = wp.user_id), 0) as review_count
        FROM worker_profiles wp 
        JOIN users u ON wp.user_id = u.id 
        JOIN categories c ON wp.category_id = c.id 
        WHERE wp.is_live = 1 AND u.status = 'active'";

if (!empty($search)) $sql .= " AND (u.full_name LIKE '%$search%' OR wp.bio LIKE '%$search%')";
if ($cat_filter > 0) $sql .= " AND wp.category_id = $cat_filter";

$sql .= " ORDER BY avg_rating DESC";
$pros_query = $conn->query($sql);

// HELPER: Sovereign Initials Generator
function getInitials($name) {
    $words = explode(" ", $name);
    $initials = "";
    foreach ($words as $w) { if(!empty($w)) $initials .= $w[0]; }
    return strtoupper(substr($initials, 0, 2));
}

// HELPER: Elite Gradient Map
function getSlateGradient($id) {
    $gradients = ['from-slate-800 to-slate-900', 'from-blue-900 to-slate-900', 'from-indigo-950 to-slate-900', 'from-slate-700 to-slate-800'];
    return $gradients[$id % count($gradients)];
}
?>

<main class="min-h-screen bg-slate-50 font-sans antialiased text-slate-900">

    <header class="bg-white border-b border-slate-100 pt-12 pb-10 px-6 relative overflow-hidden">
        <div class="absolute inset-0 opacity-[0.02] pointer-events-none" style="background-image: linear-gradient(#94a3b8 1px, transparent 1px), linear-gradient(90deg, #94a3b8 1px, transparent 1px); background-size: 30px 30px;"></div>
        
        <div class="max-w-7xl mx-auto relative z-10">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
                
                <div class="space-y-2">
                    <span class="text-[9px] font-black uppercase tracking-[0.4em] text-blue-600 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-blue-600 rounded-full animate-pulse"></span> Professional Registry
                    </span>
                    <h1 class="text-3xl md:text-4xl font-[900] tracking-tighter text-slate-900 leading-none">Find Your <span class="text-blue-600 italic">Expert</span></h1>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-tight">Vetted Talent Nodes for Maseno Territory</p>
                </div>

                <form action="" method="GET" class="relative w-full md:max-w-md group">
                    <i class="fas fa-magnifying-glass absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-600 transition-colors"></i>
                    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Search skills, names, or sectors..." 
                           class="w-full pl-14 pr-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-blue-500/5 focus:border-blue-600 transition-all duration-300 font-bold outline-none text-slate-700 text-sm shadow-sm">
                </form>

            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-6 py-12 flex flex-col lg:flex-row gap-12">
        
        <aside class="w-full lg:w-64 space-y-10 shrink-0">
            <div class="sticky top-28 space-y-8">
                <div class="space-y-4">
                    <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Sector Nodes</h4>
                    <div class="flex flex-wrap lg:flex-col gap-2">
                        <a href="services.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-wider <?= $cat_filter == 0 ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'text-slate-500 hover:bg-white border border-transparent hover:border-slate-100 hover:text-slate-900' ?> transition-all">
                            <i class="fas fa-layer-group opacity-50"></i> All Registry
                        </a>
                        <?php while($c = $sidebar_cats->fetch_assoc()): ?>
                        <a href="services.php?cat=<?= $c['id'] ?>" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-wider <?= $cat_filter == $c['id'] ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'text-slate-500 hover:bg-white border border-transparent hover:border-slate-100 hover:text-slate-900' ?> transition-all">
                            <i class="fas <?= str_contains(strtolower($c['cat_name']), 'clean') ? 'fa-broom-ball' : ($c['cat_icon'] ?: 'fa-cube') ?> opacity-50"></i> <?= $c['cat_name'] ?>
                        </a>
                        <?php endwhile; ?>
                    </div>
                </div>

                <div class="bg-slate-900 rounded-[2rem] p-8 text-white relative overflow-hidden">
                    <p class="text-[9px] text-blue-400 font-black uppercase tracking-widest">Growth</p>
                    <h5 class="text-sm font-black mt-2 leading-tight">Join the Elite Registry</h5>
                    <a href="auth/signup-choice.php" class="mt-6 inline-block text-[10px] font-black text-white uppercase tracking-widest border-b border-blue-500 pb-1 hover:text-blue-400 transition-colors">Apply Now &rarr;</a>
                </div>
            </div>
        </aside>

        <section class="flex-1 space-y-8">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-6">
                <div class="flex items-center gap-3">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Registry: <span class="text-slate-900"><?= $pros_query->num_rows ?> Experts Identified</span></p>
                </div>
                <div class="flex items-center gap-4">
                    <select onchange="location = this.value;" class="bg-white px-4 py-2 border border-slate-200 rounded-lg font-black text-[9px] uppercase tracking-widest outline-none cursor-pointer hover:border-blue-600 transition-all">
                        <option>Sort: Best Rated</option>
                        <option>Sort: Newest</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                <?php if($pros_query->num_rows > 0): ?>
                    <?php while($pro = $pros_query->fetch_assoc()): 
                        $p_initials = getInitials($pro['full_name']);
                        $p_gradient = getSlateGradient($pro['user_id']);
                        $has_photo = !empty($pro['profile_photo']) && file_exists("uploads/profiles/".$pro['profile_photo']);
                        $p_icon = str_contains(strtolower($pro['cat_name']), 'clean') ? 'fa-broom-ball' : ($pro['cat_icon'] ?: 'fa-screwdriver-wrench');
                    ?>
                    <div class="group bg-white rounded-[2.5rem] border border-slate-200/60 shadow-xl shadow-slate-200/10 hover:shadow-2xl hover:border-blue-600/30 hover:-translate-y-1.5 transition-all duration-500 overflow-hidden flex flex-col">
                        
                        <div class="p-8 pb-4 flex flex-col items-center text-center space-y-6">
                            <div class="relative">
                                <?php if($has_photo): ?>
                                    <img src="uploads/profiles/<?= $pro['profile_photo'] ?>" class="w-20 h-20 rounded-[2rem] object-cover border-4 border-slate-50 shadow-lg">
                                <?php else: ?>
                                    <div class="w-20 h-20 rounded-[2rem] bg-gradient-to-br <?= $p_gradient ?> flex items-center justify-center text-white text-lg font-black shadow-lg border-4 border-white">
                                        <?= $p_initials ?>
                                    </div>
                                <?php endif; ?>
                                <div class="absolute -top-2 -right-2 w-8 h-8 bg-white rounded-xl shadow-md flex items-center justify-center text-blue-600 border border-slate-50">
                                    <i class="fas <?= $p_icon ?> text-[10px]"></i>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-[900] text-slate-900 tracking-tighter leading-tight group-hover:text-blue-600 transition-colors"><?= htmlspecialchars($pro['full_name']) ?></h3>
                                <p class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 italic"><?= $pro['cat_name'] ?></p>
                            </div>

                            <div class="flex items-center gap-4 py-2 border-y border-slate-50 w-full justify-center">
                                <div class="text-center px-2">
                                    <p class="text-[7px] font-black text-slate-400 uppercase">Trust</p>
                                    <p class="text-[10px] font-black text-amber-500"><i class="fas fa-star mr-1"></i><?= number_format($pro['avg_rating'], 1) ?></p>
                                </div>
                                <div class="h-4 w-[1px] bg-slate-100"></div>
                                <div class="text-center px-2">
                                    <p class="text-[7px] font-black text-slate-400 uppercase">Tasks</p>
                                    <p class="text-[10px] font-black text-slate-900"><?= $pro['review_count'] ?></p>
                                </div>
                            </div>

                            <p class="text-[11px] text-slate-500 font-medium leading-relaxed line-clamp-2 min-h-[2.5rem]">
                                <?= htmlspecialchars($pro['bio'] ?: 'A verified professional expert node specializing in the '.$pro['cat_name'].' sector.') ?>
                            </p>
                        </div>
                        
                        <div class="px-8 pb-8 pt-2 mt-auto">
                            <div class="flex items-center justify-between mb-5 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <div>
                                    <p class="text-[7px] font-black text-slate-400 uppercase tracking-widest">Baseline Rate</p>
                                    <p class="text-base font-black text-slate-900 tracking-tighter">
                                        KES <?= number_format((float)($pro['base_rate'] ?? 0), 0) ?>
                                    </p>
                                </div>
                                <i class="fas fa-bolt text-blue-500 text-[10px]"></i>
                            </div>
                            <a href="client/view-worker.php?id=<?= $pro['user_id'] ?>" class="block w-full py-4 bg-slate-900 text-white rounded-xl font-black text-[9px] uppercase tracking-[0.2em] text-center hover:bg-blue-600 shadow-lg hover:shadow-blue-500/20 transition-all transform active:scale-95">
                                Book Mission <i class="fas fa-chevron-right ml-2 text-[7px]"></i>
                            </a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-span-full py-20 text-center border-2 border-dashed border-slate-200 rounded-[3rem] bg-white">
                        <i class="fas fa-radar text-slate-200 text-4xl mb-4"></i>
                        <h4 class="text-lg font-black text-slate-900 uppercase">Zero Nodes Identified</h4>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-2">Adjust your filters to expand the search radius.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>

</main>

<?php include $include_path . 'footer.php'; ?>