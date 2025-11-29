<?php
/**
 * Profile template
 */

if (!defined('ABSPATH')) {
    exit;
}

$current_user = wp_get_current_user();
$user_stats = TryOutHub_Database::get_user_stats(get_current_user_id());
$user_points = TryOutHub_Database::get_user_points(get_current_user_id());
$user_rank = TryOutHub_Database::get_user_rank(get_current_user_id());
$recent_attempts = TryOutHub_Database::get_user_attempts(get_current_user_id(), 10);
?>

<style>
    .tryouthub-profile-page { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
    .tryouthub-profile-header { 
        display: flex; 
        align-items: center; 
        gap: 2rem; 
        padding: 2rem; 
        background: white; 
        border-radius: 1rem; 
        margin-bottom: 2rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .tryouthub-profile-avatar { 
        width: 120px; 
        height: 120px; 
        border-radius: 50%; 
        background: #f87171; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        color: white; 
        font-weight: 700; 
        font-size: 3rem;
        flex-shrink: 0;
    }
    .tryouthub-profile-info h1 { 
        font-size: 2rem; 
        font-weight: 700; 
        margin: 0 0 0.25rem 0; 
        color: #1a1a1a;
    }
    .tryouthub-profile-username { 
        color: #666; 
        font-size: 1rem; 
        margin-bottom: 1rem;
    }
    .tryouthub-profile-badges { 
        display: flex; 
        gap: 1rem; 
        margin-top: 1rem;
    }
    .tryouthub-profile-badge { 
        display: flex; 
        align-items: center; 
        gap: 0.5rem; 
        padding: 0.5rem 1rem; 
        border-radius: 2rem; 
        font-weight: 600; 
        font-size: 0.95rem;
    }
    .badge-points { background: #fef3c7; color: #92400e; }
    .badge-rank { background: #dbeafe; color: #1e40af; }
    .tryouthub-stats-section { 
        margin-bottom: 2rem;
    }
    .tryouthub-stats-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
        gap: 1rem;
    }
    .tryouthub-stat-card { 
        padding: 2rem; 
        border-radius: 1rem; 
        text-align: center;
    }
    .tryouthub-stat-card h2 { 
        font-size: 3rem; 
        font-weight: 700; 
        margin: 0 0 0.5rem 0;
    }
    .tryouthub-stat-card p { 
        color: #666; 
        margin: 0; 
        font-size: 0.95rem;
    }
    .stat-yellow { background: #fef3c7; }
    .stat-blue { background: #dbeafe; }
    .stat-gray { background: #f3f4f6; }
    .stat-green { background: #d1fae5; }
    .stat-pink { background: #fce7f3; }
    .tryouthub-section-title { 
        font-size: 1.5rem; 
        font-weight: 700; 
        margin-bottom: 1.5rem; 
        color: #1a1a1a;
    }
    .tryouthub-history-list { 
        display: flex; 
        flex-direction: column; 
        gap: 1rem;
    }
    .tryouthub-history-item { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        padding: 1.5rem; 
        background: #f9fafb; 
        border-radius: 0.75rem;
        transition: all 0.2s;
    }
    .tryouthub-history-item:hover { 
        background: #f3f4f6;
    }
    .tryouthub-history-info h3 { 
        font-size: 1.1rem; 
        font-weight: 600; 
        margin: 0 0 0.5rem 0; 
        color: #1a1a1a;
    }
    .tryouthub-history-meta { 
        display: flex; 
        gap: 1rem; 
        font-size: 0.9rem; 
        color: #666;
    }
    .tryouthub-history-score { 
        font-size: 2rem; 
        font-weight: 700; 
        color: #16a34a;
    }
    .tryouthub-settings-section { 
        background: white; 
        padding: 2rem; 
        border-radius: 1rem; 
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .tryouthub-settings-item { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        padding: 1.5rem; 
        border-bottom: 1px solid #e5e7eb;
        cursor: pointer;
        transition: all 0.2s;
    }
    .tryouthub-settings-item:last-child { 
        border-bottom: none;
    }
    .tryouthub-settings-item:hover { 
        background: #f9fafb;
    }
    .tryouthub-settings-icon { 
        width: 40px; 
        height: 40px; 
        background: #f3f4f6; 
        border-radius: 0.5rem; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        margin-right: 1rem;
    }
    .tryouthub-settings-left { 
        display: flex; 
        align-items: center;
    }
    .tryouthub-settings-title { 
        font-weight: 600; 
        color: #1a1a1a; 
        margin-bottom: 0.25rem;
    }
    .tryouthub-settings-desc { 
        font-size: 0.9rem; 
        color: #666;
    }
</style>

<div class="tryouthub-profile-page">
    <!-- Profile Header -->
    <div class="tryouthub-profile-header">
        <div class="tryouthub-profile-avatar">
            <?php echo strtoupper(substr($current_user->display_name, 0, 1)); ?>
        </div>
        <div class="tryouthub-profile-info">
            <h1><?php echo esc_html($current_user->display_name); ?></h1>
            <div class="tryouthub-profile-username">@<?php echo esc_html($current_user->user_login); ?></div>
            <div class="tryouthub-profile-badges">
                <div class="tryouthub-profile-badge badge-points">
                    ⭐ <?php echo number_format($user_points); ?> Poin
                </div>
                <div class="tryouthub-profile-badge badge-rank">
                    📊 Peringkat <?php echo number_format($user_rank); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="tryouthub-stats-section">
        <h2 class="tryouthub-section-title">Hasil</h2>
        <div class="tryouthub-stats-grid">
            <div class="tryouthub-stat-card stat-yellow">
                <h2><?php echo number_format($user_points); ?></h2>
                <p>Poin</p>
            </div>
            <div class="tryouthub-stat-card stat-blue">
                <h2><?php echo $user_stats ? $user_stats->total_attempts : 0; ?></h2>
                <p>Soal Terjawab</p>
            </div>
            <div class="tryouthub-stat-card stat-gray">
                <h2><?php echo number_format($user_rank); ?>/<?php echo number_format(21163); ?></h2>
                <p>Peringkat</p>
            </div>
            <div class="tryouthub-stat-card stat-green">
                <h2><?php echo $user_stats ? $user_stats->total_correct : 0; ?></h2>
                <p>Jawaban Benar</p>
            </div>
            <div class="tryouthub-stat-card stat-pink">
                <h2><?php echo $user_stats ? $user_stats->total_wrong : 0; ?></h2>
                <p>Jawaban Salah</p>
            </div>
        </div>
    </div>

    <!-- Settings -->
    <div class="tryouthub-settings-section">
        <h2 class="tryouthub-section-title">Pengaturan</h2>
        
        <div class="tryouthub-settings-item" id="change-account-btn">
            <div class="tryouthub-settings-left">
                <div class="tryouthub-settings-icon">👤</div>
                <div>
                    <div class="tryouthub-settings-title">Ubah Akun</div>
                    <div class="tryouthub-settings-desc">Ubah informasi akun Anda</div>
                </div>
            </div>
            <span>→</span>
        </div>

        <div class="tryouthub-settings-item" id="change-password-btn">
            <div class="tryouthub-settings-left">
                <div class="tryouthub-settings-icon">🔒</div>
                <div>
                    <div class="tryouthub-settings-title">Ubah Password</div>
                    <div class="tryouthub-settings-desc">Ganti password akun Anda</div>
                </div>
            </div>
            <span>→</span>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div id="change-password-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; padding: 2rem; border-radius: 1rem; max-width: 480px; width: 90%;">
        <h2 style="margin: 0 0 1.5rem 0; font-size: 1.5rem; font-weight: 700;">Ubah Password</h2>
        
        <form id="tryouthub-change-password-form">
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Password Lama</label>
                <input type="password" name="old_password" required style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Password Baru</label>
                <input type="password" name="new_password" required style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem;">
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Konfirmasi Password Baru</label>
                <input type="password" name="confirm_password" required style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem;">
            </div>
            <div style="display: flex; gap: 1rem;">
                <button type="button" id="cancel-change-password" style="flex: 1; padding: 0.875rem; background: #f3f4f6; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="flex: 1; padding: 0.875rem; background: #16a34a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer;">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Show change password modal
    $('#change-password-btn').on('click', function() {
        $('#change-password-modal').css('display', 'flex');
    });

    // Hide modal
    $('#cancel-change-password').on('click', function() {
        $('#change-password-modal').hide();
    });

    // Submit change password
    $('#tryouthub-change-password-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: tryouthubData.ajaxUrl,
            method: 'POST',
            data: {
                action: 'tryouthub_change_password',
                nonce: tryouthubData.nonce,
                old_password: $('[name="old_password"]').val(),
                new_password: $('[name="new_password"]').val(),
                confirm_password: $('[name="confirm_password"]').val(),
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    $('#change-password-modal').hide();
                    $('form')[0].reset();
                } else {
                    alert(response.data.message);
                }
            }
        });
    });
});
</script>