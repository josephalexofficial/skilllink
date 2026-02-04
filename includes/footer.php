<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: footer.php
 * PURPOSE: Global Footer Component (Synchronized & Personalized)
 * REFINEMENTS: Navigation parity, localized contact, and developer portfolio integration.
 */
?>

<footer class="bg-slate-900 text-slate-300 pt-20 pb-10 px-6 border-t border-slate-800">
    <div class="max-w-7xl mx-auto">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
            
            <div class="space-y-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-skill-blue rounded-xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-tools text-xl"></i>
                    </div>
                    <span class="text-2xl font-black text-white tracking-tighter">SkillLink</span>
                </div>
                <p class="text-slate-400 leading-relaxed font-medium">
                    The premier marketplace for verified skilled professionals. We connect elite expertise with opportunity, ensuring quality work is always <span class="text-white">right at your door.</span>
                </p>
                <div class="flex items-center gap-4">
                    <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-skill-blue hover:text-white transition-all duration-300 group">
                        <i class="fab fa-twitter group-hover:scale-110"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-skill-blue hover:text-white transition-all duration-300 group">
                        <i class="fab fa-linkedin-in group-hover:scale-110"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-skill-blue hover:text-white transition-all duration-300 group">
                        <i class="fab fa-instagram group-hover:scale-110"></i>
                    </a>
                </div>
            </div>

            <div class="space-y-6">
                <h4 class="text-white font-bold uppercase tracking-widest text-xs">Platform</h4>
                <ul class="space-y-4 font-medium">
                    <li><a href="index.php" class="hover:text-skill-blue hover:translate-x-1 inline-block transition-all">Home</a></li>
                    <li><a href="services.php" class="hover:text-skill-blue hover:translate-x-1 inline-block transition-all">Services</a></li>
                    <li><a href="mission.php" class="hover:text-skill-blue hover:translate-x-1 inline-block transition-all">Our Mission</a></li>
                    <li><a href="contact.php" class="hover:text-skill-blue hover:translate-x-1 inline-block transition-all">Contact Us</a></li>
                </ul>
            </div>

            <div class="space-y-6">
                <h4 class="text-white font-bold uppercase tracking-widest text-xs">Top Categories</h4>
                <ul class="space-y-4 font-medium text-slate-400">
                    <li><a href="services.php?cat=plumbing" class="hover:text-white transition-colors">Plumbing Solutions</a></li>
                    <li><a href="services.php?cat=electrical" class="hover:text-white transition-colors">Electrical Systems</a></li>
                    <li><a href="services.php?cat=cleaning" class="hover:text-white transition-colors">Cleaning & Laundry</a></li>
                    <li><a href="services.php?cat=appliances" class="hover:text-white transition-colors">Appliance Repair</a></li>
                </ul>
            </div>

            <div class="space-y-6">
                <h4 class="text-white font-bold uppercase tracking-widest text-xs">Get in Touch</h4>
                <ul class="space-y-5 text-slate-400 font-medium">
                    <li class="flex items-start gap-3 group">
                        <i class="fas fa-location-dot mt-1 text-skill-blue group-hover:scale-110 transition-transform"></i>
                        <span class="group-hover:text-white transition-colors">Kisumu, Kenya</span>
                    </li>
                    <li class="flex items-center gap-3 group">
                        <i class="fas fa-envelope text-skill-blue group-hover:scale-110 transition-transform"></i>
                        <span class="group-hover:text-white transition-colors">support@skilllink.com</span>
                    </li>
                    <li class="flex items-center gap-3 group">
                        <i class="fas fa-phone text-skill-blue group-hover:scale-110 transition-transform"></i>
                        <span class="group-hover:text-white transition-colors">+254 769 591 223</span>
                    </li>
                </ul>
            </div>

        </div>

        <div class="pt-10 border-t border-slate-800 flex flex-col md:flex-row justify-between items-center gap-6">
            <p class="text-sm font-medium text-slate-500 text-center md:text-left">
                &copy; <?php echo date('Y'); ?> SkillLink Technologies. All rights reserved. <br class="md:hidden">
                <span class="hidden md:inline mx-2">•</span>
                Built with <i class="fas fa-heart text-red-500 animate-pulse"></i> 
                passion by <a href="https://alexjoseph.vercel.app/" target="_blank" class="text-slate-400 font-bold hover:text-skill-blue hover:underline decoration-skill-blue/30 underline-offset-4 transition-all transition-duration-300">Alex Joseph</a>
            </p>
            <div class="flex gap-8 text-xs font-black uppercase tracking-widest">
                <a href="privacy.php" class="hover:text-white transition-colors">Privacy</a>
                <a href="terms.php" class="hover:text-white transition-colors">Terms</a>
            </div>
        </div>

    </div>
</footer>

<script src="assets/js/main.js"></script>
</body>
</html>