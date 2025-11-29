# TryOutHub WordPress Plugin - Deployment Guide

## Deskripsi
TryOutHub adalah plugin WordPress lengkap untuk platform tryout UTBK-SNBT dengan fitur:
- Manajemen soal (CRUD, CSV import, WYSIWYG editor)
- Paket tryout (free/premium, durasi custom)
- Sistem exam dengan timer dan autosave
- Scoring otomatis dan leaderboard
- Dashboard interaktif untuk siswa
- Login/Register (email + password)

## Requirements
- WordPress 5.8+
- PHP 7.4+
- MySQL 5.7+
- jQuery (included in WordPress)

## Instalasi

### Metode 1: Upload via WordPress Admin
1. Login ke WordPress Admin
2. Navigate ke **Plugins > Add New**
3. Klik **Upload Plugin**
4. Pilih file `tryouthub.zip`
5. Klik **Install Now**
6. Klik **Activate Plugin**

### Metode 2: Manual FTP Upload
1. Extract file `tryouthub.zip`
2. Upload folder `tryouthub` ke `/wp-content/plugins/`
3. Login ke WordPress Admin
4. Navigate ke **Plugins**
5. Activate **TryOutHub** plugin

## Konfigurasi Awal

### 1. Database Setup
Plugin akan otomatis membuat:
- 6 custom tables (questions, packs, pack_questions, attempts, answers, transactions)
- Sample data (20 soal, 7 paket tryout)
- Default page `/app` dengan shortcode `[tryouthub_app]`

### 2. User Roles
Plugin menambahkan 2 roles:
- **tryouthub_student** - untuk siswa (default)
- **manage_tryouthub** capability - untuk admin

### 3. Default Settings
- Primary Color: #0070F9
- Points per Correct: 5
- Points per Wrong: -1
- Ranking: Enabled

## Struktur URL

### Frontend
- **Dashboard**: `https://tryout.tautku.id/app`
- **Login**: Pakai shortcode `[tryouthub_login]` di page mana saja
- **Tryout List**: Terintegrasi di dashboard (tab Tryout)
- **Exam**: Auto-generated saat user klik "Mulai Tryout"
- **Profile**: Terintegrasi di dashboard (tab Profil)

### Admin
- **Dashboard**: `/wp-admin/admin.php?page=tryouthub`
- **Questions**: `/wp-admin/admin.php?page=tryouthub-questions`
- **Packs**: `/wp-admin/admin.php?page=tryouthub-packs`
- **Transactions**: `/wp-admin/admin.php?page=tryouthub-transactions`
- **Settings**: `/wp-admin/admin.php?page=tryouthub-settings`

## Fitur Utama

### 1. Login & Registration
- Email + Password authentication
- No Google OAuth (sesuai permintaan)
- Auto redirect ke `/app` setelah login
- Role: `tryouthub_student`

### 2. Question Management
- CRUD soal dengan WYSIWYG editor
- Upload gambar via Media Library
- 6 kategori: PK, PM, PU, PPU, PBM, LIT_BahasaID
- CSV import (template disediakan)
- Difficulty levels: easy, medium, hard
- Status: draft/publish

### 3. Pack Management
- Create packs dengan multiple questions
- Set duration (minutes)
- Free/Premium packs
- Full UTBK pack (gabungan semua kategori)
- Category-specific packs

### 4. Exam System
- Timer countdown dengan warning (5min, 1min)
- Auto-submit saat timeout
- Autosave jawaban (debounced 500ms)
- Question grid navigation
- Previous/Next navigation
- Jump to specific question

### 5. Scoring & Leaderboard
- Auto-calculate score saat finish
- Correct/wrong/unanswered count
- Points system (configurable)
- User ranking
- Top 100 leaderboard

### 6. Dashboard (Frontend)
- Tab navigation: Beranda, Soal, Tryout, Premium, Hasil Belajar, Profil, Panduan
- User stats: points, rank, attempts
- Recent attempts history
- Quick access ke tryout

## CSV Import Template

Download template: `/wp-admin/admin.php?page=tryouthub-questions&action=download_template`

Format CSV: