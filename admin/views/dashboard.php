<?php
/**
 * Admin dashboard view
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1>TryOutHub Dashboard</h1>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin: 2rem 0;">
        <div style="background: #dbeafe; padding: 2rem; border-radius: 0.75rem;">
            <h2 style="font-size: 2.5rem; margin: 0 0 0.5rem 0;"><?php echo number_format($questions_count); ?></h2>
            <p style="color: #1e40af; font-weight: 600; margin: 0;">Total Soal</p>
        </div>
        
        <div style="background: #d1fae5; padding: 2rem; border-radius: 0.75rem;">
            <h2 style="font-size: 2.5rem; margin: 0 0 0.5rem 0;"><?php echo number_format($packs_count); ?></h2>
            <p style="color: #16a34a; font-weight: 600; margin: 0;">Total Paket Tryout</p>
        </div>
        
        <div style="background: #fef3c7; padding: 2rem; border-radius: 0.75rem;">
            <h2 style="font-size: 2.5rem; margin: 0 0 0.5rem 0;"><?php echo number_format($attempts_count); ?></h2>
            <p style="color: #92400e; font-weight: 600; margin: 0;">Total Attempts</p>
        </div>
        
        <div style="background: #fce7f3; padding: 2rem; border-radius: 0.75rem;">
            <h2 style="font-size: 2.5rem; margin: 0 0 0.5rem 0;"><?php echo number_format($users_count); ?></h2>
            <p style="color: #9f1239; font-weight: 600; margin: 0;">Total Siswa</p>
        </div>
    </div>
    
    <div style="background: white; padding: 2rem; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h2>Selamat Datang di TryOutHub</h2>
        <p>Kelola soal, paket tryout, dan transaksi dengan mudah melalui menu di sebelah kiri.</p>
        
        <div style="margin-top: 2rem;">
            <h3>Quick Actions</h3>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="<?php echo admin_url('admin.php?page=tryouthub-questions&action=add'); ?>" class="button button-primary">
                    Tambah Soal Baru
                </a>
                <a href="<?php echo admin_url('admin.php?page=tryouthub-packs&action=add'); ?>" class="button button-primary">
                    Buat Paket Tryout
                </a>
                <a href="<?php echo admin_url('admin.php?page=tryouthub-questions&action=import'); ?>" class="button">
                    Import Soal dari CSV
                </a>
            </div>
        </div>
    </div>
</div>