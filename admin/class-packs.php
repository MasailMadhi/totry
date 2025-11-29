<?php
/**
 * Packs admin handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class TryOutHub_Packs_Admin {
    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_post_tryouthub_save_pack', array($this, 'save_pack'));
        add_action('admin_post_tryouthub_delete_pack', array($this, 'delete_pack'));
    }
    
    /**
     * Render packs page
     */
    public function render_page() {
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';
        
        switch ($action) {
            case 'add':
                $this->render_edit_form();
                break;
            case 'edit':
                $this->render_edit_form(intval($_GET['id']));
                break;
            default:
                $this->render_list();
                break;
        }
    }
    
    /**
     * Render packs list
     */
    private function render_list() {
        $page = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
        
        $packs = TryOutHub_Database::get_packs(array(
            'page' => $page,
            'per_page' => 20,
            'status' => '',
        ));
        
        include TRYOUTHUB_PLUGIN_DIR . 'admin/views/packs-list.php';
    }
    
    /**
     * Render edit form
     */
    private function render_edit_form($id = 0) {
        $pack = null;
        $pack_questions = array();
        
        if ($id > 0) {
            $pack = TryOutHub_Database::get_pack($id);
            $pack_questions = TryOutHub_Database::get_pack_questions($id);
        }
        
        // Get all questions for selection
        $all_questions = TryOutHub_Database::get_questions(array(
            'per_page' => 999,
            'status' => 'publish',
        ));
        
        include TRYOUTHUB_PLUGIN_DIR . 'admin/views/pack-edit.php';
    }
    
    /**
     * Save pack
     */
    public function save_pack() {
        check_admin_referer('tryouthub_save_pack');
        
        if (!current_user_can('manage_tryouthub') && !current_user_can('administrator')) {
            wp_die('Unauthorized');
        }
        
        $id = isset($_POST['pack_id']) ? intval($_POST['pack_id']) : 0;
        
        $data = array(
            'title' => sanitize_text_field($_POST['title']),
            'description' => sanitize_textarea_field($_POST['description']),
            'duration_minutes' => intval($_POST['duration_minutes']),
            'is_full' => isset($_POST['is_full']) ? 1 : 0,
            'price' => floatval($_POST['price']),
            'is_free' => isset($_POST['is_free']) ? 1 : 0,
            'category_tag' => sanitize_text_field($_POST['category_tag']),
            'status' => sanitize_text_field($_POST['status']),
        );
        
        if ($id == 0) {
            $data['created_by'] = get_current_user_id();
        }
        
        $saved_id = TryOutHub_Database::save_pack($data, $id);
        
        // Assign questions
        if (isset($_POST['questions']) && is_array($_POST['questions'])) {
            $question_ids = array_map('intval', $_POST['questions']);
            TryOutHub_Database::assign_pack_questions($saved_id, $question_ids);
        }
        
        wp_redirect(admin_url('admin.php?page=tryouthub-packs&message=saved'));
        exit;
    }
    
    /**
     * Delete pack
     */
    public function delete_pack() {
        check_admin_referer('tryouthub_delete_pack_' . $_GET['id']);
        
        if (!current_user_can('manage_tryouthub') && !current_user_can('administrator')) {
            wp_die('Unauthorized');
        }
        
        global $wpdb;
        $id = intval($_GET['id']);
        
        // Delete pack
        $wpdb->delete(TryOutHub_Database::get_table('packs'), array('id' => $id));
        
        // Delete pack questions
        $wpdb->delete(TryOutHub_Database::get_table('pack_questions'), array('pack_id' => $id));
        
        wp_redirect(admin_url('admin.php?page=tryouthub-packs&message=deleted'));
        exit;
    }
}