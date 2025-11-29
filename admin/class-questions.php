<?php
/**
 * Questions admin handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class TryOutHub_Questions_Admin {
    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_post_tryouthub_save_question', array($this, 'save_question'));
        add_action('admin_post_tryouthub_delete_question', array($this, 'delete_question'));
        add_action('wp_ajax_tryouthub_import_csv', array($this, 'import_csv'));
    }
    
    /**
     * Render questions page
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
            case 'import':
                $this->render_import_form();
                break;
            default:
                $this->render_list();
                break;
        }
    }
    
    /**
     * Render questions list
     */
    private function render_list() {
        $page = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
        $category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';
        
        $args = array(
            'page' => $page,
            'per_page' => 20,
        );
        
        if ($category) {
            $args['category'] = $category;
        }
        
        $questions = TryOutHub_Database::get_questions($args);
        
        include TRYOUTHUB_PLUGIN_DIR . 'admin/views/questions-list.php';
    }
    
    /**
     * Render edit form
     */
    private function render_edit_form($id = 0) {
        $question = null;
        
        if ($id > 0) {
            $question = TryOutHub_Database::get_question($id);
        }
        
        include TRYOUTHUB_PLUGIN_DIR . 'admin/views/question-edit.php';
    }
    
    /**
     * Render import form
     */
    private function render_import_form() {
        include TRYOUTHUB_PLUGIN_DIR . 'admin/views/questions-import.php';
    }
    
    /**
     * Save question
     */
    public function save_question() {
        check_admin_referer('tryouthub_save_question');
        
        if (!current_user_can('manage_tryouthub') && !current_user_can('administrator')) {
            wp_die('Unauthorized');
        }
        
        $id = isset($_POST['question_id']) ? intval($_POST['question_id']) : 0;
        
        $data = array(
            'title' => sanitize_text_field($_POST['title']),
            'content' => wp_kses_post($_POST['content']),
            'option_a' => wp_kses_post($_POST['option_a']),
            'option_b' => wp_kses_post($_POST['option_b']),
            'option_c' => wp_kses_post($_POST['option_c']),
            'option_d' => wp_kses_post($_POST['option_d']),
            'option_e' => wp_kses_post($_POST['option_e']),
            'correct_option' => strtoupper(sanitize_text_field($_POST['correct_option'])),
            'explanation' => wp_kses_post($_POST['explanation']),
            'category' => sanitize_text_field($_POST['category']),
            'difficulty' => sanitize_text_field($_POST['difficulty']),
            'status' => sanitize_text_field($_POST['status']),
        );
        
        if ($id == 0) {
            $data['created_by'] = get_current_user_id();
        }
        
        $saved_id = TryOutHub_Database::save_question($data, $id);
        
        wp_redirect(admin_url('admin.php?page=tryouthub-questions&message=saved'));
        exit;
    }
    
    /**
     * Delete question
     */
    public function delete_question() {
        check_admin_referer('tryouthub_delete_question_' . $_GET['id']);
        
        if (!current_user_can('manage_tryouthub') && !current_user_can('administrator')) {
            wp_die('Unauthorized');
        }
        
        $id = intval($_GET['id']);
        TryOutHub_Database::delete_question($id);
        
        wp_redirect(admin_url('admin.php?page=tryouthub-questions&message=deleted'));
        exit;
    }
    
    /**
     * Import from CSV
     */
    public function import_csv() {
        check_ajax_referer('tryouthub_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_tryouthub') && !current_user_can('administrator')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }
        
        if (!isset($_FILES['csv_file'])) {
            wp_send_json_error(array('message' => 'No file uploaded'));
        }
        
        $file = $_FILES['csv_file'];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            wp_send_json_error(array('message' => 'Upload error'));
        }
        
        $importer = new TryOutHub_CSV_Importer();
        $result = $importer->import_questions($file['tmp_name']);
        
        if ($result['success']) {
            wp_send_json_success(array(
                'message' => sprintf('Berhasil import %d soal', $result['imported']),
                'imported' => $result['imported'],
            ));
        } else {
            wp_send_json_error(array('message' => $result['message']));
        }
    }
}