<?php
/**
 * Scoring calculation class
 */

if (!defined('ABSPATH')) {
    exit;
}

class TryOutHub_Scoring {
    
    /**
     * Calculate score for an attempt
     */
    public function calculate_score($attempt_id) {
        global $wpdb;
        
        $answers_table = TryOutHub_Database::get_table('answers');
        $questions_table = TryOutHub_Database::get_table('questions');
        
        // Get all answers with correct options
        $answers = $wpdb->get_results($wpdb->prepare(
            "SELECT a.*, q.correct_option 
            FROM {$answers_table} a 
            LEFT JOIN {$questions_table} q ON a.question_id = q.id 
            WHERE a.attempt_id = %d",
            $attempt_id
        ));
        
        // Get total questions in this attempt
        $attempts_table = TryOutHub_Database::get_table('attempts');
        $packs_table = TryOutHub_Database::get_table('packs');
        
        $attempt = $wpdb->get_row($wpdb->prepare(
            "SELECT a.*, p.total_questions 
            FROM {$attempts_table} a 
            LEFT JOIN {$packs_table} p ON a.pack_id = p.id 
            WHERE a.id = %d",
            $attempt_id
        ));
        
        $total_questions = $attempt->total_questions;
        
        $correct = 0;
        $wrong = 0;
        $unanswered = 0;
        
        // Process answers
        $answered_questions = array();
        
        foreach ($answers as $answer) {
            $answered_questions[] = $answer->question_id;
            
            if (empty($answer->selected_option)) {
                $unanswered++;
                $is_correct = 0;
            } else {
                if (strtoupper($answer->selected_option) === strtoupper($answer->correct_option)) {
                    $correct++;
                    $is_correct = 1;
                } else {
                    $wrong++;
                    $is_correct = 0;
                }
            }
            
            // Update is_correct flag
            $wpdb->update(
                $answers_table,
                array('is_correct' => $is_correct),
                array('id' => $answer->id)
            );
        }
        
        // Count truly unanswered (no record)
        $unanswered = $total_questions - count($answered_questions);
        
        // Calculate score (simple: correct * 5, wrong * -1)
        $points_per_correct = get_option('tryouthub_points_per_correct', 5);
        $points_per_wrong = get_option('tryouthub_points_per_wrong', -1);
        
        $score = ($correct * $points_per_correct) + ($wrong * $points_per_wrong);
        $score = max(0, $score); // Don't allow negative scores
        
        return array(
            'score' => $score,
            'correct' => $correct,
            'wrong' => $wrong,
            'unanswered' => $unanswered,
            'total' => $total_questions,
        );
    }
    
    /**
     * Get score percentage
     */
    public function get_percentage($correct, $total) {
        if ($total == 0) {
            return 0;
        }
        return round(($correct / $total) * 100, 2);
    }
}