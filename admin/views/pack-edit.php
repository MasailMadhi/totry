<?php
/**
 * Pack edit view
 */

if (!defined('ABSPATH')) {
    exit;
}

$is_edit = $pack ? true : false;
$selected_question_ids = array();
if ($is_edit && !empty($pack_questions)) {
    $selected_question_ids = array_column($pack_questions, 'id');
}
?>

<div class="wrap">
    <h1><?php echo $is_edit ? 'Edit Paket Tryout' : 'Buat Paket Tryout Baru'; ?></h1>
    
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <?php wp_nonce_field('tryouthub_save_pack'); ?>
        <input type="hidden" name="action" value="tryouthub_save_pack">
        <?php if ($is_edit): ?>
            <input type="hidden" name="pack_id" value="<?php echo $pack->id; ?>">
        <?php endif; ?>
        
        <table class="form-table">
            <tr>
                <th><label for="title">Judul Paket *</label></th>
                <td>
                    <input type="text" name="title" id="title" class="regular-text" required value="<?php echo $is_edit ? esc_attr($pack->title) : ''; ?>">
                </td>
            </tr>
            
            <tr>
                <th><label for="description">Deskripsi</label></th>
                <td>
                    <textarea name="description" id="description" rows="3" class="large-text"><?php echo $is_edit ? esc_textarea($pack->description) : ''; ?></textarea>
                </td>
            </tr>
            
            <tr>
                <th><label for="duration_minutes">Durasi (Menit) *</label></th>
                <td>
                    <input type="number" name="duration_minutes" id="duration_minutes" required value="<?php echo $is_edit ? $pack->duration_minutes : '60'; ?>" min="1">
                </td>
            </tr>
            
            <tr>
                <th><label for="is_full">Tipe Paket</label></th>
                <td>
                    <label>
                        <input type="checkbox" name="is_full" id="is_full" value="1" <?php echo ($is_edit && $pack->is_full) ? 'checked' : ''; ?>>
                        Full UTBK (gabungan semua kategori)
                    </label>
                </td>
            </tr>
            
            <tr>
                <th><label for="category_tag">Kategori</label></th>
                <td>
                    <select name="category_tag" id="category_tag">
                        <option value="">-- Pilih (untuk paket per kategori) --</option>
                        <option value="PK" <?php echo ($is_edit && $pack->category_tag == 'PK') ? 'selected' : ''; ?>>Pengetahuan Kuantitatif (PK)</option>
                        <option value="PM" <?php echo ($is_edit && $pack->category_tag == 'PM') ? 'selected' : ''; ?>>Penalaran Matematika (PM)</option>
                        <option value="PU" <?php echo ($is_edit && $pack->category_tag == 'PU') ? 'selected' : ''; ?>>Penalaran Umum (PU)</option>
                        <option value="PPU" <?php echo ($is_edit && $pack->category_tag == 'PPU') ? 'selected' : ''; ?>>Pengetahuan & Pemahaman Umum (PPU)</option>
                        <option value="PBM" <?php echo ($is_edit && $pack->category_tag == 'PBM') ? 'selected' : ''; ?>>Pemahaman Bacaan & Menulis (PBM)</option>
                        <option value="LIT_BahasaID" <?php echo ($is_edit && $pack->category_tag == 'LIT_BahasaID') ? 'selected' : ''; ?>>Literasi Bahasa Indonesia</option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th><label for="is_free">Akses</label></th>
                <td>
                    <label>
                        <input type="checkbox" name="is_free" id="is_free" value="1" <?php echo ($is_edit && $pack->is_free) ? 'checked' : ''; ?>>
                        Gratis (dapat diakses semua user)
                    </label>
                </td>
            </tr>
            
            <tr>
                <th><label for="price">Harga (Rp)</label></th>
                <td>
                    <input type="number" name="price" id="price" value="<?php echo $is_edit ? $pack->price : '0'; ?>" min="0" step="1000">
                    <p class="description">Kosongkan atau isi 0 jika gratis</p>
                </td>
            </tr>
            
            <tr>
                <th><label for="status">Status</label></th>
                <td>
                    <select name="status" id="status">
                        <option value="draft" <?php echo ($is_edit && $pack->status == 'draft') ? 'selected' : ''; ?>>Draft</option>
                        <option value="publish" <?php echo ($is_edit && $pack->status == 'publish') ? 'selected' : ''; ?>>Publish</option>
                    </select>
                </td>
            </tr>
        </table>
        
        <h2>Pilih Soal</h2>
        <div style="background: white; padding: 1rem; border: 1px solid #ddd; border-radius: 0.5rem; max-height: 400px; overflow-y: auto;">
            <?php if (empty($all_questions)): ?>
                <p>Belum ada soal yang published. <a href="<?php echo admin_url('admin.php?page=tryouthub-questions&action=add'); ?>">Tambah soal</a></p>
            <?php else: ?>
                <table class="wp-list-table widefat" style="margin: 0;">
                    <thead>
                        <tr>
                            <th style="width: 50px;">
                                <input type="checkbox" id="select-all-questions">
                            </th>
                            <th>Soal</th>
                            <th style="width: 100px;">Kategori</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_questions as $q): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="questions[]" value="<?php echo $q->id; ?>" <?php echo in_array($q->id, $selected_question_ids) ? 'checked' : ''; ?>>
                                </td>
                                <td><?php echo esc_html($q->title); ?></td>
                                <td><?php echo esc_html($q->category); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <p class="submit">
            <button type="submit" class="button button-primary button-large">Simpan Paket</button>
            <a href="<?php echo admin_url('admin.php?page=tryouthub-packs'); ?>" class="button button-large">Batal</a>
        </p>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Select all questions
    $('#select-all-questions').on('change', function() {
        $('input[name="questions[]"]').prop('checked', $(this).prop('checked'));
    });
});
</script>