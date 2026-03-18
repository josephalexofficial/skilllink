<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: admin/skills.php
 * VERSION: 1.0 "The Infrastructure Engine"
 * FOCUS: Category lifecycle management and skill density audit.
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

// 3. THE JUDGE: Handle Protocol Operations (CRUD)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $cat_name = $conn->real_escape_string($_POST['cat_name']);
        $cat_icon = $conn->real_escape_string($_POST['cat_icon']);
        $sort_order = intval($_POST['sort_order']);
        $status = $_POST['status'] === 'active' ? 'active' : 'hidden';

        if ($_POST['action'] === 'deploy') {
            // Deploy New Node
            $conn->query("INSERT INTO categories (cat_name, cat_icon, sort_order, status) VALUES ('$cat_name', '$cat_icon', $sort_order, '$status')");
        } elseif ($_POST['action'] === 'update') {
            // Refine Existing Node
            $id = intval($_POST['cat_id']);
            $conn->query("UPDATE categories SET cat_name='$cat_name', cat_icon='$cat_icon', sort_order=$sort_order, status='$status' WHERE id=$id");
        }
        header("Location: skills.php?status=success");
        exit();
    }
}

// 4. THE ANALYST: Fetch Skill Tree with Density Metrics
try {
    // Joining categories with worker counts and task counts for strategic insight
    $query = "
        SELECT c.*, 
            (SELECT COUNT(*) FROM worker_profiles WHERE category_id = c.id) as worker_count,
            (SELECT COUNT(*) FROM tasks WHERE category_id = c.id) as task_count
        FROM categories c 
        ORDER BY c.sort_order ASC";
    $categories = $conn->query($query);
} catch (Exception $e) {
    error_log("Skill Tree Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillLink | Skill Tree Infrastructure</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .blueprint-bg {
            background-image: radial-gradient(#e2e8f0 1.1px, transparent 1.1px);
            background-size: 30px 30px;
        }
        .hud-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .hud-card:hover {
            transform: translateY(-4px);
            border-color: #3b82f6;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
        }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="h-screen overflow-hidden flex flex-col blueprint-bg text-slate-900">

    <header class="h-16 shrink-0 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-8 z-50">
        <div class="flex items-center gap-4">
            <span class="text-[10px] font-black bg-slate-900 text-white px-2 py-0.5 rounded tracking-widest uppercase">Infrastructure Node</span>
            <div class="h-4 w-[1px] bg-slate-200"></div>
            <h1 class="text-sm font-bold text-slate-500 uppercase tracking-widest">Command Center / <span class="text-slate-900">Skill Tree</span></h1>
        </div>
        <button onclick="openDeployModal()" class="bg-blue-600 text-white text-[10px] font-black px-6 py-2.5 rounded-xl uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">
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
                <?php while($row = $categories->fetch_assoc()): ?>
                <div class="hud-card p-8 rounded-[2.5rem] relative overflow-hidden group">
                    <div class="absolute top-6 right-6">
                        <span class="text-[8px] font-black uppercase px-2 py-1 rounded-full border <?= $row['status'] === 'active' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-slate-100 text-slate-400 border-slate-200' ?>">
                            <?= $row['status'] ?>
                        </span>
                    </div>

                    <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center text-2xl text-slate-400 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 mb-6 shadow-inner">
                        <i class="fas <?= htmlspecialchars($row['cat_icon']) ?>"></i>
                    </div>

                    <h3 class="text-xl font-black text-slate-900 mb-2"><?= htmlspecialchars($row['cat_name']) ?></h3>
                    
                    <div class="grid grid-cols-2 gap-4 mt-6 pt-6 border-t border-slate-100">
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Worker Nodes</p>
                            <p class="text-sm font-bold font-mono text-slate-900"><?= number_format($row['worker_count']) ?></p>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Task Demand</p>
                            <p class="text-sm font-bold font-mono text-blue-600"><?= number_format($row['task_count']) ?></p>
                        </div>
                    </div>

                    <div class="mt-8 flex gap-2">
                        <button onclick='openEditModal(<?= json_encode($row) ?>)' class="flex-1 py-2.5 bg-slate-900 text-white text-[9px] font-black rounded-xl uppercase tracking-widest hover:bg-blue-600 transition-all">
                            Configure Protocol
                        </button>
                    </div>

                    <div class="absolute bottom-0 right-0 p-4 opacity-10 font-black text-4xl italic font-mono select-none">
                        #<?= $row['sort_order'] ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

        </main>
    </div>

    <div id="protocolModal" class="fixed inset-0 z-[100] hidden bg-slate-950/60 backdrop-blur-md flex items-center justify-center p-6">
        <div class="bg-white w-full max-w-lg rounded-[3rem] shadow-2xl overflow-hidden border border-white/20">
            <form action="skills.php" method="POST" class="p-10 space-y-8">
                <input type="hidden" name="action" id="modalAction" value="deploy">
                <input type="hidden" name="cat_id" id="modalId">

                <div class="flex justify-between items-center mb-2">
                    <div>
                        <h3 class="text-xl font-black text-slate-900 tracking-tight" id="modalTitle">Deploy Service Node</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Expanding Marketplace Infrastructure</p>
                    </div>
                    <button type="button" onclick="closeModal()" class="text-slate-300 hover:text-red-500 transition-colors"><i class="fas fa-times text-xl"></i></button>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Industry Name</label>
                        <input type="text" name="cat_name" id="field_name" required placeholder="e.g., HVAC Specialist" class="w-full bg-slate-50 border border-slate-200 px-5 py-4 rounded-2xl text-sm font-bold focus:border-blue-500 focus:ring-0 transition-all">
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Tactical Icon (FA Class)</label>
                            <input type="text" name="cat_icon" id="field_icon" required placeholder="fa-wrench" class="w-full bg-slate-50 border border-slate-200 px-5 py-4 rounded-2xl text-xs font-mono font-bold focus:border-blue-500 transition-all">
                        </div>
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Priority Sequence</label>
                            <input type="number" name="sort_order" id="field_order" required value="0" class="w-full bg-slate-50 border border-slate-200 px-5 py-4 rounded-2xl text-sm font-mono font-bold focus:border-blue-500 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Availability Status</label>
                        <select name="status" id="field_status" class="w-full bg-slate-50 border border-slate-200 px-5 py-4 rounded-2xl text-sm font-bold focus:border-blue-500 transition-all appearance-none">
                            <option value="active">ACTIVE: Visible to Clients</option>
                            <option value="hidden">HIDDEN: System Maintenance</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-5 bg-blue-600 text-white rounded-[1.5rem] font-black text-xs uppercase tracking-[0.2em] hover:bg-blue-700 transition-all shadow-xl shadow-blue-500/30">
                        Confirm Node Protocol
                    </button>
                </div>
            </form>
        </div>
    </div>

    <footer class="h-10 shrink-0 bg-slate-950 border-t border-slate-900 flex items-center px-8 z-50">
        <div class="flex items-center gap-4">
            <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.4em] whitespace-nowrap">
                INFRA_SYNC >> AUDITING SKILL NODES >> LOAD BALANCED >> STANDBY FOR NEW PROTOCOLS
            </p>
        </div>
    </footer>

    <script>
        const modal = document.getElementById('protocolModal');

        function openDeployModal() {
            document.getElementById('modalAction').value = 'deploy';
            document.getElementById('modalTitle').innerText = 'Deploy Service Node';
            document.getElementById('field_name').value = '';
            document.getElementById('field_icon').value = 'fa-cube';
            document.getElementById('field_order').value = '0';
            modal.classList.remove('hidden');
        }

        function openEditModal(data) {
            document.getElementById('modalAction').value = 'update';
            document.getElementById('modalId').value = data.id;
            document.getElementById('modalTitle').innerText = 'Refine Node Protocol';
            document.getElementById('field_name').value = data.cat_name;
            document.getElementById('field_icon').value = data.cat_icon;
            document.getElementById('field_order').value = data.sort_order;
            document.getElementById('field_status').value = data.status;
            modal.classList.remove('hidden');
        }

        function closeModal() {
            modal.classList.add('hidden');
        }
    </script>

</body>
</html>