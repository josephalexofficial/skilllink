<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: signup-choice.php
 * PURPOSE: Refined Zero-Scroll Role Selection
 * REFINEMENTS: Full-card hitboxes, sub-pixel glow, and monospace watermarks.
 */

// 1. System Initialization
$include_path = __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;

if (file_exists($include_path . 'config.php')) {
    require_once $include_path . 'config.php';
} else {
    die("Critical Error: Core configuration missing.");
}

require_once $include_path . 'db.php';

// 2. Component Loading: Dynamic Navigation State
$current_page = basename($_SERVER['PHP_SELF']);
include $include_path . 'header.php';
?>

<main class="h-[calc(100vh-80px)] flex flex-col bg-white font-sans antialiased text-slate-900 overflow-hidden relative">

    <div class="absolute inset-0 z-0 pointer-events-none select-none">
        <div class="absolute top-0 left-1/4 w-[500px] h-[500px] bg-blue-500/5 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-0 right-1/4 w-[400px] h-[400px] bg-skill-blue/5 rounded-full blur-[100px]"></div>
        <div class="absolute inset-0 opacity-[0.04]" style="background-image: linear-gradient(#94a3b8 1px, transparent 1px), linear-gradient(90deg, #94a3b8 1px, transparent 1px); background-size: 45px 45px;"></div>
    </div>

    <div class="flex-grow flex flex-col justify-between relative z-10 px-6 py-8 md:py-10 max-w-6xl mx-auto w-full">
        
        <div class="text-center space-y-3">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-slate-50 border border-slate-200 rounded-full text-skill-blue text-[9px] font-black uppercase tracking-[0.2em] shadow-sm">
                <i class="fas fa-layer-group text-[8px]"></i> Step 01: Role Selection
            </div>
            <h1 class="text-4xl md:text-5xl font-[900] text-slate-900 tracking-tighter leading-none">
                Choose your <span class="text-skill-blue italic">Path.</span>
            </h1>
            <p class="text-slate-500 text-sm md:text-base font-medium max-w-lg mx-auto leading-relaxed">
                SkillLink connects elite global talent with premium projects, <span class="text-slate-900 font-bold border-b-2 border-skill-blue/20">wherever you are.</span>
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-6 lg:gap-10 items-stretch">
            
            <div onclick="window.location.href='auth/signup.php?role=client'" 
                 class="group relative bg-white border border-slate-100 rounded-[2.8rem] p-10 cursor-pointer hover:border-skill-blue/50 hover:shadow-[0_20px_60px_-15px_rgba(0,86,210,0.12)] transition-all duration-700 transform hover:-translate-y-2 overflow-hidden flex flex-col justify-between">
                
                <div>
                    <div class="w-14 h-14 bg-blue-50 text-skill-blue rounded-2xl flex items-center justify-center text-2xl mb-8 group-hover:bg-skill-blue group-hover:text-white transition-all duration-500 shadow-inner">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div class="space-y-4">
                        <h2 class="text-2xl font-[900] text-slate-900 tracking-tighter leading-none">I want to Hire</h2>
                        <p class="text-slate-500 text-sm leading-relaxed font-medium">
                            Access a borderless network of vetted experts. Secure elite talent and get quality work done in record time.
                        </p>
                    </div>
                </div>
                
                <div class="mt-8 space-y-6">
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 bg-slate-50 border border-slate-100 rounded-lg text-[9px] font-black text-slate-600 uppercase tracking-widest"><i class="fas fa-shield-halved mr-1 text-skill-blue"></i> Vetted Pros</span>
                        <span class="px-3 py-1 bg-slate-50 border border-slate-100 rounded-lg text-[9px] font-black text-slate-600 uppercase tracking-widest"><i class="fas fa-lock mr-1 text-skill-blue"></i> Escrow</span>
                    </div>
                    <span class="w-full py-4 bg-slate-900 text-white rounded-xl font-[900] text-base text-center inline-block group-hover:bg-skill-blue group-hover:shadow-lg transition-all duration-500">
                        Start Hiring
                    </span>
                </div>
                <span class="absolute top-8 right-10 text-8xl font-mono font-black text-slate-400 opacity-[0.03] group-hover:opacity-[0.08] group-hover:text-skill-blue transition-all duration-700 pointer-events-none select-none italic">01</span>
            </div>

            <div onclick="window.location.href='auth/signup.php?role=worker'" 
                 class="group relative bg-white border border-slate-100 rounded-[2.8rem] p-10 cursor-pointer hover:border-skill-blue/50 hover:shadow-[0_20px_60px_-15px_rgba(0,86,210,0.12)] transition-all duration-700 transform hover:-translate-y-2 overflow-hidden flex flex-col justify-between">
                
                <div>
                    <div class="w-14 h-14 bg-blue-50 text-skill-blue rounded-2xl flex items-center justify-center text-2xl mb-8 group-hover:bg-skill-blue group-hover:text-white transition-all duration-500 shadow-inner">
                        <i class="fas fa-screwdriver-wrench"></i>
                    </div>
                    <div class="space-y-4">
                        <h2 class="text-2xl font-[900] text-slate-900 tracking-tighter leading-none">I want to Work</h2>
                        <p class="text-slate-500 text-sm leading-relaxed font-medium">
                            Build your global brand and manage your workflow. Gain access to premium projects and consistent earnings.
                        </p>
                    </div>
                </div>

                <div class="mt-8 space-y-6">
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 bg-slate-50 border border-slate-100 rounded-lg text-[9px] font-black text-slate-600 uppercase tracking-widest"><i class="fas fa-star mr-1 text-skill-blue"></i> Elite Badge</span>
                        <span class="px-3 py-1 bg-slate-50 border border-slate-100 rounded-lg text-[9px] font-black text-slate-600 uppercase tracking-widest"><i class="fas fa-bolt mr-1 text-skill-blue"></i> Payouts</span>
                    </div>
                    <span class="w-full py-4 bg-skill-blue text-white rounded-xl font-[900] text-base text-center inline-block group-hover:bg-blue-700 group-hover:shadow-lg transition-all duration-500">
                        Start Working
                    </span>
                </div>
                <span class="absolute top-8 right-10 text-8xl font-mono font-black text-slate-400 opacity-[0.03] group-hover:opacity-[0.08] group-hover:text-skill-blue transition-all duration-700 pointer-events-none select-none italic">02</span>
            </div>

        </div>

        <div class="text-center pb-2">
            <p class="text-slate-500 text-xs font-bold tracking-tight">
                Already part of the network? 
                <a href="auth/login.php" class="text-skill-blue hover:underline underline-offset-8 decoration-2 font-black ml-1 transition-all">Sign In here</a>
            </p>
        </div>
    </div>

</main>

<?php include $include_path . 'footer.php'; ?>