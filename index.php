<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: index.php
 * PURPOSE: Immersive Experience with Corner-Badge Process Path
 * REFINEMENTS: Implemented small professional corner badges and fixed connector-line occlusion.
 */

// 1. System Initialization
$include_path = __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;

if (file_exists($include_path . 'config.php')) {
    require_once $include_path . 'config.php';
} else {
    die("Critical Error: Core configuration missing.");
}

require_once $include_path . 'db.php';

// 2. Component Loading
include $include_path . 'header.php';
?>

<main class="min-h-screen font-sans antialiased text-slate-900 bg-white overflow-x-hidden">

    <section class="relative pt-10 pb-12 md:pt-14 md:pb-20 px-6 text-center overflow-hidden bg-gradient-to-b from-blue-50/40 via-white to-white">
        <div class="max-w-5xl mx-auto relative z-10 space-y-10">
            <div class="inline-flex items-center gap-3 px-5 py-2.5 bg-white border border-blue-100 rounded-full text-skill-blue text-xs font-black uppercase tracking-[0.15em] shadow-sm">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-skill-blue"></span>
                </span>
                Verified Professionals • Guaranteed Quality
            </div>
            <div class="space-y-8">
                <h1 class="text-5xl md:text-[5.5rem] font-[900] tracking-tighter text-slate-900 leading-[1.1] md:leading-[1]">
                    Quality Work. <br>
                    <span class="text-skill-blue italic drop-shadow-sm">Right at your Door.</span>
                </h1>
                <p class="text-slate-500 text-lg md:text-2xl max-w-3xl mx-auto leading-relaxed font-medium">
                    Access a curated network of elite skilled professionals. Get the expertise you need, <span class="text-slate-900 font-bold">verified and on-demand.</span>
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-5 justify-center items-center pt-4">
                <a href="services.php" class="w-full sm:w-auto px-12 py-5 bg-skill-blue text-white rounded-2xl font-black text-xl shadow-2xl shadow-blue-500/20 hover:bg-blue-700 hover:-translate-y-1 active:scale-95 transition-all duration-300 flex items-center justify-center gap-3">
                    <i class="fas fa-search"></i> Hire a Professional
                </a>
                <a href="signup-choice.php" class="w-full sm:w-auto px-12 py-5 bg-white border-2 border-slate-200 text-slate-700 rounded-2xl font-black text-xl hover:bg-slate-50 transition-all duration-300">
                    Earn as a Worker
                </a>
            </div>
            <div class="pt-10 border-t border-slate-100">
                <p class="text-[10px] md:text-xs font-bold text-slate-400 uppercase tracking-widest">
                    Over <span class="text-slate-600">5,000+</span> Professionals across <span class="text-slate-600">50+</span> Service Categories
                </p>
            </div>
        </div>
    </section>

    <section class="bg-slate-50 min-h-screen flex items-center py-20 px-6 border-y border-slate-100 relative z-20">
        <div class="max-w-7xl mx-auto w-full">
            <div class="text-center mb-16 space-y-3">
                <h2 class="text-4xl md:text-6xl font-black text-slate-900 tracking-tight">Popular Categories</h2>
                <p class="text-slate-500 text-lg md:text-xl font-medium max-w-2xl mx-auto">Instant access to certified home and office expertise delivered by the industry's best.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                $categories = [
                    ['icon' => 'fa-faucet', 'name' => 'Plumbing', 'desc' => 'Certified solutions for pipes, leaks, and drainage.'],
                    ['icon' => 'fa-bolt', 'name' => 'Electrical', 'desc' => 'Professional wiring, power management, and safety repairs.'],
                    ['icon' => 'fa-paint-roller', 'name' => 'Painting', 'desc' => 'Precision interior and exterior aesthetic finishes.'],
                    ['icon' => 'fa-hammer', 'name' => 'Carpentry', 'desc' => 'Custom furniture design and master-grade wood repairs.'],
                    ['icon' => 'fa-hands-bubbles', 'name' => 'Cleaning', 'desc' => 'Comprehensive home sanitization and premium garment care.'],
                    ['icon' => 'fa-plug', 'name' => 'Appliances', 'desc' => 'Expert diagnostics for home electronics and heavy appliances.']
                ];
                foreach($categories as $cat):
                ?>
                <div class="group bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 hover:border-skill-blue hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 flex items-center gap-6">
                    <div class="shrink-0 w-16 h-16 bg-blue-50 text-skill-blue rounded-2xl flex items-center justify-center text-2xl group-hover:bg-skill-blue group-hover:text-white transition-all duration-300 shadow-inner">
                        <i class="fas <?php echo $cat['icon']; ?>"></i>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-xl font-black text-slate-900 tracking-tight"><?php echo $cat['name']; ?></h3>
                        <p class="text-slate-500 text-sm leading-snug font-medium"><?php echo $cat['desc']; ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-16 text-center">
                <a href="services.php" class="inline-flex items-center gap-3 px-8 py-4 bg-white border-2 border-slate-200 text-skill-blue rounded-full font-black text-base hover:bg-skill-blue hover:text-white hover:border-skill-blue transition-all duration-300 group shadow-lg shadow-slate-200/50">
                    Explore All 50+ Services 
                    <i class="fas fa-arrow-right group-hover:translate-x-2 transition-transform duration-300"></i>
                </a>
            </div>
        </div>
    </section>

    <section class="py-32 px-6 max-w-7xl mx-auto text-center relative overflow-visible">
        <h2 class="text-4xl md:text-6xl font-black mb-24 tracking-tighter text-slate-900">How SkillLink Works</h2>
        
        <div class="relative">
            <div class="hidden md:block absolute top-[50%] left-0 w-full h-0.5 border-t-2 border-dashed border-slate-200 -z-10"></div>

            <div class="grid md:grid-cols-3 gap-12 relative z-10">
                
                <div class="group bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/40 hover:border-skill-blue transition-all duration-500 relative">
                    <div class="absolute top-6 right-6 w-10 h-10 bg-blue-50 text-skill-blue rounded-full flex items-center justify-center font-black text-sm border border-blue-100 group-hover:bg-skill-blue group-hover:text-white transition-all duration-300">
                        01
                    </div>
                    
                    <div class="relative w-20 h-20 bg-blue-50 text-skill-blue rounded-3xl flex items-center justify-center text-3xl mb-8 group-hover:bg-skill-blue group-hover:text-white transition-all duration-300 shadow-inner">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    
                    <h4 class="text-2xl font-black text-slate-900 mb-4 text-left">Post a Request</h4>
                    <p class="text-slate-500 text-lg leading-relaxed text-left">Detail your project requirements and set your preferred timeline for completion.</p>
                </div>

                <div class="group bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/40 hover:border-skill-blue transition-all duration-500 relative">
                    <div class="absolute top-6 right-6 w-10 h-10 bg-blue-50 text-skill-blue rounded-full flex items-center justify-center font-black text-sm border border-blue-100 group-hover:bg-skill-blue group-hover:text-white transition-all duration-300">
                        02
                    </div>
                    
                    <div class="relative w-20 h-20 bg-blue-50 text-skill-blue rounded-3xl flex items-center justify-center text-3xl mb-8 group-hover:bg-skill-blue group-hover:text-white transition-all duration-300 shadow-inner">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    
                    <h4 class="text-2xl font-black text-slate-900 mb-4 text-left">Experts Respond</h4>
                    <p class="text-slate-500 text-lg leading-relaxed text-left">Verified professionals review your task and submit their availability instantly.</p>
                </div>

                <div class="group bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/40 hover:border-skill-blue transition-all duration-500 relative">
                    <div class="absolute top-6 right-6 w-10 h-10 bg-blue-50 text-skill-blue rounded-full flex items-center justify-center font-black text-sm border border-blue-100 group-hover:bg-skill-blue group-hover:text-white transition-all duration-300">
                        03
                    </div>
                    
                    <div class="relative w-20 h-20 bg-blue-50 text-skill-blue rounded-3xl flex items-center justify-center text-3xl mb-8 group-hover:bg-skill-blue group-hover:text-white transition-all duration-300 shadow-inner">
                        <i class="fas fa-check-double"></i>
                    </div>
                    
                    <h4 class="text-2xl font-black text-slate-900 mb-4 text-left">Project Done</h4>
                    <p class="text-slate-500 text-lg leading-relaxed text-left">Approve the high-quality work, pay securely, and leave a professional review.</p>
                </div>

            </div>
        </div>
    </section>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>