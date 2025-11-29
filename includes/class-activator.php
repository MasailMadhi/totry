<?php
/**
 * Plugin activation and deactivation handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class TryOutHub_Activator {
    
    /**
     * Activate plugin
     */
    public static function activate() {
        // Create database tables
        self::create_tables();
        
        // Create custom user roles
        self::create_roles();
        
        // Create default page
        self::create_default_page();
        
        // Set default options
        self::set_default_options();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Deactivate plugin
     */
    public static function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Create database tables
     */
    private static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        $table_prefix = $wpdb->prefix . 'tryouthub_';
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // Questions table
        $sql_questions = "CREATE TABLE IF NOT EXISTS {$table_prefix}questions (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            title text NOT NULL,
            content longtext NOT NULL,
            option_a text NOT NULL,
            option_b text NOT NULL,
            option_c text NOT NULL,
            option_d text NOT NULL,
            option_e text NOT NULL,
            correct_option varchar(1) NOT NULL,
            explanation longtext,
            category varchar(50) NOT NULL,
            difficulty varchar(20) DEFAULT 'medium',
            status varchar(20) DEFAULT 'draft',
            created_by bigint(20) UNSIGNED NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY category (category),
            KEY status (status),
            KEY created_by (created_by)
        ) $charset_collate;";
        
        // Packs table
        $sql_packs = "CREATE TABLE IF NOT EXISTS {$table_prefix}packs (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            description text,
            duration_minutes int(11) NOT NULL,
            is_full tinyint(1) DEFAULT 0,
            price decimal(10,2) DEFAULT 0.00,
            is_free tinyint(1) DEFAULT 1,
            category_tag varchar(50),
            status varchar(20) DEFAULT 'draft',
            total_questions int(11) DEFAULT 0,
            created_by bigint(20) UNSIGNED NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY category_tag (category_tag),
            KEY status (status),
            KEY is_free (is_free)
        ) $charset_collate;";
        
        // Pack Questions (junction table)
        $sql_pack_questions = "CREATE TABLE IF NOT EXISTS {$table_prefix}pack_questions (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            pack_id bigint(20) UNSIGNED NOT NULL,
            question_id bigint(20) UNSIGNED NOT NULL,
            order_index int(11) DEFAULT 0,
            PRIMARY KEY (id),
            KEY pack_id (pack_id),
            KEY question_id (question_id),
            UNIQUE KEY pack_question (pack_id, question_id)
        ) $charset_collate;";
        
        // Attempts table
        $sql_attempts = "CREATE TABLE IF NOT EXISTS {$table_prefix}attempts (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id bigint(20) UNSIGNED NOT NULL,
            pack_id bigint(20) UNSIGNED NOT NULL,
            started_at datetime DEFAULT CURRENT_TIMESTAMP,
            finished_at datetime,
            score decimal(10,2) DEFAULT 0.00,
            correct_count int(11) DEFAULT 0,
            wrong_count int(11) DEFAULT 0,
            unanswered_count int(11) DEFAULT 0,
            status varchar(20) DEFAULT 'in_progress',
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY pack_id (pack_id),
            KEY status (status)
        ) $charset_collate;";
        
        // Answers table
        $sql_answers = "CREATE TABLE IF NOT EXISTS {$table_prefix}answers (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            attempt_id bigint(20) UNSIGNED NOT NULL,
            question_id bigint(20) UNSIGNED NOT NULL,
            selected_option varchar(1),
            is_correct tinyint(1) DEFAULT 0,
            answered_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY attempt_id (attempt_id),
            KEY question_id (question_id),
            UNIQUE KEY attempt_question (attempt_id, question_id)
        ) $charset_collate;";
        
        // Transactions table
        $sql_transactions = "CREATE TABLE IF NOT EXISTS {$table_prefix}transactions (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id bigint(20) UNSIGNED NOT NULL,
            pack_id bigint(20) UNSIGNED NOT NULL,
            amount decimal(10,2) NOT NULL,
            status varchar(20) DEFAULT 'pending',
            payment_method varchar(50),
            payment_reference varchar(255),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY pack_id (pack_id),
            KEY status (status)
        ) $charset_collate;";
        
        // Execute queries
        dbDelta($sql_questions);
        dbDelta($sql_packs);
        dbDelta($sql_pack_questions);
        dbDelta($sql_attempts);
        dbDelta($sql_answers);
        dbDelta($sql_transactions);
        
        // Insert sample data
        self::insert_sample_data();
    }
    
    /**
     * Create custom user roles
     */
    // Di method create_roles(), update menjadi:
private static function create_roles() {
    // Student role - NO dashboard access
    add_role('tryouthub_student', 'TryOutHub Student', array(
        'read_tryouthub' => true, // Custom capability for frontend only
    ));
    
    // Admin role (add capability to administrator)
    $admin_role = get_role('administrator');
    if ($admin_role) {
        $admin_role->add_cap('manage_tryouthub');
    }
}
    
    /**
     * Create default page
     */
    private static function create_default_page() {
        // Check if page already exists
        $page = get_page_by_path('app');
        
        if (!$page) {
            $page_id = wp_insert_post(array(
                'post_title' => 'TryOutHub Dashboard',
                'post_name' => 'app',
                'post_content' => '[tryouthub_app]',
                'post_status' => 'publish',
                'post_type' => 'page',
            ));
            
            update_option('tryouthub_dashboard_page_id', $page_id);
        }
    }
    
    /**
     * Set default options
     */
    private static function set_default_options() {
        $defaults = array(
            'tryouthub_primary_color' => '#0070F9',
            'tryouthub_points_per_correct' => 5,
            'tryouthub_points_per_wrong' => -1,
            'tryouthub_enable_ranking' => 1,
        );
        
        foreach ($defaults as $key => $value) {
            if (!get_option($key)) {
                update_option($key, $value);
            }
        }
    }
    
    /**
     * Insert sample data
     */
    private static function insert_sample_data() {
        global $wpdb;
        $table_prefix = $wpdb->prefix . 'tryouthub_';
        
        // Check if already have data
        $count = $wpdb->get_var("SELECT COUNT(*) FROM {$table_prefix}questions");
        if ($count > 0) {
            return; // Already has data
        }
        
        // Get admin user ID
        $admin_id = 1;
        
        // Sample questions for each category
        $sample_questions = array(
            // Pengetahuan Kuantitatif (PK)
            array(
                'title' => 'Soal Matematika Dasar 1',
                'content' => '<p>Jika 3x + 5 = 20, maka nilai x adalah...</p>',
                'option_a' => '3',
                'option_b' => '5',
                'option_c' => '7',
                'option_d' => '10',
                'option_e' => '15',
                'correct_option' => 'B',
                'explanation' => '<p>3x + 5 = 20<br>3x = 15<br>x = 5</p>',
                'category' => 'PK',
                'difficulty' => 'easy',
                'status' => 'publish',
            ),
            array(
                'title' => 'Soal Aritmatika',
                'content' => '<p>Rata-rata dari 5 bilangan adalah 20. Jika ditambah satu bilangan lagi, rata-ratanya menjadi 22. Bilangan yang ditambahkan adalah...</p>',
                'option_a' => '28',
                'option_b' => '30',
                'option_c' => '32',
                'option_d' => '34',
                'option_e' => '36',
                'correct_option' => 'C',
                'explanation' => '<p>Total 5 bilangan = 5 × 20 = 100<br>Total 6 bilangan = 6 × 22 = 132<br>Bilangan ditambahkan = 132 - 100 = 32</p>',
                'category' => 'PK',
                'difficulty' => 'medium',
                'status' => 'publish',
            ),
            array(
                'title' => 'Soal Geometri',
                'content' => '<p>Luas lingkaran dengan jari-jari 7 cm adalah... (π = 22/7)</p>',
                'option_a' => '154 cm²',
                'option_b' => '144 cm²',
                'option_c' => '134 cm²',
                'option_d' => '124 cm²',
                'option_e' => '114 cm²',
                'correct_option' => 'A',
                'explanation' => '<p>Luas = πr² = 22/7 × 7² = 22/7 × 49 = 154 cm²</p>',
                'category' => 'PK',
                'difficulty' => 'easy',
                'status' => 'publish',
            ),
            array(
                'title' => 'Soal Perbandingan',
                'content' => '<p>Perbandingan uang A, B, dan C adalah 2:3:5. Jika jumlah uang mereka Rp 200.000, maka uang B adalah...</p>',
                'option_a' => 'Rp 40.000',
                'option_b' => 'Rp 50.000',
                'option_c' => 'Rp 60.000',
                'option_d' => 'Rp 80.000',
                'option_e' => 'Rp 100.000',
                'correct_option' => 'C',
                'explanation' => '<p>Total bagian = 2 + 3 + 5 = 10<br>Uang B = 3/10 × 200.000 = Rp 60.000</p>',
                'category' => 'PK',
                'difficulty' => 'medium',
                'status' => 'publish',
            ),
            array(
                'title' => 'Soal Eksponen',
                'content' => '<p>Nilai dari 2⁵ × 2³ adalah...</p>',
                'option_a' => '128',
                'option_b' => '256',
                'option_c' => '512',
                'option_d' => '1024',
                'option_e' => '2048',
                'correct_option' => 'B',
                'explanation' => '<p>2⁵ × 2³ = 2⁽⁵⁺³⁾ = 2⁸ = 256</p>',
                'category' => 'PK',
                'difficulty' => 'easy',
                'status' => 'publish',
            ),
            
            // Penalaran Umum (PU)
            array(
                'title' => 'Soal Logika Silogisme',
                'content' => '<p>Semua mahasiswa adalah pelajar.<br>Sebagian pelajar adalah atlet.<br>Kesimpulan yang tepat adalah...</p>',
                'option_a' => 'Semua mahasiswa adalah atlet',
                'option_b' => 'Sebagian mahasiswa adalah atlet',
                'option_c' => 'Tidak ada kesimpulan yang pasti',
                'option_d' => 'Semua atlet adalah mahasiswa',
                'option_e' => 'Tidak ada mahasiswa yang atlet',
                'correct_option' => 'C',
                'explanation' => '<p>Dari premis yang ada tidak dapat ditarik kesimpulan pasti tentang hubungan mahasiswa dan atlet.</p>',
                'category' => 'PU',
                'difficulty' => 'medium',
                'status' => 'publish',
            ),
            array(
                'title' => 'Soal Analogi',
                'content' => '<p>Buku : Perpustakaan = Mobil : ...</p>',
                'option_a' => 'Jalan',
                'option_b' => 'Garasi',
                'option_c' => 'Sopir',
                'option_d' => 'Bensin',
                'option_e' => 'Roda',
                'correct_option' => 'B',
                'explanation' => '<p>Buku disimpan di perpustakaan, mobil disimpan di garasi.</p>',
                'category' => 'PU',
                'difficulty' => 'easy',
                'status' => 'publish',
            ),
            array(
                'title' => 'Soal Pola Bilangan',
                'content' => '<p>2, 6, 12, 20, 30, ... Bilangan selanjutnya adalah...</p>',
                'option_a' => '40',
                'option_b' => '42',
                'option_c' => '44',
                'option_d' => '46',
                'option_e' => '48',
                'correct_option' => 'B',
                'explanation' => '<p>Pola: n(n+1)<br>1×2=2, 2×3=6, 3×4=12, 4×5=20, 5×6=30, 6×7=42</p>',
                'category' => 'PU',
                'difficulty' => 'medium',
                'status' => 'publish',
            ),
            array(
                'title' => 'Soal Deduksi',
                'content' => '<p>Jika hari ini hujan, jalanan akan basah. Jalanan tidak basah. Kesimpulannya...</p>',
                'option_a' => 'Hari ini hujan',
                'option_b' => 'Hari ini tidak hujan',
                'option_c' => 'Mungkin hujan',
                'option_d' => 'Jalanan kering',
                'option_e' => 'Tidak ada kesimpulan',
                'correct_option' => 'B',
                'explanation' => '<p>Modus tollens: Jika P maka Q, tidak Q, maka tidak P.</p>',
                'category' => 'PU',
                'difficulty' => 'medium',
                'status' => 'publish',
            ),
            
            // Pengetahuan dan Pemahaman Umum (PPU)
            array(
                'title' => 'Soal Sejarah Indonesia',
                'content' => '<p>Proklamasi kemerdekaan Indonesia dibacakan pada tanggal...</p>',
                'option_a' => '17 Agustus 1945',
                'option_b' => '17 Agustus 1944',
                'option_c' => '18 Agustus 1945',
                'option_d' => '16 Agustus 1945',
                'option_e' => '19 Agustus 1945',
                'correct_option' => 'A',
                'explanation' => '<p>Proklamasi kemerdekaan Indonesia dibacakan pada 17 Agustus 1945.</p>',
                'category' => 'PPU',
                'difficulty' => 'easy',
                'status' => 'publish',
            ),
            array(
                'title' => 'Soal Geografi',
                'content' => '<p>Gunung tertinggi di Indonesia adalah...</p>',
                'option_a' => 'Gunung Semeru',
                'option_b' => 'Gunung Kerinci',
                'option_c' => 'Gunung Rinjani',
                'option_d' => 'Puncak Jaya',
                'option_e' => 'Gunung Merapi',
                'correct_option' => 'D',
                'explanation' => '<p>Puncak Jaya (Cartenz Pyramid) di Papua adalah gunung tertinggi di Indonesia dengan ketinggian 4.884 mdpl.</p>',
                'category' => 'PPU',
                'difficulty' => 'medium',
                'status' => 'publish',
            ),
            array(
                'title' => 'Soal Sains',
                'content' => '<p>Proses fotosintesis pada tumbuhan menghasilkan...</p>',
                'option_a' => 'Oksigen dan Air',
                'option_b' => 'Oksigen dan Glukosa',
                'option_c' => 'Karbon Dioksida dan Air',
                'option_d' => 'Nitrogen dan Oksigen',
                'option_e' => 'Hidrogen dan Karbon',
                'correct_option' => 'B',
                'explanation' => '<p>Fotosintesis menghasilkan oksigen dan glukosa dari karbon dioksida dan air dengan bantuan cahaya matahari.</p>',
                'category' => 'PPU',
                'difficulty' => 'easy',
                'status' => 'publish',
            ),
            
            // Pemahaman Bacaan dan Menulis (PBM)
            array(
                'title' => 'Soal Pemahaman Bacaan',
                'content' => '<p>Bacalah paragraf berikut:<br><br>"Teknologi artificial intelligence (AI) telah mengubah berbagai aspek kehidupan manusia. Dari asisten virtual hingga kendaraan otonom, AI memberikan kemudahan dan efisiensi. Namun, penggunaan AI juga menimbulkan kekhawatiran terkait privasi dan etika."<br><br>Ide pokok paragraf di atas adalah...</p>',
                'option_a' => 'AI sangat berbahaya',
                'option_b' => 'AI mengubah kehidupan dengan manfaat dan tantangan',
                'option_c' => 'Asisten virtual adalah produk AI',
                'option_d' => 'Privasi terancam oleh AI',
                'option_e' => 'Kendaraan otonom menggunakan AI',
                'correct_option' => 'B',
                'explanation' => '<p>Ide pokok adalah bahwa AI mengubah kehidupan dengan memberikan manfaat namun juga menimbulkan kekhawatiran.</p>',
                'category' => 'PBM',
                'difficulty' => 'medium',
                'status' => 'publish',
            ),
            array(
                'title' => 'Soal PUEBI',
                'content' => '<p>Penulisan yang benar adalah...</p>',
                'option_a' => 'Di mana kamu tinggal?',
                'option_b' => 'Dimana kamu tinggal?',
                'option_c' => 'Di-mana kamu tinggal?',
                'option_d' => 'Di mana-kah kamu tinggal?',
                'option_e' => 'Dimanakah kamu tinggal?',
                'correct_option' => 'A',
                'explanation' => '<p>"Di mana" ditulis terpisah sesuai PUEBI.</p>',
                'category' => 'PBM',
                'difficulty' => 'easy',
                'status' => 'publish',
            ),
            array(
                'title' => 'Soal Ejaan',
                'content' => '<p>Kata yang tepat untuk melengkapi kalimat "Dia ... pergi ke sekolah" adalah...</p>',
                'option_a' => 'tak',
                'option_b' => 'tidak',
                'option_c' => 'nggak',
                'option_d' => 'gak',
                'option_e' => 'kagak',
                'correct_option' => 'B',
                'explanation' => '<p>Dalam konteks formal, gunakan "tidak" bukan "tak" atau bentuk tidak baku.</p>',
                'category' => 'PBM',
                'difficulty' => 'easy',
                'status' => 'publish',
            ),
            
            // Penalaran Matematika (PM)
            array(
                'title' => 'Soal Logika Matematika',
                'content' => '<p>Jika p → q benar dan q salah, maka...</p>',
                'option_a' => 'p benar',
                'option_b' => 'p salah',
                'option_c' => 'p mungkin benar',
                'option_d' => 'Tidak ada kesimpulan',
                'option_e' => 'p dan q salah',
                'correct_option' => 'B',
                'explanation' => '<p>Dalam implikasi, jika konsekuen (q) salah dan implikasi benar, maka anteseden (p) pasti salah.</p>',
                'category' => 'PM',
                'difficulty' => 'medium',
                'status' => 'publish',
            ),
            array(
                'title' => 'Soal Peluang',
                'content' => '<p>Sebuah dadu dilempar sekali. Peluang muncul angka genap adalah...</p>',
                'option_a' => '1/6',
                'option_b' => '1/3',
                'option_c' => '1/2',
                'option_d' => '2/3',
                'option_e' => '5/6',
                'correct_option' => 'C',
                'explanation' => '<p>Angka genap pada dadu: 2, 4, 6 (3 kejadian)<br>Total kemungkinan: 6<br>Peluang = 3/6 = 1/2</p>',
                'category' => 'PM',
                'difficulty' => 'easy',
                'status' => 'publish',
            ),
            array(
                'title' => 'Soal Statistika',
                'content' => '<p>Median dari data: 5, 8, 3, 9, 4, 7, 6 adalah...</p>',
                'option_a' => '5',
                'option_b' => '6',
                'option_c' => '7',
                'option_d' => '8',
                'option_e' => '4',
                'correct_option' => 'B',
                'explanation' => '<p>Urutkan data: 3, 4, 5, 6, 7, 8, 9<br>Median = nilai tengah = 6</p>',
                'category' => 'PM',
                'difficulty' => 'medium',
                'status' => 'publish',
            ),
            
            // Literasi Bahasa Indonesia
            array(
                'title' => 'Soal Sinonim',
                'content' => '<p>Sinonim dari kata "cemerlang" adalah...</p>',
                'option_a' => 'Redup',
                'option_b' => 'Gemilang',
                'option_c' => 'Suram',
                'option_d' => 'Kelam',
                'option_e' => 'Pudar',
                'correct_option' => 'B',
                'explanation' => '<p>"Cemerlang" dan "Gemilang" memiliki makna yang sama yaitu sangat baik atau terang benderang.</p>',
                'category' => 'LIT_BahasaID',
                'difficulty' => 'easy',
                'status' => 'publish',
            ),
            array(
                'title' => 'Soal Antonim',
                'content' => '<p>Antonim dari kata "optimis" adalah...</p>',
                'option_a' => 'Pesimis',
                'option_b' => 'Realistis',
                'option_c' => 'Pragmatis',
                'option_d' => 'Idealis',
                'option_e' => 'Nasionalis',
                'correct_option' => 'A',
                'explanation' => '<p>"Optimis" (berpandangan positif) berlawanan dengan "Pesimis" (berpandangan negatif).</p>',
                'category' => 'LIT_BahasaID',
                'difficulty' => 'easy',
                'status' => 'publish',
            ),
        );
        
        // Insert questions
        foreach ($sample_questions as $question) {
            $question['created_by'] = $admin_id;
            $wpdb->insert($table_prefix . 'questions', $question);
        }
        
        // Create sample packs
        $packs = array(
            array(
                'title' => 'Full UTBK-SNBT 2026',
                'description' => 'Paket lengkap tryout UTBK-SNBT dengan semua kategori soal',
                'duration_minutes' => 180,
                'is_full' => 1,
                'price' => 0,
                'is_free' => 1,
                'category_tag' => 'FULL',
                'status' => 'publish',
                'created_by' => $admin_id,
            ),
            array(
                'title' => 'Tryout PK Fokus Materi 1',
                'description' => 'Fokus pada Pengetahuan Kuantitatif',
                'duration_minutes' => 30,
                'is_full' => 0,
                'price' => 0,
                'is_free' => 1,
                'category_tag' => 'PK',
                'status' => 'publish',
                'created_by' => $admin_id,
            ),
            array(
                'title' => 'Tryout PK Premium',
                'description' => 'Tryout premium Pengetahuan Kuantitatif',
                'duration_minutes' => 45,
                'is_full' => 0,
                'price' => 25000,
                'is_free' => 0,
                'category_tag' => 'PK',
                'status' => 'publish',
                'created_by' => $admin_id,
            ),
            array(
                'title' => 'Tryout PU Dasar',
                'description' => 'Penalaran Umum tingkat dasar',
                'duration_minutes' => 30,
                'is_full' => 0,
                'price' => 0,
                'is_free' => 1,
                'category_tag' => 'PU',
                'status' => 'publish',
                'created_by' => $admin_id,
            ),
            array(
                'title' => 'Tryout PPU Lengkap',
                'description' => 'Pengetahuan dan Pemahaman Umum',
                'duration_minutes' => 25,
                'is_full' => 0,
                'price' => 15000,
                'is_free' => 0,
                'category_tag' => 'PPU',
                'status' => 'publish',
                'created_by' => $admin_id,
            ),
            array(
                'title' => 'Tryout PM Intensif',
                'description' => 'Penalaran Matematika intensif',
                'duration_minutes' => 40,
                'is_full' => 0,
                'price' => 20000,
                'is_free' => 0,
                'category_tag' => 'PM',
                'status' => 'publish',
                'created_by' => $admin_id,
            ),
        );
        
        foreach ($packs as $pack) {
            $wpdb->insert($table_prefix . 'packs', $pack);
            $pack_id = $wpdb->insert_id;
            
            // Assign questions to pack
            if ($pack['is_full']) {
                // Full pack: add all questions
                $all_questions = $wpdb->get_results("SELECT id FROM {$table_prefix}questions ORDER BY id", ARRAY_A);
                foreach ($all_questions as $index => $q) {
                    $wpdb->insert($table_prefix . 'pack_questions', array(
                        'pack_id' => $pack_id,
                        'question_id' => $q['id'],
                        'order_index' => $index + 1,
                    ));
                }
                $wpdb->update($table_prefix . 'packs', array('total_questions' => count($all_questions)), array('id' => $pack_id));
            } else {
                // Category pack: add questions from that category
                $category_questions = $wpdb->get_results($wpdb->prepare(
                    "SELECT id FROM {$table_prefix}questions WHERE category = %s ORDER BY id LIMIT 5",
                    $pack['category_tag']
                ), ARRAY_A);
                
                foreach ($category_questions as $index => $q) {
                    $wpdb->insert($table_prefix . 'pack_questions', array(
                        'pack_id' => $pack_id,
                        'question_id' => $q['id'],
                        'order_index' => $index + 1,
                    ));
                }
                $wpdb->update($table_prefix . 'packs', array('total_questions' => count($category_questions)), array('id' => $pack_id));
            }
        }
    }
}