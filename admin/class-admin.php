<?php
/**
 * Admin interface handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class TryOutHub_Admin {
    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'check_admin_capability'));
    }
    
    /**
     * Check admin capability
     */
    public function check_admin_capability() {
        if (isset($_GET['page']) && strpos($_GET['page'], 'tryouthub') !== false) {
            if (!current_user_can('manage_tryouthub') && !current_user_can('administrator')) {
                wp_die('You do not have permission to access this page.');
            }
        }
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        // Main menu
        add_menu_page(
            'TryOutHub',
            'TryOutHub',
            'manage_tryouthub',
            'tryouthub',
            array($this, 'dashboard_page'),
            'dashicons-welcome-learn-more',
            30
        );
        
        // Dashboard
        add_submenu_page(
            'tryouthub',
            'Dashboard',
            'Dashboard',
            'manage_tryouthub',
            'tryouthub',
            array($this, 'dashboard_page')
        );
        
        // Questions
        add_submenu_page(
            'tryouthub',
            'Soal',
            'Soal',
            'manage_tryouthub',
            'tryouthub-questions',
            array($this, 'questions_page')
        );
        
        // Packs
        add_submenu_page(
            'tryouthub',
            'Paket Tryout',
            'Paket Tryout',
            'manage_tryouthub',
            'tryouthub-packs',
            array($this, 'packs_page')
        );
        
        // Transactions
        add_submenu_page(
            'tryouthub',
            'Transaksi',
            'Transaksi',
            'manage_tryouthub',
            'tryouthub-transactions',
            array($this, 'transactions_page')
        );
        
        // Settings
        add_submenu_page(
            'tryouthub',
            'Pengaturan',
            'Pengaturan',
            'manage_tryouthub',
            'tryouthub-settings',
            array($this, 'settings_page')
        );
    }
    
    /**
     * Dashboard page
     */
    public function dashboard_page() {
        global $wpdb;
        
        // Get stats
        $questions_count = $wpdb->get_var("SELECT COUNT(*) FROM " . TryOutHub_Database::get_table('questions'));
        $packs_count = $wpdb->get_var("SELECT COUNT(*) FROM " . TryOutHub_Database::get_table('packs'));
        $attempts_count = $wpdb->get_var("SELECT COUNT(*) FROM " . TryOutHub_Database::get_table('attempts'));
        $users_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->users} u INNER JOIN {$wpdb->usermeta} um ON u.ID = um.user_id WHERE um.meta_key = 'wp_capabilities' AND um.meta_value LIKE '%tryouthub_student%'");
        
        include TRYOUTHUB_PLUGIN_DIR . 'admin/views/dashboard.php';
    }
    
    /**
     * Questions page
     */
    public function questions_page() {
        $questions_handler = TryOutHub_Questions_Admin::get_instance();
        $questions_handler->render_page();
    }
    
    /**
     * Packs page
     */
    public function packs_page() {
        $packs_handler = TryOutHub_Packs_Admin::get_instance();
        $packs_handler->render_page();
    }
    
    /**
     * Transactions page
     */
    public function transactions_page() {
        include TRYOUTHUB_PLUGIN_DIR . 'admin/views/transactions.php';
    }
    
    /**
     * Settings page
     */
    public function settings_page() {
        // Save settings
        if (isset($_POST['tryouthub_save_settings'])) {
            check_admin_referer('tryouthub_settings');
            
            update_option('tryouthub_primary_color', sanitize_hex_color($_POST['primary_color']));
            update_option('tryouthub_points_per_correct', intval($_POST['points_per_correct']));
            update_option('tryouthub_points_per_wrong', intval($_POST['points_per_wrong']));
            update_option('tryouthub_enable_ranking', isset($_POST['enable_ranking']) ? 1 : 0);
            
            echo '<div class="notice notice-success"><p>Pengaturan berhasil disimpan.</p></div>';
        }
        
        include TRYOUTHUB_PLUGIN_DIR . 'admin/views/settings.php';
    }
}