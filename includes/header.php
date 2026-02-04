<?php
/**
 * SkillLink Master Header - Final "Pro" Edition
 * REFINEMENTS: Implemented Dynamic Route Detection for Navigation States.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config.php';

// --- DYNAMIC ROUTE LOGIC ---
// Captures the current filename to determine the active navigation state
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { 'skill-blue': '<?php echo BRAND_COLOR; ?>' }
                }
            }
        }
    </script>
    <style>
        /* Modern Underline Transition Logic */
        .nav-link {
            position: relative;
            padding-bottom: 4px;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: <?php echo BRAND_COLOR; ?>;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .nav-link:hover::after { width: 100%; }
        /* Forces underline visibility for the active page */
        .nav-active::after { width: 100% !important; }
    </style>
</head>
<body class="bg-gray-50 antialiased font-sans">

<nav class="sticky top-0 z-50 bg-white shadow-sm border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="flex justify-between items-center h-20">
            
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-skill-blue text-white rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                    <i class="fas fa-screwdriver-wrench text-lg"></i>
                </div>
                <a href="<?php echo BASE_URL; ?>index.php" class="text-2xl font-extrabold tracking-tighter text-slate-900">
                    Skill<span class="text-skill-blue">Link</span>
                </a>
            </div>

            <div class="hidden lg:flex items-center space-x-10">
                <a href="<?php echo BASE_URL; ?>index.php" 
                   class="nav-link text-sm font-bold tracking-wide transition-all <?php echo ($current_page == 'index.php') ? 'nav-active text-skill-blue' : 'text-slate-600 hover:text-skill-blue'; ?>">
                    Home
                </a>

                <a href="<?php echo BASE_URL; ?>services.php" 
                   class="nav-link text-sm font-bold tracking-wide transition-all <?php echo ($current_page == 'services.php') ? 'nav-active text-skill-blue' : 'text-slate-600 hover:text-skill-blue'; ?>">
                    Services
                </a>

                <a href="<?php echo BASE_URL; ?>mission.php" 
                   class="nav-link text-sm font-bold tracking-wide transition-all <?php echo ($current_page == 'mission.php') ? 'nav-active text-skill-blue' : 'text-slate-600 hover:text-skill-blue'; ?>">
                    Our Mission
                </a>

                <a href="<?php echo BASE_URL; ?>contact.php" 
                   class="nav-link text-sm font-bold tracking-wide transition-all <?php echo ($current_page == 'contact.php') ? 'nav-active text-skill-blue' : 'text-slate-600 hover:text-skill-blue'; ?>">
                    Contact Us
                </a>
            </div>

            <div class="flex items-center gap-6">
                <a href="<?php echo BASE_URL; ?>auth/login.php" class="text-sm font-bold text-slate-700 hover:text-skill-blue transition-colors">Log In</a>
                <a href="<?php echo BASE_URL; ?>signup-choice.php" class="bg-skill-blue text-white px-8 py-3 rounded-xl font-bold text-sm shadow-xl shadow-blue-500/25 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-300">
                    Join Now
                </a>
            </div>

        </div>
    </div>
</nav>