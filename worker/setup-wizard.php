<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: worker/setup-wizard.php
 * PURPOSE: Elite Zero-Scroll UI with Enhanced Text Legibility
 */

session_start();

// 1. Security Gate
if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'worker') {
    header("Location: ../auth/login.php");
    exit();
}

// 2. System Initialization
$base_dir = dirname(__DIR__);
require_once $base_dir . '/includes/config.php';

// 3. Logic Gate Check
$user_id = $_SESSION['user_id'];
$check_profile = $conn->query("SELECT profile_id FROM worker_profiles WHERE user_id = '$user_id'");
if ($check_profile->num_rows > 0) {
    header("Location: dashboard.php");
    exit();
}

// 4. Data Retrieval
$categories = $conn->query("SELECT * FROM categories WHERE status = 'active' ORDER BY sort_order ASC");
$user_name = $_SESSION['full_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Onboarding | SkillLink</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { 'skill-blue': '<?php echo defined("BRAND_COLOR") ? BRAND_COLOR : "#3b82f6"; ?>' }
                }
            }
        }
    </script>

    <style>
        /* VIEWPORT LOCK: Anchors content to screen center without scrolling */
        html, body { height: 100%; overflow: hidden; background: #f8fafc; scroll-behavior: smooth; }
        body { -webkit-font-smoothing: antialiased; text-rendering: optimizeLegibility; }

        .category-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border-width: 2.5px; }
        .category-radio:checked + .category-card {
            border-color: <?php echo defined("BRAND_COLOR") ? BRAND_COLOR : "#3b82f6"; ?>;
            background: #eff6ff;
            transform: translateY(-2px) scale(1.01);
            box-shadow: 0 15px 20px -5px rgba(59, 130, 246, 0.1);
        }
        .category-radio:checked + .category-card i { color: <?php echo defined("BRAND_COLOR") ? BRAND_COLOR : "#3b82f6"; ?>; }
        .category-radio:checked + .category-card .check-badge { opacity: 1; transform: scale(1); }
        
        .step-transition { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>
</head>
<body class="font-sans text-slate-900">

    <div class="h-screen flex flex-col items-center justify-center p-4 lg:p-10">
        
        <div class="flex items-center gap-2 mb-4 transition-all duration-500">
            <div class="w-10 h-10 bg-slate-900 text-white rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-briefcase text-sm"></i>
            </div>
            <span class="text-xl font-black tracking-tighter uppercase">Skill<span class="text-skill-blue">Link</span></span>
        </div>

        <div class="w-full max-w-4xl">
            <form action="save-profile.php" method="POST" enctype="multipart/form-data" id="wizardForm">
                
                <div id="step1" class="step-transition">
                    <div class="text-center mb-6">
                        <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight leading-none">
                            Welcome, <span class="text-skill-blue"><?php echo explode(' ', $user_name)[0]; ?>!</span>
                        </h1>
                        <p class="text-slate-900 font-black text-xs md:text-sm mt-3 tracking-widest uppercase opacity-80">
                            Choose your primary professional craft
                        </p>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-4">
                        <?php 
                        $icon_map = [
                            'fa-faucet-drip' => 'fa-sink',
                            'fa-sparkles' => 'fa-broom'
                        ];
                        while($cat = $categories->fetch_assoc()): 
                            $icon = isset($icon_map[$cat['cat_icon']]) ? $icon_map[$cat['cat_icon']] : $cat['cat_icon'];
                        ?>
                        <label class="cursor-pointer group">
                            <input type="radio" name="category_id" value="<?php echo $cat['id']; ?>" class="hidden category-radio" required>
                            <div class="category-card relative p-5 bg-white border-slate-100 rounded-[2rem] text-center shadow-sm hover:border-skill-blue/40">
                                <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-blue-50 transition-colors">
                                    <i class="fa-solid <?php echo $icon; ?> text-xl text-slate-600 transition-colors"></i>
                                </div>
                                <h3 class="text-sm font-black text-slate-900 leading-tight"><?php echo $cat['cat_name']; ?></h3>
                                <p class="text-[8px] text-slate-400 font-black uppercase tracking-widest mt-1">Specialist</p>
                                <div class="check-badge absolute top-3 right-3 opacity-0 scale-50 transition-all text-skill-blue">
                                    <i class="fas fa-circle-check text-base"></i>
                                </div>
                            </div>
                        </label>
                        <?php endwhile; ?>
                    </div>

                    <div class="mt-6 flex justify-center">
                        <button type="button" onclick="nextStep(2)" class="group px-10 py-4 bg-slate-900 text-white rounded-[1.2rem] font-black text-xs uppercase tracking-[0.2em] hover:bg-skill-blue transition-all shadow-xl active:scale-95">
                            Continue <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </div>
                </div>

                <div id="step2" class="step-transition hidden opacity-0 translate-y-4">
                    <div class="max-w-lg mx-auto">
                        <div class="text-center mb-6">
                            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Professional Identity</h2>
                            <p class="text-slate-900 font-bold text-xs mt-1 uppercase opacity-80 tracking-widest">Client View Optimization</p>
                        </div>

                        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                            
                            <div class="flex items-center gap-6 mb-8">
                                <div class="relative w-24 h-24 flex-none">
                                    <img id="preview" src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=f1f5f9&color=64748b" 
                                         class="w-full h-full object-cover rounded-[1.5rem] border-4 border-white shadow-xl">
                                    <label class="absolute -bottom-1 -right-1 w-8 h-8 bg-skill-blue text-white rounded-xl flex items-center justify-center cursor-pointer shadow-lg hover:scale-110 transition-all">
                                        <i class="fas fa-camera text-xs"></i>
                                        <input type="file" name="profile_photo" class="hidden" accept="image/*" onchange="previewImage(this)">
                                    </label>
                                </div>
                                <div class="space-y-1">
                                    <h4 class="text-sm font-black text-slate-900">Profile Photo</h4>
                                    <p class="text-xs text-slate-600 font-semibold leading-relaxed">Build trust with a professional headshot.</p>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest ml-2">Your Pitch</label>
                                <textarea name="bio" rows="3" required maxlength="250"
                                    placeholder="Explain your expertise..."
                                    class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent focus:border-skill-blue/20 rounded-[1.5rem] text-sm font-semibold text-slate-800 focus:ring-0 transition-all resize-none shadow-inner"></textarea>
                                <div class="flex justify-between items-center px-2">
                                    <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wide">Experience Overview</span>
                                    <span id="charCount" class="text-[10px] text-skill-blue font-black uppercase tracking-widest bg-skill-blue/10 px-2 py-0.5 rounded-full">0/250</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-between items-center px-4">
                            <button type="button" onclick="nextStep(1)" class="text-slate-600 font-black text-xs uppercase tracking-widest hover:text-slate-900 transition-colors flex items-center gap-1">
                                <i class="fas fa-chevron-left text-[10px]"></i> Back
                            </button>
                            <button type="submit" class="px-10 py-4 bg-skill-blue text-white rounded-[1.2rem] font-black text-xs uppercase tracking-[0.2em] hover:bg-slate-900 transition-all shadow-xl shadow-skill-blue/20 active:scale-95">
                                Complete Profile <i class="fas fa-check-circle ml-2"></i>
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <script>
        function nextStep(step) {
            const s1 = document.getElementById('step1');
            const s2 = document.getElementById('step2');

            if(step === 2) {
                const selected = document.querySelector('input[name="category_id"]:checked');
                if(!selected) {
                    alert('Please select your professional craft first!');
                    return;
                }
                
                s1.classList.add('opacity-0', 'scale-95');
                setTimeout(() => {
                    s1.classList.add('hidden');
                    s2.classList.remove('hidden');
                    setTimeout(() => s2.classList.remove('opacity-0', 'translate-y-4'), 50);
                }, 400);
            } else {
                s2.classList.add('opacity-0', 'translate-y-4');
                setTimeout(() => {
                    s2.classList.add('hidden');
                    s1.classList.remove('hidden');
                    setTimeout(() => s1.classList.remove('opacity-0', 'scale-95'), 50);
                }, 400);
            }
        }

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        const textarea = document.querySelector('textarea[name="bio"]');
        textarea.addEventListener('input', function() {
            document.getElementById('charCount').textContent = this.value.length + '/250';
        });
    </script>
</body>
</html>