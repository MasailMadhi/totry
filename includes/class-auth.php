<?php
/**
 * Authentication handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class TryOutHub_Auth {
    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_ajax_nopriv_tryouthub_login', array($this, 'ajax_login'));
        add_action('wp_ajax_nopriv_tryouthub_register', array($this, 'ajax_register'));
        add_action('wp_ajax_tryouthub_logout', array($this, 'ajax_logout'));
        add_action('wp_ajax_tryouthub_change_password', array($this, 'ajax_change_password'));
        
        // Redirect after login
        add_filter('login_redirect', array($this, 'redirect_after_login'), 10, 3);
        
        // Prevent students from accessing WordPress admin
        add_action('admin_init', array($this, 'prevent_admin_access'));
        add_filter('show_admin_bar', array($this, 'hide_admin_bar'));
    }
    
    /**
     * Prevent students from accessing WordPress admin dashboard
     */
    public function prevent_admin_access() {
        if (!current_user_can('administrator') && !current_user_can('manage_tryouthub')) {
            // Check if user has tryouthub_student role
            $user = wp_get_current_user();
            if (in_array('tryouthub_student', $user->roles)) {
                wp_redirect(home_url('/app'));
                exit;
            }
        }
    }
    
    /**
     * Hide admin bar for students
     */
    public function hide_admin_bar($show) {
        if (!current_user_can('administrator') && !current_user_can('manage_tryouthub')) {
            $user = wp_get_current_user();
            if (in_array('tryouthub_student', $user->roles)) {
                return false;
            }
        }
        return $show;
    }
    
    /**
     * AJAX login handler
     */
    public function ajax_login() {
        check_ajax_referer('tryouthub_nonce', 'nonce');
        
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $remember = isset($_POST['remember']) ? true : false;
        
        if (empty($email) || empty($password)) {
            wp_send_json_error(array(
                'message' => 'Email dan password harus diisi.'
            ));
        }
        
        // Get user by email
        $user = get_user_by('email', $email);
        
        if (!$user) {
            wp_send_json_error(array(
                'message' => 'Email atau password salah.'
            ));
        }
        
        // Authenticate
        $creds = array(
            'user_login' => $user->user_login,
            'user_password' => $password,
            'remember' => $remember,
        );
        
        $user = wp_signon($creds, is_ssl());
        
        if (is_wp_error($user)) {
            wp_send_json_error(array(
                'message' => 'Email atau password salah.'
            ));
        }
        
        wp_send_json_success(array(
            'message' => 'Login berhasil!',
            'redirect' => home_url('/app')
        ));
    }
    
    /**
     * AJAX register handler
     */
    public function ajax_register() {
        check_ajax_referer('tryouthub_nonce', 'nonce');
        
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $name = sanitize_text_field($_POST['name']);
        
        if (empty($email) || empty($password) || empty($name)) {
            wp_send_json_error(array(
                'message' => 'Semua field harus diisi.'
            ));
        }
        
        if (!is_email($email)) {
            wp_send_json_error(array(
                'message' => 'Format email tidak valid.'
            ));
        }
        
        if (strlen($password) < 6) {
            wp_send_json_error(array(
                'message' => 'Password minimal 6 karakter.'
            ));
        }
        
        if (email_exists($email)) {
            wp_send_json_error(array(
                'message' => 'Email sudah terdaftar.'
            ));
        }
        
        // Create user
        $username = sanitize_user(current(explode('@', $email)));
        
        // Make username unique
        $base_username = $username;
        $counter = 1;
        while (username_exists($username)) {
            $username = $base_username . $counter;
            $counter++;
        }
        
        $user_id = wp_create_user($username, $password, $email);
        
        if (is_wp_error($user_id)) {
            wp_send_json_error(array(
                'message' => 'Gagal membuat akun. Silakan coba lagi.'
            ));
        }
        
        // Update user data
        wp_update_user(array(
            'ID' => $user_id,
            'display_name' => $name,
            'first_name' => $name,
        ));
        
        // Set role - ONLY tryouthub_student, no other capabilities
        $user = new WP_User($user_id);
        $user->set_role('tryouthub_student');
        
        // Remove any default WordPress capabilities
        $user->remove_cap('read'); // This prevents dashboard access
        $user->add_cap('read_tryouthub'); // Custom capability for frontend only
        
        // Initialize points
        update_user_meta($user_id, 'tryouthub_points', 0);
        
        // Auto login
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);
        
        wp_send_json_success(array(
            'message' => 'Pendaftaran berhasil!',
            'redirect' => home_url('/app')
        ));
    }
    
    /**
     * AJAX logout handler
     */
    public function ajax_logout() {
        check_ajax_referer('tryouthub_nonce', 'nonce');
        
        wp_logout();
        
        wp_send_json_success(array(
            'message' => 'Logout berhasil.',
            'redirect' => home_url()
        ));
    }
    
    /**
     * AJAX change password handler
     */
    public function ajax_change_password() {
        check_ajax_referer('tryouthub_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(array(
                'message' => 'Anda harus login terlebih dahulu.'
            ));
        }
        
        $user_id = get_current_user_id();
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
            wp_send_json_error(array(
                'message' => 'Semua field harus diisi.'
            ));
        }
        
        if ($new_password !== $confirm_password) {
            wp_send_json_error(array(
                'message' => 'Password baru tidak cocok.'
            ));
        }
        
        if (strlen($new_password) < 6) {
            wp_send_json_error(array(
                'message' => 'Password baru minimal 6 karakter.'
            ));
        }
        
        // Verify old password
        $user = get_userdata($user_id);
        if (!wp_check_password($old_password, $user->user_pass, $user_id)) {
            wp_send_json_error(array(
                'message' => 'Password lama tidak benar.'
            ));
        }
        
        // Update password
        wp_set_password($new_password, $user_id);
        
        wp_send_json_success(array(
            'message' => 'Password berhasil diubah.'
        ));
    }
    
    /**
     * Redirect to /app after login
     */
    public function redirect_after_login($redirect_to, $request, $user) {
        if (isset($user->roles) && is_array($user->roles)) {
            if (in_array('tryouthub_student', $user->roles)) {
                return home_url('/app');
            }
            if (in_array('administrator', $user->roles) || current_user_can('manage_tryouthub')) {
                return admin_url();
            }
        }
        return $redirect_to;
    }
    
    /**
     * Check if user can access pack
     */
    public static function can_access_pack($pack_id, $user_id = 0) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        if (!$user_id) {
            return false;
        }
        
        $pack = TryOutHub_Database::get_pack($pack_id);
        
        if (!$pack) {
            return false;
        }
        
        // Free packs are accessible to all
        if ($pack->is_free) {
            return true;
        }
        
        // Check if user has purchased
        global $wpdb;
        $transactions_table = TryOutHub_Database::get_table('transactions');
        
        $transaction = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$transactions_table} 
            WHERE user_id = %d AND pack_id = %d AND status = 'completed'",
            $user_id,
            $pack_id
        ));
        
        return $transaction ? true : false;
    }
}