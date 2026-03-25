<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: admin/users_registry.php
 * VERSION: 1.2 "The Executive Command Ledger"
 * FOCUS: Inline editing, status toggling, and real-time background sync.
 */

session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../auth/login.php?error=unauthorized");
    exit();
}

$base_path = dirname(__DIR__) . DIRECTORY_SEPARATOR;
require_once $base_path . 'includes' . DIRECTORY_SEPARATOR . 'config.php';

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="SkillLink_Registry_'.date('Y-m-d').'.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Full Name', 'Email', 'Phone', 'County', 'Area', 'Role', 'Status', 'Registration Date']);
    
    $export_query = $conn->query("SELECT id, full_name, email, phone, county, area, role, status, created_at FROM users ORDER BY created_at DESC");
    while ($row = $export_query->fetch_assoc()) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
}

$page_title = "Personnel Registry";

try {
    $counts = [
        'total'     => $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0] ?? 0,
        'workers'   => $conn->query("SELECT COUNT(*) FROM users WHERE role='worker'")->fetch_row()[0] ?? 0,
        'clients'   => $conn->query("SELECT COUNT(*) FROM users WHERE role='client'")->fetch_row()[0] ?? 0,
        'suspended' => $conn->query("SELECT COUNT(*) FROM users WHERE status='suspended'")->fetch_row()[0] ?? 0,
    ];

    $role_filter = $_GET['role'] ?? 'all';
    $search = $conn->real_escape_string($_GET['search'] ?? '');
    
    $where_clauses = [];
    if ($role_filter !== 'all') $where_clauses[] = "role = '$role_filter'";
    if ($search !== '') $where_clauses[] = "(full_name LIKE '%$search%' OR email LIKE '%$search%' OR phone LIKE '%$search%')";
    
    $where_sql = count($where_clauses) > 0 ? "WHERE " . implode(' AND ', $where_clauses) : "";
    $registry = $conn->query("SELECT id, full_name, email, phone, role, status, county, area FROM users $where_sql ORDER BY created_at DESC");

} catch (Exception $e) {
    error_log("Registry Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillLink | Personnel Registry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .blueprint-bg { background-image: radial-gradient(#e2e8f0 1.1px, transparent 1.1px); background-size: 30px 30px; }
        .hud-card { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(226, 232, 240, 0.8); transition: all 0.3s ease; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        #profileDrawer { transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .drawer-hidden { transform: translateX(100%); }
        
        /* Tactical Edit Styles */
        .edit-input { width: 100%; background: white; border: 1px solid #3b82f6; border-radius: 8px; padding: 4px 8px; font-weight: 700; color: #1e293b; outline: none; }
    </style>
</head>
<body class="h-screen overflow-hidden flex flex-col blueprint-bg text-slate-900">

    <header class="h-16 shrink-0 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-8 z-50">
        <div class="flex items-center gap-4">
            <span class="text-[10px] font-black bg-slate-900 text-white px-2 py-0.5 rounded tracking-widest uppercase">Global Registry</span>
            <div class="h-4 w-[1px] bg-slate-200"></div>
            <h1 class="text-sm font-bold text-slate-500 uppercase tracking-widest">Command Center / <span class="text-slate-900">Personnel Ledger</span></h1>
        </div>
        <a href="?export=csv" class="bg-slate-900 text-white text-[10px] font-black px-6 py-2.5 rounded-xl uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg">
            <i class="fas fa-file-export mr-2"></i> Export Registry Report
        </a>
    </header>

    <div class="flex flex-1 overflow-hidden">
        <aside class="w-72 bg-slate-900 text-white flex flex-col h-full flex-none shadow-2xl z-40 relative">
            <div class="p-8 border-b border-slate-800/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users-gear text-white"></i>
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
                <a href="skills.php" class="flex items-center gap-4 px-5 py-3.5 text-slate-400 hover:text-white hover:bg-slate-800/50 rounded-xl font-bold transition-all group">
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
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="hud-card p-6 rounded-3xl">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Population</p>
                    <p class="text-3xl font-black text-slate-900 font-mono"><?= $counts['total'] ?></p>
                </div>
                <div class="hud-card p-6 rounded-3xl border-blue-100">
                    <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest mb-1">Worker Nodes</p>
                    <p class="text-3xl font-black text-blue-600 font-mono"><?= $counts['workers'] ?></p>
                </div>
                <div class="hud-card p-6 rounded-3xl">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Client Nodes</p>
                    <p class="text-3xl font-black text-slate-700 font-mono"><?= $counts['clients'] ?></p>
                </div>
                <div class="hud-card p-6 rounded-3xl border-red-100">
                    <p class="text-[9px] font-black text-red-400 uppercase tracking-widest mb-1">Suspended Nodes</p>
                    <p class="text-3xl font-black text-red-600 font-mono"><?= $counts['suspended'] ?></p>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200/60 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[9px] uppercase tracking-[0.25em] text-slate-400 font-black bg-slate-50/50">
                                <th class="px-10 py-6">Identity Node</th>
                                <th class="px-10 py-6">Operational Area</th>
                                <th class="px-10 py-6 text-center">Status</th>
                                <th class="px-10 py-6 text-right">Moderation</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php while($user = $registry->fetch_assoc()): ?>
                            <tr id="row-<?= $user['id'] ?>" class="hover:bg-blue-50/20 transition-all group cursor-pointer" onclick="examineNode(<?= $user['id'] ?>)">
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-4">
                                        <div id="avatar-<?= $user['id'] ?>" class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center font-black text-slate-500 group-hover:bg-blue-600 group-hover:text-white transition-all">
                                            <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <p id="t-name-<?= $user['id'] ?>" class="text-sm font-extrabold text-slate-900 leading-none mb-1"><?= htmlspecialchars($user['full_name']) ?></p>
                                            <div class="flex items-center gap-2">
                                                <span class="text-[9px] font-black uppercase px-1.5 py-0.5 rounded border <?= $user['role'] === 'worker' ? 'bg-blue-50 text-blue-600 border-blue-100' : 'bg-slate-50 text-slate-500 border-slate-200' ?>"><?= $user['role'] ?></span>
                                                <span class="text-[10px] text-slate-400 font-bold tracking-tighter"><?= htmlspecialchars($user['email']) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-10 py-6">
                                    <p id="t-county-<?= $user['id'] ?>" class="text-[10px] font-black text-slate-500 uppercase tracking-widest"><?= htmlspecialchars($user['county']) ?></p>
                                    <p id="t-area-<?= $user['id'] ?>" class="text-xs font-bold text-slate-900"><?= htmlspecialchars($user['area']) ?></p>
                                </td>
                                <td class="px-10 py-6 text-center">
                                    <span id="badge-<?= $user['id'] ?>" class="status-badge inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest <?= $user['status'] === 'active' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' ?>">
                                        <span class="pulse-dot w-1.5 h-1.5 rounded-full bg-current <?= $user['status'] === 'active' ? 'animate-pulse' : '' ?>"></span>
                                        <span class="status-text"><?= $user['status'] ?></span>
                                    </span>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <button class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 group-hover:bg-slate-900 group-hover:text-white transition-all">
                                        <i class="fas fa-fingerprint text-xs"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div id="drawerOverlay" class="fixed inset-0 bg-slate-950/40 backdrop-blur-sm z-[60] hidden opacity-0 transition-opacity duration-300" onclick="closeDrawer()"></div>
    <aside id="profileDrawer" class="fixed top-0 right-0 h-full w-full max-w-md bg-white shadow-2xl z-[70] drawer-hidden flex flex-col">
        <div class="p-8 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <div>
                <h3 class="text-xl font-black text-slate-900 tracking-tight uppercase">Node Audit</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Surgical Identity Examination</p>
            </div>
            <button onclick="closeDrawer()" class="w-10 h-10 rounded-full hover:bg-white flex items-center justify-center text-slate-400 hover:text-red-500 transition-all border border-transparent hover:border-slate-100"><i class="fas fa-times"></i></button>
        </div>
        <div id="drawerContent" class="flex-1 overflow-y-auto p-10 custom-scrollbar">
            <div class="flex flex-col items-center text-center mb-10">
                <div id="dAvatar" class="w-24 h-24 rounded-[2rem] bg-blue-600 flex items-center justify-center text-3xl font-black text-white shadow-xl mb-6">?</div>
                <h4 id="dName" class="text-2xl font-black text-slate-900 leading-none mb-2">---</h4>
                <p id="dEmail" class="text-xs font-bold text-slate-400 uppercase tracking-widest">---</p>
            </div>
            <div class="space-y-8">
                <div class="grid grid-cols-2 gap-4">
                    <div class="hud-card p-4 rounded-2xl">
                        <p class="text-[8px] font-black text-slate-400 uppercase mb-1">Phone Protocol</p>
                        <p id="dPhone" class="text-xs font-bold text-slate-900">---</p>
                    </div>
                    <div class="hud-card p-4 rounded-2xl">
                        <p class="text-[8px] font-black text-slate-400 uppercase mb-1">Node Status</p>
                        <p id="dStatus" class="text-xs font-black uppercase">---</p>
                    </div>
                </div>
                <div class="hud-card p-6 rounded-[2rem]">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3 border-b border-slate-100 pb-2">Geospatial Data</p>
                    <div class="space-y-2">
                        <div class="flex justify-between text-[11px] font-bold">
                            <span class="text-slate-400 uppercase">County:</span>
                            <span id="dCounty" class="text-slate-900">---</span>
                        </div>
                        <div class="flex justify-between text-[11px] font-bold">
                            <span class="text-slate-400 uppercase">Area Node:</span>
                            <span id="dArea" class="text-slate-900">---</span>
                        </div>
                    </div>
                </div>
                <div class="pt-6 space-y-3">
                    <button id="btnEdit" onclick="toggleEditMode()" class="w-full py-4 bg-slate-900 text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] hover:bg-blue-600 transition-all shadow-lg">Edit Registry Entry</button>
                    <button id="btnTerminate" onclick="executeStatusToggle()" class="w-full py-4 bg-red-50 text-red-600 border border-red-100 rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] hover:bg-red-600 hover:text-white transition-all">Terminate Protocol Access</button>
                </div>
            </div>
        </div>
    </aside>

    <footer class="h-10 shrink-0 bg-slate-950 border-t border-slate-900 flex items-center px-8 z-50">
        <div class="flex items-center gap-4">
            <span class="w-2 h-2 bg-blue-500 rounded-full animate-ping"></span>
            <p class="text-[9px] font-bold text-slate-500 uppercase tracking-[0.4em] whitespace-nowrap">
                REGISTRY_LEDGER >> BROADCASTING PERSONNEL DATA >> DATABASE SYNCED >> MONITORING <?= $counts['total'] ?> HUMAN NODES
            </p>
        </div>
    </footer>

    <script>
        let activeNodeId = null;
        let isEditing = false;
        let originalData = {};

        async function examineNode(userId) {
            if(isEditing) toggleEditMode(); // Exit edit mode if switching nodes
            activeNodeId = userId;
            
            const drawer = document.getElementById('profileDrawer');
            const overlay = document.getElementById('drawerOverlay');
            overlay.classList.remove('hidden');
            setTimeout(() => overlay.classList.add('opacity-100'), 10);
            drawer.classList.remove('drawer-hidden');

            document.getElementById('dName').innerText = "LOADING...";

            try {
                const response = await fetch(`get_user_intel.php?id=${userId}`);
                const data = await response.json();

                if (data.success) {
                    originalData = data;
                    updateDrawerUI(data);
                }
            } catch (err) {
                document.getElementById('dName').innerText = "PROTOCOL ERROR";
            }
        }

        function updateDrawerUI(data) {
            document.getElementById('dName').innerText = data.full_name;
            document.getElementById('dEmail').innerText = data.email;
            document.getElementById('dPhone').innerText = data.phone;
            document.getElementById('dStatus').innerText = data.status;
            document.getElementById('dCounty').innerText = data.county;
            document.getElementById('dArea').innerText = data.area;
            
            const avatar = document.getElementById('dAvatar');
            avatar.innerText = data.full_name.charAt(0).toUpperCase();
            avatar.className = `w-24 h-24 rounded-[2rem] bg-blue-600 flex items-center justify-center text-3xl font-black text-white shadow-xl mb-6 ${data.role === 'WORKER' ? 'bg-blue-600' : 'bg-slate-900'}`;

            const statusEl = document.getElementById('dStatus');
            statusEl.className = `text-xs font-black uppercase ${data.status === 'ACTIVE' ? 'text-green-600' : 'text-red-600'}`;

            // Adjust Terminate button text based on status
            const btnT = document.getElementById('btnTerminate');
            if(data.status === 'ACTIVE') {
                btnT.innerText = "Terminate Protocol Access";
                btnT.className = "w-full py-4 bg-red-50 text-red-600 border border-red-100 rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] hover:bg-red-600 hover:text-white transition-all";
            } else {
                btnT.innerText = "Restore Node Access";
                btnT.className = "w-full py-4 bg-green-50 text-green-600 border border-green-100 rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] hover:bg-green-600 hover:text-white transition-all";
            }
        }

        function toggleEditMode() {
            isEditing = !isEditing;
            const btn = document.getElementById('btnEdit');
            const fields = ['dName', 'dPhone', 'dCounty', 'dArea'];

            if (isEditing) {
                fields.forEach(id => {
                    const el = document.getElementById(id);
                    const val = el.innerText;
                    el.innerHTML = `<input type="text" id="input-${id}" class="edit-input" value="${val}">`;
                });
                btn.innerText = "Commit Protocol Changes";
                btn.classList.replace('bg-slate-900', 'bg-blue-600');
            } else {
                // If this is triggered by "Commit", logic is handled in save function. 
                // If triggered by switching nodes, we just revert the HTML.
                updateDrawerUI(originalData);
                btn.innerText = "Edit Registry Entry";
                btn.classList.replace('bg-blue-600', 'bg-slate-900');
            }

            // If we just clicked "Commit" while isEditing was true
            if (!isEditing) executeMetadataUpdate();
        }

        async function executeMetadataUpdate() {
            const payload = {
                action: 'UPDATE_METADATA',
                user_id: activeNodeId,
                full_name: document.getElementById('input-dName').value,
                phone: document.getElementById('input-dPhone').value,
                county: document.getElementById('input-dCounty').value,
                area: document.getElementById('input-dArea').value
            };

            const response = await fetch('update_user_node.php', {
                method: 'POST',
                body: JSON.stringify(payload)
            });
            const result = await response.json();

            if (result.success) {
                // Sync Background Table
                document.getElementById(`t-name-${activeNodeId}`).innerText = payload.full_name;
                document.getElementById(`t-county-${activeNodeId}`).innerText = payload.county;
                document.getElementById(`t-area-${activeNodeId}`).innerText = payload.area;
                document.getElementById(`avatar-${activeNodeId}`).innerText = payload.full_name.charAt(0).toUpperCase();
                
                // Revert Drawer
                originalData.full_name = payload.full_name;
                originalData.phone = payload.phone;
                originalData.county = payload.county;
                originalData.area = payload.area;
                updateDrawerUI(originalData);
            }
        }

        async function executeStatusToggle() {
            if(!confirm("Execute Node Status Transition?")) return;

            const response = await fetch('update_user_node.php', {
                method: 'POST',
                body: JSON.stringify({ action: 'TOGGLE_STATUS', user_id: activeNodeId })
            });
            const result = await response.json();

            if (result.success) {
                // Update Table Row Status
                const badge = document.getElementById(`badge-${activeNodeId}`);
                const dot = badge.querySelector('.pulse-dot');
                const text = badge.querySelector('.status-text');

                if(result.new_status === 'ACTIVE') {
                    badge.className = "status-badge inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-green-100 text-green-600";
                    dot.classList.add('animate-pulse');
                } else {
                    badge.className = "status-badge inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-red-100 text-red-600";
                    dot.classList.remove('animate-pulse');
                }
                text.innerText = result.new_status.toLowerCase();

                // Update Drawer
                originalData.status = result.new_status;
                updateDrawerUI(originalData);
            }
        }

        function closeDrawer() {
            document.getElementById('drawerOverlay').classList.remove('opacity-100');
            setTimeout(() => document.getElementById('drawerOverlay').classList.add('hidden'), 300);
            document.getElementById('profileDrawer').classList.add('drawer-hidden');
            isEditing = false;
        }
    </script>
</body>
</html>