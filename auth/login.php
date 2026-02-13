<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: auth/login.php
 * PURPOSE: Inclusive, High-Fidelity Login Gate for Workers & Clients
 * REFINEMENTS: Universal Wording, High-Contrast Labels, and Mass-Weighted Buttons.
 */

// 1. System Initialization
$include_path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;

if (file_exists($include_path . 'config.php')) {
    require_once $include_path . 'config.php';
} else {
    die("Critical Error: Core configuration missing.");
}

// 2. Component Loading
$current_page = 'login';
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
                <div class="text-center space-y-2 mb-8">
                    <h1 class="text-4xl font-[900] tracking-tighter text-slate-900 leading-none">
                        Welcome <span class="text-skill-blue italic">Back</span>
                    </h1>
                    <p class="text-slate-500 text-sm font-medium px-4">Log in to manage your projects and connections</p>
                </div>

                <form action="login-process.php" method="POST" class="space-y-4">
                    
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-[900] uppercase tracking-widest text-slate-900 ml-3">Email Address</label>
                        <div class="relative group">
                            <i class="fas fa-envelope absolute left-7 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-skill-blue transition-colors text-base"></i>
                            <input type="email" name="email" placeholder="alex@gmail.com" required
                                   class="w-full pl-16 pr-8 py-7.5 bg-slate-50 border-2 border-transparent rounded-[2rem] focus:bg-white focus:border-skill-blue transition-all outline-none font-bold text-slate-800 shadow-inner text-base placeholder:text-slate-300">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex justify-between items-center px-3">
                            <label class="text-[11px] font-[900] uppercase tracking-widest text-slate-900">Password</label>
                            <a href="forgot-password.php" class="text-[10px] font-bold text-skill-blue hover:underline">Forgot password?</a>
                        </div>
                        <div class="relative group">
                            <i class="fas fa-lock absolute left-7 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-skill-blue transition-colors text-base"></i>
                            <input type="password" id="loginPassword" name="password" placeholder="••••••••" required
                                   class="w-full pl-16 pr-16 py-7.5 bg-slate-50 border-2 border-transparent rounded-[2rem] focus:bg-white focus:border-skill-blue transition-all outline-none font-bold text-slate-800 shadow-inner text-base">
                            <button type="button" onclick="togglePass()" class="absolute right-7 top-1/2 -translate-y-1/2 text-slate-300 hover:text-skill-blue transition-colors">
                                <i id="eyeIcon" class="fas fa-eye text-base"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 px-3 py-1">
                        <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded border-slate-200 text-skill-blue focus:ring-skill-blue cursor-pointer">
                        <label for="remember" class="text-xs font-bold text-slate-500 cursor-pointer">Stay logged in</label>
                    </div>

                    <button type="submit" class="w-full py-7.5 mt-2 bg-slate-900 text-white rounded-[2rem] font-black text-xl flex items-center justify-center gap-4 hover:bg-skill-blue transition-all duration-500 transform active:scale-95 shadow-2xl">
                        Log In <i class="fas fa-arrow-right text-base"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-8 text-center">
            <p class="text-slate-400 text-[11px] font-[900] uppercase tracking-[0.3em] flex items-center justify-center gap-3">
                Don't have an account? <a href="signup.php" class="text-skill-blue hover:text-blue-600 underline decoration-2 underline-offset-8 transition-all font-black">Sign Up</a>
            </p>
        </div>
    </div>
</main>

<script>
    /**
     * HAPTIC TOGGLE: Swaps password visibility for accessibility
     */
    function togglePass() {
        const input = document.getElementById('loginPassword');
        const icon = document.getElementById('eyeIcon');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>

<?php include $include_path . 'footer.php'; ?>