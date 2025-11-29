<?php
/**
 * Database operations helper
 */

if (!defined('ABSPATH')) {
    exit;
}

class TryOutHub_Database {
    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Get table name with prefix
     */
    public static function get_table($table_name) {
        global $wpdb;
        return $wpdb->prefix . 'tryouthub_' . $table_name;
    }
    
    /**
     * Get questions with pagination
     */
    public static function get_questions($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'per_page' => 20,
            'page' => 1,
            'category' => '',
            'status' => 'publish',
            'orderby' => 'created_at',
            'order' => 'DESC',
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $table = self::get_table('questions');
        $offset = ($args['page'] - 1) * $args['per_page'];
        
        $where = array();
        $where_values = array();
        
        if (!empty($args['category'])) {
            $where[] = 'category = %s';
            $where_values[] = $args['category'];
        }
        
        if (!empty($args['status'])) {
            $where[] = 'status = %s';
            $where_values[] = $args['status'];
        }
        
        $where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $order_sql = sprintf('ORDER BY %s %s', sanitize_sql_orderby($args['orderby']), $args['order']);
        
        $limit_sql = $wpdb->prepare('LIMIT %d, %d', $offset, $args['per_page']);
        
        if (!empty($where_values)) {
            $query = $wpdb->prepare(
                "SELECT * FROM {$table} {$where_sql} {$order_sql} {$limit_sql}",
                $where_values
            );
        } else {
            $query = "SELECT * FROM {$table} {$where_sql} {$order_sql} {$limit_sql}";
        }
        
        return $wpdb->get_results($query);
    }
    
    /**
     * Get single question
     */
    public static function get_question($id) {
        global $wpdb;
        $table = self::get_table('questions');
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE id = %d",
            $id
        ));
    }
    
    /**
     * Insert or update question
     */
    public static function save_question($data, $id = 0) {
        global $wpdb;
        $table = self::get_table('questions');
        
        if ($id > 0) {
            $wpdb->update($table, $data, array('id' => $id));
            return $id;
        } else {
            $wpdb->insert($table, $data);
            return $wpdb->insert_id;
        }
    }
    
    /**
     * Delete question
     */
    public static function delete_question($id) {
        global $wpdb;
        $table = self::get_table('questions');
        
        return $wpdb->delete($table, array('id' => $id));
    }
    
    /**
     * Get packs
     */
    public static function get_packs($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'per_page' => 20,
            'page' => 1,
            'category_tag' => '',
            'status' => 'publish',
            'is_free' => '',
            'orderby' => 'created_at',
            'order' => 'DESC',
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $table = self::get_table('packs');
        $offset = ($args['page'] - 1) * $args['per_page'];
        
        $where = array();
        $where_values = array();
        
        if (!empty($args['category_tag'])) {
            $where[] = 'category_tag = %s';
            $where_values[] = $args['category_tag'];
        }
        
        if ($args['status'] !== '') {
            $where[] = 'status = %s';
            $where_values[] = $args['status'];
        }
        
        if ($args['is_free'] !== '') {
            $where[] = 'is_free = %d';
            $where_values[] = $args['is_free'];
        }
        
        $where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $order_sql = sprintf('ORDER BY %s %s', sanitize_sql_orderby($args['orderby']), $args['order']);
        
        $limit_sql = $wpdb->prepare('LIMIT %d, %d', $offset, $args['per_page']);
        
        if (!empty($where_values)) {
            $query = $wpdb->prepare(
                "SELECT * FROM {$table} {$where_sql} {$order_sql} {$limit_sql}",
                $where_values
            );
        } else {
            $query = "SELECT * FROM {$table} {$where_sql} {$order_sql} {$limit_sql}";
        }
        
        return $wpdb->get_results($query);
    }
    
    /**
     * Get single pack with questions
     */
    public static function get_pack($id, $include_questions = false) {
        global $wpdb;
        $pack_table = self::get_table('packs');
        
        $pack = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$pack_table} WHERE id = %d",
            $id
        ));
        
        if ($pack && $include_questions) {
            $pack->questions = self::get_pack_questions($id);
        }
        
        return $pack;
    }
    
    /**
     * Get pack questions
     */
    public static function get_pack_questions($pack_id) {
        global $wpdb;
        $pq_table = self::get_table('pack_questions');
        $q_table = self::get_table('questions');
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT q.*, pq.order_index 
            FROM {$q_table} q 
            INNER JOIN {$pq_table} pq ON q.id = pq.question_id 
            WHERE pq.pack_id = %d 
            ORDER BY pq.order_index ASC",
            $pack_id
        ));
    }
    
    /**
     * Save pack
     */
    public static function save_pack($data, $id = 0) {
        global $wpdb;
        $table = self::get_table('packs');
        
        if ($id > 0) {
            $wpdb->update($table, $data, array('id' => $id));
            return $id;
        } else {
            $wpdb->insert($table, $data);
            return $wpdb->insert_id;
        }
    }
    
    /**
     * Assign questions to pack
     */
    public static function assign_pack_questions($pack_id, $question_ids) {
        global $wpdb;
        $table = self::get_table('pack_questions');
        
        // Delete existing
        $wpdb->delete($table, array('pack_id' => $pack_id));
        
        // Insert new
        foreach ($question_ids as $index => $question_id) {
            $wpdb->insert($table, array(
                'pack_id' => $pack_id,
                'question_id' => $question_id,
                'order_index' => $index + 1,
            ));
        }
        
        // Update total questions
        $count = count($question_ids);
        $wpdb->update(
            self::get_table('packs'),
            array('total_questions' => $count),
            array('id' => $pack_id)
        );
        
        return $count;
    }
    
    /**
     * Get user attempts
     */
    public static function get_user_attempts($user_id, $limit = 10) {
        global $wpdb;
        $attempts_table = self::get_table('attempts');
        $packs_table = self::get_table('packs');
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT a.*, p.title as pack_title 
            FROM {$attempts_table} a 
            LEFT JOIN {$packs_table} p ON a.pack_id = p.id 
            WHERE a.user_id = %d 
            ORDER BY a.started_at DESC 
            LIMIT %d",
            $user_id,
            $limit
        ));
    }
    
    /**
     * Get user statistics
     */
    public static function get_user_stats($user_id) {
        global $wpdb;
        $table = self::get_table('attempts');
        
        $stats = $wpdb->get_row($wpdb->prepare(
            "SELECT 
                COUNT(*) as total_attempts,
                SUM(correct_count) as total_correct,
                SUM(wrong_count) as total_wrong,
                AVG(score) as avg_score
            FROM {$table} 
            WHERE user_id = %d AND status = 'completed'",
            $user_id
        ));
        
        return $stats;
    }
    
    /**
     * Get user points
     */
    public static function get_user_points($user_id) {
        return (int) get_user_meta($user_id, 'tryouthub_points', true);
    }
    
    /**
     * Update user points
     */
    public static function update_user_points($user_id, $points) {
        $current = self::get_user_points($user_id);
        $new_total = $current + $points;
        update_user_meta($user_id, 'tryouthub_points', $new_total);
        return $new_total;
    }
    
    /**
     * Get leaderboard
     */
    public static function get_leaderboard($limit = 100) {
        global $wpdb;
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT u.ID, u.display_name, um.meta_value as points 
            FROM {$wpdb->users} u 
            INNER JOIN {$wpdb->usermeta} um ON u.ID = um.user_id 
            WHERE um.meta_key = 'tryouthub_points' 
            ORDER BY CAST(um.meta_value AS UNSIGNED) DESC 
            LIMIT %d",
            $limit
        ));
        
        return $results;
    }
    
    /**
     * Get user rank
     */
    public static function get_user_rank($user_id) {
        global $wpdb;
        
        $user_points = self::get_user_points($user_id);
        
        $rank = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) + 1 
            FROM {$wpdb->usermeta} 
            WHERE meta_key = 'tryouthub_points' 
            AND CAST(meta_value AS UNSIGNED) > %d",
            $user_points
        ));
        
        return $rank ? $rank : 1;
    }
}