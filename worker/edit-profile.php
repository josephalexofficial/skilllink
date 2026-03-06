<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: worker/edit-profile.php
 * PURPOSE: Elite Command Center - Auto-Dismissing Feedback & URL Logic
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

// 3. Data Retrieval
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];

$profile_query = $conn->query("SELECT wp.*, c.cat_name 
                               FROM worker_profiles wp 
                               JOIN categories c ON wp.category_id = c.id 
                               WHERE wp.user_id = '$user_id'");
$worker = $profile_query->fetch_assoc();

$categories = $conn->query("SELECT * FROM categories WHERE status = 'active' ORDER BY sort_order ASC");

// 4. Dynamic Status Engine
$status_map = [
    'synced'        => ['msg' => 'Profile Suite Synced Successfully', 'color' => 'bg-green-500', 'icon' => 'fa-check-circle'],
    'mismatch'      => ['msg' => 'New Passwords Do Not Match', 'color' => 'bg-red-500', 'icon' => 'fa-exclamation-triangle'],
    'wrong_pass'    => ['msg' => 'Incorrect Current Password', 'color' => 'bg-red-500', 'icon' => 'fa-lock-keyhole-dash'],
    'upload_failed' => ['msg' => 'Document Upload Failed', 'color' => 'bg-amber-500', 'icon' => 'fa-cloud-xmark'],
    'error'         => ['msg' => 'System Error: Sync Interrupted', 'color' => 'bg-slate-900', 'icon' => 'fa-bug']
];

$active_status = isset($_GET['status']) && isset($status_map[$_GET['status']]) ? $status_map[$_GET['status']] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings | SkillLink Pro</title>
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
        .custom-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .tab-active { color: skill-blue; border-bottom: 3px solid currentColor; opacity: 1 !important; }
        .step-content { display: none; }
        .step-content.active { display: block; animation: fadeIn 0.4s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        /* Elite Interaction Styles */
        .upload-zone:hover { border-color: #3b82f6; background-color: #f0f7ff; }
        .password-match { border-color: #10b981 !important; }
        .password-mismatch { border-color: #ef4444 !important; }
        
        /* Toast Exit Transition */
        .toast-exit { 
            opacity: 0; 
            transform: translateY(-20px) scale(0.95); 
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1); 
        }
    </style>
</head>
<body class="bg-[#f1f5f9] font-sans antialiased text-slate-900 overflow-hidden">

    <div class="flex h-screen overflow-hidden">
        
        <aside class="w-72 glass-sidebar border-r border-slate-200 flex flex-col z-50">
            <div class="p-8">
                <div class="flex items-center gap-3 mb-10">
                    <div class="w-10 h-10 bg-slate-900 text-white rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-briefcase text-sm"></i>
                    </div>
                    <span class="text-xl font-black tracking-tighter uppercase">Skill<span class="text-skill-blue">Link</span></span>
                </div>

                <nav class="space-y-2">
                    <a href="dashboard.php" class="flex items-center gap-4 px-6 py-4 text-slate-500 hover:bg-slate-50 rounded-2xl font-bold text-sm transition-all">
                        <i class="fas fa-chart-line text-xs"></i> Overview
                    </a>
                    <a href="manage-tasks.php" class="flex items-center gap-4 px-6 py-4 text-slate-500 hover:bg-slate-50 rounded-2xl font-bold text-sm transition-all">
                        <i class="fas fa-list-check text-xs"></i> Active Tasks
                    </a>
                    <a href="earnings.php" class="flex items-center gap-4 px-6 py-4 text-slate-500 hover:bg-slate-50 rounded-2xl font-bold text-sm transition-all">
                        <i class="fas fa-wallet text-xs"></i> Earnings
                    </a>
                    <a href="edit-profile.php" class="bg-slate-50 text-skill-blue border-r-4 border-skill-blue flex items-center gap-4 px-6 py-4 rounded-2xl font-bold text-sm transition-all">
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
            
            <header class="h-24 flex items-center justify-between px-12 bg-white border-b border-slate-100">
                <div class="flex items-center gap-4">
                    <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
                    <div>
                        <h1 class="text-lg font-black text-slate-900 leading-none">Settings: <span class="text-skill-blue">Command Center</span></h1>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1">Nationwide Professional Suite</p>
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    <div class="text-right hidden md:block">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Professional</p>
                        <p class="font-black text-slate-900"><?php echo $user_name; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-slate-100 rounded-2xl overflow-hidden border-2 border-white shadow-xl">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=0f172a&color=fff" alt="User">
                    </div>
                </div>
            </header>

            <section class="flex-1 overflow-y-auto p-12 custom-scroll bg-[#f8fafc]/50">
                
                <?php if($active_status): ?>
                <div id="status-toast" class="mb-8 flex items-center justify-between <?php echo $active_status['color']; ?> text-white px-8 py-5 rounded-[2rem] shadow-2xl animate-bounce border-4 border-white/20 transition-all duration-500">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center shadow-inner">
                            <i class="fas <?php echo $active_status['icon']; ?> text-lg"></i>
                        </div>
                        <span class="text-xs font-black uppercase tracking-[0.1em]"><?php echo $active_status['msg']; ?></span>
                    </div>
                    <button onclick="dismissToast()" class="bg-white/10 hover:bg-white/30 w-8 h-8 rounded-full flex items-center justify-center transition-all">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
                <?php endif; ?>

                <div class="flex items-center gap-12 border-b border-slate-200 mb-10 px-4">
                    <button onclick="switchTab('brand')" id="btn-brand" class="tab-active py-4 text-xs font-black uppercase tracking-widest transition-all">My Profile</button>
                    <button onclick="switchTab('ops')" id="btn-ops" class="py-4 text-slate-400 hover:text-slate-900 text-xs font-black uppercase tracking-widest transition-all opacity-60">Work Settings</button>
                    <button onclick="switchTab('vault')" id="btn-vault" class="py-4 text-slate-400 hover:text-slate-900 text-xs font-black uppercase tracking-widest transition-all opacity-60">Verification</button>
                    <button onclick="switchTab('shield')" id="btn-shield" class="py-4 text-slate-400 hover:text-slate-900 text-xs font-black uppercase tracking-widest transition-all opacity-60">Security</button>
                </div>

                <form action="update-logic.php" method="POST" enctype="multipart/form-data" id="profileForm">
                    
                    <div id="tab-brand" class="step-content active grid grid-cols-1 lg:grid-cols-3 gap-12">
                        <div class="lg:col-span-2 space-y-8">
                            <div class="bg-white p-10 rounded-[3rem] border border-slate-100 shadow-sm space-y-8">
                                <div class="flex items-center gap-8 pb-8 border-b border-slate-50">
                                    <div class="relative w-32 h-32 flex-none group">
                                        <img id="preview" src="../assets/uploads/profiles/<?php echo $worker['profile_photo']; ?>" class="w-full h-full object-cover rounded-[2.5rem] border-4 border-white shadow-2xl">
                                        <label class="absolute -bottom-2 -right-2 w-10 h-10 bg-skill-blue text-white rounded-xl flex items-center justify-center cursor-pointer shadow-lg hover:scale-110 transition-all">
                                            <i class="fas fa-camera text-sm"></i>
                                            <input type="file" name="profile_photo" class="hidden" onchange="previewImage(this)">
                                        </label>
                                    </div>
                                    <div class="space-y-2">
                                        <h3 class="text-xl font-black text-slate-900 leading-none">Professional Identity</h3>
                                        <p class="text-xs text-slate-500 font-bold leading-relaxed max-w-sm">Clear headshots increase booking rates by 40% nationwide.</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div>
                                        <label class="block text-xs font-black text-slate-600 uppercase tracking-widest mb-3 ml-2">Professional Craft</label>
                                        <select name="category_id" class="w-full px-6 py-4 bg-slate-50 rounded-2xl text-xs font-black text-slate-900 focus:ring-4 focus:ring-skill-blue/10 border-none transition-all">
                                            <?php while($cat = $categories->fetch_assoc()): ?>
                                            <option value="<?php echo $cat['id']; ?>" <?php echo ($cat['id'] == $worker['category_id']) ? 'selected' : ''; ?>>
                                                <?php echo $cat['cat_name']; ?>
                                            </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-black text-slate-600 uppercase tracking-widest mb-3 ml-2">Display Name</label>
                                        <input type="text" name="full_name" value="<?php echo $user_name; ?>" class="w-full px-6 py-4 bg-slate-50 rounded-2xl text-xs font-bold text-slate-900 border-none focus:ring-4 focus:ring-skill-blue/10 transition-all">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-black text-slate-600 uppercase tracking-widest mb-3 ml-2">Professional Pitch</label>
                                    <textarea name="bio" rows="4" maxlength="250" oninput="updateCharCount(this)" class="w-full px-8 py-6 bg-slate-50 rounded-[2rem] text-sm font-semibold text-slate-800 border-none focus:ring-4 focus:ring-skill-blue/10 transition-all resize-none shadow-inner"><?php echo $worker['bio']; ?></textarea>
                                    <div class="flex justify-end mt-3 pr-4"><span id="charCount" class="text-[9px] font-black text-skill-blue bg-skill-blue/10 px-3 py-1 rounded-full uppercase"><?php echo strlen($worker['bio']); ?>/250</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest px-4">Marketplace Preview</h3>
                            <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-xl text-center relative overflow-hidden">
                                <div class="absolute top-0 right-0 p-6 opacity-10"><i class="fas fa-briefcase text-6xl"></i></div>
                                <img id="preview-side" src="../assets/uploads/profiles/<?php echo $worker['profile_photo']; ?>" class="w-24 h-24 rounded-[2rem] mx-auto mb-6 border-4 border-slate-50 shadow-lg">
                                <h4 class="text-lg font-black text-slate-900"><?php echo $user_name; ?></h4>
                                <div class="inline-block mt-2 px-4 py-1.5 bg-skill-blue/10 rounded-full">
                                    <span class="text-[10px] text-skill-blue font-black uppercase tracking-widest"><?php echo $worker['cat_name']; ?> Specialist</span>
                                </div>
                                <p id="bio-preview" class="text-xs text-slate-500 font-medium leading-relaxed mt-6 italic">"<?php echo $worker['bio']; ?>"</p>
                                <div class="mt-8 pt-6 border-t border-slate-50 flex justify-center gap-1 text-amber-400 text-xs">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="tab-ops" class="step-content">
                        <div class="max-w-4xl grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="bg-white p-10 rounded-[3.5rem] shadow-sm border border-slate-100 space-y-10">
                                <div>
                                    <h3 class="text-xl font-black text-slate-900 mb-2">Operations & Logistics</h3>
                                    <p class="text-xs text-slate-500 font-bold tracking-tight">Manage nationwide regional leads.</p>
                                </div>
                                
                                <div class="flex items-center justify-between bg-slate-50 p-6 rounded-[2rem]">
                                    <div>
                                        <h4 class="text-sm font-black text-slate-900">Market Visibility</h4>
                                        <p class="text-[9px] text-slate-400 font-bold uppercase mt-1">Live status in your service area</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_live" value="1" <?php echo ($worker['is_live'] == 1) ? 'checked' : ''; ?> class="sr-only peer">
                                        <div class="w-14 h-8 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-500 shadow-inner"></div>
                                    </label>
                                </div>

                                <div class="space-y-6 pt-4">
                                    <div>
                                        <label class="block text-xs font-black text-slate-600 uppercase tracking-widest mb-3 ml-2">Service Radius (km)</label>
                                        <input type="number" name="service_radius" value="<?php echo $worker['service_radius']; ?>" class="w-full px-6 py-4 bg-slate-50 rounded-2xl text-xs font-black text-slate-900 border-none shadow-inner">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-black text-slate-600 uppercase tracking-widest mb-3 ml-2">Starting Rate (Ksh)</label>
                                        <input type="number" name="hourly_rate" value="<?php echo $worker['hourly_rate']; ?>" class="w-full px-6 py-4 bg-slate-50 rounded-2xl text-xs font-black text-slate-900 border-none shadow-inner">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-slate-900 p-10 rounded-[3.5rem] text-white flex flex-col justify-center relative overflow-hidden shadow-2xl">
                                <i class="fas fa-route absolute -right-10 -bottom-10 text-9xl opacity-10"></i>
                                <h3 class="text-2xl font-black mb-4">Regional Reach</h3>
                                <p class="text-slate-400 text-sm leading-relaxed mb-8">Your settings currently cover a <strong><?php echo $worker['service_radius']; ?>km</strong> radius across Kenya.</p>
                                <div class="flex items-center gap-4 bg-white/5 p-4 rounded-2xl border border-white/10">
                                    <div class="w-10 h-10 bg-skill-blue rounded-xl flex items-center justify-center shadow-lg"><i class="fas fa-map-location-dot"></i></div>
                                    <span class="text-xs font-bold uppercase tracking-widest">Optimization: Active</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="tab-vault" class="step-content">
                        <div class="max-w-2xl bg-white p-12 rounded-[3.5rem] shadow-sm border border-slate-100 text-center">
                            <div class="w-20 h-20 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner">
                                <i class="fas fa-shield-halved text-3xl"></i>
                            </div>
                            <h3 class="text-2xl font-[900] text-slate-900 tracking-tight">National Trust Docs</h3>
                            <p class="text-slate-500 font-bold text-xs mt-4 mb-10 max-w-sm mx-auto uppercase tracking-widest">Achieve Certified Pro Status</p>
                            
                            <div onclick="document.getElementById('id_proof').click()" 
                                 class="upload-zone border-2 border-dashed border-slate-200 rounded-[3rem] p-16 transition-all cursor-pointer group relative overflow-hidden">
                                <i class="fas fa-cloud-arrow-up text-4xl text-slate-300 group-hover:text-skill-blue mb-4 transition-colors"></i>
                                <p id="file-status" class="text-xs font-black text-slate-400 group-hover:text-skill-blue uppercase tracking-widest">Upload National ID or Permit</p>
                                
                                <input type="file" id="id_proof" name="id_proof" class="hidden" onchange="handleVaultUpload(this)">
                            </div>
                            <div id="file-badge" class="hidden mt-6 inline-flex items-center gap-2 bg-skill-blue/10 text-skill-blue px-4 py-2 rounded-full text-[10px] font-black uppercase">
                                <i class="fas fa-file-invoice"></i> <span id="file-name-text"></span>
                            </div>
                        </div>
                    </div>

                    <div id="tab-shield" class="step-content">
                        <div class="max-w-xl bg-white p-12 rounded-[3.5rem] shadow-sm border border-slate-100 space-y-10">
                            <div>
                                <h3 class="text-xl font-black text-slate-900 mb-2">Account Shield</h3>
                                <p class="text-xs text-slate-500 font-bold tracking-tight">Protect your professional account.</p>
                            </div>
                            
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-xs font-black text-slate-600 uppercase tracking-widest mb-3 ml-2">Current Password</label>
                                    <input type="password" name="current_password" class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none shadow-inner text-sm">
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-xs font-black text-slate-600 uppercase tracking-widest mb-3 ml-2">New Password</label>
                                        <input type="password" id="new_password" name="new_password" oninput="checkPassMatch()"
                                               class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none shadow-inner text-sm transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-black text-slate-600 uppercase tracking-widest mb-3 ml-2">Confirm New</label>
                                        <input type="password" id="confirm_password" name="confirm_password" oninput="checkPassMatch()"
                                               class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none shadow-inner text-sm transition-all">
                                    </div>
                                </div>
                                <div id="match-indicator" class="hidden flex items-center gap-2 px-4 py-2 rounded-xl text-[10px] font-black uppercase shadow-sm">
                                    <i id="match-icon" class="fas"></i> <span id="match-text"></span>
                                </div>
                            </div>

                            <div class="pt-6 border-t border-slate-50 text-right">
                                <button type="button" class="text-red-500 text-xs font-black uppercase tracking-[0.2em] hover:underline transition-all">Deactivate my account</button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 flex items-center justify-between bg-white px-10 py-6 rounded-[2.5rem] border border-slate-100 shadow-xl">
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest italic opacity-70">Changes synced nationwide</p>
                        <button type="submit" id="submitBtn" class="px-12 py-5 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-skill-blue transition-all shadow-xl active:scale-95">
                            Update Suite <i class="fas fa-sync-alt ml-3"></i>
                        </button>
                    </div>

                </form>
            </section>
        </main>
    </div>

    <script>
        // ELITE TOAST ENGINE: Auto-dismiss and URL Cleanup
        window.addEventListener('DOMContentLoaded', (event) => {
            const toast = document.getElementById('status-toast');
            if (toast) {
                // Wait 4 seconds, then gracefully dismiss
                setTimeout(() => {
                    dismissToast();
                }, 4000);
            }
        });

        function dismissToast() {
            const toast = document.getElementById('status-toast');
            if (toast) {
                toast.classList.add('toast-exit');
                setTimeout(() => {
                    toast.remove();
                    // Surgical URL Cleanup
                    const url = new URL(window.location);
                    url.searchParams.delete('status');
                    window.history.replaceState({}, document.title, url);
                }, 600);
            }
        }

        // TAB NAVIGATION
        function switchTab(tabId) {
            document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('[id^="btn-"]').forEach(el => {
                el.classList.remove('tab-active', 'text-slate-900');
                el.classList.add('text-slate-400', 'opacity-60');
            });
            
            document.getElementById('tab-' + tabId).classList.add('active');
            const activeBtn = document.getElementById('btn-' + tabId);
            activeBtn.classList.add('tab-active', 'text-slate-900');
            activeBtn.classList.remove('text-slate-400', 'opacity-60');
        }

        // VAULT INTERACTION
        function handleVaultUpload(input) {
            if (input.files && input.files[0]) {
                const badge = document.getElementById('file-badge');
                const text = document.getElementById('file-name-text');
                const status = document.getElementById('file-status');
                
                text.textContent = input.files[0].name;
                badge.classList.remove('hidden');
                status.textContent = "Document Ready";
                status.classList.add('text-skill-blue');
            }
        }

        // SECURITY VALIDATION
        function checkPassMatch() {
            const p1 = document.getElementById('new_password');
            const p2 = document.getElementById('confirm_password');
            const indicator = document.getElementById('match-indicator');
            const icon = document.getElementById('match-icon');
            const text = document.getElementById('match-text');
            const btn = document.getElementById('submitBtn');

            if (p1.value === "" && p2.value === "") {
                indicator.classList.add('hidden');
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                return;
            }

            indicator.classList.remove('hidden');

            if (p1.value === p2.value) {
                indicator.className = "flex items-center gap-2 px-4 py-2 rounded-xl text-[10px] font-black uppercase bg-green-50 text-green-500";
                icon.className = "fas fa-check-circle";
                text.textContent = "Passwords Match";
                p2.classList.add('password-match');
                p2.classList.remove('password-mismatch');
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                indicator.className = "flex items-center gap-2 px-4 py-2 rounded-xl text-[10px] font-black uppercase bg-red-50 text-red-500";
                icon.className = "fas fa-times-circle";
                text.textContent = "Passwords Do Not Match";
                p2.classList.add('password-mismatch');
                p2.classList.remove('password-match');
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }

        // BIOMETRIC SYNC HELPERS
        function updateCharCount(textarea) {
            document.getElementById('charCount').textContent = textarea.value.length + '/250';
            document.getElementById('bio-preview').textContent = '"' + textarea.value + '"';
        }

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                    document.getElementById('preview-side').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>