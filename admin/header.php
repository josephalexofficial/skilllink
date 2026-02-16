<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: admin/header.php
 * PURPOSE: Elite HUD (Heads-Up Display) with Zero Marketing Clutter
 */

// 1. Path Resolver (Ensures assets load regardless of sub-folder depth)
$base_path = "/skilllink/"; 
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillLink | Admin Command Center</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'skill-blue': '#3b82f6',
                        'slate-900': '#0f172a',
                    }
                }
            }
        }
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        
        /* The Rigid Frame Reset */
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            margin: 0;
            padding: 0;
            overflow: hidden; /* Prevents the whole browser window from scrolling */
            height: 100vh;
        }

        .glass-hud {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(15, 23, 42, 0.05);
        }
    </style>
</head>
<body class="bg-[#F8FAFC]">

    <header class="glass-hud w-full h-20 flex flex-none items-center px-10 z-50">
        
        <div class="flex-1 flex items-center gap-3">
            <div class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                <i class="fas fa-microchip text-skill-blue"></i>
                <span>Admin</span>
                <span class="text-slate-200">/</span>
                <span class="text-slate-900"><?php echo $page_title ?? 'Dashboard'; ?></span>
            </div>
            <div class="ml-4 px-3 py-1 bg-green-500/10 rounded-full border border-green-500/20 flex items-center gap-2">
                <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                <span class="text-[9px] font-black uppercase text-green-600 tracking-tighter">Live Node</span>
            </div>
        </div>

        <div class="flex-1 max-w-md relative group hidden md:block">
            <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-skill-blue transition-colors"></i>
            <input type="text" placeholder="Search system records..." 
                   class="w-full bg-slate-100/50 border-2 border-transparent py-2.5 pl-14 pr-6 rounded-2xl text-sm font-bold outline-none focus:bg-white focus:border-skill-blue transition-all">
        </div>

        <div class="flex-1 flex justify-end items-center gap-6">
            <div class="flex items-center gap-4 pl-6 border-l border-slate-100">
                <div class="text-right hidden lg:block">
                    <p class="text-xs font-black text-slate-900 uppercase tracking-tight leading-none">
                        <?php echo $_SESSION['full_name'] ?? 'Admin'; ?>
                    </p>
                    <p class="text-[9px] font-bold text-slate-400 mt-1 uppercase tracking-widest">Master Access</p>
                </div>
                <div class="w-11 h-11 bg-slate-900 rounded-2xl flex items-center justify-center text-white font-black shadow-lg shadow-slate-900/10">
                    S
                </div>
            </div>
        </div>
    </header>