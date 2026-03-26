<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: auth/forgot-password.php
 * PURPOSE: Elite Identity Recovery Terminal
 */

// 1. System Initialization
$include_path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;

if (file_exists($include_path . 'config.php')) {
    require_once $include_path . 'config.php';
} else {
    die("Critical Error: Core configuration missing.");
}

// 2. State Detection
$mail_sent = isset($_GET['sent']) && $_GET['sent'] === 'success';
$error_node = isset($_GET['error']) && $_GET['error'] === 'node_not_found';

// 3. Component Loading
$current_page = 'forgot-password';
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
                
                <?php if (!$mail_sent): ?>
                    <div class="text-center space-y-2 mb-8">
                        <span class="text-[9px] font-black bg-slate-900 text-white px-3 py-1 rounded-full tracking-widest uppercase mb-4 inline-block">Security Protocol</span>
                        <h1 class="text-4xl font-[900] tracking-tighter text-slate-900 leading-none">
                            Reset <span class="text-skill-blue italic">Access</span>
                        </h1>
                        <p class="text-slate-500 text-sm font-medium px-4">Enter your email node to initiate the recovery handshake</p>
                    </div>

                    <form action="forgot-password-process.php" method="POST" class="space-y-6">
                        
                        <?php if ($error_node): ?>
                            <div class="bg-red-50 border border-red-100 p-4 rounded-2xl flex items-center gap-3">
                                <i class="fas fa-triangle-exclamation text-red-500 text-xs"></i>
                                <span class="text-[10px] font-bold text-red-600 uppercase tracking-tight">ERR_NODE_NOT_FOUND: ACCOUNT_UNRECOGNIZED</span>
                            </div>
                        <?php endif; ?>

                        <div class="space-y-1.5">
                            <label class="text-[11px] font-[900] uppercase tracking-widest text-slate-900 ml-3">Email Address</label>
                            <div class="relative group">
                                <i class="fas fa-envelope-open-text absolute left-7 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-skill-blue transition-colors text-base"></i>
                                <input type="email" name="email" placeholder="Enter registered email..." required
                                       class="w-full pl-16 pr-8 py-7.5 bg-slate-50 border-2 border-transparent rounded-[2rem] focus:bg-white focus:border-skill-blue transition-all outline-none font-bold text-slate-800 shadow-inner text-base placeholder:text-slate-300">
                            </div>
                        </div>

                        <button type="submit" class="w-full py-7.5 mt-2 bg-slate-900 text-white rounded-[2rem] font-black text-xl flex items-center justify-center gap-4 hover:bg-skill-blue transition-all duration-500 transform active:scale-95 shadow-2xl shadow-blue-500/10">
                            Dispatch Link <i class="fas fa-paper-plane text-base"></i>
                        </button>
                    </form>

                <?php else: ?>
                    <div class="text-center py-10 space-y-6">
                        <div class="w-24 h-24 bg-blue-50 text-skill-blue rounded-[2.5rem] flex items-center justify-center mx-auto shadow-inner border border-blue-100">
                            <i class="fas fa-shield-check text-4xl animate-pulse"></i>
                        </div>
                        <div class="space-y-2">
                            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Link <span class="text-skill-blue italic">Dispatched</span></h2>
                            <p class="text-slate-500 text-sm font-medium px-8 leading-relaxed">Please check your secure inbox. An identity recovery handshake has been sent to your node.</p>
                        </div>
                        <div class="pt-6">
                            <a href="login.php" class="inline-flex items-center gap-3 text-xs font-black text-slate-900 uppercase tracking-widest hover:text-skill-blue transition-colors">
                                <i class="fas fa-arrow-left"></i> Return to Gate
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <div class="mt-8 text-center">
            <a href="login.php" class="text-slate-400 text-[11px] font-[900] uppercase tracking-[0.3em] hover:text-skill-blue transition-all">
                <i class="fas fa-chevron-left mr-2"></i> Back to Login Gate
            </a>
        </div>
    </div>
</main>

<?php include $include_path . 'footer.php'; ?>