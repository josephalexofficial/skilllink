<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: contact.php
 * PURPOSE: Optimized Human-Centric Support Interface
 * REFINEMENTS: Brand-aligned email, expanded inquiry logic, and high-fidelity UI.
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

    <section class="relative pt-24 pb-20 md:pt-32 md:pb-32 px-6 text-center overflow-hidden bg-[#0a0f1d]">
        <div class="absolute inset-0 z-0">
            <div class="absolute top-[-20%] left-[-10%] w-[70%] h-[70%] bg-skill-blue/20 rounded-full blur-[120px]"></div>
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: linear-gradient(#3b82f6 1px, transparent 1px), linear-gradient(90deg, #3b82f6 1px, transparent 1px); background-size: 50px 50px;"></div>
        </div>
        
        <div class="max-w-4xl mx-auto relative z-10 space-y-8">
            <div class="inline-flex items-center gap-3 px-5 py-2 bg-white/5 backdrop-blur-xl border border-white/10 rounded-full text-blue-400 text-[10px] font-black uppercase tracking-[0.3em] shadow-sm">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                </span>
                Active: support@skilllink.com
            </div>
            <h1 class="text-5xl md:text-[5.5rem] font-[900] tracking-tighter text-white leading-[1] md:leading-[0.9]">
                Let's <br>
                <span class="text-skill-blue italic">Connect.</span>
            </h1>
            <p class="text-slate-400 text-lg md:text-xl max-w-2xl mx-auto leading-relaxed font-medium">
                Our team is standing by to assist with your journey. Reach out for technical help, verification, or general questions.
            </p>
        </div>
    </section>

    <section class="py-24 px-6 max-w-7xl mx-auto">
        <div class="grid lg:grid-cols-2 gap-20">
            
            <div class="space-y-12">
                <div class="space-y-4">
                    <h2 class="text-3xl font-black tracking-tight text-slate-900">Send a Message</h2>
                    <p class="text-slate-500 font-medium">Use the form below to transmit your inquiry directly to our team.</p>
                </div>

                <form action="process-contact.php" method="POST" class="space-y-8">
                    <div class="grid md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Full Name</label>
                            <input type="text" name="name" placeholder="Alex Joseph" required
                                   class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-skill-blue focus:ring-8 focus:ring-blue-500/5 transition-all outline-none font-bold text-slate-700 shadow-inner">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Email Address</label>
                            <input type="email" name="email" placeholder="support@skilllink.com" required
                                   class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-skill-blue focus:ring-8 focus:ring-blue-500/5 transition-all outline-none font-bold text-slate-700 shadow-inner">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">How can we help?</label>
                        <select name="subject" class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-skill-blue focus:ring-8 focus:ring-blue-500/5 transition-all outline-none font-bold text-slate-700 cursor-pointer shadow-inner">
                            <option value="verification">Professional Verification</option>
                            <option value="support">General Customer Support</option>
                            <option value="technical">Report a Technical Issue</option>
                            <option value="partnership">Partnership Opportunities</option>
                            <option value="other">Other / General Inquiry</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Your Message</label>
                        <textarea name="message" rows="5" placeholder="Tell us more about your request..." required
                                  class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent rounded-[2rem] focus:bg-white focus:border-skill-blue focus:ring-8 focus:ring-blue-500/5 transition-all outline-none font-bold text-slate-700 resize-none shadow-inner"></textarea>
                    </div>

                    <button type="submit" class="w-full py-5 bg-slate-900 text-white rounded-2xl font-black text-lg flex items-center justify-center gap-3 hover:bg-skill-blue hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-500 transform active:scale-95">
                        Send Message <i class="fas fa-paper-plane text-sm"></i>
                    </button>
                </form>
            </div>

            <div class="space-y-12 lg:pl-10 border-l border-slate-100">
                <div class="space-y-4">
                    <h2 class="text-3xl font-black tracking-tight text-slate-900">Get In Touch</h2>
                    <p class="text-slate-500 font-medium tracking-tight">Direct access to the SkillLink support channels.</p>
                </div>

                <div class="grid gap-6">
                    <div class="flex items-center gap-6 p-6 bg-slate-50 rounded-[2.5rem] border border-transparent hover:bg-white hover:shadow-xl hover:border-skill-blue transition-all duration-500 group">
                        <div class="w-14 h-14 bg-blue-100 text-skill-blue rounded-2xl flex items-center justify-center text-xl group-hover:bg-skill-blue group-hover:text-white transition-all">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-black uppercase text-slate-400 block tracking-widest">Email</span>
                            <span class="text-lg font-black text-slate-700">support@skilllink.com</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-6 p-6 bg-slate-50 rounded-[2.5rem] border border-transparent hover:bg-white hover:shadow-xl hover:border-skill-blue transition-all duration-500 group">
                        <div class="w-14 h-14 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center text-xl group-hover:bg-green-600 group-hover:text-white transition-all">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-black uppercase text-slate-400 block tracking-widest">Phone</span>
                            <span class="text-lg font-black text-slate-700">+254 769 591 223</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-6 p-6 bg-slate-50 rounded-[2.5rem] border border-transparent hover:bg-white hover:shadow-xl hover:border-skill-blue transition-all duration-500 group">
                        <div class="w-14 h-14 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center text-xl group-hover:bg-red-600 group-hover:text-white transition-all">
                            <i class="fas fa-location-dot"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-black uppercase text-slate-400 block tracking-widest">Location</span>
                            <span class="text-lg font-black text-slate-700">Kisumu, Kenya</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-6 pt-6">
                    <h4 class="text-sm font-black uppercase tracking-[0.2em] text-slate-400">Follow Me</h4>
                    <div class="flex items-center gap-4">
                        <a href="#" class="w-14 h-14 rounded-2xl bg-slate-900 text-white flex items-center justify-center hover:bg-skill-blue hover:-translate-y-2 transition-all duration-300">
                            <i class="fab fa-linkedin-in text-xl"></i>
                        </a>
                        <a href="#" class="w-14 h-14 rounded-2xl bg-slate-900 text-white flex items-center justify-center hover:bg-skill-blue hover:-translate-y-2 transition-all duration-300">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="w-14 h-14 rounded-2xl bg-slate-900 text-white flex items-center justify-center hover:bg-skill-blue hover:-translate-y-2 transition-all duration-300">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include $include_path . 'footer.php'; ?>