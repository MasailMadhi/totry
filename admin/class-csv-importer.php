<?php
/**
 * CSV Import handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class TryOutHub_CSV_Importer {
    
    /**
     * Import questions from CSV
     * 
     * CSV Format:
     * title, content, option_a, option_b, option_c, option_d, option_e, correct_option, explanation, category, difficulty
     */
    public function import_questions($file_path) {
        if (!file_exists($file_path)) {
            return array(
                'success' => false,
                'message' => 'File tidak ditemukan',
            );
        }
        
        $handle = fopen($file_path, 'r');
        
        if (!$handle) {
            return array(
                'success' => false,
                'message' => 'Tidak dapat membaca file',
            );
        }
        
        $imported = 0;
        $line = 0;
        
        // Skip header
        fgetcsv($handle);
        
        while (($data = fgetcsv($handle)) !== false) {
            $line++;
            
            if (count($data) < 11) {
                continue; // Skip invalid rows
            }
            
            $question_data = array(
                'title' => sanitize_text_field($data[0]),
                'content' => wp_kses_post($data[1]),
                'option_a' => wp_kses_post($data[2]),
                'option_b' => wp_kses_post($data[3]),
                'option_c' => wp_kses_post($data[4]),
                'option_d' => wp_kses_post($data[5]),
                'option_e' => wp_kses_post($data[6]),
                'correct_option' => strtoupper(sanitize_text_field($data[7])),
                'explanation' => wp_kses_post($data[8]),
                'category' => sanitize_text_field($data[9]),
                'difficulty' => sanitize_text_field($data[10]),
                'status' => 'publish',
                'created_by' => get_current_user_id(),
            );
            
            $saved = TryOutHub_Database::save_question($question_data);
            
            if ($saved) {
                $imported++;
            }
        }
        
        fclose($handle);
        
        return array(
            'success' => true,
            'imported' => $imported,
        );
    }
    
    /**
     * Generate CSV template
     */
    public static function generate_template() {
        $headers = array(
            'title',
            'content',
            'option_a',
            'option_b',
            'option_c',
            'option_d',
            'option_e',
            'correct_option',
            'explanation',
            'category',
            'difficulty',
        );
        
        $sample_data = array(
            array(
                'Soal Contoh 1',
                '<p>Konten soal dengan HTML</p>',
                'Opsi A',
                'Opsi B',
                'Opsi C',
                'Opsi D',
                'Opsi E',
                'A',
                '<p>Penjelasan jawaban</p>',
                'PK',
                'medium',
            ),
        );
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="questions_template.csv"');
        
        $output = fopen('php://output', 'w');
        
        fputcsv($output, $headers);
        
        foreach ($sample_data as $row) {
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit;
    }
}