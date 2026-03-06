<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: client/post-job.php
 * PURPOSE: Elite Project Studio - Surgical Layout & Total Consistency
 */

session_start();

// 1. Security Guard
if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'client') {
    header("Location: ../auth/login.php");
    exit();
}

// 2. System Initialization
$base_dir = dirname(__DIR__);
require_once $base_dir . '/includes/config.php';

$user_name = $_SESSION['full_name'];

// 3. Data Retrieval
$categories = $conn->query("SELECT * FROM categories WHERE status = 'active' ORDER BY sort_order ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Launch Project | SkillLink Studio</title>
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
        .glass-sidebar { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(20px); }
        .custom-scroll::-webkit-scrollbar { width: 6px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        
        /* ELITE MATERIAL SYSTEM */
        .elite-card { 
            background: white; 
            border: 1px solid rgba(255, 255, 255, 0.6); 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 20px 25px -5px rgba(0, 0, 0, 0.04); 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .elite-input-field {
            box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.03);
        }
        
        /* SIDEBAR CONSISTENCY - Dashboard Match */
        .active-nav { 
            background: white; 
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); 
            color: #3b82f6 !important; 
            border-radius: 1.25rem;
        }

        /* CRAFT SELECTION FEEDBACK */
        .category-card:hover { transform: translateY(-3px); }
        .category-card.selected { 
            border: 2px solid #3b82f6; 
            background: #f0f7ff; 
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.1); 
        }
        .category-card.selected i { color: #3b82f6; transform: scale(1.1); }
        .category-card.selected p { color: #3b82f6; font-weight: 900; }

        /* SEGMENTED CONTROL */
        .segment-btn { transition: all 0.3s ease; }
        .segment-btn.active { background: #0f172a; color: white; }

        @keyframes slideIn { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }
        .animate-slide { animation: slideIn 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>
</head>
<body class="bg-[#f8fafc] font-sans antialiased text-slate-900 overflow-hidden">

    <div class="flex h-screen overflow-hidden">
        
        <aside class="w-72 glass-sidebar border-r border-slate-200 flex flex-col z-50">
            <div class="p-8">
                <div class="flex items-center gap-3 mb-10">
                    <div class="w-10 h-10 bg-slate-900 text-white rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-screwdriver-wrench"></i>
                    </div>
                    <span class="text-xl font-black tracking-tighter uppercase">Skill<span class="text-skill-blue">Link</span></span>
                </div>

                <nav class="space-y-2">
                    <a href="dashboard.php" class="flex items-center gap-4 px-6 py-4 text-slate-500 hover:text-skill-blue transition-all rounded-2xl font-bold text-sm">
                        <i class="fas fa-th-large text-xs"></i> Dashboard
                    </a>
                    <a href="post-job.php" class="active-nav flex items-center gap-4 px-6 py-4 rounded-2xl font-black text-sm transition-all">
                        <i class="fas fa-plus-circle text-xs"></i> Launch Project
                    </a>
                    <a href="#" class="flex items-center gap-4 px-6 py-4 text-slate-500 hover:text-skill-blue transition-all rounded-2xl font-bold text-sm">
                        <i class="fas fa-calendar-check text-xs"></i> My Bookings
                    </a>
                    <a href="#" class="flex items-center gap-4 px-6 py-4 text-slate-500 hover:text-skill-blue transition-all rounded-2xl font-bold text-sm">
                        <i class="fas fa-clock-rotate-left text-xs"></i> Activity Log
                    </a>
                </nav>
            </div>
            <div class="mt-auto p-8 border-t border-slate-100">
                <a href="../auth/logout.php" class="flex items-center gap-4 px-6 py-4 text-red-500 hover:bg-red-50 rounded-2xl font-bold text-sm transition-all">
                    <i class="fas fa-power-off text-xs"></i> Sign Out
                </a>
            </div>
        </aside>

        <main class="flex-1 flex flex-col overflow-hidden relative">
            
            <header class="h-24 flex items-center justify-between px-12 bg-white/50 backdrop-blur-md border-b border-slate-100 relative z-20">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-skill-blue/10 text-skill-blue rounded-xl flex items-center justify-center">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-slate-900 leading-none tracking-tight">Project Studio</h1>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Engineer your professional request</p>
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Project Lead</p>
                        <p class="font-black text-slate-900"><?php echo $user_name; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-slate-900 rounded-2xl overflow-hidden border-2 border-white shadow-xl">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=0f172a&color=fff" alt="User">
                    </div>
                </div>
            </header>

            <section class="flex-1 overflow-y-auto p-12 custom-scroll bg-[#f1f5f9]/50">
                
                <form action="post-logic.php" method="POST" id="projectForm" class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-12 pb-24">
                    
                    <div class="lg:col-span-2 space-y-8">
                        <div class="elite-card p-10 rounded-[3rem] space-y-12">
                            
                            <div>
                                <label class="block text-xs font-black text-slate-900 uppercase tracking-widest mb-6 ml-2">1. Define the Craft</label>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <?php while($cat = $categories->fetch_assoc()): ?>
                                    <label class="category-card elite-card p-6 rounded-3xl text-center cursor-pointer border-2 border-transparent group">
                                        <input type="radio" name="category_id" value="<?php echo $cat['id']; ?>" class="hidden" required onclick="updateCategoryDisplay('<?php echo $cat['cat_name']; ?>')">
                                        <i class="fas fa-screwdriver-wrench text-2xl mb-3 text-slate-300 transition-all"></i>
                                        <p class="text-[10px] font-black uppercase tracking-tighter text-slate-600"><?php echo $cat['cat_name']; ?></p>
                                    </label>
                                    <?php endwhile; ?>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <label class="block text-xs font-black text-slate-900 uppercase tracking-widest ml-2">2. Project Blueprint</label>
                                <input type="text" name="title" placeholder="Project Headline (e.g., Fix Electrical Surge)" required
                                       class="elite-input-field w-full px-8 py-5 bg-slate-50 rounded-2xl text-sm font-bold text-slate-900 border-none outline-none focus:ring-2 focus:ring-skill-blue/10">
                                <textarea name="description" rows="5" placeholder="Describe the specific problem or task details..." required
                                          class="elite-input-field w-full px-8 py-6 bg-slate-50 rounded-[2.5rem] text-sm font-medium text-slate-700 border-none resize-none outline-none focus:ring-2 focus:ring-skill-blue/10"></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-8 border-t border-slate-50">
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 ml-2">3. Location Name</label>
                                    <div class="relative">
                                        <i class="fas fa-map-marker-alt absolute left-6 top-1/2 -translate-y-1/2 text-skill-blue"></i>
                                        <input type="text" name="location_name" placeholder="Area (e.g. Maseno)" required
                                               class="elite-input-field w-full pl-14 pr-8 py-5 bg-slate-50 rounded-2xl text-sm font-bold text-slate-900 border-none">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 ml-2">4. Financial Capsule</label>
                                    <div class="flex items-center bg-slate-50 rounded-2xl p-1.5 elite-input-field">
                                        <input type="number" name="budget" placeholder="0.00" step="0.01"
                                               class="flex-1 bg-transparent border-none px-4 py-3 text-sm font-black text-slate-900 outline-none w-24">
                                        
                                        <div class="flex gap-1 bg-white p-1 rounded-xl shadow-sm border border-slate-100">
                                            <input type="radio" name="budget_type" value="fixed" id="bt_fixed" class="hidden" checked onclick="toggleSegment(this)">
                                            <label for="bt_fixed" id="lbl_fixed" class="segment-btn active px-4 py-2 rounded-lg text-[9px] font-black uppercase cursor-pointer">Fixed</label>
                                            
                                            <input type="radio" name="budget_type" value="negotiable" id="bt_neg" class="hidden" onclick="toggleSegment(this)">
                                            <label for="bt_neg" id="lbl_neg" class="segment-btn px-4 py-2 rounded-lg text-[9px] font-black uppercase cursor-pointer text-slate-400">Negotiable</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div class="bg-slate-900/95 backdrop-blur-2xl p-10 rounded-[4rem] text-white space-y-8 relative overflow-hidden shadow-2xl border border-white/10 animate-slide sticky top-6">
                            <div class="absolute -top-10 -right-10 w-40 h-40 bg-skill-blue/20 rounded-full blur-3xl"></div>
                            
                            <div class="space-y-2">
                                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-skill-blue">Marketplace Snapshot</h3>
                                <p class="text-[10px] text-slate-400 font-medium leading-relaxed">Local professionals will see this request exactly like this.</p>
                            </div>

                            <div class="space-y-6 pt-6 border-t border-white/5">
                                <p id="preview-category" class="inline-block px-4 py-1.5 bg-skill-blue/20 text-skill-blue text-[10px] font-black uppercase rounded-full tracking-widest border border-skill-blue/30">Craft: Select...</p>
                                <h4 id="preview-title" class="text-2xl font-black leading-tight tracking-tight">Untitled Project</h4>
                                
                                <div class="flex flex-col gap-5">
                                    <div class="flex items-center gap-4 group">
                                        <div class="w-10 h-10 bg-white/5 rounded-xl flex items-center justify-center group-hover:bg-skill-blue/20 transition-all"><i class="fas fa-bolt-lightning text-amber-400"></i></div>
                                        <div>
                                            <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Market Status</p>
                                            <p class="text-[11px] font-bold">Standard Broadcast</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-white/5 rounded-xl flex items-center justify-center"><i class="fas fa-radar text-skill-blue animate-pulse"></i></div>
                                        <div>
                                            <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Live Reach</p>
                                            <p class="text-[11px] font-bold">Local Pro Matching</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="w-full py-6 bg-skill-blue text-white rounded-[1.5rem] font-black text-xs uppercase tracking-[0.2em] hover:scale-[1.03] transition-all shadow-xl shadow-skill-blue/30 active:scale-95">
                                Launch Project <i class="fas fa-paper-plane ml-3"></i>
                            </button>
                        </div>
                    </div>

                </form>
            </section>
        </main>
    </div>

    <script>
        // STUDIO INTERACTIVE LOGIC
        const form = document.getElementById('projectForm');
        const previewTitle = document.getElementById('preview-title');
        const previewCat = document.getElementById('preview-category');

        form.title.addEventListener('input', (e) => {
            previewTitle.textContent = e.target.value || "Untitled Project";
        });

        function updateCategoryDisplay(name) {
            previewCat.textContent = "Craft: " + name;
            // Visual Selected Feedback
            document.querySelectorAll('.category-card').forEach(card => {
                card.classList.remove('selected');
                if(card.querySelector('p').textContent === name) {
                    card.classList.add('selected');
                }
            });
        }

        function toggleSegment(radio) {
            document.querySelectorAll('.segment-btn').forEach(btn => {
                btn.classList.remove('active', 'text-white');
                btn.classList.add('text-slate-400');
            });
            const activeLabel = document.getElementById('lbl_' + (radio.value === 'fixed' ? 'fixed' : 'neg'));
            activeLabel.classList.add('active', 'text-white');
            activeLabel.classList.remove('text-slate-400');
        }
    </script>
</body>
</html>