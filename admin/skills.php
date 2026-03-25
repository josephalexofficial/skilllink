<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: admin/skills.php
 * VERSION: 2.0 "The Infrastructure Engine"
 * FOCUS: Tactical category management, market velocity auditing, and high-density UI.
 */

session_start();

// 1. THE STEEL VAULT: Session Guard
if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../auth/login.php?error=unauthorized");
    exit();
}

// 2. System Initialization
$base_path = dirname(__DIR__) . DIRECTORY_SEPARATOR;
require_once $base_path . 'includes' . DIRECTORY_SEPARATOR . 'config.php';

// HELPER: Map Category Names to Tactical Node Hues
function getNodeStyles($name) {
    $name = strtolower($name);
    if (str_contains($name, 'plumb')) return ['color' => 'blue', 'glow' => 'shadow-blue-500/20', 'bg' => 'bg-blue-50'];
    if (str_contains($name, 'elect')) return ['color' => 'amber', 'glow' => 'shadow-amber-500/20', 'bg' => 'bg-amber-50'];
    if (str_contains($name, 'paint')) return ['color' => 'indigo', 'glow' => 'shadow-indigo-500/20', 'bg' => 'bg-indigo-50'];
    if (str_contains($name, 'clean')) return ['color' => 'emerald', 'glow' => 'shadow-emerald-500/20', 'bg' => 'bg-emerald-50'];
    if (str_contains($name, 'carp'))  return ['color' => 'orange', 'glow' => 'shadow-orange-500/20', 'bg' => 'bg-orange-50'];
    return ['color' => 'slate', 'glow' => 'shadow-slate-500/20', 'bg' => 'bg-slate-50'];
}

// 3. THE JUDGE: Handle Protocol Operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $cat_name = $conn->real_escape_string($_POST['cat_name']);
        $cat_icon = $conn->real_escape_string($_POST['cat_icon']);
        $sort_order = intval($_POST['sort_order']);
        $status = $_POST['status'] === 'active' ? 'active' : 'hidden';

        if ($_POST['action'] === 'deploy') {
            $conn->query("INSERT INTO categories (cat_name, cat_icon, sort_order, status) VALUES ('$cat_name', '$cat_icon', $sort_order, '$status')");
        } elseif ($_POST['action'] === 'update') {
            $id = intval($_POST['cat_id']);
            $conn->query("UPDATE categories SET cat_name='$cat_name', cat_icon='$cat_icon', sort_order=$sort_order, status='$status' WHERE id=$id");
        }
        header("Location: skills.php?status=success");
        exit();
    }
}

// 4. THE ANALYST: Fetch Skill Tree Metrics
try {
    $query = "
        SELECT c.*, 
            (SELECT COUNT(*) FROM worker_profiles WHERE category_id = c.id) as worker_count,
            (SELECT COUNT(*) FROM tasks WHERE category_id = c.id) as task_count
        FROM categories c 
        ORDER BY c.sort_order ASC";
    $categories = $conn->query($query);
    
    // Aggregates for the Header Ticker
    $totals = $conn->query("SELECT COUNT(*) as total_nodes, SUM((SELECT COUNT(*) FROM tasks WHERE category_id = categories.id)) as global_demand FROM categories")->fetch_assoc();
} catch (Exception $e) {
    error_log("Skill Tree Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillLink | Skill Taxonomy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .blueprint-bg { background-image: radial-gradient(#e2e8f0 1.1px, transparent 1.1px); background-size: 30px 30px; }
        .hud-card { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(226, 232, 240, 0.8); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .hud-card:hover { transform: translateY(-4px); border-color: #3b82f6; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05); }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        
        #configDrawer { transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .drawer-hidden { transform: translateX(100%); }
    </style>
</head>
<body class="h-screen overflow-hidden flex flex-col blueprint-bg text-slate-900">

    <header class="h-16 shrink-0 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-8 z-50">
        <div class="flex items-center gap-4">
            <span class="text-[10px] font-black bg-slate-900 text-white px-2 py-0.5 rounded tracking-widest uppercase">Infrastructure Node</span>
            <div class="h-4 w-[1px] bg-slate-200"></div>
            <p class="text-[9px] font-mono font-bold text-slate-500 uppercase tracking-widest hidden md:block">
                INFRA_SYNC >> TOTAL_NODES: [<?= $totals['total_nodes'] ?>] | GLOBAL_DEMAND: [<?= $totals['global_demand'] ?>] >> STATUS: OPTIMAL
            </p>
        </div>
        <button onclick="openDeployDrawer()" class="bg-blue-600 text-white text-[10px] font-black px-6 py-2.5 rounded-xl uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">
            <i class="fas fa-plus mr-2"></i> Deploy New Node
        </button>
    </header>

    <div class="flex flex-1 overflow-hidden">
        
        <aside class="w-72 bg-slate-900 text-white flex flex-col h-full flex-none shadow-2xl z-40 relative">
            <div class="p-8 border-b border-slate-800/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                        <i class="fas fa-layer-group text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black tracking-tighter uppercase leading-none">Skill<span class="text-blue-500">Link</span></h2>
                        <p class="text-[9px] uppercase tracking-[0.3em] text-slate-500 font-bold mt-1">Registry Core</p>
                    </div>
                </div>
            </div>
            <nav class="flex-1 p-6 space-y-2 overflow-y-auto custom-scrollbar">
                <a href="dashboard.php" class="flex items-center gap-4 px-5 py-3.5 text-slate-400 hover:text-white hover:bg-slate-800/50 rounded-xl font-bold transition-all group">
                    <i class="fas fa-grid-2 text-sm"></i> Dashboard
                </a>
                <a href="verifications.php" class="flex items-center gap-4 px-5 py-3.5 text-slate-400 hover:text-white hover:bg-slate-800/50 rounded-xl font-bold transition-all group">
                    <i class="fas fa-user-check text-sm"></i> Verification
                </a>
                <a href="skills.php" class="flex items-center gap-4 px-5 py-3.5 bg-blue-600/10 text-blue-500 rounded-xl font-bold transition-all border border-blue-600/20">
                    <i class="fas fa-layer-group text-sm"></i> Skill Tree
                </a>
                <a href="reports.php" class="flex items-center gap-4 px-5 py-3.5 text-slate-400 hover:text-white hover:bg-slate-800/50 rounded-xl font-bold transition-all group">
                    <i class="fas fa-flag text-sm"></i> Reports
                </a>
            </nav>
            <div class="p-6 border-t border-slate-800/50 bg-slate-900/50">
                <a href="../auth/logout.php" class="flex items-center justify-center gap-3 w-full py-3.5 bg-red-500/5 text-red-500 rounded-xl font-bold hover:bg-red-500 hover:text-white transition-all border border-red-500/20">
                    <i class="fas fa-power-off text-sm"></i> Logout
                </a>
            </div>
        </aside>

        <main class="flex-1 overflow-y-auto custom-scrollbar p-10 space-y-8">
            <div>
                <h2 class="text-3xl font-black tracking-tight text-slate-900 uppercase">Skill Taxonomy</h2>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mt-1">Managing Global Marketplace Service Nodes</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pb-12">
                <?php while($row = $categories->fetch_assoc()): 
                    $styles = getNodeStyles($row['cat_name']);
                    $icon = !empty($row['cat_icon']) ? $row['cat_icon'] : (str_contains(strtolower($row['cat_name']), 'clean') ? 'fa-broom-ball' : 'fa-microchip');
                    $is_demand_high = ($row['task_count'] > $row['worker_count']);
                ?>
                <div class="hud-card p-8 rounded-[2.5rem] relative overflow-hidden group">
                    <div class="absolute top-6 right-6">
                        <span class="inline-flex items-center gap-1.5 text-[8px] font-black uppercase px-2.5 py-1 rounded-full border <?= $row['status'] === 'active' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-slate-100 text-slate-400 border-slate-200' ?>">
                            <span class="w-1 h-1 rounded-full bg-current <?= $row['status'] === 'active' ? 'animate-pulse' : '' ?>"></span>
                            <?= $row['status'] ?>
                        </span>
                    </div>

                    <div class="w-16 h-16 rounded-2xl <?= $styles['bg'] ?> flex items-center justify-center text-2xl text-<?= $styles['color'] ?>-600 shadow-xl <?= $styles['glow'] ?> group-hover:scale-110 transition-all duration-500 mb-8 border border-white">
                        <i class="fas <?= htmlspecialchars($icon) ?>"></i>
                    </div>

                    <h3 class="text-2xl font-black text-slate-900 mb-1 tracking-tight"><?= htmlspecialchars($row['cat_name']) ?></h3>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em]">Service Infrastructure Node</p>
                    
                    <div class="grid grid-cols-2 gap-4 mt-8 pt-6 border-t border-slate-100">
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Worker Nodes</p>
                            <p class="text-lg font-bold font-mono text-slate-900"><?= number_format($row['worker_count']) ?></p>
                        </div>
                        <div class="bg-blue-50/30 p-4 rounded-2xl border border-blue-50">
                            <p class="text-[8px] font-black text-blue-400 uppercase tracking-widest mb-1">Task Demand</p>
                            <div class="flex items-center gap-2">
                                <p class="text-lg font-bold font-mono text-blue-600"><?= number_format($row['task_count']) ?></p>
                                <?php if($is_demand_high): ?>
                                    <i class="fas fa-arrow-trend-up text-green-500 text-[10px]" title="High Market Opportunity"></i>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <button onclick='openConfigDrawer(<?= json_encode($row) ?>)' class="w-full py-4 bg-slate-900 text-white text-[10px] font-black rounded-2xl uppercase tracking-[0.2em] hover:bg-blue-600 transition-all shadow-lg hover:shadow-blue-500/20">
                            Configure Protocol
                        </button>
                    </div>

                    <div class="absolute bottom-0 right-0 p-6 opacity-5 font-black text-5xl italic font-mono select-none">
                        #0<?= $row['sort_order'] ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </main>
    </div>

    <div id="drawerOverlay" class="fixed inset-0 bg-slate-950/40 backdrop-blur-sm z-[60] hidden opacity-0 transition-opacity duration-300" onclick="closeDrawer()"></div>
    <aside id="configDrawer" class="fixed top-0 right-0 h-full w-full max-w-md bg-white shadow-2xl z-[70] drawer-hidden flex flex-col">
        <div class="p-10 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <div>
                <h3 class="text-2xl font-black text-slate-900 tracking-tight uppercase" id="drawerTitle">Deploy Node</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Expanding Infrastructure Taxonomy</p>
            </div>
            <button onclick="closeDrawer()" class="w-10 h-10 rounded-full hover:bg-white flex items-center justify-center text-slate-400 hover:text-red-500 transition-all"><i class="fas fa-times"></i></button>
        </div>
        
        <form action="skills.php" method="POST" class="flex-1 overflow-y-auto p-10 custom-scrollbar space-y-10">
            <input type="hidden" name="action" id="modalAction" value="deploy">
            <input type="hidden" name="cat_id" id="modalId">

            <div class="flex justify-center">
                <div id="iconPreview" class="w-24 h-24 rounded-3xl bg-slate-900 text-white flex items-center justify-center text-4xl shadow-2xl border-4 border-slate-50">
                    <i class="fas fa-microchip"></i>
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Industry Designation</label>
                    <input type="text" name="cat_name" id="field_name" required class="w-full bg-slate-50 border border-slate-200 px-6 py-4 rounded-2xl text-sm font-bold focus:border-blue-500 focus:ring-0 transition-all">
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Icon Node (FA Class)</label>
                        <input type="text" name="cat_icon" id="field_icon" oninput="updateIconPreview(this.value)" required class="w-full bg-slate-50 border border-slate-200 px-6 py-4 rounded-2xl text-xs font-mono font-bold focus:border-blue-500 transition-all">
                    </div>
                    <div>
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Sequence Priority</label>
                        <input type="number" name="sort_order" id="field_order" required class="w-full bg-slate-50 border border-slate-200 px-6 py-4 rounded-2xl text-sm font-mono font-bold focus:border-blue-500 transition-all">
                    </div>
                </div>

                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Visibility Protocol</label>
                    <select name="status" id="field_status" class="w-full bg-slate-50 border border-slate-200 px-6 py-4 rounded-2xl text-sm font-bold focus:border-blue-500 transition-all appearance-none">
                        <option value="active">PROTOCOL: ACTIVE</option>
                        <option value="hidden">PROTOCOL: HIDDEN</option>
                    </select>
                </div>
            </div>

            <div class="pt-10">
                <button type="submit" class="w-full py-5 bg-blue-600 text-white rounded-3xl font-black text-xs uppercase tracking-[0.2em] hover:bg-blue-700 transition-all shadow-xl shadow-blue-500/30">
                    Commit Node Protocol
                </button>
            </div>
        </form>
    </aside>

    <script>
        const drawer = document.getElementById('configDrawer');
        const overlay = document.getElementById('drawerOverlay');

        function openDeployDrawer() {
            document.getElementById('modalAction').value = 'deploy';
            document.getElementById('drawerTitle').innerText = 'Deploy Node';
            document.getElementById('field_name').value = '';
            document.getElementById('field_icon').value = 'fa-microchip';
            document.getElementById('field_order').value = '0';
            updateIconPreview('fa-microchip');
            
            overlay.classList.remove('hidden');
            setTimeout(() => overlay.classList.add('opacity-100'), 10);
            drawer.classList.remove('drawer-hidden');
        }

        function openConfigDrawer(data) {
            document.getElementById('modalAction').value = 'update';
            document.getElementById('modalId').value = data.id;
            document.getElementById('drawerTitle').innerText = 'Refine Node';
            document.getElementById('field_name').value = data.cat_name;
            document.getElementById('field_icon').value = data.cat_icon || 'fa-microchip';
            document.getElementById('field_order').value = data.sort_order;
            document.getElementById('field_status').value = data.status;
            updateIconPreview(data.cat_icon || 'fa-microchip');
            
            overlay.classList.remove('hidden');
            setTimeout(() => overlay.classList.add('opacity-100'), 10);
            drawer.classList.remove('drawer-hidden');
        }

        function updateIconPreview(val) {
            const preview = document.getElementById('iconPreview');
            preview.innerHTML = `<i class="fas ${val}"></i>`;
        }

        function closeDrawer() {
            overlay.classList.remove('opacity-100');
            setTimeout(() => overlay.classList.add('hidden'), 300);
            drawer.classList.add('drawer-hidden');
        }
    </script>
</body>
</html>