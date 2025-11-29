/**
 * TryOutHub Frontend JavaScript
 */

(function($) {
    'use strict';

    // Login form handler
    $('#tryouthub-login-form').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $message = $('#tryouthub-login-message');
        const $button = $form.find('button[type="submit"]');
        
        $button.prop('disabled', true).text('Memproses...');
        $message.hide();
        
        $.ajax({
            url: tryouthubData.ajaxUrl,
            method: 'POST',
            data: {
                action: 'tryouthub_login',
                nonce: tryouthubData.nonce,
                email: $form.find('[name="email"]').val(),
                password: $form.find('[name="password"]').val(),
                remember: $form.find('[name="remember"]').is(':checked'),
            },
            success: function(response) {
                if (response.success) {
                    $message
                        .html('<p style="color: #16a34a;">' + response.data.message + '</p>')
                        .show();
                    
                    // Redirect after 1 second
                    setTimeout(function() {
                        window.location.href = response.data.redirect;
                    }, 1000);
                } else {
                    $message
                        .html('<p style="color: #ef4444;">' + response.data.message + '</p>')
                        .show();
                    $button.prop('disabled', false).text('Masuk');
                }
            },
            error: function() {
                $message
                    .html('<p style="color: #ef4444;">Terjadi kesalahan. Silakan coba lagi.</p>')
                    .show();
                $button.prop('disabled', false).text('Masuk');
            }
        });
    });

    // Register form handler
    $('#tryouthub-register-form').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $message = $('#tryouthub-login-message');
        const $button = $form.find('button[type="submit"]');
        
        $button.prop('disabled', true).text('Memproses...');
        $message.hide();
        
        $.ajax({
            url: tryouthubData.ajaxUrl,
            method: 'POST',
            data: {
                action: 'tryouthub_register',
                nonce: tryouthubData.nonce,
                name: $form.find('[name="name"]').val(),
                email: $form.find('[name="email"]').val(),
                password: $form.find('[name="password"]').val(),
            },
            success: function(response) {
                if (response.success) {
                    $message
                        .html('<p style="color: #16a34a;">' + response.data.message + '</p>')
                        .show();
                    
                    // Redirect after 1 second
                    setTimeout(function() {
                        window.location.href = response.data.redirect;
                    }, 1000);
                } else {
                    $message
                        .html('<p style="color: #ef4444;">' + response.data.message + '</p>')
                        .show();
                    $button.prop('disabled', false).text('Daftar');
                }
            },
            error: function() {
                $message
                    .html('<p style="color: #ef4444;">Terjadi kesalahan. Silakan coba lagi.</p>')
                    .show();
                $button.prop('disabled', false).text('Daftar');
            }
        });
    });

})(jQuery);