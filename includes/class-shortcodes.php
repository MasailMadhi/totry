<?php
/**
 * Shortcode handlers
 */

if (!defined('ABSPATH')) {
    exit;
}

class TryOutHub_Shortcodes {
    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_shortcode('tryouthub_login', array($this, 'login_shortcode'));
        add_shortcode('tryouthub_app', array($this, 'app_shortcode'));
        add_shortcode('tryouthub_tryout_list', array($this, 'tryout_list_shortcode'));
        add_shortcode('tryouthub_exam', array($this, 'exam_shortcode'));
        add_shortcode('tryouthub_profile', array($this, 'profile_shortcode'));
    }
    
    /**
     * Login form shortcode
     */
    public function login_shortcode($atts) {
        if (is_user_logged_in()) {
            return '<p>Anda sudah login. <a href="' . home_url('/app') . '">Ke Dashboard</a></p>';
        }
        
        ob_start();
        include TRYOUTHUB_PLUGIN_DIR . 'templates/login-form.php';
        return ob_get_clean();
    }
    
    /**
     * Main app dashboard shortcode
     */
    /**
 * Main app dashboard shortcode
 */
/**
/**
 * Main app dashboard shortcode
 */
public function app_shortcode($atts) {
    if (!is_user_logged_in()) {
        return do_shortcode('[tryouthub_login]');
    }
    
    // Check if starting exam (actual exam interface)
    if (isset($_GET['action']) && $_GET['action'] === 'start_exam' && isset($_GET['pack_id'])) {
        $pack_id = intval($_GET['pack_id']);
        
        // Check access
        if (!TryOutHub_Auth::can_access_pack($pack_id)) {
            return '<p>Anda tidak memiliki akses ke tryout ini.</p>';
        }
        
        // Render exam page (full screen, no dashboard)
        ob_start();
        $atts = array('id' => $pack_id);
        include TRYOUTHUB_PLUGIN_DIR . 'templates/exam.php';
        return ob_get_clean();
    }
    
    // Show exam info (inside dashboard)
    if (isset($_GET['action']) && $_GET['action'] === 'start' && isset($_GET['pack_id'])) {
        // This will be handled inside dashboard.php
    }
    
    // Check if buying
    if (isset($_GET['action']) && $_GET['action'] === 'buy' && isset($_GET['pack_id'])) {
        return '<p>Halaman pembayaran akan segera tersedia.</p>';
    }
    
    ob_start();
    include TRYOUTHUB_PLUGIN_DIR . 'templates/dashboard.php';
    return ob_get_clean();
}
    
    /**
     * Tryout list shortcode
     */
    public function tryout_list_shortcode($atts) {
        if (!is_user_logged_in()) {
            return '<p>Silakan <a href="' . wp_login_url(get_permalink()) . '">login</a> untuk mengakses tryout.</p>';
        }
        
        $atts = shortcode_atts(array(
            'category' => '',
        ), $atts);
        
        ob_start();
        include TRYOUTHUB_PLUGIN_DIR . 'templates/tryout-list.php';
        return ob_get_clean();
    }
    
    /**
     * Exam interface shortcode
     */
    public function exam_shortcode($atts) {
        if (!is_user_logged_in()) {
            return '<p>Silakan <a href="' . wp_login_url(get_permalink()) . '">login</a> untuk mengikuti tryout.</p>';
        }
        
        $atts = shortcode_atts(array(
            'id' => 0,
        ), $atts);
        
        $pack_id = intval($atts['id']);
        
        if (!$pack_id) {
            return '<p>ID tryout tidak valid.</p>';
        }
        
        // Check access
        if (!TryOutHub_Auth::can_access_pack($pack_id)) {
            return '<p>Anda tidak memiliki akses ke tryout ini. Silakan beli terlebih dahulu.</p>';
        }
        
        ob_start();
        include TRYOUTHUB_PLUGIN_DIR . 'templates/exam.php';
        return ob_get_clean();
    }
    
    /**
     * Profile shortcode
     */
    public function profile_shortcode($atts) {
        if (!is_user_logged_in()) {
            return '<p>Silakan <a href="' . wp_login_url(get_permalink()) . '">login</a> untuk melihat profil.</p>';
        }
        
        ob_start();
        include TRYOUTHUB_PLUGIN_DIR . 'templates/profile.php';
        return ob_get_clean();
    }
}