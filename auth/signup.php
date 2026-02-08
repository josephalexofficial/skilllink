<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: auth/signup.php
 * PURPOSE: User-Centric, Zero-Scroll Multi-Step Registration
 * REFINEMENTS: Identity-Gate Validation, Custom Dropdown, and Mass-Weighted Buttons.
 */

// 1. System Initialization
$include_path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;

if (file_exists($include_path . 'config.php')) {
    require_once $include_path . 'config.php';
} else {
    die("Critical Error: Core configuration missing.");
}

// 2. State Detection: Determine User Role
$role = isset($_GET['role']) ? $_GET['role'] : 'worker';
$is_worker = ($role === 'worker');

// 3. Component Loading
$current_page = 'signup';
include $include_path . 'header.php';

// 4. Data Preparation: Kenyan Counties
$counties = [
    "Baringo", "Bomet", "Bungoma", "Busia", "Elgeyo Marakwet", "Embu", "Garissa", "Homa Bay", "Isiolo", "Kajiado", "Kakamega", "Kericho", "Kiambu", "Kilifi", "Kirinyaga", "Kisii", "Kisumu", "Kitui", "Kwale", "Laikipia", "Lamu", "Machakos", "Makueni", "Mandera", "Meru", "Migori", "Marsabit", "Mombasa", "Murang'a", "Nairobi", "Nakuru", "Nandi", "Narok", "Nyamira", "Nyandarua", "Nyeri", "Samburu", "Siaya", "Taita Taveta", "Tana River", "Tharaka Nithi", "Trans Nzoia", "Turkana", "Uasin Gishu", "Vihiga", "Wajir", "West Pokot"
];
?>

<main class="h-[calc(100vh-80px)] flex items-center justify-center bg-white font-sans antialiased text-slate-900 overflow-hidden relative">

    <div class="absolute inset-0 z-0 pointer-events-none select-none">
        <div class="absolute top-[-10%] left-[-5%] w-[600px] h-[600px] bg-blue-500/5 rounded-full blur-[150px]"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[500px] h-[500px] bg-skill-blue/5 rounded-full blur-[120px]"></div>
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: linear-gradient(#94a3b8 1px, transparent 1px), linear-gradient(90deg, #94a3b8 1px, transparent 1px); background-size: 50px 50px;"></div>
    </div>

    <div class="w-full max-w-xl relative z-10 px-6">
        
        <div class="bg-white rounded-[3rem] shadow-[0_40px_100px_-20px_rgba(0,0,0,0.08)] border border-slate-100 overflow-hidden">
            
            <div class="h-1.5 w-full bg-slate-100 relative">
                <div id="progress-bar" class="absolute top-0 left-0 h-full bg-skill-blue transition-all duration-700 ease-in-out" style="width: 50%;"></div>
            </div>

            <div class="p-10 md:p-14">
                <div class="text-center space-y-2 mb-8">
                    <h1 class="text-3xl font-[900] tracking-tighter text-slate-900 leading-none">
                        <?php echo $is_worker ? 'Create <span class="text-skill-blue italic">Pro Profile</span>' : 'Create <span class="text-skill-blue italic">Project Account</span>'; ?>
                    </h1>
                    <p class="text-slate-500 text-sm font-medium">Step <span id="step-number">1</span>: Basic Information</p>
                </div>

                <form action="signup-process.php" method="POST" id="signup-form" class="relative min-h-[380px]">
                    <input type="hidden" name="role" value="<?php echo htmlspecialchars($role); ?>">

                    <div id="step-1" class="space-y-5 transition-all duration-500">
                        <div class="space-y-2">
                            <label class="text-[11px] font-black uppercase tracking-widest text-slate-900 ml-2">Full Name</label>
                            <div class="relative">
                                <i class="fas fa-user absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 text-sm"></i>
                                <input type="text" id="name" name="name" placeholder="Alex Joseph" required
                                       class="w-full pl-12 pr-6 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-skill-blue transition-all outline-none font-bold text-slate-800 shadow-inner text-base placeholder:text-slate-300">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[11px] font-black uppercase tracking-widest text-slate-900 ml-2">Email Address</label>
                            <div class="relative">
                                <i class="fas fa-envelope absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 text-sm"></i>
                                <input type="email" id="email" name="email" placeholder="alex@gmail.com" required
                                       class="w-full pl-12 pr-6 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-skill-blue transition-all outline-none font-bold text-slate-800 shadow-inner text-base placeholder:text-slate-300">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[11px] font-black uppercase tracking-widest text-slate-900 ml-2">Phone Number</label>
                            <div class="relative">
                                <i class="fas fa-phone absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 text-sm"></i>
                                <input type="tel" id="phone" name="phone" placeholder="0769 591 223" required
                                       class="w-full pl-12 pr-6 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-skill-blue transition-all outline-none font-bold text-slate-800 shadow-inner text-base placeholder:text-slate-300">
                            </div>
                        </div>

                        <button type="button" onclick="nextStep()" class="w-full py-6.5 mt-2 bg-slate-900 text-white rounded-2xl font-black text-lg flex items-center justify-center gap-3 hover:bg-skill-blue transition-all duration-500 transform active:scale-95 shadow-xl">
                            Continue <i class="fas fa-arrow-right text-sm"></i>
                        </button>
                    </div>

                    <div id="step-2" class="space-y-5 hidden transition-all duration-500 opacity-0 translate-x-10">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2 relative">
                                <label class="text-[11px] font-black uppercase tracking-widest text-slate-900 ml-2">Select County</label>
                                <div class="relative">
                                    <i class="fas fa-map-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-sm"></i>
                                    <input type="text" id="countyInput" name="county" placeholder="e.g. Kisumu" autocomplete="off" required
                                           class="w-full pl-10 pr-4 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-skill-blue transition-all outline-none font-bold text-slate-800 shadow-inner text-sm placeholder:text-slate-300">
                                    
                                    <div id="countyDropdown" class="hidden absolute left-0 right-0 mt-2 bg-white border border-slate-100 rounded-2xl shadow-2xl z-[100] max-h-48 overflow-y-auto overflow-x-hidden backdrop-blur-xl bg-white/95">
                                        <?php foreach($counties as $c): ?>
                                            <div class="county-option px-6 py-3.5 hover:bg-skill-blue/5 hover:text-skill-blue cursor-pointer font-bold text-sm transition-all border-b border-slate-50 last:border-0" data-value="<?php echo $c; ?>">
                                                <?php echo $c; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[11px] font-black uppercase tracking-widest text-slate-900 ml-2">Area / Estate</label>
                                <div class="relative">
                                    <i class="fas fa-house-chimney absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-sm"></i>
                                    <input type="text" name="area" placeholder="e.g. Milimani" required
                                           class="w-full pl-10 pr-4 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-skill-blue transition-all outline-none font-bold text-slate-800 shadow-inner text-sm placeholder:text-slate-300">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[11px] font-black uppercase tracking-widest text-slate-900 ml-2">Password</label>
                            <div class="relative">
                                <i class="fas fa-lock absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 text-sm"></i>
                                <input type="password" name="password" id="password" placeholder="••••••••" required
                                       class="w-full pl-12 pr-6 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-skill-blue transition-all outline-none font-bold text-slate-800 shadow-inner text-base">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[11px] font-black uppercase tracking-widest text-slate-900 ml-2">Confirm Password</label>
                            <div class="relative">
                                <i class="fas fa-shield-check absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 text-sm"></i>
                                <input type="password" name="confirm_password" id="confirm_password" placeholder="••••••••" required
                                       class="w-full pl-12 pr-6 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-skill-blue transition-all outline-none font-bold text-slate-800 shadow-inner text-base">
                            </div>
                        </div>

                        <div class="flex gap-4 mt-6">
                            <button type="button" onclick="prevStep()" class="w-1/3 py-6.5 bg-slate-100 text-slate-600 rounded-2xl font-black text-base hover:bg-slate-200 transition-all active:scale-95">
                                Back
                            </button>
                            <button type="submit" class="flex-1 py-6.5 bg-slate-900 text-white rounded-2xl font-black text-lg flex items-center justify-center gap-3 hover:bg-skill-blue transition-all duration-500 shadow-xl active:scale-95">
                                Create Account <i class="fas fa-rocket text-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-8 text-center">
            <p class="text-slate-400 text-xs font-bold uppercase tracking-[0.2em]">
                Already a member? <a href="login.php" class="text-skill-blue ml-2 hover:underline decoration-2 underline-offset-4 font-black transition-all">Sign In</a>
            </p>
        </div>
    </div>
</main>

<script>
    /**
     * CUSTOM DROPDOWN ENGINE
     */
    const countyInput = document.getElementById('countyInput');
    const countyDropdown = document.getElementById('countyDropdown');
    const countyOptions = document.querySelectorAll('.county-option');

    countyInput.addEventListener('focus', () => countyDropdown.classList.remove('hidden'));

    countyInput.addEventListener('input', (e) => {
        const val = e.target.value.toLowerCase();
        countyOptions.forEach(opt => {
            const text = opt.getAttribute('data-value').toLowerCase();
            opt.style.display = text.includes(val) ? 'block' : 'none';
        });
    });

    countyOptions.forEach(opt => {
        opt.addEventListener('click', () => {
            countyInput.value = opt.getAttribute('data-value');
            countyInput.style.borderColor = 'transparent';
            countyDropdown.classList.add('hidden');
        });
    });

    document.addEventListener('click', (e) => {
        if (!countyInput.contains(e.target) && !countyDropdown.contains(e.target)) {
            countyDropdown.classList.add('hidden');
        }
    });

    /**
     * PROTOCOL VALIDATION & TRANSITION ENGINE
     */
    function nextStep() {
        // Grab Step 1 Inputs
        const name = document.getElementById('name');
        const email = document.getElementById('email');
        const phone = document.getElementById('phone');

        // Check if Identity Node is complete
        let isValid = true;
        [name, email, phone].forEach(input => {
            if (!input.value.trim()) {
                input.style.borderColor = '#ef4444'; // Red Haptic Glow
                isValid = false;
            } else {
                input.style.borderColor = 'transparent';
            }
        });

        if (!isValid) return; // Exit if validation fails

        // Proceed with Transition
        const s1 = document.getElementById('step-1');
        const s2 = document.getElementById('step-2');
        const progress = document.getElementById('progress-bar');
        const stepNum = document.getElementById('step-number');

        s1.classList.add('opacity-0', '-translate-x-10');
        setTimeout(() => {
            s1.classList.add('hidden');
            s2.classList.remove('hidden');
            setTimeout(() => {
                s2.classList.remove('opacity-0', 'translate-x-10');
                progress.style.width = '100%';
                stepNum.innerText = '2';
            }, 50);
        }, 300);
    }

    function prevStep() {
        const s1 = document.getElementById('step-1');
        const s2 = document.getElementById('step-2');
        const progress = document.getElementById('progress-bar');
        const stepNum = document.getElementById('step-number');

        s2.classList.add('opacity-0', 'translate-x-10');
        setTimeout(() => {
            s2.classList.add('hidden');
            s1.classList.remove('hidden');
            setTimeout(() => {
                s1.classList.remove('opacity-0', '-translate-x-10');
                progress.style.width = '50%';
                stepNum.innerText = '1';
            }, 50);
        }, 300);
    }

    // Live matching logic
    const pass = document.getElementById('password');
    const confirm = document.getElementById('confirm_password');

    confirm.addEventListener('input', () => {
        if (confirm.value === pass.value && pass.value !== '') {
            confirm.style.borderColor = '#10b981';
        } else {
            confirm.style.borderColor = 'transparent';
        }
    });
</script>

<?php include $include_path . 'footer.php'; ?>