# 🚀 Panduan Lengkap Deploy SkinCheck ke InfinityFree (Gratis 24 Jam)

Panduan ini disusun secara bertahap agar Yang Mulia Tuan Besar dapat meng-online-kan website **SkinCheck** ke hosting gratis **InfinityFree** dengan lancar dan mudah.

---

## 📋 Ringkasan Berkas yang Telah Disiapkan:
1. `database_infinityfree.sql`: Berkas database SQL lengkap (seluruh tabel, akun demo admin/customer, dan 30+ produk skincare) yang **siap di-import langsung di phpMyAdmin** InfinityFree tanpa error.
2. `app/config/config.infinityfree.example.php`: Templat konfigurasi database khusus InfinityFree.
3. Otomatisasi URL: Sistem SkinCheck sudah diperbarui sehingga otomatis mendeteksi domain InfinityFree tanpa konfigurasi rumit.

---

## 🛠️ Langkah-Langkah Deploy:

### LANGKAH 1: Buat Akun & Domain Gratis di InfinityFree
1. Buka situs [InfinityFree](https://www.infinityfree.com/) dan klik **Register / Sign Up**.
2. Masuk ke Dashboard, klik tombol **Create Account**.
3. Pilih domain gratis (contoh: `skincheck.infinityfreeapp.com` atau `skincheck.rf.gd`).
4. Ikuti langkah pembuatan hingga akun selesai dibuat.

---

### LANGKAH 2: Buat Database MySQL di Control Panel (vPanel)
1. Di Dashboard akun InfinityFree Anda, klik **Control Panel** (vPanel).
2. Cari menu **Databases** $\rightarrow$ pilih **MySQL Databases**.
3. Di kolom *Create New Database*, beri nama: `skincheck` lalu klik **Create Database**.
4. Catat informasi koneksi yang tampil di layar:
   - **MySQL Host Name** (contoh: `sql208.infinityfree.com`)
   - **MySQL User Name** (contoh: `if0_38123456`)
   - **MySQL Password** (password akun vPanel Anda)
   - **MySQL Database Name** (contoh: `if0_38123456_skincheck`)

---

### LANGKAH 3: Import Database melalui phpMyAdmin
1. Kembali ke menu **MySQL Databases** di Control Panel.
2. Di baris database yang baru dibuat, klik tombol **phpMyAdmin**.
3. Di dalam phpMyAdmin:
   - Klik tab **Import** di bagian atas.
   - Klik tombol **Choose File** / *Pilih Berkas*.
   - Pilih berkas `database_infinityfree.sql` dari folder proyek SkinCheck Anda.
   - Gulir ke bawah dan klik tombol **Import** (atau *Go*).
4. Selesai! Seluruh tabel dan data produk kini telah terpasang rapi di cloud.

---

### LANGKAH 4: Konfigurasi Database di Proyek
1. Buka folder `app/config/` di komputer Tuan.
2. Gandakan (*duplicate*) berkas `config.infinityfree.example.php` dan ubah namanya menjadi:
   $$\text{app/config/config.local.php}$$
3. Buka file `config.local.php` tersebut, lalu sesuaikan nilainya dengan data dari Langkah 2:
   ```php
   <?php
   define('DB_HOST', 'sql208.infinityfree.com'); // Ganti dengan Host InfinityFree Anda
   define('DB_USER', 'if0_38123456');           // Ganti dengan User InfinityFree Anda
   define('DB_PASS', 'PasswordVpanelAnda');     // Password vPanel Anda
   define('DB_NAME', 'if0_38123456_skincheck'); // Ganti dengan Nama Database Anda
   ```
4. Simpan file tersebut.

---

### LANGKAH 5: Upload Berkas ke InfinityFree
Ada dua cara mudah untuk mengunggah file:

#### Opsi A: Menggunakan File Manager Bawaan Web (Paling Simpel)
1. Di Control Panel InfinityFree, buka **Online File Manager**.
2. Masuk ke dalam folder **`htdocs/`**.
3. Hapus file bawaan `DO NOT UPLOAD FILES HERE` jika ada.
4. Unggah seluruh isi folder proyek SkinCheck Anda ke dalam folder `htdocs/`.
   *(Pastikan file `.htaccess`, folder `app`, `public`, `storage` berada langsung di dalam `htdocs/`)*.

#### Opsi B: Menggunakan FileZilla FTP (Direkomendasikan untuk upload banyak file)
1. Unduh dan buka aplikasi gratis **FileZilla Client**.
2. Masukkan data FTP dari halaman akun InfinityFree Anda:
   - Host: `ftpupload.net`
   - Username: `if0_38123456`
   - Password: Password vPanel Anda
   - Port: `21`
3. Klik **Quickconnect**.
4. Di sisi kanan (remote server), buka folder **`htdocs/`**.
5. Di sisi kiri (komputer lokal), pilih seluruh file proyek SkinCheck Tuan lalu klik kanan $\rightarrow$ **Upload**.

---

### LANGKAH 6: Aktifkan SSL / HTTPS Gratis
1. Di dashboard InfinityFree, buka menu **Free SSL Certificates**.
2. Pilih domain Anda, klik **Create SSL Certificate**.
3. Pilih provider **Let's Encrypt** atau **ZeroSSL** (gratis), lalu ikuti petunjuk verifikasi DNS CNAME otomatis dengan 1 klik.
4. Sekarang website Tuan memiliki gembok hijau aman (`https://`).

---

## 🔑 Login ke Website Anda:
Buka domain Anda di browser (contoh: `https://skincheck.infinityfreeapp.com`):
- **Login Admin**: `admin@skincheck.com` | Password: `admin123`
- **Login Customer**: `customer@example.com` | Password: `password123`
- Anda juga dapat langsung mendaftar sebagai akun baru melalui halaman Register!
