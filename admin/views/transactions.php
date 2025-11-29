<?php
/**
 * Transactions view
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$transactions_table = TryOutHub_Database::get_table('transactions');
$packs_table = TryOutHub_Database::get_table('packs');

$transactions = $wpdb->get_results("
    SELECT t.*, p.title as pack_title, u.display_name as user_name 
    FROM {$transactions_table} t 
    LEFT JOIN {$packs_table} p ON t.pack_id = p.id 
    LEFT JOIN {$wpdb->users} u ON t.user_id = u.ID 
    ORDER BY t.created_at DESC 
    LIMIT 50
");
?>

<div class="wrap">
    <h1>Transaksi</h1>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th style="width: 50px;">ID</th>
                <th>User</th>
                <th>Paket</th>
                <th style="width: 120px;">Jumlah</th>
                <th style="width: 100px;">Status</th>
                <th style="width: 120px;">Metode</th>
                <th style="width: 150px;">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($transactions)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem;">
                        Belum ada transaksi.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?php echo $transaction->id; ?></td>
                        <td><?php echo esc_html($transaction->user_name); ?></td>
                        <td><?php echo esc_html($transaction->pack_title); ?></td>
                        <td>Rp <?php echo number_format($transaction->amount, 0, ',', '.'); ?></td>
                        <td>
                            <span style="padding: 0.25rem 0.75rem; border-radius: 1rem; background: <?php 
                                echo $transaction->status == 'completed' ? '#d1fae5' : ($transaction->status == 'pending' ? '#fef3c7' : '#f3f4f6'); 
                            ?>; color: <?php 
                                echo $transaction->status == 'completed' ? '#16a34a' : ($transaction->status == 'pending' ? '#92400e' : '#666'); 
                            ?>;">
                                <?php echo ucfirst($transaction->status); ?>
                            </span>
                        </td>
                        <td><?php echo esc_html($transaction->payment_method ?: '-'); ?></td>
                        <td><?php echo date('d M Y H:i', strtotime($transaction->created_at)); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <p class="description" style="margin-top: 1rem;">
        <strong>Note:</strong> Fitur transaksi ini adalah placeholder. Integrasi payment gateway seperti Midtrans dapat ditambahkan sesuai kebutuhan.
    </p>
</div>