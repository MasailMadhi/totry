<?php
/**
 * Login form template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<style>
    /* Force remove any conflicting styles */
    .tryouthub-login-page-wrapper * {
        box-sizing: border-box;
    }
</style>

<div class="tryouthub-login-page-wrapper" style="margin: 0; padding: 0; width: 100%;">
    <div class="tryouthub-login-wrapper" style="width: 100%; min-height: 500px; padding: 4rem 0; margin: 0; background: linear-gradient(90deg, #ffffff 0%, #e0f2fe 30%, #bae6fd 60%, #7dd3fc 100%); display: flex; align-items: center; justify-content: center;">
        <div class="tryouthub-login-container" style="background: white; border-radius: 1.5rem; box-shadow: 0 10px 40px rgba(0,0,0,0.08); max-width: 520px; width: 90%; padding: 3rem; margin: 0 1rem;">
            
            <div class="tryouthub-login-header" style="text-align: center; margin-bottom: 2rem;">
                <h2 style="font-size: 2rem; font-weight: 700; color: #1a1a1a; margin: 0 0 0.5rem 0;">Masuk Akun</h2>
                <p style="color: #666; font-size: 0.95rem; margin: 0;">Selamat datang di TryOutHub, masukan email dan passwordmu untuk mengakses aplikasi.</p>
            </div>

            <div id="tryouthub-login-message" style="display: none; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;"></div>

            <form id="tryouthub-login-form" class="tryouthub-form">
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="login_email" style="display: block; margin-bottom: 0.5rem; color: #333; font-weight: 500; font-size: 0.95rem;">Email</label>
                    <input 
                        type="email" 
                        id="login_email" 
                        name="email" 
                        required 
                        placeholder="Email"
                        style="width: 100%; padding: 0.875rem 1rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem; transition: all 0.2s; background: white;"
                        onfocus="this.style.borderColor='#0070F9'" 
                        onblur="this.style.borderColor='#e5e7eb'"
                    >
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label for="login_password" style="display: block; margin-bottom: 0.5rem; color: #333; font-weight: 500; font-size: 0.95rem;">Password</label>
                    <div style="position: relative;">
                        <input 
                            type="password" 
                            id="login_password" 
                            name="password" 
                            required 
                            placeholder="Password"
                            style="width: 100%; padding: 0.875rem 1rem; padding-right: 3rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem; transition: all 0.2s; background: white;"
                            onfocus="this.style.borderColor='#0070F9'" 
                            onblur="this.style.borderColor='#e5e7eb'"
                        >
                        <button 
                            type="button" 
                            id="toggle-password" 
                            style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #666; font-size: 1.25rem; padding: 0; line-height: 1;"
                            onclick="togglePasswordVisibility()"
                        >
                            👁️
                        </button>
                    </div>
                </div>

                <div style="text-align: right; margin-bottom: 1.5rem;">
                    <a href="#" style="color: #0070F9; font-size: 0.9rem; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#0060d9'" onmouseout="this.style.color='#0070F9'">Lupa Password</a>
                </div>

                <button 
                    type="submit" 
                    class="tryouthub-btn-primary"
                    id="login-submit-btn"
                    style="width: 100%; padding: 1rem; background: #0070F9; color: white; border: none; border-radius: 0.5rem; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.2s;"
                    onmouseover="this.style.background='#0060d9'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(0,112,249,0.3)'" 
                    onmouseout="this.style.background='#0070F9'; this.style.transform='translateY(0)'; this.style.boxShadow='none'"
                >
                    Masuk
                </button>
            </form>

            <div style="text-align: center; margin: 1.5rem 0; position: relative;">
                <div style="position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: #e5e7eb; z-index: 0;"></div>
                <span style="position: relative; background: white; padding: 0 1rem; color: #999; font-size: 0.9rem; z-index: 1;">Atau</span>
            </div>

            <div style="text-align: center; margin-top: 1.5rem;">
                <p style="color: #666; font-size: 0.95rem; margin: 0;">
                    Belum punya akun? 
                    <a href="#" id="show-register-form" style="color: #0070F9; font-weight: 600; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#0060d9'" onmouseout="this.style.color='#0070F9'">Buat sekarang</a>
                </p>
            </div>

            <!-- Register Form (hidden by default) -->
            <form id="tryouthub-register-form" class="tryouthub-form" style="display: none;">
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="register_name" style="display: block; margin-bottom: 0.5rem; color: #333; font-weight: 500; font-size: 0.95rem;">Nama Lengkap</label>
                    <input 
                        type="text" 
                        id="register_name" 
                        name="name" 
                        required 
                        placeholder="Nama Lengkap"
                        style="width: 100%; padding: 0.875rem 1rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem; transition: all 0.2s; background: white;"
                        onfocus="this.style.borderColor='#0070F9'" 
                        onblur="this.style.borderColor='#e5e7eb'"
                    >
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="register_email" style="display: block; margin-bottom: 0.5rem; color: #333; font-weight: 500; font-size: 0.95rem;">Email</label>
                    <input 
                        type="email" 
                        id="register_email" 
                        name="email" 
                        required 
                        placeholder="Email"
                        style="width: 100%; padding: 0.875rem 1rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem; transition: all 0.2s; background: white;"
                        onfocus="this.style.borderColor='#0070F9'" 
                        onblur="this.style.borderColor='#e5e7eb'"
                    >
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="register_password" style="display: block; margin-bottom: 0.5rem; color: #333; font-weight: 500; font-size: 0.95rem;">Password</label>
                    <div style="position: relative;">
                        <input 
                            type="password" 
                            id="register_password" 
                            name="password" 
                            required 
                            placeholder="Minimal 6 karakter"
                            style="width: 100%; padding: 0.875rem 1rem; padding-right: 3rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem; transition: all 0.2s; background: white;"
                            onfocus="this.style.borderColor='#0070F9'" 
                            onblur="this.style.borderColor='#e5e7eb'"
                        >
                        <button 
                            type="button" 
                            id="toggle-register-password" 
                            style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #666; font-size: 1.25rem; padding: 0; line-height: 1;"
                            onclick="toggleRegisterPasswordVisibility()"
                        >
                            👁️
                        </button>
                    </div>
                </div>

                <button 
                    type="submit" 
                    class="tryouthub-btn-primary"
                    id="register-submit-btn"
                    style="width: 100%; padding: 1rem; background: #0070F9; color: white; border: none; border-radius: 0.5rem; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.2s;"
                    onmouseover="this.style.background='#0060d9'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(0,112,249,0.3)'" 
                    onmouseout="this.style.background='#0070F9'; this.style.transform='translateY(0)'; this.style.boxShadow='none'"
                >
                    Daftar
                </button>

                <div style="text-align: center; margin-top: 1.5rem;">
                    <a href="#" id="show-login-form" style="color: #0070F9; font-weight: 600; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#0060d9'" onmouseout="this.style.color='#0070F9'">Kembali ke Login</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility() {
    const passwordInput = document.getElementById('login_password');
    const button = document.getElementById('toggle-password');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        button.textContent = '🙈';
    } else {
        passwordInput.type = 'password';
        button.textContent = '👁️';
    }
}

function toggleRegisterPasswordVisibility() {
    const passwordInput = document.getElementById('register_password');
    const button = document.getElementById('toggle-register-password');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        button.textContent = '🙈';
    } else {
        passwordInput.type = 'password';
        button.textContent = '👁️';
    }
}

document.getElementById('show-register-form').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('tryouthub-login-form').style.display = 'none';
    document.getElementById('tryouthub-register-form').style.display = 'block';
    document.querySelector('.tryouthub-login-header h2').textContent = 'Buat Akun';
    document.querySelector('.tryouthub-login-header p').textContent = 'Daftar untuk mulai belajar dan berlatih UTBK-SNBT';
});

document.getElementById('show-login-form').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('tryouthub-login-form').style.display = 'block';
    document.getElementById('tryouthub-register-form').style.display = 'none';
    document.querySelector('.tryouthub-login-header h2').textContent = 'Masuk Akun';
    document.querySelector('.tryouthub-login-header p').textContent = 'Selamat datang di TryOutHub, masukan email dan passwordmu untuk mengakses aplikasi.';
});
</script>