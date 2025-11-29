<?php
/**
 * REST API endpoints
 */

if (!defined('ABSPATH')) {
    exit;
}

class TryOutHub_REST_API {
    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }
    
    /**
     * Register REST API routes
     */
    public function register_routes() {
        $namespace = 'tryouthub/v1';
        
        // Start attempt
        register_rest_route($namespace, '/start', array(
            'methods' => 'POST',
            'callback' => array($this, 'start_attempt'),
            'permission_callback' => array($this, 'check_user_permission'),
        ));
        
        // Save answer
        register_rest_route($namespace, '/answer', array(
            'methods' => 'POST',
            'callback' => array($this, 'save_answer'),
            'permission_callback' => array($this, 'check_user_permission'),
        ));
        
        // Finish attempt
        register_rest_route($namespace, '/finish', array(
            'methods' => 'POST',
            'callback' => array($this, 'finish_attempt'),
            'permission_callback' => array($this, 'check_user_permission'),
        ));
        
        // Get packs
        register_rest_route($namespace, '/packs', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_packs'),
            'permission_callback' => '__return_true',
        ));
        
        // Get pack detail
        register_rest_route($namespace, '/pack/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_pack'),
            'permission_callback' => '__return_true',
        ));
        
        // Get result
        register_rest_route($namespace, '/result/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_result'),
            'permission_callback' => array($this, 'check_user_permission'),
        ));
        
        // Get ranking
        register_rest_route($namespace, '/ranking', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_ranking'),
            'permission_callback' => '__return_true',
        ));
        
        // Get user stats
        register_rest_route($namespace, '/stats', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_user_stats'),
            'permission_callback' => array($this, 'check_user_permission'),
        ));
    }
    
    /**
     * Permission callback
     */
    public function check_user_permission() {
        return is_user_logged_in();
    }
    
    /**
     * Start new attempt
     */
    public function start_attempt($request) {
        global $wpdb;
        
        $pack_id = intval($request->get_param('pack_id'));
        $user_id = get_current_user_id();
        
        if (!$pack_id) {
            return new WP_Error('invalid_pack', 'ID pack tidak valid', array('status' => 400));
        }
        
        // Check access
        if (!TryOutHub_Auth::can_access_pack($pack_id, $user_id)) {
            return new WP_Error('no_access', 'Anda tidak memiliki akses ke tryout ini', array('status' => 403));
        }
        
        // Get pack with questions
        $pack = TryOutHub_Database::get_pack($pack_id, true);
        
        if (!$pack) {
            return new WP_Error('pack_not_found', 'Tryout tidak ditemukan', array('status' => 404));
        }
        
        // Check for existing active attempt
        $attempts_table = TryOutHub_Database::get_table('attempts');
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$attempts_table} 
            WHERE user_id = %d AND pack_id = %d AND status = 'in_progress'
            ORDER BY id DESC LIMIT 1",
            $user_id,
            $pack_id
        ));
        
        if ($existing) {
            // Resume existing attempt
            $attempt_id = $existing->id;
            $started_at = $existing->started_at;
        } else {
            // Create new attempt
            $wpdb->insert($attempts_table, array(
                'user_id' => $user_id,
                'pack_id' => $pack_id,
                'status' => 'in_progress',
            ));
            
            $attempt_id = $wpdb->insert_id;
            $started_at = current_time('mysql');
        }
        
        // Calculate end time
        $end_time = strtotime($started_at) + ($pack->duration_minutes * 60);
        
        // Get existing answers
        $answers_table = TryOutHub_Database::get_table('answers');
        $saved_answers = $wpdb->get_results($wpdb->prepare(
            "SELECT question_id, selected_option 
            FROM {$answers_table} 
            WHERE attempt_id = %d",
            $attempt_id
        ), OBJECT_K);
        
        // Prepare questions
        $questions = array();
        foreach ($pack->questions as $q) {
            $questions[] = array(
                'id' => $q->id,
                'title' => $q->title,
                'content' => $q->content,
                'option_a' => $q->option_a,
                'option_b' => $q->option_b,
                'option_c' => $q->option_c,
                'option_d' => $q->option_d,
                'option_e' => $q->option_e,
                'selected' => isset($saved_answers[$q->id]) ? $saved_answers[$q->id]->selected_option : null,
            );
        }
        
        return rest_ensure_response(array(
            'success' => true,
            'data' => array(
                'attempt_id' => $attempt_id,
                'pack_title' => $pack->title,
                'duration_minutes' => $pack->duration_minutes,
                'end_time' => $end_time,
                'questions' => $questions,
            ),
        ));
    }
    
    /**
     * Save answer
     */
    public function save_answer($request) {
        global $wpdb;
        
        $attempt_id = intval($request->get_param('attempt_id'));
        $question_id = intval($request->get_param('question_id'));
        $selected_option = sanitize_text_field($request->get_param('selected_option'));
        
        if (!$attempt_id || !$question_id) {
            return new WP_Error('invalid_params', 'Parameter tidak valid', array('status' => 400));
        }
        
        // Verify attempt ownership
        $attempts_table = TryOutHub_Database::get_table('attempts');
        $attempt = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$attempts_table} WHERE id = %d AND user_id = %d",
            $attempt_id,
            get_current_user_id()
        ));
        
        if (!$attempt) {
            return new WP_Error('invalid_attempt', 'Attempt tidak valid', array('status' => 403));
        }
        
        // Check if already answered
        $answers_table = TryOutHub_Database::get_table('answers');
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM {$answers_table} WHERE attempt_id = %d AND question_id = %d",
            $attempt_id,
            $question_id
        ));
        
        if ($existing) {
            // Update existing answer
            $wpdb->update(
                $answers_table,
                array(
                    'selected_option' => $selected_option,
                    'answered_at' => current_time('mysql'),
                ),
                array(
                    'id' => $existing->id,
                )
            );
        } else {
            // Insert new answer
            $wpdb->insert($answers_table, array(
                'attempt_id' => $attempt_id,
                'question_id' => $question_id,
                'selected_option' => $selected_option,
            ));
        }
        
        return rest_ensure_response(array(
            'success' => true,
            'message' => 'Jawaban berhasil disimpan',
        ));
    }
    
    /**
     * Finish attempt and calculate score
     */
    public function finish_attempt($request) {
        global $wpdb;
        
        $attempt_id = intval($request->get_param('attempt_id'));
        
        if (!$attempt_id) {
            return new WP_Error('invalid_attempt', 'Attempt tidak valid', array('status' => 400));
        }
        
        // Verify attempt ownership
        $attempts_table = TryOutHub_Database::get_table('attempts');
        $attempt = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$attempts_table} WHERE id = %d AND user_id = %d AND status = 'in_progress'",
            $attempt_id,
            get_current_user_id()
        ));
        
        if (!$attempt) {
            return new WP_Error('invalid_attempt', 'Attempt tidak ditemukan', array('status' => 404));
        }
        
        // Calculate score
        $scoring = new TryOutHub_Scoring();
        $result = $scoring->calculate_score($attempt_id);
        
        // Update attempt
        $wpdb->update(
            $attempts_table,
            array(
                'finished_at' => current_time('mysql'),
                'score' => $result['score'],
                'correct_count' => $result['correct'],
                'wrong_count' => $result['wrong'],
                'unanswered_count' => $result['unanswered'],
                'status' => 'completed',
            ),
            array('id' => $attempt_id)
        );
        
        // Update user points
        $points_to_add = $result['correct'] * get_option('tryouthub_points_per_correct', 5);
        $points_to_add += $result['wrong'] * get_option('tryouthub_points_per_wrong', -1);
        
        TryOutHub_Database::update_user_points(get_current_user_id(), $points_to_add);
        
        return rest_ensure_response(array(
            'success' => true,
            'data' => array(
                'attempt_id' => $attempt_id,
                'score' => $result['score'],
                'correct' => $result['correct'],
                'wrong' => $result['wrong'],
                'unanswered' => $result['unanswered'],
                'points_earned' => $points_to_add,
            ),
        ));
    }
    
    /**
     * Get packs list
     */
    public function get_packs($request) {
        $category = $request->get_param('category');
        $is_free = $request->get_param('is_free');
        
        $args = array(
            'status' => 'publish',
        );
        
        if ($category) {
            $args['category_tag'] = $category;
        }
        
        if ($is_free !== null) {
            $args['is_free'] = $is_free;
        }
        
        $packs = TryOutHub_Database::get_packs($args);
        
        return rest_ensure_response(array(
            'success' => true,
            'data' => $packs,
        ));
    }
    
    /**
     * Get pack detail
     */
    public function get_pack($request) {
        $pack_id = intval($request['id']);
        
        $pack = TryOutHub_Database::get_pack($pack_id);
        
        if (!$pack) {
            return new WP_Error('pack_not_found', 'Tryout tidak ditemukan', array('status' => 404));
        }
        
        return rest_ensure_response(array(
            'success' => true,
            'data' => $pack,
        ));
    }
    
    /**
     * Get result detail
     */
    public function get_result($request) {
        global $wpdb;
        
        $attempt_id = intval($request['id']);
        $user_id = get_current_user_id();
        
        // Get attempt
        $attempts_table = TryOutHub_Database::get_table('attempts');
        $packs_table = TryOutHub_Database::get_table('packs');
        
        $attempt = $wpdb->get_row($wpdb->prepare(
            "SELECT a.*, p.title as pack_title 
            FROM {$attempts_table} a 
            LEFT JOIN {$packs_table} p ON a.pack_id = p.id 
            WHERE a.id = %d AND a.user_id = %d",
            $attempt_id,
            $user_id
        ));
        
        if (!$attempt) {
            return new WP_Error('not_found', 'Hasil tidak ditemukan', array('status' => 404));
        }
        
        // Get answers with questions
        $answers_table = TryOutHub_Database::get_table('answers');
        $questions_table = TryOutHub_Database::get_table('questions');
        
        $answers = $wpdb->get_results($wpdb->prepare(
            "SELECT a.*, q.title as question_title, q.correct_option, q.explanation 
            FROM {$answers_table} a 
            LEFT JOIN {$questions_table} q ON a.question_id = q.id 
            WHERE a.attempt_id = %d",
            $attempt_id
        ));
        
        return rest_ensure_response(array(
            'success' => true,
            'data' => array(
                'attempt' => $attempt,
                'answers' => $answers,
            ),
        ));
    }
    
    /**
     * Get ranking
     */
    public function get_ranking($request) {
        $limit = intval($request->get_param('limit')) ?: 100;
        
        $leaderboard = TryOutHub_Database::get_leaderboard($limit);
        
        return rest_ensure_response(array(
            'success' => true,
            'data' => $leaderboard,
        ));
    }
    
    /**
     * Get user stats
     */
    public function get_user_stats($request) {
        $user_id = get_current_user_id();
        
        $stats = TryOutHub_Database::get_user_stats($user_id);
        $points = TryOutHub_Database::get_user_points($user_id);
        $rank = TryOutHub_Database::get_user_rank($user_id);
        $attempts = TryOutHub_Database::get_user_attempts($user_id, 10);
        
        return rest_ensure_response(array(
            'success' => true,
            'data' => array(
                'stats' => $stats,
                'points' => $points,
                'rank' => $rank,
                'recent_attempts' => $attempts,
            ),
        ));
    }
}