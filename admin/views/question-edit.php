<?php
/**
 * Question edit view
 */

if (!defined('ABSPATH')) {
    exit;
}

$is_edit = $question ? true : false;
?>

<div class="wrap">
    <h1><?php echo $is_edit ? 'Edit Soal' : 'Tambah Soal Baru'; ?></h1>
    
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <?php wp_nonce_field('tryouthub_save_question'); ?>
        <input type="hidden" name="action" value="tryouthub_save_question">
        <?php if ($is_edit): ?>
            <input type="hidden" name="question_id" value="<?php echo $question->id; ?>">
        <?php endif; ?>
        
        <table class="form-table">
            <tr>
                <th><label for="title">Judul Soal *</label></th>
                <td>
                    <input type="text" name="title" id="title" class="regular-text" required value="<?php echo $is_edit ? esc_attr($question->title) : ''; ?>">
                    <p class="description">Judul singkat untuk identifikasi soal</p>
                </td>
            </tr>
            
            <tr>
                <th><label for="content">Konten Soal *</label></th>
                <td>
                    <?php 
                    $content = $is_edit ? $question->content : '';
                    wp_editor($content, 'content', array(
                        'textarea_name' => 'content',
                        'textarea_rows' => 10,
                        'media_buttons' => true,
                        'teeny' => false,
                    )); 
                    ?>
                    <p class="description">Isi soal dengan format HTML. Anda bisa upload gambar via Media Library.</p>
                </td>
            </tr>
            
            <tr>
                <th><label for="option_a">Opsi A *</label></th>
                <td>
                    <?php 
                    wp_editor($is_edit ? $question->option_a : '', 'option_a', array(
                        'textarea_name' => 'option_a',
                        'textarea_rows' => 3,
                        'media_buttons' => false,
                        'teeny' => true,
                    )); 
                    ?>
                </td>
            </tr>
            
            <tr>
                <th><label for="option_b">Opsi B *</label></th>
                <td>
                    <?php 
                    wp_editor($is_edit ? $question->option_b : '', 'option_b', array(
                        'textarea_name' => 'option_b',
                        'textarea_rows' => 3,
                        'media_buttons' => false,
                        'teeny' => true,
                    )); 
                    ?>
                </td>
            </tr>
            
            <tr>
                <th><label for="option_c">Opsi C *</label></th>
                <td>
                    <?php 
                    wp_editor($is_edit ? $question->option_c : '', 'option_c', array(
                        'textarea_name' => 'option_c',
                        'textarea_rows' => 3,
                        'media_buttons' => false,
                        'teeny' => true,
                    )); 
                    ?>
                </td>
            </tr>
            
            <tr>
                <th><label for="option_d">Opsi D *</label></th>
                <td>
                    <?php 
                    wp_editor($is_edit ? $question->option_d : '', 'option_d', array(
                        'textarea_name' => 'option_d',
                        'textarea_rows' => 3,
                        'media_buttons' => false,
                        'teeny' => true,
                    )); 
                    ?>
                </td>
            </tr>
            
            <tr>
                <th><label for="option_e">Opsi E *</label></th>
                <td>
                    <?php 
                    wp_editor($is_edit ? $question->option_e : '', 'option_e', array(
                        'textarea_name' => 'option_e',
                        'textarea_rows' => 3,
                        'media_buttons' => false,
                        'teeny' => true,
                    )); 
                    ?>
                </td>
            </tr>
            
            <tr>
                <th><label for="correct_option">Jawaban Benar *</label></th>
                <td>
                    <select name="correct_option" id="correct_option" required>
                        <option value="">-- Pilih --</option>
                        <option value="A" <?php echo ($is_edit && $question->correct_option == 'A') ? 'selected' : ''; ?>>A</option>
                        <option value="B" <?php echo ($is_edit && $question->correct_option == 'B') ? 'selected' : ''; ?>>B</option>
                        <option value="C" <?php echo ($is_edit && $question->correct_option == 'C') ? 'selected' : ''; ?>>C</option>
                        <option value="D" <?php echo ($is_edit && $question->correct_option == 'D') ? 'selected' : ''; ?>>D</option>
                        <option value="E" <?php echo ($is_edit && $question->correct_option == 'E') ? 'selected' : ''; ?>>E</option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th><label for="explanation">Pembahasan</label></th>
                <td>
                    <?php 
                    wp_editor($is_edit ? $question->explanation : '', 'explanation', array(
                        'textarea_name' => 'explanation',
                        'textarea_rows' => 5,
                        'media_buttons' => true,
                        'teeny' => false,
                    )); 
                    ?>
                    <p class="description">Penjelasan jawaban (opsional)</p>
                </td>
            </tr>
            
            <tr>
                <th><label for="category">Kategori *</label></th>
                <td>
                    <select name="category" id="category" required>
                        <option value="">-- Pilih --</option>
                        <option value="PK" <?php echo ($is_edit && $question->category == 'PK') ? 'selected' : ''; ?>>Pengetahuan Kuantitatif (PK)</option>
                        <option value="PM" <?php echo ($is_edit && $question->category == 'PM') ? 'selected' : ''; ?>>Penalaran Matematika (PM)</option>
                        <option value="PU" <?php echo ($is_edit && $question->category == 'PU') ? 'selected' : ''; ?>>Penalaran Umum (PU)</option>
                        <option value="PPU" <?php echo ($is_edit && $question->category == 'PPU') ? 'selected' : ''; ?>>Pengetahuan & Pemahaman Umum (PPU)</option>
                        <option value="PBM" <?php echo ($is_edit && $question->category == 'PBM') ? 'selected' : ''; ?>>Pemahaman Bacaan & Menulis (PBM)</option>
                        <option value="LIT_BahasaID" <?php echo ($is_edit && $question->category == 'LIT_BahasaID') ? 'selected' : ''; ?>>Literasi Bahasa Indonesia</option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th><label for="difficulty">Tingkat Kesulitan</label></th>
                <td>
                    <select name="difficulty" id="difficulty">
                        <option value="easy" <?php echo ($is_edit && $question->difficulty == 'easy') ? 'selected' : ''; ?>>Mudah</option>
                        <option value="medium" <?php echo ($is_edit && $question->difficulty == 'medium') ? 'selected' : ''; ?>>Sedang</option>
                        <option value="hard" <?php echo ($is_edit && $question->difficulty == 'hard') ? 'selected' : ''; ?>>Sulit</option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th><label for="status">Status</label></th>
                <td>
                    <select name="status" id="status">
                        <option value="draft" <?php echo ($is_edit && $question->status == 'draft') ? 'selected' : ''; ?>>Draft</option>
                        <option value="publish" <?php echo ($is_edit && $question->status == 'publish') ? 'selected' : ''; ?>>Publish</option>
                    </select>
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <button type="submit" class="button button-primary button-large">Simpan Soal</button>
            <a href="<?php echo admin_url('admin.php?page=tryouthub-questions'); ?>" class="button button-large">Batal</a>
        </p>
    </form>
</div>