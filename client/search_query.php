<?php
/**
 * PROJECT: SkillLink - The Professional Marketplace
 * FILE: client/search_query.php
 */
require_once dirname(__DIR__) . '/includes/config.php';

$q = $_GET['query'] ?? '';
if (empty($q)) exit();

$q = $conn->real_escape_string($q);

// Sector Matches
$cats = $conn->query("SELECT * FROM categories WHERE cat_name LIKE '%$q%' AND status = 'active' LIMIT 3");

// Expert Matches
$pros = $conn->query("
    SELECT u.full_name, u.id, wp.category_id, c.cat_name, c.cat_icon
    FROM users u
    JOIN worker_profiles wp ON u.id = wp.user_id
    JOIN categories c ON wp.category_id = c.id
    WHERE (u.full_name LIKE '%$q%' OR wp.bio LIKE '%$q%')
    AND u.status = 'active' LIMIT 5");

echo '<div class="p-6 space-y-6">';

if ($cats->num_rows > 0) {
    echo '<p class="text-[8px] font-black text-slate-400 uppercase tracking-[0.3em] px-4">Suggested Sectors</p>';
    while($c = $cats->fetch_assoc()) {
        $icon = str_contains(strtolower($c['cat_name']), 'clean') ? 'fa-broom-ball' : ($c['cat_icon'] ?: 'fa-cube');
        echo "
        <a href='explore.php?cat={$c['id']}' class='flex items-center gap-4 p-4 hover:bg-slate-50 rounded-2xl transition-all group border border-transparent hover:border-slate-100'>
            <div class='w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all'>
                <i class='fas $icon'></i>
            </div>
            <span class='font-black text-sm text-slate-900'>{$c['cat_name']}</span>
        </a>";
    }
}

if ($pros->num_rows > 0) {
    echo '<p class="text-[8px] font-black text-slate-400 uppercase tracking-[0.3em] px-4 mt-2">Verified Experts</p>';
    while($p = $pros->fetch_assoc()) {
        $initials = strtoupper(substr($p['full_name'], 0, 1));
        echo "
        <a href='view-worker.php?id={$p['id']}' class='flex items-center gap-4 p-4 hover:bg-slate-50 rounded-2xl transition-all'>
            <div class='w-10 h-10 bg-slate-900 text-white rounded-xl flex items-center justify-center text-[10px] font-black'>$initials</div>
            <div>
                <p class='font-black text-sm text-slate-900 leading-none'>{$p['full_name']}</p>
                <p class='text-[9px] font-bold text-blue-500 uppercase mt-1'>{$p['cat_name']}</p>
            </div>
        </a>";
    }
}

if ($cats->num_rows == 0 && $pros->num_rows == 0) {
    echo '<div class="p-10 text-center text-slate-400 text-[10px] font-black uppercase italic">No expert nodes identified.</div>';
}

echo '</div>';