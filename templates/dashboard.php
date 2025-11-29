<?php
/**
 * Dashboard template - With Auto Tab Switch
 */

if (!defined('ABSPATH')) {
    exit;
}

$current_user = wp_get_current_user();
$user_stats = TryOutHub_Database::get_user_stats(get_current_user_id());
$user_points = TryOutHub_Database::get_user_points(get_current_user_id());
$user_rank = TryOutHub_Database::get_user_rank(get_current_user_id());
$recent_attempts = TryOutHub_Database::get_user_attempts(get_current_user_id(), 5);

// Determine active tab based on URL params
$active_tab = 'beranda';
if (isset($_GET['action']) && ($_GET['action'] === 'start' || $_GET['action'] === 'buy') && isset($_GET['pack_id'])) {
    $active_tab = 'tryout';
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TryOutHub Dashboard</title>
    <style>
        /* All previous styles remain the same... */
        
        /* Reset */
        * { margin: 0 !important; padding: 0 !important; box-sizing: border-box !important; }
        
        html, body {
            width: 100% !important;
            height: 100% !important;
            overflow: hidden !important;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Inter', sans-serif !important;
            background: #f8fafc;
        }
        
        /* Hide WordPress elements */
        #wpadminbar,
        body > header,
        body > footer,
        body > nav:not(.tryouthub-nav),
        body > .site,
        body > .site-header,
        body > .site-footer,
        body > .elementor-location-header,
        body > .elementor-location-footer,
        body > .ast-header,
        body > .ast-footer,
        body > #masthead,
        body > #colophon,
        .entry-header,
        .entry-title,
        .page-header,
        .page-title,
        h1.entry-title,
        [data-elementor-type="header"],
        [data-elementor-type="footer"] {
            display: none !important;
            visibility: hidden !important;
        }
        
        /* Dashboard Container */
        .tryouthub-dashboard {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
            width: 100vw !important;
            height: 100vh !important;
            background: #f8fafc !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            z-index: 999999 !important;
        }
        
        /* Sidebar */
        .tryouthub-sidebar {
            width: 280px !important;
            background: white !important;
            padding: 2rem 1.5rem !important;
            box-shadow: 2px 0 8px rgba(0,0,0,0.04) !important;
            height: 100vh !important;
            overflow-y: auto !important;
            flex-shrink: 0 !important;
        }
        
        .tryouthub-logo {
            font-size: 1.75rem !important;
            font-weight: 700 !important;
            color: #0070F9 !important;
            margin-bottom: 3rem !important;
            display: flex !important;
            align-items: center !important;
            gap: 0.75rem !important;
            text-decoration: none !important;
        }
        
        .tryouthub-logo-icon {
            width: 40px !important;
            height: 40px !important;
            background: linear-gradient(135deg, #0070F9 0%, #0060d9 100%) !important;
            border-radius: 0.75rem !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: white !important;
            font-size: 1.25rem !important;
        }
        
        .tryouthub-nav {
            display: flex !important;
            flex-direction: column !important;
            gap: 0.5rem !important;
        }
        
        .tryouthub-nav-item {
            display: flex !important;
            align-items: center !important;
            padding: 0.875rem 1rem !important;
            border-radius: 0.75rem !important;
            color: #64748b !important;
            text-decoration: none !important;
            cursor: pointer !important;
            transition: all 0.2s !important;
            font-weight: 500 !important;
            font-size: 0.95rem !important;
        }
        
        .tryouthub-nav-item:hover {
            background: #f1f5f9 !important;
            color: #0070F9 !important;
            text-decoration: none !important;
        }
        
        .tryouthub-nav-item.active {
            background: linear-gradient(135deg, #0070F9 0%, #0060d9 100%) !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(0,112,249,0.25) !important;
            text-decoration: none !important;
        }
        
        .tryouthub-nav-icon {
            margin-right: 0.875rem !important;
            font-size: 1.25rem !important;
            width: 24px !important;
        }
        
        /* Main Content */
        .tryouthub-main {
            flex: 1 !important;
            height: 100vh !important;
            overflow-y: auto !important;
            padding: 2rem !important;
        }
        
        /* Top Bar */
        .tryouthub-topbar {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            margin-bottom: 2rem !important;
            background: white !important;
            padding: 1rem 1.5rem !important;
            border-radius: 1rem !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04) !important;
        }
        
        .tryouthub-search {
            flex: 1 !important;
            max-width: 400px !important;
            position: relative !important;
        }
        
        .tryouthub-search input {
            width: 100% !important;
            padding: 0.75rem 1rem 0.75rem 2.75rem !important;
            border: 2px solid #e2e8f0 !important;
            border-radius: 0.75rem !important;
            font-size: 0.95rem !important;
            transition: all 0.2s !important;
        }
        
        .tryouthub-search input:focus {
            outline: none !important;
            border-color: #0070F9 !important;
            box-shadow: 0 0 0 3px rgba(0,112,249,0.1) !important;
        }
        
        .tryouthub-search-icon {
            position: absolute !important;
            left: 1rem !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            color: #94a3b8 !important;
            font-size: 1.1rem !important;
        }
        
        .tryouthub-topbar-right {
            display: flex !important;
            align-items: center !important;
            gap: 1rem !important;
        }
        
        .tryouthub-points-badge {
            display: flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
            padding: 0.625rem 1.25rem !important;
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%) !important;
            color: white !important;
            border-radius: 2rem !important;
            font-weight: 600 !important;
            font-size: 0.95rem !important;
            box-shadow: 0 2px 8px rgba(251,191,36,0.3) !important;
        }
        
        .tryouthub-user-avatar {
            width: 40px !important;
            height: 40px !important;
            border-radius: 50% !important;
            background: linear-gradient(135deg, #f87171 0%, #ef4444 100%) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: white !important;
            font-weight: 700 !important;
            font-size: 1.1rem !important;
            cursor: pointer !important;
            transition: all 0.2s !important;
        }
        
        .tryouthub-user-avatar:hover {
            transform: scale(1.05) !important;
            box-shadow: 0 4px 12px rgba(248,113,113,0.3) !important;
        }
        
        /* Hero Banner */
        .tryouthub-hero {
            background: linear-gradient(135deg, #0070F9 0%, #0060d9 100%) !important;
            border-radius: 1.5rem !important;
            padding: 3rem !important;
            margin-bottom: 2rem !important;
            color: white !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            box-shadow: 0 8px 24px rgba(0,112,249,0.25) !important;
            position: relative !important;
            overflow: hidden !important;
        }
        
        .tryouthub-hero::before {
            content: '' !important;
            position: absolute !important;
            top: -50% !important;
            right: -10% !important;
            width: 400px !important;
            height: 400px !important;
            background: rgba(255,255,255,0.1) !important;
            border-radius: 50% !important;
        }
        
        .tryouthub-hero::after {
            content: '' !important;
            position: absolute !important;
            bottom: -30% !important;
            left: -5% !important;
            width: 300px !important;
            height: 300px !important;
            background: rgba(255,255,255,0.08) !important;
            border-radius: 50% !important;
        }
        
        .tryouthub-hero-content {
            position: relative !important;
            z-index: 1 !important;
        }
        
        .tryouthub-hero h1 {
            font-size: 2rem !important;
            font-weight: 700 !important;
            margin: 0 0 0.5rem 0 !important;
        }
        
        .tryouthub-hero p {
            font-size: 1rem !important;
            opacity: 0.95 !important;
            margin: 0 0 1.5rem 0 !important;
        }
        
        .tryouthub-hero-btn {
            display: inline-flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
            padding: 0.75rem 1.5rem !important;
            background: white !important;
            color: #0070F9 !important;
            border-radius: 0.75rem !important;
            text-decoration: none !important;
            font-weight: 600 !important;
            transition: all 0.2s !important;
            box-shadow: 0 4px 12px rgba(255,255,255,0.3) !important;
        }
        
        .tryouthub-hero-btn:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 16px rgba(255,255,255,0.4) !important;
            text-decoration: none !important;
        }
        
        .tryouthub-hero-illustration {
            position: relative !important;
            z-index: 1 !important;
            font-size: 8rem !important;
            animation: float 3s ease-in-out infinite !important;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0) !important; }
            50% { transform: translateY(-20px) !important; }
        }
        
        /* Section */
        .tryouthub-section {
            margin-bottom: 2rem !important;
        }
        
        .tryouthub-section-header {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            margin-bottom: 1.5rem !important;
        }
        
        .tryouthub-section-title {
            font-size: 1.5rem !important;
            font-weight: 700 !important;
            color: #1e293b !important;
        }
        
        .tryouthub-view-all {
            color: #0070F9 !important;
            text-decoration: none !important;
            font-weight: 600 !important;
            font-size: 0.9rem !important;
            transition: all 0.2s !important;
        }
        
        .tryouthub-view-all:hover {
            color: #0060d9 !important;
            text-decoration: none !important;
        }
        
        /* Stats Grid */
        .tryouthub-stats-grid {
            display: grid !important;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)) !important;
            gap: 1.5rem !important;
        }
        
        .tryouthub-stat-card {
            background: white !important;
            padding: 2rem !important;
            border-radius: 1.25rem !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04) !important;
            transition: all 0.3s !important;
            position: relative !important;
            overflow: hidden !important;
        }
        
        .tryouthub-stat-card:hover {
            transform: translateY(-4px) !important;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08) !important;
        }
        
        .tryouthub-stat-card::before {
            content: '' !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            height: 4px !important;
        }
        
        .stat-yellow::before { background: linear-gradient(90deg, #fbbf24, #f59e0b) !important; }
        .stat-blue::before { background: linear-gradient(90deg, #0ea5e9, #0070F9) !important; }
        .stat-pink::before { background: linear-gradient(90deg, #ec4899, #db2777) !important; }
        .stat-green::before { background: linear-gradient(90deg, #10b981, #059669) !important; }
        
        .tryouthub-stat-card h3 {
            font-size: 2.5rem !important;
            font-weight: 700 !important;
            color: #1e293b !important;
            margin: 0 0 0.5rem 0 !important;
        }
        
        .tryouthub-stat-card p {
            color: #64748b !important;
            margin: 0 !important;
            font-size: 0.95rem !important;
        }
        
        .tryouthub-stat-icon {
            width: 48px !important;
            height: 48px !important;
            border-radius: 0.75rem !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 1.5rem !important;
            margin-bottom: 1rem !important;
        }
        
        .stat-yellow .tryouthub-stat-icon { background: #fef3c7 !important; }
        .stat-blue .tryouthub-stat-icon { background: #dbeafe !important; }
        .stat-pink .tryouthub-stat-icon { background: #fce7f3 !important; }
        .stat-green .tryouthub-stat-icon { background: #d1fae5 !important; }
        
        /* Premium Card */
        .tryouthub-premium-card {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%) !important;
            color: white !important;
        }
        
        .tryouthub-premium-card::before {
            background: linear-gradient(90deg, #8b5cf6, #7c3aed) !important;
        }
        
        .tryouthub-premium-card .tryouthub-stat-icon {
            background: rgba(255,255,255,0.2) !important;
        }
        
        .tryouthub-premium-card h3,
        .tryouthub-premium-card p {
            color: white !important;
        }
        
        .tryouthub-premium-btn {
            display: inline-block !important;
            padding: 0.625rem 1.25rem !important;
            background: white !important;
            color: #8b5cf6 !important;
            border-radius: 0.5rem !important;
            text-decoration: none !important;
            font-weight: 600 !important;
            font-size: 0.9rem !important;
            margin-top: 1rem !important;
            transition: all 0.2s !important;
        }
        
        .tryouthub-premium-btn:hover {
            transform: scale(1.05) !important;
            box-shadow: 0 4px 12px rgba(255,255,255,0.3) !important;
            text-decoration: none !important;
        }
        
        /* Tab content */
        .tryouthub-tab-content {
            display: none !important;
        }
        
        .tryouthub-tab-content.active {
            display: block !important;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .tryouthub-sidebar {
                position: fixed !important;
                left: 0 !important;
                top: 0 !important;
                transform: translateX(-100%) !important;
                transition: transform 0.3s !important;
                z-index: 1000 !important;
            }
            
            .tryouthub-sidebar.open {
                transform: translateX(0) !important;
            }
            
            .tryouthub-main {
                padding: 1rem !important;
            }
            
            .tryouthub-hero {
                flex-direction: column !important;
                text-align: center !important;
                padding: 2rem !important;
            }
            
            .tryouthub-hero-illustration {
                font-size: 5rem !important;
            }
            
            .tryouthub-stats-grid {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</head>
<body>

<div class="tryouthub-dashboard">
    <!-- Sidebar -->
    <div class="tryouthub-sidebar">
        <div class="tryouthub-logo">
            <div class="tryouthub-logo-icon">📚</div>
            TryOutHub
        </div>
        
        <nav class="tryouthub-nav">
            <a href="javascript:void(0)" class="tryouthub-nav-item <?php echo $active_tab === 'beranda' ? 'active' : ''; ?>" data-tab="beranda">
                <span class="tryouthub-nav-icon">🏠</span>
                Beranda
            </a>
            <a href="javascript:void(0)" class="tryouthub-nav-item <?php echo $active_tab === 'tryout' ? 'active' : ''; ?>" data-tab="tryout">
                <span class="tryouthub-nav-icon">📋</span>
                Tryout
            </a>
            <a href="javascript:void(0)" class="tryouthub-nav-item <?php echo $active_tab === 'profil' ? 'active' : ''; ?>" data-tab="profil">
                <span class="tryouthub-nav-icon">👤</span>
                Profil
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="tryouthub-main">
        <!-- Top Bar -->
        <div class="tryouthub-topbar">
            <div class="tryouthub-search">
                <span class="tryouthub-search-icon">🔍</span>
                <input type="text" placeholder="Search...">
            </div>
            <div class="tryouthub-topbar-right">
                <div class="tryouthub-points-badge">
                    ⭐ <?php echo number_format($user_points); ?>
                </div>
                <div class="tryouthub-user-avatar" title="<?php echo esc_attr($current_user->display_name); ?>">
                    <?php echo strtoupper(substr($current_user->display_name, 0, 1)); ?>
                </div>
            </div>
        </div>

        <!-- Tab Content: Beranda -->
        <div id="tab-beranda" class="tryouthub-tab-content <?php echo $active_tab === 'beranda' ? 'active' : ''; ?>">
            <!-- Hero Banner -->
            <div class="tryouthub-hero">
                <div class="tryouthub-hero-content">
                    <h1>Hi, <?php echo esc_html($current_user->display_name); ?>!</h1>
                    <p>Persiapkan diri Anda untuk UTBK-SNBT dengan latihan soal dan tryout terlengkap.</p>
                    <a href="javascript:void(0)" class="tryouthub-hero-btn">
                        Mulai Belajar →
                    </a>
                </div>
                <div class="tryouthub-hero-illustration">
                    📚
                </div>
            </div>

            <!-- Stats Section -->
            <div class="tryouthub-section">
                <div class="tryouthub-section-header">
                    <h2 class="tryouthub-section-title">Pencapaian Kamu</h2>
                    <a href="javascript:void(0)" class="tryouthub-view-all">VIEW ALL</a>
                </div>
                
                <div class="tryouthub-stats-grid">
                    <div class="tryouthub-stat-card stat-yellow">
                        <div class="tryouthub-stat-icon">⭐</div>
                        <h3><?php echo number_format($user_points); ?></h3>
                        <p>Total Poin</p>
                    </div>
                    
                    <div class="tryouthub-stat-card stat-blue">
                        <div class="tryouthub-stat-icon">✅</div>
                        <h3><?php echo $user_stats ? $user_stats->total_attempts : 0; ?></h3>
                        <p>Tryout Selesai</p>
                    </div>
                    
                    <div class="tryouthub-stat-card stat-pink">
                        <div class="tryouthub-stat-icon">📊</div>
                        <h3>#<?php echo number_format($user_rank); ?></h3>
                        <p>Peringkat</p>
                    </div>
                    
                    <div class="tryouthub-stat-card stat-green">
                        <div class="tryouthub-stat-icon">🎯</div>
                        <h3><?php echo $user_stats ? $user_stats->total_correct : 0; ?></h3>
                        <p>Jawaban Benar</p>
                    </div>
                </div>
            </div>

            <!-- Upgrade Card -->
            <div class="tryouthub-section">
                <div class="tryouthub-stat-card tryouthub-premium-card">
                    <div class="tryouthub-stat-icon">💎</div>
                    <h3>Upgrade to Pro</h3>
                    <p>Unlock all features and access premium content for more learning opportunities.</p>
                    <a href="javascript:void(0)" class="tryouthub-premium-btn">Upgrade →</a>
                </div>
            </div>
        </div>

        <!-- Tab Content: Tryout -->
        <div id="tab-tryout" class="tryouthub-tab-content <?php echo $active_tab === 'tryout' ? 'active' : ''; ?>">
            <?php 
            // Check if showing exam info
            if (isset($_GET['action']) && $_GET['action'] === 'start' && isset($_GET['pack_id'])):
                $pack_id = intval($_GET['pack_id']);
                $atts = array('id' => $pack_id);
                include TRYOUTHUB_PLUGIN_DIR . 'templates/exam-info.php';
            else:
                echo do_shortcode('[tryouthub_tryout_list]'); 
            endif;
            ?>
        </div>

        <!-- Tab Content: Profil -->
        <div id="tab-profil" class="tryouthub-tab-content <?php echo $active_tab === 'profil' ? 'active' : ''; ?>">
            <?php echo do_shortcode('[tryouthub_profile]'); ?>
        </div>
    </div>
</div>

<script>
(function() {
    // Hide WordPress elements
    function hideWordPressElements() {
        var selectors = [
            '#wpadminbar',
            'body > header:not(.tryouthub-dashboard *)',
            'body > footer:not(.tryouthub-dashboard *)',
            'body > .site',
            'body > .elementor-location-header',
            'body > .elementor-location-footer',
            '.entry-title',
            '.page-title',
            'h1.entry-title'
        ];
        
        selectors.forEach(function(selector) {
            try {
                var elements = document.querySelectorAll(selector);
                elements.forEach(function(el) {
                    if (!el.classList.contains('tryouthub-dashboard') && 
                        !el.closest('.tryouthub-dashboard')) {
                        el.style.display = 'none';
                    }
                });
            } catch(e) {}
        });
        
        var dashboard = document.querySelector('.tryouthub-dashboard');
        if (dashboard) {
            dashboard.style.display = 'flex';
            dashboard.style.visibility = 'visible';
            dashboard.style.opacity = '1';
        }
    }
    
    hideWordPressElements();
    window.addEventListener('load', hideWordPressElements);
    setTimeout(hideWordPressElements, 100);
    
    // Tab navigation with URL awareness
    
    document.addEventListener('DOMContentLoaded', function() {
        var navItems = document.querySelectorAll('.tryouthub-nav-item');
        
        // Check for hash on load
        if (window.location.hash) {
            var hashTab = window.location.hash.substring(1); // Remove #
            switchToTab(hashTab);
        }
        
        navItems.forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                var tab = this.getAttribute('data-tab');
                switchToTab(tab);
            });
        });
        
        function switchToTab(tab) {
            // Update active nav
            navItems.forEach(function(nav) {
                nav.classList.remove('active');
            });
            
            var targetNav = document.querySelector('[data-tab="' + tab + '"]');
            if (targetNav) {
                targetNav.classList.add('active');
            }
            
            // Show corresponding content
            var tabContents = document.querySelectorAll('.tryouthub-tab-content');
            tabContents.forEach(function(content) {
                content.classList.remove('active');
            });
            
            var targetTab = document.getElementById('tab-' + tab);
            if (targetTab) {
                targetTab.classList.add('active');
            }
        }
    });
})();
</script>

</body>
</html>
<?php
exit();
?>