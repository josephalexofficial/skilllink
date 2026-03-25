<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: admin/verifications.php
 * VERSION: 1.3 "The Identity Audit Bureau - Hardened HUD"
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

// HELPER: Generate Consistent Architectural Gradients for Initials
function getSlateGradient($id) {
    $gradients = [
        'from-slate-800 to-slate-900',
        'from-blue-900 to-slate-900',
        'from-indigo-950 to-slate-900',
        'from-slate-700 to-slate-800',
        'from-blue-950 to-indigo-950'
    ];
    return $gradients[$id % count($gradients)];
}

// 3. THE JUDGE: Handle Verification Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $target_user_id = intval($_POST['user_id']);
    
    if ($_POST['action'] === 'approve') {
        $conn->query("UPDATE worker_profiles SET is_verified = 1 WHERE user_id = $target_user_id");
    } elseif ($_POST['action'] === 'reject') {
        $conn->query("UPDATE users SET status = 'suspended' WHERE id = $target_user_id");
    }
    header("Location: verifications.php?status=updated");
    exit();
}

// 4. THE ANALYST: Fetch Pending Queue
try {
    $query = "
        SELECT 
            u.id, u.full_name, u.email, u.phone, u.created_at,
            wp.bio, wp.profile_photo, wp.id_proof_path, wp.onboarded_at,
            c.cat_name, c.cat_icon
        FROM users u
        JOIN worker_profiles wp ON u.id = wp.user_id
        JOIN categories c ON wp.category_id = c.id
        WHERE wp.is_verified = 0 AND u.status = 'active'
        ORDER BY wp.onboarded_at ASC";
    
    $queue = $conn->query($query);
    $pending_count = $queue->num_rows;

} catch (Exception $e) {
    error_log("Verification Error: " . $e->getMessage());
    $pending_count = 0;
}
?>

<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillLink | Identity Audit Bureau</title>
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
            transition: all 0.3s ease;
        }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        
        /* Zoom Effect for ID Proof */
        .zoom-container img {
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: zoom-in;
        }
        .zoom-container:hover img {
            transform: scale(1.4);
        }

        /* Identity Ring Pulse */
        .identity-pulse {
            position: relative;
        }
        .identity-pulse::after {
            content: '';
            position: absolute;
            inset: -4px;
            border: 2px solid #f59e0b;
            border-radius: 2.5rem;
            animation: pulse-ring 2s infinite;
        }
        @keyframes pulse-ring {
            0% { transform: scale(1); opacity: 0.8; }
            100% { transform: scale(1.1); opacity: 0; }
        }
    </style>
</head>
<body class="h-screen overflow-hidden flex flex-col blueprint-bg">

    <header class="h-16 shrink-0 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-8 z-50">
        <div class="flex items-center gap-4">
            <span class="text-[10px] font-black bg-blue-600 text-white px-2 py-0.5 rounded tracking-widest uppercase shadow-lg shadow-blue-500/20">Audit Mode</span>
            <div class="h-4 w-[1px] bg-slate-200"></div>
            <h1 class="text-sm font-bold text-slate-500 uppercase tracking-widest">Command Center / <span class="text-slate-900">Identity Bureau</span></h1>
        </div>
        <div class="flex items-center gap-4">
            <div class="bg-amber-50 text-amber-600 px-4 py-1.5 rounded-full border border-amber-100 text-[10px] font-black uppercase tracking-widest">
                <?= $pending_count ?> Nodes Awaiting Certification
            </div>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">
        <aside class="w-72 bg-slate-900 text-white flex flex-col h-full flex-none shadow-2xl z-40 relative">
            <div class="p-8 border-b border-slate-800/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                        <i class="fas fa-shield-halved text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black tracking-tighter uppercase leading-none">Skill<span class="text-blue-500">Link</span></h2>
                        <p class="text-[9px] uppercase tracking-[0.3em] text-slate-500 font-bold mt-1">Security Node</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 p-6 space-y-2 overflow-y-auto custom-scrollbar">
                <a href="dashboard.php" class="flex items-center gap-4 px-5 py-3.5 text-slate-400 hover:text-white hover:bg-slate-800/50 rounded-xl font-bold transition-all group">
                    <i class="fas fa-grid-2 text-sm"></i> Dashboard
                </a>
                <a href="verifications.php" class="flex items-center gap-4 px-5 py-3.5 bg-blue-600/10 text-blue-500 rounded-xl font-bold transition-all border border-blue-600/20">
                    <i class="fas fa-user-check text-sm"></i> Verification
                </a>
                <a href="skills.php" class="flex items-center gap-4 px-5 py-3.5 text-slate-400 hover:text-white hover:bg-slate-800/50 rounded-xl font-bold transition-all group">
                    <i class="fas fa-layer-group group-hover:text-blue-500 text-sm"></i> Skill Tree
                </a>
                <a href="reports.php" class="flex items-center gap-4 px-5 py-3.5 text-slate-400 hover:text-white hover:bg-slate-800/50 rounded-xl font-bold transition-all group">
                    <i class="fas fa-flag group-hover:text-blue-500 text-sm"></i> Reports
                </a>
            </nav>

            <div class="p-6 border-t border-slate-800/50 bg-slate-900/50">
                <a href="../auth/logout.php" class="flex items-center justify-center gap-3 w-full py-3.5 bg-red-500/5 text-red-500 rounded-xl font-bold hover:bg-red-500 hover:text-white transition-all border border-red-500/20">
                    <i class="fas fa-power-off text-sm"></i> Logout
                </a>
            </div>
        </aside>

        <section class="flex-1 h-full overflow-y-auto custom-scrollbar p-10 space-y-8">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-slate-900 uppercase">Audit Ledger</h1>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mt-1">Pending Professional Certification Queue</p>
            </div>

            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200/60 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[10px] uppercase tracking-[0.25em] text-slate-400 font-black bg-slate-50/50">
                                <th class="px-10 py-6 border-b border-slate-100">Worker Node</th>
                                <th class="px-10 py-6 border-b border-slate-100">Classification</th>
                                <th class="px-10 py-6 border-b border-slate-100">Submission Date</th>
                                <th class="px-10 py-6 border-b border-slate-100 text-right">Audit Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if ($pending_count > 0): ?>
                                <?php while($row = $queue->fetch_assoc()): 
                                    $has_photo = !empty($row['profile_photo']) && file_exists("../uploads/profiles/" . $row['profile_photo']);
                                    $initials = strtoupper(substr($row['full_name'], 0, 1) . substr(explode(' ', $row['full_name'])[1] ?? '', 0, 1));
                                    $gradient = getSlateGradient($row['id']);
                                ?>
                                <tr class="hover:bg-blue-50/20 transition-all group">
                                    <td class="px-10 py-6">
                                        <div class="flex items-center gap-4">
                                            <?php if ($has_photo): ?>
                                                <img src="../uploads/profiles/<?= $row['profile_photo'] ?>" class="w-12 h-12 rounded-2xl object-cover shadow-sm border-2 border-white">
                                            <?php else: ?>
                                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br <?= $gradient ?> flex items-center justify-center font-black text-white text-xs shadow-sm">
                                                    <?= $initials ?>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <p class="text-sm font-extrabold text-slate-900 leading-none mb-1"><?= $row['full_name'] ?></p>
                                                <p class="text-[10px] text-slate-400 font-bold tracking-tighter"><?= $row['email'] ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-10 py-6">
                                        <div class="flex items-center gap-2">
                                            <i class="fas <?= $row['cat_icon'] ?> text-blue-500 text-xs"></i>
                                            <span class="text-[11px] font-black uppercase text-slate-600"><?= $row['cat_name'] ?></span>
                                        </div>
                                    </td>
                                    <td class="px-10 py-6">
                                        <p class="text-[10px] font-mono font-bold text-slate-500 uppercase tracking-tighter">
                                            <?= date('M d, Y | H:i', strtotime($row['onboarded_at'])) ?>
                                        </p>
                                    </td>
                                    <td class="px-10 py-6 text-right">
                                        <button onclick='openAuditModal(<?= json_encode($row) ?>, "<?= $initials ?>", "<?= $gradient ?>", <?= $has_photo ? "true" : "false" ?>)' class="px-6 py-2.5 bg-slate-900 text-white text-[10px] font-black rounded-xl hover:bg-blue-600 transition-all uppercase tracking-widest">
                                            Inspect Node
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="px-10 py-24 text-center">
                                        <div class="flex flex-col items-center opacity-30">
                                            <i class="fas fa-shield-check text-6xl mb-4 text-slate-400"></i>
                                            <p class="text-sm font-black uppercase tracking-widest text-slate-500">All Nodes Certified. Protocol Clear.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <div id="auditModal" class="fixed inset-0 z-[100] hidden bg-slate-950/90 backdrop-blur-xl flex items-center justify-center p-6">
        <div class="bg-white w-full max-w-6xl rounded-[3rem] shadow-2xl overflow-hidden flex flex-col max-h-[90vh] border border-white/20">
            
            <div class="px-12 py-8 border-b border-slate-100 flex justify-between items-center shrink-0 bg-slate-50/50">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="text-[8px] font-black bg-slate-900 text-white px-2 py-0.5 rounded uppercase tracking-[0.2em]">Security Check</span>
                        <span id="modalNodeId" class="text-[9px] font-mono font-bold text-slate-400 uppercase">NODE_ID: ---</span>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 tracking-tight" id="modalName">Deep-Review HUD</h3>
                </div>
                <button onclick="closeAuditModal()" class="w-12 h-12 rounded-full bg-white shadow-sm flex items-center justify-center text-slate-400 hover:text-red-500 transition-all border border-slate-100">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="flex flex-1 overflow-hidden">
                
                <div class="w-2/5 p-12 border-r border-slate-100 overflow-y-auto custom-scrollbar bg-white">
                    <div class="flex justify-center mb-10">
                        <div id="modalAvatarContainer" class="identity-pulse">
                            </div>
                    </div>
                    
                    <div class="space-y-8">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Professional Bio</p>
                                <span class="text-[8px] font-bold text-blue-500 uppercase tracking-widest">Verified Content Required</span>
                            </div>
                            <p id="modalBio" class="text-sm text-slate-600 leading-relaxed font-medium italic bg-slate-50 p-6 rounded-3xl border border-slate-100"></p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-5 bg-slate-900 rounded-2xl shadow-xl shadow-slate-200">
                                <p class="text-[8px] font-black text-slate-500 mb-2 uppercase tracking-widest">Phone Protocol</p>
                                <p id="modalPhone" class="text-xs font-bold text-white font-mono"></p>
                            </div>
                            <div class="p-5 bg-blue-600 rounded-2xl shadow-xl shadow-blue-200">
                                <p class="text-[8px] font-black text-blue-200 mb-2 uppercase tracking-widest">Classification</p>
                                <p id="modalSkill" class="text-xs font-black text-white uppercase"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex-1 bg-slate-950 flex flex-col p-12 relative overflow-hidden">
                    <div class="absolute inset-0 opacity-5 pointer-events-none blueprint-bg"></div>
                    <div class="mb-6 flex justify-between items-center relative z-10">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em]">ID Evidence: Optical Analysis</p>
                        </div>
                        <a id="modalIdLink" href="#" target="_blank" class="px-4 py-2 bg-white/5 border border-white/10 text-[9px] font-black text-white uppercase rounded-lg hover:bg-white/10 transition-all">
                            View Source <i class="fas fa-external-link-alt ml-2"></i>
                        </a>
                    </div>
                    
                    <div class="flex-1 bg-black/60 rounded-[2.5rem] border border-white/10 flex items-center justify-center overflow-hidden zoom-container relative z-10 shadow-inner">
                        <img id="modalIdImg" src="" class="max-h-full max-w-full object-contain shadow-2xl">
                        <div id="idMissingMsg" class="hidden flex flex-col items-center gap-4">
                             <i class="fas fa-file-circle-exclamation text-slate-700 text-5xl"></i>
                             <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.3em] italic">No document node found</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-12 py-10 border-t border-slate-100 bg-slate-50/90 flex justify-between gap-6 shrink-0 z-20">
                <form action="" method="POST" class="w-full flex justify-between gap-6">
                    <input type="hidden" name="user_id" id="modalUserId">
                    
                    <button type="submit" name="action" value="reject" class="px-10 py-5 bg-white text-red-600 rounded-[1.5rem] font-black text-[11px] uppercase tracking-[0.2em] hover:bg-red-600 hover:text-white transition-all border border-red-100 shadow-xl shadow-red-500/5">
                        <i class="fas fa-user-xmark mr-3"></i> Terminate Protocol
                    </button>

                    <button type="submit" name="action" value="approve" class="px-16 py-5 bg-slate-900 text-white rounded-[1.5rem] font-black text-[11px] uppercase tracking-[0.2em] hover:bg-blue-600 transition-all shadow-2xl shadow-slate-900/40">
                        <i class="fas fa-shield-check mr-3"></i> Certify Professional Node
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAuditModal(data, initials, gradient, hasPhoto) {
            document.getElementById('modalName').innerText = data.full_name;
            document.getElementById('modalNodeId').innerText = "NODE_ID: " + String(data.id).padStart(4, '0');
            document.getElementById('modalBio').innerText = data.bio || "No professional metadata provided.";
            document.getElementById('modalPhone').innerText = data.phone || "UNSPECIFIED";
            document.getElementById('modalSkill').innerText = data.cat_name.toUpperCase();
            document.getElementById('modalUserId').value = data.id;
            
            // Handle Avatar Fallback in Modal
            const avatarContainer = document.getElementById('modalAvatarContainer');
            if(hasPhoto) {
                avatarContainer.classList.remove('identity-pulse');
                avatarContainer.innerHTML = `<img src="../uploads/profiles/${data.profile_photo}" class="w-32 h-32 rounded-[2.5rem] object-cover shadow-2xl border-4 border-white">`;
            } else {
                avatarContainer.classList.add('identity-pulse');
                avatarContainer.innerHTML = `<div class="w-32 h-32 rounded-[2.5rem] bg-gradient-to-br ${gradient} flex items-center justify-center text-4xl font-black text-white shadow-2xl">${initials}</div>`;
            }

            const idPath = data.id_proof_path;
            const idImg = document.getElementById('modalIdImg');
            const idMsg = document.getElementById('idMissingMsg');
            const idLink = document.getElementById('modalIdLink');

            if(idPath) {
                idImg.src = "../uploads/ids/" + idPath;
                idLink.href = "../uploads/ids/" + idPath;
                idImg.classList.remove('hidden');
                idMsg.classList.add('hidden');
                idLink.classList.remove('hidden');
            } else {
                idImg.classList.add('hidden');
                idMsg.classList.remove('hidden');
                idLink.classList.add('hidden');
            }

            document.getElementById('auditModal').classList.remove('hidden');
        }

        function closeAuditModal() {
            document.getElementById('auditModal').classList.add('hidden');
        }
    </script>
</body>
</html>