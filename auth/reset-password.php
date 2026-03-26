<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: auth/reset-password.php
 * VERSION: 1.0 "The Credential Reset Terminal"
 */

// 1. System Initialization
$include_path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;

if (file_exists($include_path . 'config.php')) {
    require_once $include_path . 'config.php';
} else {
    die("Critical Error: Core configuration missing.");
}

// 2. Token Intelligence Scan
$token = isset($_GET['token']) ? mysqli_real_escape_string($conn, $_GET['token']) : '';
$is_valid = false;
$email_context = '';

if (!empty($token)) {
    // Check if token exists and has NOT expired
    $check_token = $conn->query("SELECT email FROM password_resets WHERE token = '$token' AND expires_at > NOW() LIMIT 1");
    
    if ($check_token->num_rows > 0) {
        $is_valid = true;
        $email_context = $check_token->fetch_assoc()['email'];
    }
}

// 3. Component Loading
$current_page = 'reset-password';
include $include_path . 'header.php';
?>

<main class="h-[calc(100vh-80px)] flex items-center justify-center bg-white font-sans antialiased text-slate-900 overflow-hidden relative">

    <div class="absolute inset-0 z-0 pointer-events-none select-none">
        <div class="absolute top-[-10%] left-[-5%] w-[600px] h-[600px] bg-blue-500/5 rounded-full blur-[150px]"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[500px] h-[500px] bg-skill-blue/5 rounded-full blur-[120px]"></div>
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: linear-gradient(#94a3b8 1px, transparent 1px), linear-gradient(90deg, #94a3b8 1px, transparent 1px); background-size: 50px 50px;"></div>
    </div>

    <div class="w-full max-w-lg relative z-10 px-6">
        
        <div class="bg-white rounded-[3.5rem] shadow-[0_40px_100px_-20px_rgba(0,0,0,0.08)] border border-slate-100 overflow-hidden">
            
            <div class="p-10 md:p-14">
                
                <?php if ($is_valid): ?>
                    <div class="text-center space-y-2 mb-8">
                        <span class="text-[9px] font-black bg-blue-600 text-white px-3 py-1 rounded-full tracking-widest uppercase mb-4 inline-block italic">Secure Reset Node</span>
                        <h1 class="text-4xl font-[900] tracking-tighter text-slate-900 leading-none">
                            Update <span class="text-skill-blue italic">Password</span>
                        </h1>
                        <p class="text-slate-500 text-sm font-medium px-4">Establishing a new secure access key for <b><?= htmlspecialchars($email_context) ?></b></p>
                    </div>

                    <form action="reset-password-process.php" method="POST" onsubmit="return validatePasswords()" class="space-y-5">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                        <input type="hidden" name="email" value="<?= htmlspecialchars($email_context) ?>">

                        <div class="space-y-1.5">
                            <label class="text-[11px] font-[900] uppercase tracking-widest text-slate-900 ml-3">New Password</label>
                            <div class="relative group">
                                <i class="fas fa-key-skeleton absolute left-7 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-skill-blue transition-colors text-base"></i>
                                <input type="password" id="newPass" name="password" placeholder="••••••••" required
                                       class="w-full pl-16 pr-8 py-7 bg-slate-50 border-2 border-transparent rounded-[2rem] focus:bg-white focus:border-skill-blue transition-all outline-none font-bold text-slate-800 shadow-inner text-base">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[11px] font-[900] uppercase tracking-widest text-slate-900 ml-3">Confirm New Password</label>
                            <div class="relative group">
                                <i class="fas fa-shield-check absolute left-7 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-skill-blue transition-colors text-base"></i>
                                <input type="password" id="confirmPass" placeholder="••••••••" required
                                       class="w-full pl-16 pr-8 py-7 bg-slate-50 border-2 border-transparent rounded-[2rem] focus:bg-white focus:border-skill-blue transition-all outline-none font-bold text-slate-800 shadow-inner text-base">
                            </div>
                        </div>

                        <button type="submit" class="w-full py-7.5 mt-4 bg-slate-900 text-white rounded-[2rem] font-black text-xl flex items-center justify-center gap-4 hover:bg-skill-blue transition-all duration-500 transform active:scale-95 shadow-2xl shadow-blue-500/10">
                            Secure Credentials <i class="fas fa-lock-open text-base"></i>
                        </button>
                    </form>

                <?php else: ?>
                    <div class="text-center py-10 space-y-6">
                        <div class="w-24 h-24 bg-red-50 text-red-500 rounded-[2.5rem] flex items-center justify-center mx-auto shadow-inner border border-red-100">
                            <i class="fas fa-link-slash text-4xl"></i>
                        </div>
                        <div class="space-y-2">
                            <h2 class="text-3xl font-black text-slate-900 tracking-tight leading-none">Access <span class="text-red-500 italic">Expired</span></h2>
                            <p class="text-slate-500 text-xs font-bold uppercase tracking-widest mt-4">HANDSHAKE_TIMEOUT: TOKEN_INVALID_OR_DEPRECATED</p>
                        </div>
                        <div class="pt-6">
                            <a href="forgot-password.php" class="inline-flex items-center gap-4 px-10 py-5 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-skill-blue transition-all shadow-xl">
                                Request New Handshake <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</main>

<script>
    function validatePasswords() {
        const p1 = document.getElementById('newPass').value;
        const p2 = document.getElementById('confirmPass').value;
        
        if (p1 !== p2) {
            alert("Security Node Conflict: Passwords do not match.");
            return false;
        }
        
        if (p1.length < 8) {
            alert("Security Protocol: Password must be at least 8 characters.");
            return false;
        }
        return true;
    }
</script>

<?php include $include_path . 'footer.php'; ?>