<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: auth/success.php
 * PURPOSE: Elite Success Transition & Identity Recognition
 * REFINEMENTS: Haptic Checkmark, Progress Bridge, and Automated Redirect.
 */
session_start();

// Security: If no signup session exists, redirect back to signup
if (!isset($_SESSION['temp_name'])) {
    header("Location: signup.php");
    exit();
}

$full_name = $_SESSION['temp_name'];
$first_name = explode(' ', $full_name)[0]; // Extracting only the first name for a warmer greeting
$role = isset($_SESSION['temp_role']) ? $_SESSION['temp_role'] : 'User';

// Pulling core config for the Brand Color logic
require_once '../includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to SkillLink | Success</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">

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
        @keyframes scale-up {
            0% { transform: scale(0.5); opacity: 0; }
            60% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-scale { animation: scale-up 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
        .progress-fill { transition: width 3s linear; }
    </style>
</head>
<body class="bg-white font-sans antialiased text-slate-900 overflow-hidden flex items-center justify-center h-screen relative">

    <div class="absolute inset-0 z-0 select-none pointer-events-none">
        <div class="absolute top-[-10%] left-[-5%] w-[600px] h-[600px] bg-blue-500/5 rounded-full blur-[150px]"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[500px] h-[500px] bg-skill-blue/5 rounded-full blur-[120px]"></div>
        <div class="absolute inset-0 opacity-[0.02]" style="background-image: linear-gradient(#94a3b8 1px, transparent 1px), linear-gradient(90deg, #94a3b8 1px, transparent 1px); background-size: 60px 60px;"></div>
    </div>

    <div class="max-w-md w-full px-10 py-16 text-center relative z-10">
        
        <div class="mb-10 inline-flex items-center justify-center w-28 h-28 bg-green-50 text-green-500 rounded-[2.5rem] text-5xl shadow-inner animate-scale">
            <i class="fas fa-check-double"></i>
        </div>

        <div class="space-y-3 mb-12">
            <h1 class="text-4xl font-[900] tracking-tighter text-slate-900 leading-tight">
                You're in, <span class="text-skill-blue italic"><?php echo $first_name; ?>!</span>
            </h1>
            <p class="text-slate-500 font-medium">
                Your <span class="text-slate-900 font-bold"><?php echo ucfirst($role); ?></span> account is officially verified. Welcome to the professional marketplace.
            </p>
        </div>

        <div class="space-y-5 bg-slate-50 p-8 rounded-[2rem] border border-slate-100">
            <div class="flex justify-between items-center mb-1">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">System Handshake</span>
                <span id="timer-text" class="text-[10px] font-black text-skill-blue">Redirecting in 3s</span>
            </div>
            <div class="h-1.5 w-full bg-slate-200 rounded-full overflow-hidden">
                <div id="progress-bar" class="h-full bg-skill-blue progress-fill" style="width: 0%;"></div>
            </div>
        </div>

        <div class="mt-12">
            <a href="login.php" class="group flex items-center justify-center gap-3 text-xs font-black uppercase tracking-[0.1em] text-slate-400 hover:text-slate-900 transition-all">
                Skip wait <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
    </div>

    <script>
        window.addEventListener('load', () => {
            const bar = document.getElementById('progress-bar');
            const timerText = document.getElementById('timer-text');
            
            // Start the visual fill
            setTimeout(() => { bar.style.width = '100%'; }, 100);

            // Numerical Countdown Logic
            let timeLeft = 3;
            const interval = setInterval(() => {
                timeLeft--;
                if (timeLeft > 0) {
                    timerText.innerText = `Redirecting in ${timeLeft}s`;
                } else {
                    timerText.innerText = `Opening Login...`;
                    clearInterval(interval);
                    window.location.href = 'login.php';
                }
            }, 1000);
        });
    </script>
</body>
</html>