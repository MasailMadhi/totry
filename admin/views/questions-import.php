<?php
/**
 * Questions import view
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1>Import Soal dari CSV</h1>
    
    <div style="background: white; padding: 2rem; border-radius: 0.5rem; margin: 2rem 0;">
        <h2>Format CSV</h2>
        <p>File CSV harus memiliki kolom berikut (dengan header di baris pertama):</p>
        <ol>
            <li><strong>title</strong> - Judul soal</li>
            <li><strong>content</strong> - Konten soal (bisa HTML)</li>
            <li><strong>option_a</strong> - Opsi A</li>
            <li><strong>option_b</strong> - Opsi B</li>
            <li><strong>option_c</strong> - Opsi C</li>
            <li><strong>option_d</strong> - Opsi D</li>
            <li><strong>option_e</strong> - Opsi E</li>
            <li><strong>correct_option</strong> - Jawaban benar (A/B/C/D/E)</li>
            <li><strong>explanation</strong> - Pembahasan (opsional)</li>
            <li><strong>category</strong> - Kategori (PK/PM/PU/PPU/PBM/LIT_BahasaID)</li>
            <li><strong>difficulty</strong> - Tingkat kesulitan (easy/medium/hard)</li>
        </ol>
        
        <p>
            <a href="<?php echo admin_url('admin.php?page=tryouthub-questions&action=download_template'); ?>" class="button">
                Download Template CSV
            </a>
        </p>
    </div>
    
    <div style="background: white; padding: 2rem; border-radius: 0.5rem;">
        <h2>Upload File CSV</h2>
        <form id="tryouthub-import-form" enctype="multipart/form-data">
            <table class="form-table">
                <tr>
                    <th><label for="csv_file">Pilih File CSV</label></th>
                    <td>
                        <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
                        <p class="description">Maksimal ukuran file: 5MB</p>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" class="button button-primary button-large" id="import-btn">
                    Import Soal
                </button>
                <span id="import-progress" style="display: none; margin-left: 1rem;">
                    <span class="spinner is-active" style="float: none;"></span> Importing...
                </span>
            </p>
        </form>
        
        <div id="import-result" style="margin-top: 2rem;"></div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#tryouthub-import-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData();
        formData.append('action', 'tryouthub_import_csv');
        formData.append('nonce', tryouthubAdmin.nonce);
        formData.append('csv_file', $('#csv_file')[0].files[0]);
        
        $('#import-btn').prop('disabled', true);
        $('#import-progress').show();
        $('#import-result').html('');
        
        $.ajax({
            url: tryouthubAdmin.ajaxUrl,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#import-btn').prop('disabled', false);
                $('#import-progress').hide();
                
                if (response.success) {
                    $('#import-result').html(
                        '<div class="notice notice-success"><p>' + response.data.message + '</p></div>'
                    );
                    $('#csv_file').val('');
                } else {
                    $('#import-result').html(
                        '<div class="notice notice-error"><p>' + response.data.message + '</p></div>'
                    );
                }
            },
            error: function() {
                $('#import-btn').prop('disabled', false);
                $('#import-progress').hide();
                $('#import-result').html(
                    '<div class="notice notice-error"><p>Terjadi kesalahan saat import.</p></div>'
                );
            }
        });
    });
});
</script>