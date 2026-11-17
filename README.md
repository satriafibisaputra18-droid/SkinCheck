# 🌿 SkinCheck — AI-Powered Skin Barrier & Sunscreen Analysis Platform

<div align="center">

[![🌿 SkinCheck CI/CD DevOps Pipeline](https://github.com/satriafibisaputra18-droid/SkinCheck/actions/workflows/main.yml/badge.svg)](https://github.com/satriafibisaputra18-droid/SkinCheck/actions/workflows/main.yml)
![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![NodeJS](https://img.shields.io/badge/Node.js-18+-339933?style=for-the-badge&logo=node.js&logoColor=white)
![Google Gemini](https://img.shields.io/badge/Google%20Gemini-Flash%20AI-4285F4?style=for-the-badge&logo=google&logoColor=white)
![MobileNetV3](https://img.shields.io/badge/Model-MobileNetV3%20ONNX-FF6F00?style=for-the-badge&logo=pytorch&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

**Platform evaluasi mandiri 4 indikator sawar kulit wajah (skin barrier) & verifikasi kerataan tabir surya (sunscreen) berbasis Computer Vision AI terintegrasi dengan Google Gemini.**

[Identitas Proyek](#-identitas-proyek-akhir-obe) • [Tautan Video](#-tautan-video-presentasi-proyek) • [Dokumen Rekayasa](#-berkas-dokumen-rekayasa-perangkat-lunak) • [Fitur Utama](#-fitur-utama) • [Panduan Instalasi](#-panduan-instalasi--menjalankan-proyek)

</div>

---

## 🎓 Identitas Proyek Akhir (OBE)
* **Program Studi**: S1 Teknik Informatika — STMIK IKMI Cirebon
* **Mata Kuliah**: Rekayasa Perangkat Lunak (TIKB2503021) — 4 SKS (2 Teori + 2 Praktik)
* **Dosen Pengampu**: Yudhistira Arie Wijaya, M.Kom., CDS.
* **Mahasiswa / Full-Stack Engineer**: **Satria Fibi Saputra**
* **NIM**: **41257159**
* **Tema Topik Kelompok**: *Smart Healthcare & Public Service AI*
* **Sifat Pengerjaan**: Mandiri / Perorangan (*100% Individual Ownership & Full-App Build*)

---

## 🎥 Tautan Video Presentasi Proyek
Sesuai pedoman tugas dan rubrik penilaian OBE STMIK IKMI Cirebon:
1. 📹 **Video Progres 1: PRD & Konsep Aplikasi (Durasi 3–5 Menit)**:  
   🔗 `[Tempelkan Tautan YouTube / Google Drive Video 1 di Sini]`
2. 📹 **Video Progres 2: Pemodelan 4 Diagram UML & UI/UX Figma (Durasi 5–7 Menit)**:  
   🔗 `[Tempelkan Tautan YouTube / Google Drive Video 2 di Sini]`
3. 📹 **Video Final: Live Demo Aplikasi Web & Sistem Utuh (Durasi 8–12 Menit)**:  
   🔗 `[Tempelkan Tautan YouTube / Google Drive Video Final di Sini]`

---

## 📑 Berkas Dokumen Rekayasa Perangkat Lunak
* 📕 **Berkas Dokumen Lengkap (PDF Tunggal 69 Halaman — PRD + SDD UML & UI/UX Figma + Laporan Pengujian & CI/CD)**: [`docs/PROYEK_AKHIR_MANDIRI_SKINCHECK_SATRIA_FIBI_SAPUTRA_41257159.pdf`](docs/PROYEK_AKHIR_MANDIRI_SKINCHECK_SATRIA_FIBI_SAPUTRA_41257159.pdf)
* 📄 **Dokumen PRD / SRS (Standar IEEE 830)**: [`docs/PRD_SRS_SkinCheck.pdf`](docs/PRD_SRS_SkinCheck.pdf)
* 📄 **Dokumen SDD & Pemodelan UML (Standar IEEE 1016)**: [`docs/SDD_UML_SkinCheck.pdf`](docs/SDD_UML_SkinCheck.pdf)
* 📄 **Dokumen Laporan Pengujian & CI/CD (Standar IEEE 829)**: [`docs/LAPORAN_PENGUJIAN_CICD_SkinCheck.pdf`](docs/LAPORAN_PENGUJIAN_CICD_SkinCheck.pdf)
* 🧪 **Master Automated Unit Test Runner**: [`tests/run_all_tests.php`](tests/run_all_tests.php)
* ⚙️ **GitHub Actions CI/CD Pipeline**: [`.github/workflows/main.yml`](.github/workflows/main.yml)

---

---

## 🌟 Tentang SkinCheck

**SkinCheck** adalah platform *HealthTech / BeautyTech* yang menjembatani kebutuhan analisis kondisi kulit wajah mandiri secara ilmiah, presisi, dan mudah diakses langsung melalui peramban (*browser-only*).

Dengan menggabungkan teknologi **Computer Vision (MobileNetV3 ONNX)** dan **Generative AI (Google Gemini)**, SkinCheck mampu mengevaluasi integritas sawar kulit, mengukur persentase kerataan tabir surya (*sunscreen coverage*), serta memberikan rekomendasi produk perawatan kulit yang terkurasi secara personal.

---

## ✨ Fitur Utama

### 👤 1. Portal Pelanggan (Customer Experience)
* 🔐 **Autentikasi Multi-Kanal & Keamanan OTP**: Registrasi akun terproteksi dengan verifikasi 6-digit OTP ganda via **Bot Telegram** (`@Kode_OTP_SkinCheckBot`) dan **Resend Email API**, dilengkapi *1-click instant verification link*.
* 📷 **Smart Camera HUD Viewfinder**: Pengambilan foto wajah langsung melalui webcam browser dengan *Face Oval Guide HUD*, deteksi orientasi, validasi kecerahan (*brightness*), resolusi, dan efek animasi *laser scanner*.
* 🔬 **Evaluasi 4 Pilar Klinis Sawar Kulit**:
  1. **Acne & Pores**: Estimasi bintik jerawat aktif, komedo, dan kepadatan pori.
  2. **Redness (Eritema)**: Pemetaan saturasi kemerahan & iritasi vaskular permukaan kulit.
  3. **Texture Smoothness**: Analisis kehalusan mikrotekstur stratum korneum vs kekasaran.
  4. **Hyperpigmentation**: Deteksi bintik gelap, flek, dan kontras diskolorasi melanin.
* ☀️ **Pemeriksaan Kerataan Sunscreen (Sunscreen Check)**:
  - Estimasi persentase cakupan proteksi (*Coverage Score 0–100%*).
  - Klasifikasi status (*Sangat Merata*, *Cukup Merata*, *Kurang Merata*).
  - Pemetaan area rawan yang terlewat (*Low Coverage Areas*).
  - Visualisasi Heatmap proteksi tabir surya.
* 📊 **Riwayat & Pelacakan Progres (History Tracking)**: Arsip seluruh riwayat pemeriksaan dengan metrik perbandingan waktu dan *ownership-scoped secure image streaming*.
* 🧴 **Rekomendasi Skincare Terkurasi**: Rekomendasi otomatis produk skincare dari katalog resmi berdasarkan masalah dominan pengguna.
* 💬 **AI Skincare Assistant (Chatbot Gemini)**: Chatbot interaktif ramah pengguna yang memahami riwayat scan pengguna untuk konsultasi bahan aktif dan rutinitas perawatan kulit.

---

### 🛡️ 2. Portal Manajemen (Executive Administrator)
* 📊 **Executive Dashboard**: Ringkasan KPI bisnis (Total Pelanggan, Total Scan Pemeriksaan, Katalog Kosmetik, dan Rata-rata Skor Kesehatan Kulit Global).
* ⚡ **Live Gemini AI Token & Engine Monitor**:
  - Pemantauan konsumsi kuota token Gemini API secara langsung (*Input/Prompt Tokens* vs *Output/Completion Tokens*).
  - Pelacakan akumulasi token harian (*tokens today*) dan volume request.
  - Pemantauan status tier mesin AI (*Free Tier / Live Active*).
* 👥 **Customer Registry**: Manajemen akun pelanggan terdaftar, tanggal registrasi, dan kendali status akun.
* 🧴 **Cosmetics Catalog Management**: Database katalog skincare aktif (brand, nama, kategori, masalah kulit, tipe kulit, harga, kandungan bahan aktif/ingredients, dan panduan penggunaan).
* 📝 **Scan Audits**: Log audit seluruh aktivitas pemeriksaan wajah oleh pengguna dalam sistem.
* 🤖 **Executive AI Assistant & Analytics Console**: Konsol AI level eksekutif (ADMIN CLEARANCE) terhubung ke database untuk menjawab analitik bisnis, tren keluhan kulit pelanggan, dan insight performa produk secara instan.
* ⚙️ **Centralized Settings Console**:
  - Konfigurasi Bot Telegram (Bot Token, Chat ID, dan fitur *Live AJAX Test*).
  - Konfigurasi Google Gemini (API Key, Model Utama, Model Fallback, Temperature, dan uji koneksi API).
  - Konfigurasi Resend Email API (API Key, Sender Email, dan uji kirim email).
  - Manajemen parameter keamanan (masa berlaku OTP, cooldown kirim ulang).

---

## 🏗️ Arsitektur Sistem

```
┌────────────────────────────────────────────────────────┐
│                   Pengguna / Browser                   │
│   (Webcam HUD, Dashboard, Konsul Chatbot, Scan History) │
└───────────────────────────┬────────────────────────────┘
                            │ HTTP / AJAX
┌───────────────────────────▼────────────────────────────┐
│                    Web Server Apache                   │
│             SkinCheck Core (PHP 8.1+ MVC)              │
│   [Controllers: Auth, Scan, Sunscreen, Chatbot, Admin] │
└─────────────┬───────────────────────────┬──────────────┘
              │                           │
              ▼                           ▼
┌───────────────────────────┐   ┌───────────────────────────┐
│       MySQL Database      │   │   Node.js Chatbot Engine  │
│  (Users, Scans, Products, │   │    (Express Microservice) │
│    Settings, AI Tokens)   │   └─────────────┬─────────────┘
└───────────────────────────┘                 │
              ▲                               ▼
              │                  ┌──────────────────────────┐
┌─────────────┴─────────────┐    │   Google Gemini API Hub  │
│  Computer Vision / ONNX   │    │  (Flash Models Inference)│
│ (MobileNetV3 Multi-Task)  │    └──────────────────────────┘
└───────────────────────────┘
```

---

## 🚀 Panduan Instalasi & Menjalankan Proyek

### 1. Prasyarat Sistem
- Web Server: **Apache** (disarankan via **XAMPP**)
- Bahasa Pemrograman: **PHP 8.1+** dengan ekstensi `pdo_mysql`, `curl`, `gd`, `openssl` aktif
- Database: **MySQL 8.0+** / MariaDB
- Runtime AI Service: **Node.js 18+** & npm

---

### 2. Kloning Repository
```bash
git clone https://github.com/satriafibisaputra18-droid/SkinCheck.git
cd SkinCheck
```

---

### 3. Migrasi & Seeding Database
1. Pastikan modul **Apache** dan **MySQL** di XAMPP sudah berjalan (*running*).
2. Jalankan perintah migrasi PHP otomatis dari terminal:
```bash
php migrate.php
```
> Skrip di atas akan secara otomatis membuat database `skincheck`, seluruh tabel relasional, menyuntikkan data katalog produk skincare, akun admin, serta pengaturan awal sistem.

---

### 4. Menjalankan Chatbot Server (Node.js)
1. Buka folder microservice chatbot:
```bash
cd chatbot-server
```
2. Salin berkas lingkungan dan instal dependencies:
```bash
cp .env.example .env
npm install
```
3. Sesuaikan konfigurasi di `.env` (isi `GEMINI_API_KEY` milik Anda).
4. Jalankan server chatbot:
```bash
npm start
# Atau gunakan: node server.js
```
*Server chatbot akan berjalan di `http://127.0.0.1:5000`.*

---

### 5. Akses Melalui Browser
Buka peramban favorit Anda dan kunjungi:
```
http://localhost/Skin%20Check/
```
*(Atau sesuaikan dengan nama folder di direktori `htdocs` XAMPP Anda).*

---

## 🔑 Kredensial Demo Bawaan

| Peran (Role) | Email | Password |
|---|---|---|
| **Administrator** | `admin@skincheck.com` | `admin123` |
| **Customer (Demo)** | `customer@example.com` | `password123` |

> *Catatan: Anda juga dapat mendaftar sebagai customer baru kapan saja melalui halaman registrasi.*

---

## 🧪 Pelatihan Model AI (Machine Learning)

SkinCheck dilengkapi berkas notebook pelatihan machine learning mandiri:
* `SkinCheck_AI_Trainer.ipynb`: Pelatihan arsitektur MobileNetV3 multi-task untuk evaluasi 4 pilar kondisi kulit (Acne, Redness, Texture, Hyperpigmentation) berbasis keragaman warna kulit Fitzpatrick I–VI di Google Colab.
* `SkinCheck_AI_Trainer_Kaggle.ipynb`: Pelatihan dan pemrosesan dataset berskala besar di platform Kaggle GPU.
* Panduan lengkap dapat dibaca pada berkas `PANDUAN_TRAINING_AI_COLAB.md` dan `PANDUAN_TRAINING_AI_KAGGLE.md`.

---

## ⚠️ Disclaimer Medis

> **Pemberitahuan Penting**: SkinCheck dirancang khusus sebagai alat evaluasi mandiri dan platform edukasi perawatan sawar kulit berbasis kecerdasan buatan (AI/Computer Vision). Seluruh skor indikator visual, catatan analisis, dan visualisasi heatmap yang dihasilkan **BUKAN merupakan diagnosis medis klinis** dan **TIDAK menggantikan pemeriksaan, diagnosis, maupun perawatan langsung oleh dokter spesialis dermatologi atau tenaga medis profesional**.

---

<div align="center">
  <sub>Dikembangkan dengan sepenuh hati untuk kemajuan teknologi kesehatan & kecantikan modern.</sub>
</div>

