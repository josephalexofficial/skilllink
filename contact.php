<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: contact.php
 * PURPOSE: Paginated High-Fidelity Support Interface
 * REFINEMENTS: Dynamic Symmetry Alignment, Social Anchor Tray, and Viewport Calibration.
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

<main class="h-screen overflow-y-scroll snap-y snap-mandatory font-sans antialiased text-slate-900 bg-slate-50 scroll-smooth">

    <section class="relative h-screen flex flex-col pt-[20vh] items-center text-center px-6 overflow-hidden bg-[#0a0f1d] snap-start z-10">
        <div class="absolute inset-0 z-0 pointer-events-none">
            <div class="absolute top-[-20%] left-[-10%] w-[70%] h-[70%] bg-skill-blue/20 rounded-full blur-[120px]"></div>
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: linear-gradient(#3b82f6 1px, transparent 1px), linear-gradient(90deg, #3b82f6 1px, transparent 1px); background-size: 50px 50px;"></div>
        </div>
        
        <div class="max-w-4xl mx-auto relative z-10 space-y-6 md:space-y-8">
            <div class="inline-flex items-center gap-3 px-5 py-2 bg-white/5 backdrop-blur-xl border border-white/10 rounded-full text-blue-400 text-[10px] font-black uppercase tracking-[0.3em] shadow-sm">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                </span>
                Active Support Protocol
            </div>
            <h1 class="text-6xl md:text-[7.5rem] font-[900] tracking-tighter text-white leading-[1] md:leading-[0.8]">
                Let's <br>
                <span class="text-skill-blue italic">Connect.</span>
            </h1>
            <p class="text-slate-400 text-lg md:text-xl max-w-xl mx-auto leading-relaxed font-medium">
                Our team is standing by to assist with your professional journey. Reach out to our engineering nodes.
            </p>
        </div>

        <div class="absolute bottom-12 left-1/2 -translate-x-1/2 flex flex-col items-center gap-3 opacity-60">
            <span class="text-[9px] font-black uppercase tracking-[0.4em] text-slate-500">Initialize Interface</span>
            <div class="w-px h-12 bg-gradient-to-b from-skill-blue to-transparent"></div>
        </div>
    </section>

    <section class="relative h-screen flex flex-col justify-center px-6 py-4 max-w-[85rem] mx-auto w-full snap-start z-20 overflow-hidden">
        
        <div class="absolute inset-0 pointer-events-none opacity-[0.02] z-0" style="background-image: radial-gradient(#3b82f6 1px, transparent 1px); background-size: 30px 30px;"></div>

        <div class="grid lg:grid-cols-[1.1fr_0.9fr] gap-6 lg:gap-10 relative z-10 items-stretch h-[80vh]">
            
            <div class="bg-white rounded-[3rem] p-8 md:p-10 shadow-[0_40px_120px_-20px_rgba(0,0,0,0.08)] border border-slate-100 flex flex-col justify-between">
                <div class="space-y-1 mb-6">
                    <h2 class="text-3xl font-[900] tracking-tight text-slate-900">Send a Message</h2>
                    <p class="text-slate-500 font-medium text-xs">Transmit your inquiry directly to our engineering team.</p>
                </div>

                <form action="process-contact.php" method="POST" class="space-y-4">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Full Name</label>
                            <input type="text" name="name" placeholder="Alex Joseph" required
                                   class="w-full px-5 py-4 bg-slate-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-skill-blue transition-all outline-none font-bold text-slate-800 shadow-inner text-base">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Email</label>
                            <input type="email" name="email" placeholder="support@skilllink.com" required
                                   class="w-full px-5 py-4 bg-slate-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-skill-blue transition-all outline-none font-bold text-slate-800 shadow-inner text-base">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Service Protocol</label>
                        <select name="subject" class="w-full px-5 py-4 bg-slate-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-skill-blue transition-all outline-none font-bold text-slate-800 cursor-pointer shadow-inner text-base">
                            <option value="verification">Professional Verification</option>
                            <option value="support">General Customer Support</option>
                            <option value="technical">Technical Bug Report</option>
                            <option value="other">Other Inquiry</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Message Payload</label>
                        <textarea name="message" rows="3" placeholder="Tell us more about your request..." required
                                  class="w-full px-5 py-4 bg-slate-50 border-2 border-transparent rounded-[2rem] focus:bg-white focus:border-skill-blue transition-all outline-none font-bold text-slate-800 resize-none shadow-inner text-base"></textarea>
                    </div>

                    <button type="submit" class="w-full py-5 bg-slate-900 text-white rounded-2xl font-black text-lg flex items-center justify-center gap-4 hover:bg-skill-blue transition-all duration-500 transform active:scale-[0.98] shadow-2xl">
                        Establish Connection <i class="fas fa-paper-plane text-sm"></i>
                    </button>
                </form>
            </div>

            <div class="flex flex-col justify-between space-y-6 lg:pl-6 h-full">
                <div class="space-y-4">
                    <div class="space-y-2">
                        <h2 class="text-4xl font-black tracking-tighter text-slate-900 leading-none">Direct Access</h2>
                        <p class="text-slate-600 font-medium text-sm">Reach out via our established nodes.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-6 bg-white rounded-3xl border border-slate-100 shadow-sm flex flex-col gap-4 group hover:border-skill-blue transition-all duration-300">
                            <div class="w-12 h-12 bg-blue-50 text-skill-blue rounded-xl flex items-center justify-center text-xl group-hover:bg-skill-blue group-hover:text-white transition-all shadow-inner">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="space-y-0">
                                <span class="text-[9px] font-black uppercase text-slate-400 tracking-widest block">Relay Node</span>
                                <span class="text-xs font-black text-slate-800 break-all">support@skilllink.com</span>
                            </div>
                        </div>

                        <div class="p-6 bg-white rounded-3xl border border-slate-100 shadow-sm flex flex-col gap-4 group hover:border-skill-blue transition-all duration-300">
                            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center text-xl group-hover:bg-green-600 group-hover:text-white transition-all shadow-inner">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <div class="space-y-0">
                                <span class="text-[9px] font-black uppercase text-slate-400 tracking-widest block">WhatsApp</span>
                                <span class="text-xs font-black text-slate-800">+254 769 591 223</span>
                            </div>
                        </div>

                        <div class="p-6 bg-white rounded-3xl border border-slate-100 shadow-sm flex items-center gap-5 col-span-2 group hover:border-skill-blue transition-all duration-300">
                            <div class="w-12 h-12 bg-red-50 text-red-600 rounded-xl flex items-center justify-center text-xl group-hover:bg-red-600 group-hover:text-white transition-all shadow-inner">
                                <i class="fas fa-location-dot"></i>
                            </div>
                            <div>
                                <span class="text-[9px] font-black uppercase text-slate-400 tracking-widest block">Main Protocol Node</span>
                                <span class="text-sm font-black text-slate-800">Kisumu, Kenya <span class="text-slate-300 font-medium ml-1 italic">East Africa</span></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-slate-100/50 backdrop-blur-sm rounded-[2.5rem] border border-slate-200/50 space-y-4">
                    <h4 class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-400 text-center lg:text-left">Follow the Journey</h4>
                    <div class="flex items-center justify-center lg:justify-start gap-5">
                        <a href="#" class="w-14 h-14 rounded-2xl bg-slate-900 text-white flex items-center justify-center hover:bg-skill-blue hover:-translate-y-2 transition-all duration-500 shadow-xl">
                            <i class="fab fa-linkedin-in text-xl"></i>
                        </a>
                        <a href="#" class="w-14 h-14 rounded-2xl bg-slate-900 text-white flex items-center justify-center hover:bg-skill-blue hover:-translate-y-2 transition-all duration-500 shadow-xl">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="w-14 h-14 rounded-2xl bg-slate-900 text-white flex items-center justify-center hover:bg-skill-blue hover:-translate-y-2 transition-all duration-500 shadow-xl">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="snap-start min-h-fit">
        <?php include $include_path . 'footer.php'; ?>
    </div>

</main>