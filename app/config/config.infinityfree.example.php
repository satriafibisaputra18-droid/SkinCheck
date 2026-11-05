<?php
/**
 * SkinCheck - Konfigurasi Khusus InfinityFree / cPanel Hosting
 * 
 * Cara Penggunaan:
 * 1. Buat MySQL Database di Control Panel InfinityFree.
 * 2. Salin file ini menjadi 'config.local.php' di folder yang sama (app/config/).
 * 3. Ganti nilai di bawah ini dengan kredensial database dari InfinityFree Anda.
 */

// Kredensial MySQL InfinityFree (Bisa dilihat di Control Panel InfinityFree / vPanel)
define('DB_HOST', 'sqlxxx.infinityfree.com'); // Contoh: sql200.infinityfree.com
define('DB_USER', 'if0_38xxxxxx');           // Contoh: if0_38123456
define('DB_PASS', 'PasswordVpanelAnda');     // Password akun vPanel Anda
define('DB_NAME', 'if0_38xxxxxx_skincheck'); // Contoh: if0_38123456_skincheck

// URL Domain Website Anda di InfinityFree (Opsional, sistem dapat mendeteksi otomatis)
// define('URLROOT', 'https://skincheck.infinityfreeapp.com');

