<?php
/**
 * Settings view
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1>Pengaturan TryOutHub</h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('tryouthub_settings'); ?>
        <input type="hidden" name="tryouthub_save_settings" value="1">
        
        <table class="form-table">
            <tr>
                <th><label for="primary_color">Warna Utama</label></th>
                <td>
                    <input type="color" name="primary_color" id="primary_color" value="<?php echo get_option('tryouthub_primary_color', '#0070F9'); ?>">
                    <p class="description">Warna tema utama aplikasi</p>
                </td>
            </tr>
            
            <tr>
                <th><label for="points_per_correct">Poin per Jawaban Benar</label></th>
                <td>
                    <input type="number" name="points_per_correct" id="points_per_correct" value="<?php echo get_option('tryouthub_points_per_correct', 5); ?>" min="0">
                </td>
            </tr>
            
            <tr>
                <th><label for="points_per_wrong">Poin per Jawaban Salah</label></th>
                <td>
                    <input type="number" name="points_per_wrong" id="points_per_wrong" value="<?php echo get_option('tryouthub_points_per_wrong', -1); ?>" max="0">
                    <p class="description">Gunakan nilai negatif untuk mengurangi poin</p>
                </td>
            </tr>
            
            <tr>
                <th><label for="enable_ranking">Sistem Ranking</label></th>
                <td>
                    <label>
                        <input type="checkbox" name="enable_ranking" id="enable_ranking" value="1" <?php checked(get_option('tryouthub_enable_ranking', 1), 1); ?>>
                        Aktifkan sistem ranking dan leaderboard
                    </label>
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <button type="submit" class="button button-primary button-large">Simpan Pengaturan</button>
        </p>
    </form>
</div>