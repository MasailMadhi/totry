<?php
/**
 * Exam management class
 */

if (!defined('ABSPATH')) {
    exit;
}

class TryOutHub_Exam {
    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // AJAX handlers for fallback
        add_action('wp_ajax_tryouthub_autosave_answer', array($this, 'ajax_autosave'));
        add_action('wp_ajax_tryouthub_start', array($this, 'ajax_start'));
        add_action('wp_ajax_tryouthub_finish', array($this, 'ajax_finish'));
    }
    
    /**
     * AJAX autosave answer
     */
    public function ajax_autosave() {
        check_ajax_referer('tryouthub_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }
        
        global $wpdb;
        
        $attempt_id = intval($_POST['attempt_id']);
        $question_id = intval($_POST['question_id']);
        $selected_option = sanitize_text_field($_POST['selected_option']);
        
        // Verify attempt ownership
        $attempts_table = TryOutHub_Database::get_table('attempts');
        $attempt = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$attempts_table} WHERE id = %d AND user_id = %d",
            $attempt_id,
            get_current_user_id()
        ));
        
        if (!$attempt) {
            wp_send_json_error(array('message' => 'Invalid attempt'));
        }
        
        // Save answer
        $answers_table = TryOutHub_Database::get_table('answers');
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM {$answers_table} WHERE attempt_id = %d AND question_id = %d",
            $attempt_id,
            $question_id
        ));
        
        if ($existing) {
            $wpdb->update(
                $answers_table,
                array(
                    'selected_option' => $selected_option,
                    'answered_at' => current_time('mysql'),
                ),
                array('id' => $existing->id)
            );
        } else {
            $wpdb->insert($answers_table, array(
                'attempt_id' => $attempt_id,
                'question_id' => $question_id,
                'selected_option' => $selected_option,
            ));
        }
        
        wp_send_json_success(array('message' => 'Answer saved'));
    }
    
    /**
     * AJAX start exam
     */
    public function ajax_start() {
        check_ajax_referer('tryouthub_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }
        
        $pack_id = intval($_POST['pack_id']);
        
        // Use REST API logic
        $api = TryOutHub_REST_API::get_instance();
        $request = new WP_REST_Request('POST', '/tryouthub/v1/start');
        $request->set_param('pack_id', $pack_id);
        
        $response = $api->start_attempt($request);
        
        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response->get_error_message()));
        }
        
        wp_send_json_success($response->data);
    }
    
    /**
     * AJAX finish exam
     */
    public function ajax_finish() {
        check_ajax_referer('tryouthub_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }
        
        $attempt_id = intval($_POST['attempt_id']);
        
        // Use REST API logic
        $api = TryOutHub_REST_API::get_instance();
        $request = new WP_REST_Request('POST', '/tryouthub/v1/finish');
        $request->set_param('attempt_id', $attempt_id);
        
        $response = $api->finish_attempt($request);
        
        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response->get_error_message()));
        }
        
        wp_send_json_success($response->data);
    }
}