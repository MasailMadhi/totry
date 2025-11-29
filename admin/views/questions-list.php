<?php
/**
 * Questions list view
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1>
        Manajemen Soal
        <a href="<?php echo admin_url('admin.php?page=tryouthub-questions&action=add'); ?>" class="page-title-action">
            Tambah Soal
        </a>
        <a href="<?php echo admin_url('admin.php?page=tryouthub-questions&action=import'); ?>" class="page-title-action">
            Import CSV
        </a>
    </h1>
    
    <?php if (isset($_GET['message']) && $_GET['message'] == 'saved'): ?>
        <div class="notice notice-success is-dismissible">
            <p>Soal berhasil disimpan.</p>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['message']) && $_GET['message'] == 'deleted'): ?>
        <div class="notice notice-success is-dismissible">
            <p>Soal berhasil dihapus.</p>
        </div>
    <?php endif; ?>
    
    <div style="background: white; padding: 1rem; margin: 1rem 0; border-radius: 0.5rem;">
        <form method="get">
            <input type="hidden" name="page" value="tryouthub-questions">
            <label>Kategori: </label>
            <select name="category" onchange="this.form.submit()">
                <option value="">Semua</option>
                <option value="PK" <?php selected($category, 'PK'); ?>>Pengetahuan Kuantitatif (PK)</option>
                <option value="PM" <?php selected($category, 'PM'); ?>>Penalaran Matematika (PM)</option>
                <option value="PU" <?php selected($category, 'PU'); ?>>Penalaran Umum (PU)</option>
                <option value="PPU" <?php selected($category, 'PPU'); ?>>Pengetahuan & Pemahaman Umum (PPU)</option>
                <option value="PBM" <?php selected($category, 'PBM'); ?>>Pemahaman Bacaan & Menulis (PBM)</option>
                <option value="LIT_BahasaID" <?php selected($category, 'LIT_BahasaID'); ?>>Literasi Bahasa Indonesia</option>
            </select>
        </form>
    </div>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th style="width: 50px;">ID</th>
                <th>Judul</th>
                <th style="width: 120px;">Kategori</th>
                <th style="width: 100px;">Difficulty</th>
                <th style="width: 80px;">Jawaban</th>
                <th style="width: 100px;">Status</th>
                <th style="width: 150px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($questions)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem;">
                        Belum ada soal. <a href="<?php echo admin_url('admin.php?page=tryouthub-questions&action=add'); ?>">Tambah soal pertama</a>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($questions as $question): ?>
                    <tr>
                        <td><?php echo $question->id; ?></td>
                        <td>
                            <strong><?php echo esc_html($question->title); ?></strong>
                        </td>
                        <td><?php echo esc_html($question->category); ?></td>
                        <td><?php echo esc_html($question->difficulty); ?></td>
                        <td><strong><?php echo esc_html($question->correct_option); ?></strong></td>
                        <td>
                            <span style="padding: 0.25rem 0.75rem; border-radius: 1rem; background: <?php echo $question->status == 'publish' ? '#d1fae5' : '#f3f4f6'; ?>; color: <?php echo $question->status == 'publish' ? '#16a34a' : '#666'; ?>;">
                                <?php echo $question->status == 'publish' ? 'Published' : 'Draft'; ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=tryouthub-questions&action=edit&id=' . $question->id); ?>" class="button button-small">
                                Edit
                            </a>
                            <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=tryouthub_delete_question&id=' . $question->id), 'tryouthub_delete_question_' . $question->id); ?>" class="button button-small" onclick="return confirm('Yakin ingin menghapus soal ini?');">
                                Hapus
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>