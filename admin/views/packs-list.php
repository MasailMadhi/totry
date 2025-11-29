<?php
/**
 * Packs list view
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1>
        Manajemen Paket Tryout
        <a href="<?php echo admin_url('admin.php?page=tryouthub-packs&action=add'); ?>" class="page-title-action">
            Buat Paket Baru
        </a>
    </h1>
    
    <?php if (isset($_GET['message']) && $_GET['message'] == 'saved'): ?>
        <div class="notice notice-success is-dismissible">
            <p>Paket tryout berhasil disimpan.</p>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['message']) && $_GET['message'] == 'deleted'): ?>
        <div class="notice notice-success is-dismissible">
            <p>Paket tryout berhasil dihapus.</p>
        </div>
    <?php endif; ?>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th style="width: 50px;">ID</th>
                <th>Judul Paket</th>
                <th style="width: 100px;">Kategori</th>
                <th style="width: 80px;">Durasi</th>
                <th style="width: 80px;">Soal</th>
                <th style="width: 100px;">Harga</th>
                <th style="width: 100px;">Status</th>
                <th style="width: 150px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($packs)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 2rem;">
                        Belum ada paket tryout. <a href="<?php echo admin_url('admin.php?page=tryouthub-packs&action=add'); ?>">Buat paket pertama</a>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($packs as $pack): ?>
                    <tr>
                        <td><?php echo $pack->id; ?></td>
                        <td>
                            <strong><?php echo esc_html($pack->title); ?></strong>
                            <?php if ($pack->is_full): ?>
                                <span style="padding: 0.125rem 0.5rem; border-radius: 1rem; background: #fbbf24; color: white; font-size: 0.75rem; margin-left: 0.5rem;">FULL</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html($pack->category_tag ?: '-'); ?></td>
                        <td><?php echo $pack->duration_minutes; ?> mnt</td>
                        <td><?php echo $pack->total_questions; ?></td>
                        <td>
                            <?php if ($pack->is_free): ?>
                                <span style="color: #16a34a; font-weight: 600;">Gratis</span>
                            <?php else: ?>
                                Rp <?php echo number_format($pack->price, 0, ',', '.'); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span style="padding: 0.25rem 0.75rem; border-radius: 1rem; background: <?php echo $pack->status == 'publish' ? '#d1fae5' : '#f3f4f6'; ?>; color: <?php echo $pack->status == 'publish' ? '#16a34a' : '#666'; ?>;">
                                <?php echo $pack->status == 'publish' ? 'Published' : 'Draft'; ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=tryouthub-packs&action=edit&id=' . $pack->id); ?>" class="button button-small">
                                Edit
                            </a>
                            <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=tryouthub_delete_pack&id=' . $pack->id), 'tryouthub_delete_pack_' . $pack->id); ?>" class="button button-small" onclick="return confirm('Yakin ingin menghapus paket ini?');">
                                Hapus
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>