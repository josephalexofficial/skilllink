<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: admin/verifications.php
 * VERSION: 1.1 "The Identity Audit Bureau"
 * FIX: Restored missing Reports node in sidebar navigation.
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
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-slate-900 uppercase">Audit Ledger</h1>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mt-1">Pending Professional Certification Queue</p>
                </div>
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
                                <?php while($row = $queue->fetch_assoc()): ?>
                                <tr class="hover:bg-blue-50/20 transition-all group">
                                    <td class="px-10 py-6">
                                        <div class="flex items-center gap-4">
                                            <img src="../uploads/profiles/<?= $row['profile_photo'] ?>" class="w-12 h-12 rounded-2xl object-cover shadow-sm border-2 border-white">
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
                                        <button onclick='openAuditModal(<?= json_encode($row) ?>)' class="px-6 py-2.5 bg-slate-900 text-white text-[10px] font-black rounded-xl hover:bg-blue-600 transition-all uppercase tracking-widest">
                                            Inspect Node
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="px-10 py-24 text-center">
                                        <div class="flex flex-col items-center opacity-30">
                                            <i class="fas fa-shield-check text-6xl mb-4"></i>
                                            <p class="text-sm font-black uppercase tracking-widest">All Nodes Certified. Protocol Clear.</p>
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

    <div id="auditModal" class="fixed inset-0 z-[100] hidden bg-slate-950/60 backdrop-blur-md flex items-center justify-center p-6">
        <div class="bg-white w-full max-w-5xl rounded-[3rem] shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-10 py-6 border-b border-slate-100 flex justify-between items-center shrink-0">
                <div>
                    <h3 class="text-xl font-black text-slate-900 tracking-tight" id="modalName">Deep-Review HUD</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Validating Credentials Against Registry</p>
                </div>
                <button onclick="closeAuditModal()" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:text-red-500 transition-all">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="flex flex-1 overflow-hidden">
                <div class="w-1/3 p-10 border-r border-slate-50 bg-slate-50/30 overflow-y-auto custom-scrollbar">
                    <img id="modalImg" src="" class="w-full aspect-square rounded-[2rem] object-cover mb-6 shadow-xl border-4 border-white">
                    <div class="space-y-6">
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Professional Bio</p>
                            <p id="modalBio" class="text-sm text-slate-600 leading-relaxed font-medium italic"></p>
                        </div>
                        <div class="pt-6 border-t border-slate-200/60">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Metadata Summary</p>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-3 bg-white rounded-xl border border-slate-100">
                                    <p class="text-[8px] font-black text-slate-400 mb-1">PHONE</p>
                                    <p id="modalPhone" class="text-[10px] font-bold text-slate-900"></p>
                                </div>
                                <div class="p-3 bg-white rounded-xl border border-slate-100">
                                    <p class="text-[8px] font-black text-slate-400 mb-1">SKILL</p>
                                    <p id="modalSkill" class="text-[10px] font-bold text-blue-600"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex-1 bg-slate-900 flex flex-col p-10">
                    <div class="mb-4 flex justify-between items-center">
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Digital Identification Evidence</p>
                        <a id="modalIdLink" href="#" target="_blank" class="text-[10px] font-bold text-blue-400 hover:underline">Open Original <i class="fas fa-external-link text-[8px]"></i></a>
                    </div>
                    <div class="flex-1 bg-white/5 rounded-2xl border border-white/10 flex items-center justify-center overflow-hidden">
                        <img id="modalIdImg" src="" class="max-h-full max-w-full object-contain">
                        <p id="idMissingMsg" class="hidden text-slate-600 text-xs italic">No ID Document found for this node.</p>
                    </div>
                </div>
            </div>

            <div class="px-10 py-8 border-t border-slate-100 bg-slate-50/50 flex justify-between gap-4 shrink-0">
                <form action="" method="POST" class="w-full flex justify-between gap-4">
                    <input type="hidden" name="user_id" id="modalUserId">
                    
                    <button type="submit" name="action" value="reject" class="px-8 py-4 bg-red-500/10 text-red-500 rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] hover:bg-red-500 hover:text-white transition-all border border-red-500/20">
                        <i class="fas fa-user-xmark mr-2"></i> Terminate Protocol
                    </button>

                    <button type="submit" name="action" value="approve" class="px-12 py-4 bg-blue-600 text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] hover:bg-blue-700 transition-all shadow-xl shadow-blue-500/30">
                        <i class="fas fa-check-double mr-2"></i> Certify Node
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAuditModal(data) {
            document.getElementById('modalName').innerText = data.full_name;
            document.getElementById('modalBio').innerText = data.bio;
            document.getElementById('modalPhone').innerText = data.phone;
            document.getElementById('modalSkill').innerText = data.cat_name.toUpperCase();
            document.getElementById('modalUserId').value = data.id;
            document.getElementById('modalImg').src = "../uploads/profiles/" + data.profile_photo;
            
            const idPath = data.id_proof_path;
            const idImg = document.getElementById('modalIdImg');
            const idMsg = document.getElementById('idMissingMsg');
            const idLink = document.getElementById('modalIdLink');

            if(idPath) {
                idImg.src = "../uploads/ids/" + idPath;
                idLink.href = "../uploads/ids/" + idPath;
                idImg.classList.remove('hidden');
                idMsg.classList.add('hidden');
            } else {
                idImg.classList.add('hidden');
                idMsg.classList.remove('hidden');
            }

            document.getElementById('auditModal').classList.remove('hidden');
        }

        function closeAuditModal() {
            document.getElementById('auditModal').classList.add('hidden');
        }
    </script>

</body>
</html>