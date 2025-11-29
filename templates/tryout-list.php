<?php
/**
 * Tryout list template - Fixed Button Links
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get Full UTBK packs
$full_packs = TryOutHub_Database::get_packs(array(
    'is_full' => 1,
    'status' => 'publish',
    'per_page' => 10,
));

// Categories for TPS
$tps_categories = array(
    'PU' => array('name' => 'Penalaran Umum', 'color' => '#f87171', 'icon' => 'PU'),
    'PPU' => array('name' => 'Pengetahuan & Pemahaman Umum', 'color' => '#fb923c', 'icon' => 'PPU'),
    'PBM' => array('name' => 'Pemahaman Bacaan & Menulis', 'color' => '#a78bfa', 'icon' => 'PBM'),
    'PK' => array('name' => 'Pengetahuan Kuantitatif', 'color' => '#4ade80', 'icon' => 'PK'),
);

// Categories for Literasi
$literasi_categories = array(
    'LIT_BahasaID' => array('name' => 'Literasi Bahasa Indonesia', 'color' => '#60a5fa', 'icon' => 'LBI'),
    'LIT_BahasaEN' => array('name' => 'Literasi Bahasa Inggris', 'color' => '#34d399', 'icon' => 'LBE'),
    'PM' => array('name' => 'Penalaran Matematika', 'color' => '#fbbf24', 'icon' => 'PM'),
);

$current_url = home_url('/app');

// Di bagian tombol, update URL generation:
$start_url = add_query_arg(array('action' => 'start', 'pack_id' => $tryout['id']), home_url('/app'));
$buy_url = add_query_arg(array('action' => 'buy', 'pack_id' => $tryout['id']), home_url('/app'));
?>

<style>
    /* All styles remain the same as before */
    .tryouthub-tryout-page * {
        box-sizing: border-box !important;
    }
    
    .tryouthub-tryout-hero {
        background: linear-gradient(135deg, #a8e6cf 0%, #dcedc8 100%) !important;
        border-radius: 1.5rem !important;
        padding: 3rem !important;
        margin-bottom: 3rem !important;
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
    }
    
    .tryouthub-tryout-hero h1 {
        font-size: 2rem !important;
        font-weight: 700 !important;
        color: #1a1a1a !important;
        margin: 0 0 0.5rem 0 !important;
    }
    
    .tryouthub-tryout-hero p {
        color: #4b5563 !important;
        margin: 0 !important;
        font-size: 1rem !important;
    }
    
    .tryouthub-tryout-illustration {
        width: 200px !important;
        height: 120px !important;
        background: white !important;
        border-radius: 1rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 3rem !important;
        font-weight: 700 !important;
        color: #16a34a !important;
    }
    
    .tryouthub-section-title {
        font-size: 1.5rem !important;
        font-weight: 700 !important;
        margin-bottom: 1.5rem !important;
        color: #1e293b !important;
    }
    
    .tryouthub-section-subtitle {
        font-size: 1.25rem !important;
        font-weight: 600 !important;
        margin-top: 3rem !important;
        margin-bottom: 1.5rem !important;
        color: #1e293b !important;
    }
    
    /* Full UTBK Tryout Cards */
    .tryouthub-full-tryout-grid {
        display: grid !important;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)) !important;
        gap: 1.5rem !important;
        margin-bottom: 3rem !important;
    }
    
    .tryouthub-full-tryout-card {
        background: white !important;
        border-radius: 1.25rem !important;
        padding: 2rem !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04) !important;
        transition: all 0.3s !important;
        text-decoration: none !important;
        display: block !important;
    }
    
    .tryouthub-full-tryout-card:hover {
        transform: translateY(-4px) !important;
        box-shadow: 0 8px 24px rgba(0,0,0,0.08) !important;
        text-decoration: none !important;
    }
    
    .tryouthub-tryout-badge {
        display: inline-block !important;
        padding: 0.375rem 0.875rem !important;
        border-radius: 2rem !important;
        font-size: 0.85rem !important;
        font-weight: 600 !important;
        margin-bottom: 1rem !important;
    }
    
    .badge-free {
        background: #d1fae5 !important;
        color: #16a34a !important;
    }
    
    .badge-premium {
        background: #fef3c7 !important;
        color: #92400e !important;
    }
    
    .tryouthub-tryout-title {
        font-size: 1.25rem !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        margin-bottom: 1rem !important;
    }
    
    .tryouthub-tryout-meta {
        display: flex !important;
        gap: 1rem !important;
        font-size: 0.9rem !important;
        color: #64748b !important;
        margin-bottom: 1.5rem !important;
    }
    
    .tryouthub-tryout-price {
        font-size: 1.5rem !important;
        font-weight: 700 !important;
        color: #0070F9 !important;
        margin-bottom: 1rem !important;
    }
    
    .tryouthub-tryout-price.free {
        color: #16a34a !important;
    }
    
    .tryouthub-btn-start {
        display: block !important;
        width: 100% !important;
        padding: 0.875rem !important;
        background: linear-gradient(135deg, #0070F9 0%, #0060d9 100%) !important;
        color: white !important;
        border: none !important;
        border-radius: 0.75rem !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        text-align: center !important;
        text-decoration: none !important;
        transition: all 0.2s !important;
    }
    
    .tryouthub-btn-start:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 12px rgba(0,112,249,0.3) !important;
        text-decoration: none !important;
    }
    
    .tryouthub-btn-buy {
        display: block !important;
        width: 100% !important;
        padding: 0.875rem !important;
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%) !important;
        color: white !important;
        border: none !important;
        border-radius: 0.75rem !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        text-align: center !important;
        text-decoration: none !important;
        transition: all 0.2s !important;
    }
    
    .tryouthub-btn-buy:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 12px rgba(251,191,36,0.3) !important;
        text-decoration: none !important;
    }
    
    /* Category Grid */
    .tryouthub-category-grid {
        display: grid !important;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)) !important;
        gap: 1.5rem !important;
        margin-bottom: 2rem !important;
    }
    
    .tryouthub-category-card {
        background: white !important;
        border-radius: 1.25rem !important;
        overflow: hidden !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04) !important;
        cursor: pointer !important;
        transition: all 0.2s !important;
        text-decoration: none !important;
        display: block !important;
    }
    
    .tryouthub-category-card:hover {
        transform: translateY(-4px) !important;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        text-decoration: none !important;
    }
    
    .tryouthub-category-header {
        padding: 3rem 2rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    
    .tryouthub-category-icon {
        font-size: 4rem !important;
        font-weight: 700 !important;
        color: white !important;
    }
    
    .tryouthub-category-body {
        padding: 1.5rem !important;
        background: white !important;
    }
    
    .tryouthub-category-title {
        font-size: 1.1rem !important;
        font-weight: 600 !important;
        color: #1a1a1a !important;
        margin: 0 !important;
        text-decoration: none !important;
    }
    
    .tryouthub-report-btn {
        position: fixed !important;
        bottom: 2rem !important;
        right: 2rem !important;
        padding: 1rem 1.5rem !important;
        background: #0070F9 !important;
        color: white !important;
        border: none !important;
        border-radius: 0.75rem !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        box-shadow: 0 4px 12px rgba(0,112,249,0.3) !important;
        display: flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
        z-index: 999 !important;
        text-decoration: none !important;
    }
    
    .tryouthub-report-btn:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 16px rgba(0,112,249,0.4) !important;
        text-decoration: none !important;
    }
</style>

<div class="tryouthub-tryout-page">
    <!-- Hero -->
    <div class="tryouthub-tryout-hero">
        <div>
            <h1>Tryout UTBK-SNBT</h1>
            <p>Pilih sub tes tryout yang ingin kamu kerjakan</p>
        </div>
        <div class="tryouthub-tryout-illustration">
            UTBK
        </div>
    </div>

    <!-- Full UTBK Tryout Section -->
    <h2 class="tryouthub-section-title">Tryout</h2>
    <div class="tryouthub-full-tryout-grid">
        <?php 
        // Manually create 3 tryout cards as requested
        $tryout_items = array(
            array(
                'id' => isset($full_packs[0]) ? $full_packs[0]->id : 1,
                'title' => 'Try Out UTBK – SNBT 2026 Edisi 001',
                'duration' => 195,
                'questions' => 160,
                'price' => 0,
                'is_free' => true,
            ),
            array(
                'id' => isset($full_packs[1]) ? $full_packs[1]->id : 2,
                'title' => 'Try Out UTBK – SNBT 2026 Edisi 002',
                'duration' => 195,
                'questions' => 160,
                'price' => 30000,
                'is_free' => false,
            ),
            array(
                'id' => isset($full_packs[2]) ? $full_packs[2]->id : 3,
                'title' => 'Try Out UTBK – SNBT 2026 Edisi 003',
                'duration' => 195,
                'questions' => 160,
                'price' => 30000,
                'is_free' => false,
            ),
        );
        
        foreach ($tryout_items as $tryout):
            $is_accessible = TryOutHub_Auth::can_access_pack($tryout['id']);
            $start_url = add_query_arg(array('action' => 'start', 'pack_id' => $tryout['id']), $current_url);
            $buy_url = add_query_arg(array('action' => 'buy', 'pack_id' => $tryout['id']), $current_url);
        ?>
            <div class="tryouthub-full-tryout-card">
                <span class="tryouthub-tryout-badge <?php echo $tryout['is_free'] ? 'badge-free' : 'badge-premium'; ?>">
                    <?php echo $tryout['is_free'] ? 'Gratis' : 'Premium'; ?>
                </span>
                
                <h3 class="tryouthub-tryout-title"><?php echo esc_html($tryout['title']); ?></h3>
                
                <div class="tryouthub-tryout-meta">
                    <span>⏱️ <?php echo $tryout['duration']; ?> menit</span>
                    <span>📝 <?php echo $tryout['questions']; ?> soal</span>
                </div>
                
                <div class="tryouthub-tryout-price <?php echo $tryout['is_free'] ? 'free' : ''; ?>">
                    <?php echo $tryout['is_free'] ? 'GRATIS' : 'Rp ' . number_format($tryout['price'], 0, ',', '.'); ?>
                </div>
                
                <?php if ($tryout['is_free'] || $is_accessible): ?>
                    <!-- Free or already purchased -->
                    <a href="<?php echo esc_url($start_url); ?>" class="tryouthub-btn-start">
                        Mulai Tryout
                    </a>
                <?php else: ?>
                    <!-- Needs to purchase -->
                    <a href="<?php echo esc_url($buy_url); ?>" class="tryouthub-btn-buy">
                        🛒 Beli Sekarang
                    </a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- TPS Section -->
    <h2 class="tryouthub-section-subtitle">Tes Potensi Skolastik (TPS)</h2>
    <div class="tryouthub-category-grid">
        <a href="<?php echo add_query_arg('category', 'PU', $current_url); ?>" class="tryouthub-category-card">
            <div class="tryouthub-category-header" style="background: #f87171 !important;">
                <div class="tryouthub-category-icon">PU</div>
            </div>
            <div class="tryouthub-category-body">
                <div class="tryouthub-category-title">Penalaran Umum</div>
            </div>
        </a>

        <a href="<?php echo add_query_arg('category', 'PPU', $current_url); ?>" class="tryouthub-category-card">
            <div class="tryouthub-category-header" style="background: #fb923c !important;">
                <div class="tryouthub-category-icon">PPU</div>
            </div>
            <div class="tryouthub-category-body">
                <div class="tryouthub-category-title">Pengetahuan & Pemahaman Umum</div>
            </div>
        </a>

        <a href="<?php echo add_query_arg('category', 'PBM', $current_url); ?>" class="tryouthub-category-card">
            <div class="tryouthub-category-header" style="background: #a78bfa !important;">
                <div class="tryouthub-category-icon">PBM</div>
            </div>
            <div class="tryouthub-category-body">
                <div class="tryouthub-category-title">Pemahaman Bacaan & Menulis</div>
            </div>
        </a>

        <a href="<?php echo add_query_arg('category', 'PK', $current_url); ?>" class="tryouthub-category-card">
            <div class="tryouthub-category-header" style="background: #4ade80 !important;">
                <div class="tryouthub-category-icon">PK</div>
            </div>
            <div class="tryouthub-category-body">
                <div class="tryouthub-category-title">Pengetahuan Kuantitatif</div>
            </div>
        </a>
    </div>

    <!-- Literasi Section -->
    <h2 class="tryouthub-section-subtitle">Tes Literasi</h2>
    <div class="tryouthub-category-grid">
        <a href="<?php echo add_query_arg('category', 'LIT_BahasaID', $current_url); ?>" class="tryouthub-category-card">
            <div class="tryouthub-category-header" style="background: #60a5fa !important;">
                <div class="tryouthub-category-icon">LBI</div>
            </div>
            <div class="tryouthub-category-body">
                <div class="tryouthub-category-title">Literasi Bahasa Indonesia</div>
            </div>
        </a>

        <a href="<?php echo add_query_arg('category', 'LIT_BahasaEN', $current_url); ?>" class="tryouthub-category-card">
            <div class="tryouthub-category-header" style="background: #34d399 !important;">
                <div class="tryouthub-category-icon">LBE</div>
            </div>
            <div class="tryouthub-category-body">
                <div class="tryouthub-category-title">Literasi Bahasa Inggris</div>
            </div>
        </a>

        <a href="<?php echo add_query_arg('category', 'PM', $current_url); ?>" class="tryouthub-category-card">
            <div class="tryouthub-category-header" style="background: #fbbf24 !important;">
                <div class="tryouthub-category-icon">PM</div>
            </div>
            <div class="tryouthub-category-body">
                <div class="tryouthub-category-title">Penalaran Matematika</div>
            </div>
        </a>
    </div>

    <!-- Report Button -->
    <button class="tryouthub-report-btn">
        📨 Laporkan Masalah
    </button>
</div>

<script>
(function() {
    // Report button handler
    var reportBtn = document.querySelector('.tryouthub-report-btn');
    if (reportBtn) {
        reportBtn.addEventListener('click', function() {
            alert('Fitur pelaporan masalah akan segera hadir.');
        });
    }
})();
</script>