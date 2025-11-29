<?php
/**
 * Exam info template - More Compact Design
 */

if (!defined('ABSPATH')) {
    exit;
}

$pack_id = intval($atts['id']);

if (!$pack_id && isset($_GET['pack_id'])) {
    $pack_id = intval($_GET['pack_id']);
}

$pack = TryOutHub_Database::get_pack($pack_id);

if (!$pack) {
    echo '<div style="padding: 2rem !important; text-align: center !important;">Tryout tidak ditemukan.</div>';
    return;
}

$start_exam_url = add_query_arg(array('action' => 'start_exam', 'pack_id' => $pack_id), home_url('/app'));
$back_url = home_url('/app') . '#tryout';
?>

<style>
    /* Exam Info - Very Compact */
    .tryouthub-exam-info-container {
        max-width: 700px !important;
        margin: 0 auto !important;
        padding: 0 !important;
    }
    
    .tryouthub-exam-info-card {
        background: white !important;
        border-radius: 1rem !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04) !important;
        padding: 1.5rem !important;
    }
    
    .tryouthub-exam-info-header {
        display: flex !important;
        align-items: center !important;
        gap: 0.875rem !important;
        margin-bottom: 1.5rem !important;
    }
    
    .tryouthub-exam-icon {
        font-size: 1.75rem !important;
    }
    
    .tryouthub-exam-info-title {
        font-size: 1.25rem !important;
        font-weight: 700 !important;
        color: #1a1a1a !important;
        margin: 0 !important;
    }
    
    .tryouthub-info-stats {
        display: grid !important;
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 0.75rem !important;
        margin-bottom: 1.5rem !important;
    }
    
    .tryouthub-info-stat {
        background: #f8fafc !important;
        padding: 0.875rem !important;
        border-radius: 0.625rem !important;
        text-align: center !important;
    }
    
    .tryouthub-info-label {
        font-size: 0.8rem !important;
        color: #94a3b8 !important;
        font-weight: 500 !important;
        margin-bottom: 0.25rem !important;
        display: block !important;
    }
    
    .tryouthub-info-value {
        font-size: 1.25rem !important;
        font-weight: 700 !important;
        color: #0070F9 !important;
        display: block !important;
    }
    
    .tryouthub-composition-box {
        background: #f0f9ff !important;
        border-radius: 0.75rem !important;
        padding: 1.25rem !important;
        margin-bottom: 1.5rem !important;
    }
    
    .tryouthub-composition-title {
        font-size: 0.95rem !important;
        font-weight: 700 !important;
        color: #0070F9 !important;
        margin-bottom: 0.875rem !important;
    }
    
    .tryouthub-composition-grid {
        display: grid !important;
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 0.5rem !important;
    }
    
    .tryouthub-composition-item {
        padding: 0.5rem 0.75rem !important;
        background: white !important;
        border-radius: 0.375rem !important;
        color: #334155 !important;
        font-size: 0.8rem !important;
        text-align: center !important;
    }
    
    .tryouthub-exam-info-actions {
        display: flex !important;
        gap: 0.75rem !important;
    }
    
    .tryouthub-btn-back {
        flex: 1 !important;
        padding: 0.875rem !important;
        background: #f1f5f9 !important;
        color: #334155 !important;
        border: none !important;
        border-radius: 0.625rem !important;
        font-size: 0.95rem !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        transition: all 0.2s !important;
        text-decoration: none !important;
        text-align: center !important;
        display: block !important;
    }
    
    .tryouthub-btn-back:hover {
        background: #e2e8f0 !important;
        text-decoration: none !important;
    }
    
    .tryouthub-btn-start-exam {
        flex: 2 !important;
        padding: 0.875rem !important;
        background: linear-gradient(135deg, #0070F9, #0060d9) !important;
        color: white !important;
        border: none !important;
        border-radius: 0.625rem !important;
        font-size: 0.95rem !important;
        font-weight: 700 !important;
        cursor: pointer !important;
        transition: all 0.2s !important;
        text-decoration: none !important;
        display: block !important;
        text-align: center !important;
    }
    
    .tryouthub-btn-start-exam:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 18px rgba(0,112,249,0.3) !important;
        text-decoration: none !important;
    }
    
    @media (max-width: 768px) {
        .tryouthub-info-stats {
            grid-template-columns: 1fr !important;
        }
        
        .tryouthub-composition-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
        
        .tryouthub-exam-info-card {
            padding: 1.25rem !important;
        }
        
        .tryouthub-exam-info-actions {
            flex-direction: column !important;
        }
    }
</style>

<div class="tryouthub-exam-info-container">
    <div class="tryouthub-exam-info-card">
        <div class="tryouthub-exam-info-header">
            <div class="tryouthub-exam-icon">🎯</div>
            <h1 class="tryouthub-exam-info-title">Tryout Simulasi UTBK 2025</h1>
        </div>
        
        <div class="tryouthub-info-stats">
            <div class="tryouthub-info-stat">
                <span class="tryouthub-info-label">Total Soal</span>
                <span class="tryouthub-info-value">180</span>
            </div>
            
            <div class="tryouthub-info-stat">
                <span class="tryouthub-info-label">Durasi</span>
                <span class="tryouthub-info-value">195 Mnt</span>
            </div>
            
            <div class="tryouthub-info-stat">
                <span class="tryouthub-info-label">Kategori</span>
                <span class="tryouthub-info-value">7</span>
            </div>
        </div>
        
        <div class="tryouthub-composition-box">
            <div class="tryouthub-composition-title">
                📋 Komposisi Soal
            </div>
            
            <div class="tryouthub-composition-grid">
                <div class="tryouthub-composition-item">PU: 30</div>
                <div class="tryouthub-composition-item">PPU: 20</div>
                <div class="tryouthub-composition-item">PBM: 20</div>
                <div class="tryouthub-composition-item">PK: 20</div>
                <div class="tryouthub-composition-item">Lit. BI: 30</div>
                <div class="tryouthub-composition-item">Lit. EN: 20</div>
                <div class="tryouthub-composition-item">PM: 20</div>
            </div>
        </div>
        
        <div class="tryouthub-exam-info-actions">
            <a href="<?php echo esc_url($back_url); ?>" class="tryouthub-btn-back">
                ← Kembali
            </a>
            <a href="<?php echo esc_url($start_exam_url); ?>" class="tryouthub-btn-start-exam">
                Mulai Tryout
            </a>
        </div>
    </div>
</div>