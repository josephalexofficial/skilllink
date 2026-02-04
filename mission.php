<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: mission.php
 * PURPOSE: High-Fidelity Brand Identity & Trust Engineering
 * REFINEMENTS: Gradient Mesh Hero, Blueprint Overlays, and Universally Stable Icon Logic.
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

<main class="min-h-screen font-sans antialiased text-slate-900 bg-white overflow-x-hidden">

    <section class="relative pt-24 pb-24 md:pt-40 md:pb-40 px-6 text-center overflow-hidden bg-[#0a0f1d]">
        <div class="absolute inset-0 z-0">
            <div class="absolute top-[-20%] left-[-10%] w-[70%] h-[70%] bg-skill-blue/20 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-[-20%] right-[-10%] w-[60%] h-[60%] bg-blue-600/10 rounded-full blur-[100px]"></div>
            <div class="absolute inset-0 opacity-[0.05]" style="background-image: linear-gradient(#3b82f6 1px, transparent 1px), linear-gradient(90deg, #3b82f6 1px, transparent 1px); background-size: 50px 50px;"></div>
        </div>
        
        <div class="max-w-5xl mx-auto relative z-10 space-y-12">
            <div class="inline-flex items-center gap-3 px-6 py-2 bg-white/5 backdrop-blur-xl border border-white/10 rounded-full text-blue-400 text-[10px] font-black uppercase tracking-[0.3em] shadow-2xl">
                Our Purpose • Our Promise
            </div>
            <h1 class="text-5xl md:text-[6.5rem] font-[900] tracking-tighter text-white leading-[1] md:leading-[0.9]">
                Engineering a <br>
                <span class="text-skill-blue drop-shadow-[0_0_30px_rgba(0,86,210,0.3)]">Reliable Economy.</span>
            </h1>
            <p class="text-slate-400 text-lg md:text-2xl max-w-3xl mx-auto leading-relaxed font-medium">
                SkillLink was founded in <span class="text-white font-bold border-b-2 border-skill-blue/50">Kisumu</span> to bridge the digital gap. We aren't just a marketplace; we are a standard for local excellence.
            </p>
        </div>
    </section>

    <section class="py-32 px-6 bg-slate-50 relative border-y border-slate-100">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-24 space-y-4">
                <h2 class="text-4xl md:text-6xl font-[900] text-slate-900 tracking-tight">The SkillLink Pillars</h2>
                <p class="text-slate-500 font-bold uppercase tracking-widest text-xs">Foundation of Trust</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
                <?php
                $pillars = [
                    // REFINED: Switched to 'fa-award' for guaranteed visibility and premium look
                    ['icon' => 'fa-award', 'title' => 'Verified Excellence', 'desc' => 'Every professional undergoes a rigorous 3-step vetting process before joining our network.'],
                    ['icon' => 'fa-users-gear', 'title' => 'Community First', 'desc' => 'We prioritize local growth in Kisumu, keeping our economy strong and self-reliant.'],
                    ['icon' => 'fa-lock-open', 'title' => 'Secure Systems', 'desc' => 'Our system ensures that transactions are handled with absolute transparency and safety.'],
                    ['icon' => 'fa-microchip', 'title' => 'Digital Accuracy', 'desc' => 'Reducing hire time through high-speed matching and verified availability.']
                ];

                foreach($pillars as $p):
                ?>
                <div class="group bg-white p-12 rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/40 hover:border-skill-blue hover:-translate-y-4 transition-all duration-700">
                    <div class="w-16 h-16 bg-blue-50 text-skill-blue rounded-2xl flex items-center justify-center text-3xl mb-8 group-hover:bg-skill-blue group-hover:text-white transition-all duration-500 shadow-inner">
                        <i class="fas <?php echo $p['icon']; ?>"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-4 tracking-tighter"><?php echo $p['title']; ?></h3>
                    <p class="text-slate-500 text-sm leading-relaxed font-medium"><?php echo $p['desc']; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="py-32 px-6 max-w-6xl mx-auto">
        <div class="flex flex-col lg:flex-row items-center gap-20">
            <div class="w-full lg:w-1/2 flex justify-center lg:justify-start">
                <div class="relative group">
                    <div class="w-72 h-72 md:w-[28rem] md:h-[28rem] bg-slate-200 rounded-[4rem] border-8 border-white shadow-2xl overflow-hidden transform -rotate-2 group-hover:rotate-0 transition-transform duration-700">
                        <img src="assets/img/alexjoseph.jpg" alt="Alex Joseph" class="w-full h-full object-cover grayscale-[20%] group-hover:grayscale-0 transition-all duration-700">
                    </div>
                    <div class="absolute -bottom-8 -right-8 w-28 h-28 bg-skill-blue rounded-[2.5rem] flex items-center justify-center text-white text-4xl shadow-2xl shadow-blue-500/40 transform rotate-12">
                        <i class="fas fa-code"></i>
                    </div>
                </div>
            </div>
            
            <div class="w-full lg:w-1/2 space-y-10 text-left">
                <div class="space-y-4">
                    <h2 class="text-4xl md:text-5xl font-[900] tracking-tighter text-slate-900 leading-none">Engineering a Vision</h2>
                    <p class="text-skill-blue font-black uppercase tracking-[0.2em] text-[10px]">Founder & Lead Systems Engineer</p>
                </div>
                <div class="space-y-6 text-slate-600 text-lg md:text-xl leading-relaxed font-medium italic">
                    <p>"As a computer scientist, I view the world through systems. SkillLink was born to replace uncertainty with digital infrastructure."</p>
                    <p>"We are building more than an app in Kisumu; we are building a verifiable standard for professional excellence."</p>
                </div>
                <div class="pt-8">
                    <p class="text-[3.5rem] font-bold text-skill-blue leading-none select-none" style="font-family: 'Brush Script MT', cursive;">
                        Alex Joseph
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 px-6 text-center">
        <div class="max-w-5xl mx-auto bg-skill-blue rounded-[4rem] p-20 relative overflow-hidden shadow-2xl shadow-blue-500/40">
            <div class="absolute top-[-50%] left-[-20%] w-[100%] h-[100%] bg-blue-400 opacity-20 blur-[100px] rounded-full"></div>
            
            <div class="relative z-10 space-y-10">
                <h2 class="text-4xl md:text-6xl font-[900] text-white tracking-tighter">Ready to be part of <br> the evolution?</h2>
                <p class="text-blue-100 text-lg md:text-2xl font-medium max-w-2xl mx-auto">Join thousands of elite professionals and clients already standardizing their work on SkillLink.</p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center pt-6">
                    <a href="signup-choice.php" class="px-14 py-6 bg-white text-skill-blue rounded-3xl font-[900] text-xl hover:scale-105 hover:shadow-2xl transition-all duration-300">
                        Join Now
                    </a>
                    <a href="contact.php" class="px-14 py-6 bg-skill-blue border-2 border-white/30 text-white rounded-3xl font-[900] text-xl hover:bg-white/10 transition-all duration-300">
                        Get Support
                    </a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include $include_path . 'footer.php'; ?>