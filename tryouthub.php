<?php
/**
 * Plugin Name: TryOutHub
 * Plugin URI: https://tryout.tautku.id
 * Description: Platform tryout UTBK lengkap untuk WordPress dengan dashboard interaktif, soal builder, dan sistem penilaian otomatis
 * Version: 1.0.0
 * Author: TryOutHub Team
 * Author URI: https://tryout.tautku.id
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: tryouthub
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('TRYOUTHUB_VERSION', '1.0.0');
define('TRYOUTHUB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TRYOUTHUB_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TRYOUTHUB_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Require dependencies
require_once TRYOUTHUB_PLUGIN_DIR . 'includes/class-activator.php';
require_once TRYOUTHUB_PLUGIN_DIR . 'includes/class-database.php';
require_once TRYOUTHUB_PLUGIN_DIR . 'includes/class-auth.php';
require_once TRYOUTHUB_PLUGIN_DIR . 'includes/class-shortcodes.php';
require_once TRYOUTHUB_PLUGIN_DIR . 'includes/class-rest-api.php';
require_once TRYOUTHUB_PLUGIN_DIR . 'includes/class-exam.php';
require_once TRYOUTHUB_PLUGIN_DIR . 'includes/class-scoring.php';

if (is_admin()) {
    require_once TRYOUTHUB_PLUGIN_DIR . 'admin/class-admin.php';
    require_once TRYOUTHUB_PLUGIN_DIR . 'admin/class-questions.php';
    require_once TRYOUTHUB_PLUGIN_DIR . 'admin/class-packs.php';
    require_once TRYOUTHUB_PLUGIN_DIR . 'admin/class-csv-importer.php';
}

/**
 * Main plugin class
 */
class TryOutHub {
    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
    }
    
    private function init_hooks() {
        register_activation_hook(__FILE__, array('TryOutHub_Activator', 'activate'));
        register_deactivation_hook(__FILE__, array('TryOutHub_Activator', 'deactivate'));
        
        add_action('plugins_loaded', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }
    
    public function init() {
        // Initialize components
        TryOutHub_Auth::get_instance();
        TryOutHub_Shortcodes::get_instance();
        TryOutHub_REST_API::get_instance();
        
        if (is_admin()) {
            TryOutHub_Admin::get_instance();
        }
    }
    
    public function enqueue_frontend_assets() {
        // Enqueue Tailwind CSS
        wp_enqueue_style(
            'tryouthub-styles',
            TRYOUTHUB_PLUGIN_URL . 'assets/css/app.css',
            array(),
            TRYOUTHUB_VERSION
        );
        
        // Enqueue main JavaScript
        wp_enqueue_script(
            'tryouthub-app',
            TRYOUTHUB_PLUGIN_URL . 'assets/js/app.js',
            array('jquery'),
            TRYOUTHUB_VERSION,
            true
        );
        
        // Localize script with AJAX URL and nonce
        wp_localize_script('tryouthub-app', 'tryouthubData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'restUrl' => rest_url('tryouthub/v1'),
            'nonce' => wp_create_nonce('tryouthub_nonce'),
            'currentUserId' => get_current_user_id(),
        ));
    }
    
    public function enqueue_admin_assets($hook) {
        // Only enqueue on TryOutHub admin pages
        if (strpos($hook, 'tryouthub') === false) {
            return;
        }
        
        wp_enqueue_style(
            'tryouthub-admin',
            TRYOUTHUB_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            TRYOUTHUB_VERSION
        );
        
        wp_enqueue_script(
            'tryouthub-admin',
            TRYOUTHUB_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'wp-tinymce'),
            TRYOUTHUB_VERSION,
            true
        );
        
        wp_localize_script('tryouthub-admin', 'tryouthubAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tryouthub_admin_nonce'),
        ));
    }
}

// Initialize plugin
function tryouthub_init() {
    return TryOutHub::get_instance();
}

// Start the plugin
tryouthub_init();
