<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: services.php
 * PURPOSE: Refined Professional Service Discovery
 * REFINEMENTS: Aspect-ratio locking, verified badges, and dynamic nav-state logic.
 */

// 1. System Initialization
$include_path = __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;

if (file_exists($include_path . 'config.php')) {
    require_once $include_path . 'config.php';
} else {
    die("Critical Error: Core configuration missing.");
}

require_once $include_path . 'db.php';

// 2. Dynamic Navigation Logic: Define current page for header.php
$current_page = basename($_SERVER['PHP_SELF']);
include $include_path . 'header.php';
?>

<main class="min-h-screen bg-slate-50 font-sans antialiased text-slate-900">

    <header class="bg-white border-b border-slate-200 pt-16 pb-10 px-6">
        <div class="max-w-7xl mx-auto space-y-8">
            <div class="space-y-2 text-center md:text-left">
                <h1 class="text-4xl md:text-5xl font-[900] tracking-tighter text-slate-900">Browse Services</h1>
                <p class="text-slate-500 text-lg font-medium">Find and book elite, verified professionals for any task.</p>
            </div>

            <div class="relative max-w-3xl mx-auto md:mx-0 group">
                <i class="fas fa-magnifying-glass absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-skill-blue transition-colors"></i>
                <input type="text" placeholder="Search for plumbers, electricians, or cleaners..." 
                       class="w-full pl-16 pr-8 py-5 bg-slate-100 border-2 border-transparent rounded-[2rem] focus:bg-white focus:ring-8 focus:ring-blue-500/5 focus:border-skill-blue transition-all duration-500 font-bold outline-none text-slate-700 shadow-inner">
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-6 py-16 flex flex-col lg:flex-row gap-16">
        
        <aside class="w-full lg:w-72 space-y-12 shrink-0">
            <div class="sticky top-28 space-y-12">
                
                <div class="space-y-6">
                    <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Categories</h4>
                    <div class="space-y-4">
                        <?php 
                        $filters = ['All Services', 'Plumbing', 'Electrical', 'Painting', 'Carpentry', 'Cleaning', 'Appliances'];
                        foreach($filters as $index => $f): 
                        ?>
                        <label class="flex items-center gap-4 cursor-pointer group">
                            <input type="checkbox" class="w-5 h-5 rounded-lg border-slate-200 text-skill-blue focus:ring-skill-blue transition-all" <?php echo $index === 0 ? 'checked' : ''; ?>>
                            <span class="text-sm font-black text-slate-600 group-hover:text-skill-blue transition-colors"><?php echo $f; ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="space-y-6 pt-8 border-t border-slate-200">
                    <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 text-left">Professional Rating</h4>
                    <div class="space-y-4">
                        <?php for($i=4; $i>=3; $i--): ?>
                        <label class="flex items-center gap-4 cursor-pointer group">
                            <input type="radio" name="rating" class="w-5 h-5 border-slate-200 text-skill-blue focus:ring-skill-blue">
                            <div class="flex items-center gap-1.5 text-amber-400 text-[10px]">
                                <?php for($j=0; $j<5; $j++) echo $j < $i ? '<i class="fas fa-star"></i>' : '<i class="far fa-star text-slate-200"></i>'; ?>
                                <span class="ml-2 text-slate-700 font-black tracking-tighter text-sm">& up</span>
                            </div>
                        </label>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </aside>

        <section class="flex-1 space-y-10">
            
            <div class="flex items-center justify-between border-b border-slate-200 pb-8">
                <p class="text-sm font-black text-slate-400 uppercase tracking-widest">Showing <span class="text-slate-900">12</span> Experts Available</p>
                <div class="flex items-center gap-4">
                    <span class="hidden md:block text-[10px] font-black uppercase tracking-widest text-slate-400">Sort:</span>
                    <select class="bg-white px-4 py-2 border border-slate-200 rounded-xl font-black text-xs outline-none cursor-pointer hover:border-skill-blue transition-all shadow-sm">
                        <option>Recommended</option>
                        <option>Top Rated</option>
                        <option>Response Time</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-10">
                <?php
                $pros = [
                    ['name' => 'John Kamau', 'cat' => 'Plumbing', 'price' => '1,500', 'rating' => '4.9', 'jobs' => '124', 'img' => 'https://i.pravatar.cc/150?u=1'],
                    ['name' => 'Sarah Otieno', 'cat' => 'Electrical', 'price' => '2,200', 'rating' => '4.8', 'jobs' => '89', 'img' => 'https://i.pravatar.cc/150?u=2'],
                    ['name' => 'Michael Chen', 'cat' => 'Cleaning', 'price' => '800', 'rating' => '4.7', 'jobs' => '210', 'img' => 'https://i.pravatar.cc/150?u=3'],
                    ['name' => 'David Musembi', 'cat' => 'Carpentry', 'price' => '3,500', 'rating' => '5.0', 'jobs' => '45', 'img' => 'https://i.pravatar.cc/150?u=4'],
                    ['name' => 'Aisha Bakari', 'cat' => 'Painting', 'price' => '1,200', 'rating' => '4.6', 'jobs' => '67', 'img' => 'https://i.pravatar.cc/150?u=5'],
                    ['name' => 'Peter Juma', 'cat' => 'Appliances', 'price' => '2,000', 'rating' => '4.9', 'jobs' => '112', 'img' => 'https://i.pravatar.cc/150?u=6']
                ];

                foreach($pros as $pro):
                ?>
                <div class="group bg-white rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/20 hover:shadow-2xl hover:border-skill-blue hover:-translate-y-3 transition-all duration-700 overflow-hidden relative">
                    
                    <div class="relative aspect-square md:aspect-video bg-slate-200 overflow-hidden">
                        <img src="<?php echo $pro['img']; ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-[1.5s] ease-out">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="absolute top-5 left-5">
                            <span class="px-4 py-1.5 bg-white/95 backdrop-blur-md rounded-full text-[10px] font-black uppercase tracking-widest text-skill-blue shadow-lg border border-white">
                                <i class="fas fa-circle text-[6px] mr-2 animate-pulse"></i> <?php echo $pro['cat']; ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-10 space-y-8">
                        <div class="flex justify-between items-start gap-4">
                            <div class="space-y-2">
                                <h3 class="text-2xl font-[900] text-slate-900 tracking-tighter leading-none"><?php echo $pro['name']; ?></h3>
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center text-amber-400 text-[10px]">
                                        <i class="fas fa-star"></i>
                                        <span class="ml-1.5 text-slate-900 font-black text-sm"><?php echo $pro['rating']; ?></span>
                                    </div>
                                    <span class="text-slate-400 text-xs font-bold tracking-tight">(<?php echo $pro['jobs']; ?> Jobs)</span>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">From</span>
                                <span class="text-xl font-black text-skill-blue leading-none">KES <?php echo $pro['price']; ?></span>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-slate-50">
                            <a href="pro-profile.php?id=1" class="w-full py-5 bg-slate-900 text-white rounded-[1.5rem] font-black text-sm flex items-center justify-center gap-3 hover:bg-skill-blue hover:shadow-xl hover:shadow-blue-500/20 transition-all duration-500 transform active:scale-95">
                                Book Now <i class="fas fa-arrow-right text-[10px] group-hover:translate-x-2 transition-transform"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="pt-20 flex justify-center">
                <button class="px-16 py-6 bg-white border-2 border-slate-100 rounded-[2rem] font-black text-slate-900 hover:bg-slate-50 hover:border-slate-200 hover:-translate-y-1 transition-all duration-300 shadow-sm">
                    Load More Professionals
                </button>
            </div>
        </section>

    </div>

</main>

<?php include $include_path . 'footer.php'; ?>