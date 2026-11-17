# -*- coding: utf-8 -*-
"""
PART 1: PRODUCT REQUIREMENTS DOCUMENT / SOFTWARE REQUIREMENTS SPECIFICATION (PRD / SRS)
Standard IEEE 830 / ISO/IEC/IEEE 29148:2018 — SkinCheck
"""
from styles import get_css

def render_toc_items(items: list[tuple[int, str, str]]) -> str:
    rows = []
    for level, title, page in items:
        rows.append(
            f'<li class="toc-item level-{level}">'
            f'<span class="toc-title">{title}</span>'
            f'<span class="toc-dots"></span>'
            f'<span class="toc-page">{page}</span>'
            f'</li>'
        )
    return '<ul class="toc-list">' + "\n".join(rows) + '</ul>'

def build_prd_html(page_map: dict[str, str] = None) -> str:
    pm = page_map or {}
    def pg(key: str) -> str:
        return str(pm.get(key, "—"))

    toc_entries = [
        (1, "Riwayat Revisi", pg("Riwayat Revisi")),
        (1, "Daftar Isi", pg("Daftar Isi")),
        (1, "1. Pendahuluan", pg("1. Pendahuluan")),
        (2, "1.1 Tujuan", pg("1.1 Tujuan")),
        (2, "1.2 Ruang Lingkup", pg("1.2 Ruang Lingkup")),
        (2, "1.3 Definisi, Akronim, dan Singkatan", pg("1.3 Definisi, Akronim, dan Singkatan")),
        (2, "1.4 Referensi", pg("1.4 Referensi")),
        (2, "1.5 Gambaran Dokumen", pg("1.5 Gambaran Dokumen")),
        (1, "2. Gambaran Umum", pg("2. Gambaran Umum")),
        (2, "2.1 Latar Belakang Masalah", pg("2.1 Latar Belakang Masalah")),
        (2, "2.2 Deskripsi Produk", pg("2.2 Deskripsi Produk")),
        (2, "2.3 Pengguna dan Peran", pg("2.3 Pengguna dan Peran")),
        (2, "2.4 Lingkungan Operasi", pg("2.4 Lingkungan Operasi")),
        (2, "2.5 Batasan Desain dan Implementasi", pg("2.5 Batasan Desain dan Implementasi")),
        (2, "2.6 Asumsi dan Ketergantungan", pg("2.6 Asumsi dan Ketergantungan")),
        (1, "3. Kebutuhan Fungsional", pg("3. Kebutuhan Fungsional")),
        (2, "3.1 Autentikasi, OTP Multi-Kanal, dan Manajemen Akun", pg("3.1 Autentikasi")),
        (2, "3.2 Pemindaian Sawar Kulit (4 Pilar Klinis & Smart Camera HUD)", pg("3.2 Pemindaian Sawar Kulit")),
        (2, "3.3 Pemeriksaan Kerataan Sunscreen & Visualisasi Heatmap", pg("3.3 Pemeriksaan Kerataan Sunscreen")),
        (2, "3.4 Riwayat Progres, Akses Citra Aman, dan Rekomendasi Produk", pg("3.4 Riwayat Progres")),
        (2, "3.5 AI Skincare Assistant (Google Gemini Context-Aware Chatbot)", pg("3.5 AI Skincare Assistant")),
        (2, "3.6 Panel Administrator Eksekutif, Token Monitor & API Gateway", pg("3.6 Panel Administrator Eksekutif")),
        (1, "4. Kebutuhan Non-Fungsional", pg("4. Kebutuhan Non-Fungsional")),
        (2, "4.1 Kinerja", pg("4.1 Kinerja")),
        (2, "4.2 Keamanan dan Privasi", pg("4.2 Keamanan dan Privasi")),
        (2, "4.3 Keandalan dan Ketersediaan", pg("4.3 Keandalan dan Ketersediaan")),
        (2, "4.4 Kegunaan dan Kepatuhan Terminologi", pg("4.4 Kegunaan")),
        (2, "4.5 Keterpeliharaan, Prinsip SOLID, dan Keterujian", pg("4.5 Keterpeliharaan")),
        (2, "4.6 Portabilitas dan Kompatibilitas", pg("4.6 Portabilitas")),
        (1, "5. Model Data", pg("5. Model Data")),
        (2, "5.1 Ringkasan Skema Basis Data", pg("5.1 Ringkasan Skema Basis Data")),
        (2, "5.2 Tabel per Domain", pg("5.2 Tabel per Domain")),
        (2, "5.3 Relasi Utama", pg("5.3 Relasi Utama")),
        (1, "6. Antarmuka Sistem", pg("6. Antarmuka Sistem")),
        (1, "7. Batasan, Risiko, dan Pekerjaan Mendatang", pg("7. Batasan, Risiko")),
        (1, "Lampiran A — Daftar Tabel Basis Data", pg("Lampiran A")),
        (1, "Lampiran B — Glosarium", pg("Lampiran B")),
        (1, "Lampiran C — Matriks Ketertelusuran Kebutuhan", pg("Lampiran C")),
    ]

    css = get_css("PRD / SRS — SKINCHECK | Rekayasa Perangkat Lunak (TIKB2503021)")

    return f"""<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>PRD / SRS — SKINCHECK (Satria Fibi Saputra - 41257159)</title>
<style>{css}</style>
</head>
<body>

<!-- ==================== COVER PAGE ==================== -->
<div class="cover-page">
  <p class="cover-inst">PROGRAM STUDI S1 TEKNIK INFORMATIKA</p>
  <p class="cover-inst">STMIK IKMI CIREBON</p>
  <hr class="cover-rule"/>

  <p class="cover-doctype">PRODUCT REQUIREMENTS DOCUMENT</p>
  <p class="cover-doctype">SOFTWARE REQUIREMENTS SPECIFICATION</p>
  <p class="cover-acronym">(PRD / SRS)</p>

  <p class="cover-product">SKINCHECK</p>
  <p class="cover-subtitle">Platform Analisis Sawar Kulit &amp; Pemerataan Sunscreen Berbasis AI</p>
  <p class="cover-std">Standar IEEE 830 / ISO/IEC/IEEE 29148:2018</p>

  <table class="meta-table">
    <thead>
      <tr><th colspan="2">Identitas Dokumen &amp; Proyek Akhir Mandiri</th></tr>
    </thead>
    <tbody>
      <tr><td>Mata Kuliah</td><td>Rekayasa Perangkat Lunak (TIKB2503021) — 4 SKS, Semester 3</td></tr>
      <tr><td>Nama Mahasiswa</td><td>Satria Fibi Saputra</td></tr>
      <tr><td>NIM</td><td>41257159</td></tr>
      <tr><td>Program Studi</td><td>S1 Teknik Informatika — STMIK IKMI Cirebon</td></tr>
      <tr><td>Tema Topik Kelompok</td><td>Smart Healthcare &amp; Public Service AI</td></tr>
      <tr><td>Sifat Pengerjaan</td><td>Mandiri / Perorangan (100% Individual Ownership &amp; Full-App Build)</td></tr>
      <tr><td>Dosen Pengampu</td><td>Yudhistira Arie Wijaya, M.Kom., CDS.</td></tr>
      <tr><td>Repositori GitHub</td><td>github.com/satriafibisaputra18-droid/SkinCheck (publik)</td></tr>
      <tr><td>Versi Dokumen</td><td>1.0</td></tr>
      <tr><td>Tanggal</td><td>November 2026</td></tr>
    </tbody>
  </table>

  <p class="cover-note">
    Dokumen ini disusun sebagai luaran Tahap 1 (Inisiasi &amp; Penyusunan PRD/SRS) Proyek Akhir Mandiri mata kuliah Rekayasa Perangkat Lunak, dan menjadi acuan tunggal kebutuhan sistem SKINCHECK pada Tahap 2 (SDD), Tahap 3 (Konstruksi, Pengujian &amp; CI/CD), dan Tahap 4 (Pelaporan Final &amp; Live Demo).
  </p>
</div>

<!-- ==================== RIWAYAT REVISI & DAFTAR ISI ==================== -->
<h1>Riwayat Revisi</h1>
<table class="data-table">
  <thead>
    <tr><th style="width:10%">Versi</th><th style="width:20%">Tanggal</th><th style="width:22%">Penulis</th><th>Perubahan</th></tr>
  </thead>
  <tbody>
    <tr>
      <td>1.0</td>
      <td>18 November 2026</td>
      <td>Satria Fibi Saputra (41257159)</td>
      <td>Dokumen formal Tahap 1. Seluruh 34 kebutuhan fungsional (F-01–F-34) dan 20 kebutuhan non-fungsional (NF-01–NF-20) diverifikasi langsung terhadap kode sumber pada repositori publik <code>satriafibisaputra18-droid/SkinCheck</code> (cabang <code>main</code>), skema basis data (<code>schema.sql</code> dan <code>database_infinityfree.sql</code>), serta hasil pengujian otomatis.</td>
    </tr>
  </tbody>
</table>

<p>Konvensi penomoran kebutuhan: <strong>F-xx</strong> untuk kebutuhan fungsional dan <strong>NF-xx</strong> untuk kebutuhan non-fungsional. Setiap kebutuhan fungsional dirumuskan dalam bentuk <em>user story</em> beserta kriteria penerimaan (<em>acceptance criteria</em>) yang dapat diuji sesuai ketentuan Tahap 1 Panduan Proyek Akhir.</p>

<div class="callout">
  Status implementasi setiap kebutuhan dicantumkan secara jujur berdasarkan pemeriksaan kode sumber aktual. Kebutuhan yang telah berjalan penuh ditandai "Terimplementasi", sedangkan fitur yang masih berjalan pada mode estimasi/abstraksi atau memerlukan kunci API eksternal dicatat secara transparan pada Bab 7 (Batasan, Risiko, dan Pekerjaan Mendatang).
</div>

<div class="toc-section">
  <h1>Daftar Isi</h1>
  {render_toc_items(toc_entries)}
</div>

<!-- ==================== BAB 1: PENDAHULUAN ==================== -->
<h1 class="chapter-start">1. Pendahuluan</h1>

<h2>1.1 Tujuan</h2>
<p>Dokumen ini menetapkan spesifikasi kebutuhan perangkat lunak untuk <strong>SKINCHECK</strong> — platform evaluasi kesehatan sawar kulit wajah (<em>skin barrier</em>) dan pemeriksaan pemerataan tabir surya (<em>sunscreen</em>) berbasis web yang mengintegrasikan <em>Computer Vision</em> dan <em>Generative AI</em>. Dokumen ini disusun sebagai luaran Tahap 1 Proyek Akhir Mandiri mata kuliah Rekayasa Perangkat Lunak (TIKB2503021) dan berfungsi sebagai kontrak acuan tunggal bagi tahap perancangan (Tahap 2 — SDD), konstruksi dan otomatisasi CI/CD (Tahap 3), serta demonstrasi akhir (Tahap 4).</p>
<p>Pembaca utama dokumen ini adalah: (1) dosen pengampu sebagai evaluator rubrik OBE; (2) pengembang tunggal sekaligus penulis dokumen sebagai pelaksana; dan (3) penguji sistem yang memverifikasi keselarasan antara spesifikasi kebutuhan dengan implementasi kode aktual pada repositori GitHub.</p>

<h2>1.2 Ruang Lingkup</h2>
<p><strong>SKINCHECK</strong> adalah aplikasi web <em>HealthTech / BeautyTech</em> yang dapat diakses langsung melalui peramban (<em>browser-only / zero-install</em>) tanpa memerlukan instalasi aplikasi seluler native. Ruang lingkup fungsional sistem meliputi:</p>
<ul>
  <li><strong>Autentikasi &amp; Verifikasi Multi-Kanal</strong>: Registrasi akun pelanggan dengan verifikasi kode OTP 6-digit melalui Bot Telegram (<code>@Kode_OTP_SkinCheckBot</code>), Resend Email API, serta layanan gateway WhatsApp Fonnte; dilengkapi login berbasis sesi aman dan reset kata sandi berbasis token.</li>
  <li><strong>Pemindaian Wajah Cerdas (Smart Camera HUD)</strong>: Pengambilan citra wajah langsung dari kamera depan peramban menggunakan WebRTC dengan panduan oval wajah (<em>Face Oval Guide</em>), pengecekan kualitas pencahayaan (<em>luminance</em>), validasi dimensi, serta animasi pemindai laser.</li>
  <li><strong>Evaluasi 4 Pilar Sawar Kulit Berbasis AI</strong>: Analisis kuantitatif (skor 0–100) untuk <em>Acne &amp; Pores</em>, <em>Redness (Eritema)</em>, <em>Texture Smoothness</em>, dan <em>Hyperpigmentation</em> menggunakan model <em>MobileNetV3 ONNX</em> dan <em>Computer Vision Engine</em> dengan mekanisme <em>fallback</em> otomatis.</li>
  <li><strong>Pemeriksaan Kerataan Sunscreen &amp; Heatmap</strong>: Estimasi persentase cakupan perlindungan tabir surya (0–100%), klasifikasi status kerataan, identifikasi area rawan yang terlewat (<em>low coverage areas</em>), dan pembangkitan citra visual <em>Heatmap</em> proteksi.</li>
  <li><strong>Riwayat Progres &amp; Kurasi Skincare</strong>: Penyimpanan riwayat scan terproteksi dengan akses gambar terkontrol kepemilikan (<em>ownership-scoped image streaming</em>) serta pencocokan rekomendasi produk skincare dari katalog berdasarkan masalah dominan pengguna.</li>
  <li><strong>Asisten Konsultasi AI Interaktif (Google Gemini Flash)</strong>: Chatbot cerdas sadar-konteks (<em>context-aware</em>) bagi pelanggan untuk berkonsultasi mengenai hasil scan dan bahan aktif skincare, serta konsol <em>Executive AI Assistant</em> bagi administrator untuk analitik bisnis instan.</li>
  <li><strong>Panel Administrasi Eksekutif &amp; Token Monitor</strong>: Dasbor KPI global, manajemen akun pelanggan, manajemen katalog produk, audit log pemindaian, pemantau konsumsi token Google Gemini secara langsung, serta pusat pengaturan API dinamis dengan uji koneksi AJAX.</li>
</ul>
<p><em>Di luar ruang lingkup versi ini</em>: aplikasi seluler native (Android/iOS), diagnosis klinis/resep obat keras pengganti dokter spesialis kulit (Sp.D.V.E.), serta pengukuran spektroskopi sinar ultraviolet (UV) langsung dengan kamera perangkat keras UV khusus (kamera standar RGB hanya menghasilkan estimasi visual AI disertai sanggahan non-UV).</p>

<h2>1.3 Definisi, Akronim, dan Singkatan</h2>
<table class="data-table">
  <thead>
    <tr><th style="width:22%">Istilah / Akronim</th><th>Definisi dalam Konteks SKINCHECK</th></tr>
  </thead>
  <tbody>
    <tr><td><strong>Skin Barrier</strong></td><td>Lapisan pelindung terluar kulit (<em>stratum korneum</em>) yang menjaga kelembapan dan menangkal iritan, polusi, serta patogen eksternal.</td></tr>
    <tr><td><strong>Eritema (Redness)</strong></td><td>Kemerahan pada permukaan kulit akibat pelebaran pembuluh kapiler yang mengindikasikan sensitivitas atau iritasi.</td></tr>
    <tr><td><strong>Computer Vision (CV)</strong></td><td>Cabang kecerdasan artifisial untuk mengekstraksi fitur visual citra wajah (tekstur, saturasi warna, kontras noda, dan kerataan lapisan).</td></tr>
    <tr><td><strong>ONNX</strong></td><td><em>Open Neural Network Exchange</em> — format standar terbuka untuk eksekusi model jaringan saraf tiruan (MobileNetV3) berlatensi rendah.</td></tr>
    <tr><td><strong>LLM</strong></td><td><em>Large Language Model</em> — model bahasa besar (Google Gemini Flash) yang menggerakkan asisten konsultasi skincare dan analitik admin.</td></tr>
    <tr><td><strong>HUD</strong></td><td><em>Heads-Up Display</em> — antarmuka overlay panduan oval wajah dan indikator kualitas pencahayaan pada viewfinder kamera web.</td></tr>
    <tr><td><strong>OTP</strong></td><td><em>One-Time Password</em> — kode verifikasi keamanan 6-digit sekali pakai yang dikirim melalui Telegram, Email Resend, atau WhatsApp Fonnte.</td></tr>
    <tr><td><strong>MVC</strong></td><td><em>Model-View-Controller</em> — pola arsitektur PHP yang memisahkan akses data (Model), presentasi (View), dan logika kendali (Controller).</td></tr>
    <tr><td><strong>SOLID</strong></td><td>Lima prinsip perancangan berorientasi objek (<em>Single Responsibility, Open/Closed, Liskov Substitution, Interface Segregation, Dependency Inversion</em>).</td></tr>
    <tr><td><strong>CI/CD</strong></td><td><em>Continuous Integration / Continuous Delivery</em> — otomatisasi linting sintaks dan eksekusi pengujian melalui GitHub Actions.</td></tr>
  </tbody>
</table>

<h2>1.4 Referensi</h2>
<ul>
  <li>Panduan Tugas Proyek Akhir Mandiri &amp; Rubrik Penilaian OBE, Mata Kuliah Rekayasa Perangkat Lunak (TIKB2503021), Program Studi S1 Teknik Informatika, STMIK IKMI Cirebon (Yudhistira Arie Wijaya, M.Kom., CDS.).</li>
  <li>IEEE Std 830-1998, <em>IEEE Recommended Practice for Software Requirements Specifications</em>.</li>
  <li>ISO/IEC/IEEE 29148:2018, <em>Systems and software engineering — Life cycle processes — Requirements engineering</em>.</li>
  <li>Repositori publik proyek: <code>github.com/satriafibisaputra18-droid/SkinCheck</code> (cabang <code>main</code>), mencakup <code>README.md</code>, <code>schema.sql</code>, <code>database_infinityfree.sql</code>, <code>app/</code>, <code>chatbot-server/</code>, <code>tests/</code>, dan <code>.github/workflows/main.yml</code>.</li>
  <li>Dokumentasi resmi: PHP 8.2, MySQL 8.0, Node.js 18 Express, ONNX Runtime, Google Gemini API, Telegram Bot API, Resend API, dan Fonnte API.</li>
</ul>

<h2>1.5 Gambaran Dokumen</h2>
<p>Bab 2 menyajikan latar belakang masalah, deskripsi produk, karakterisasi peran pengguna, lingkungan operasi, serta batasan desain. Bab 3 merinci 34 kebutuhan fungsional (F-01 hingga F-34) dalam format <em>user story</em>, kriteria penerimaan, dan status implementasi. Bab 4 menetapkan 20 kebutuhan non-fungsional (NF-01 hingga NF-20) yang terukur. Bab 5 menguraikan model data relasional 8 tabel. Bab 6 memaparkan antarmuka sistem. Bab 7 merangkum keterbatasan yang diketahui dan rencana pengembangan. Lampiran A–C memuat daftar lengkap tabel basis data, glosarium, dan matriks ketertelusuran kebutuhan.</p>

<!-- ==================== BAB 2: GAMBARAN UMUM ==================== -->
<h1 class="chapter-start">2. Gambaran Umum</h1>

<h2>2.1 Latar Belakang Masalah</h2>
<p>Kesehatan sawar kulit wajah dan perlindungan terhadap sinar ultraviolet (UV) merupakan aspek mendasar kesehatan dermatologi preventif. Namun, masyarakat luas menghadapi tiga kesenjangan nyata dalam merawat kulit secara sehari-hari:</p>
<ol>
  <li><strong>Biaya dan Aksesibilitas Konsultasi Klinis</strong>: Pemeriksaan kulit menggunakan alat penganalisis kulit profesional (<em>skin analyzer</em>) di klinik dermatologi berbiaya tinggi dan tidak selalu mudah diakses oleh mahasiswa, pekerja muda, maupun masyarakat umum di luar kota besar.</li>
  <li><strong>Keterlambatan Deteksi Dini Kondisi Sawar Kulit</strong>: Kerusakan mikroskopis pada sawar kulit — seperti iritasi vaskular (kemerahan), penurunan kehalusan tekstur, peningkatan komedo, atau hiperpigmentasi awal — sering kali tidak terdeteksi secara kasat mata sampai berkembang menjadi peradangan berat.</li>
  <li><strong>Ketidakmerataan Aplikasi Tabir Surya (Under-Application)</strong>: Studi dermatologi menunjukkan lebih dari 70% pengguna tabir surya tidak mengoleskan sunscreen secara merata, terutama pada sudut hidung, area sekitar mata, dan garis rahang, sehingga proteksi terhadap sinar UV-A dan UV-B menjadi tidak optimal.</li>
</ol>
<p><strong>SKINCHECK</strong> menjawab ketiga masalah tersebut dengan menghadirkan platform skrining mandiri berbasis web yang memanfaatkan kamera peramban standar, inferensi <em>Computer Vision</em> berkecepatan tinggi, serta asisten edukasi berbasis Google Gemini AI yang memahami riwayat kondisi kulit setiap pengguna secara personal.</p>

<h2>2.2 Deskripsi Produk</h2>
<p>SKINCHECK dibangun sebagai aplikasi web full-stack berbasis <strong>PHP 8.1+ MVC</strong> dengan basis data <strong>MySQL 8.0</strong>, dilengkapi mesin inferensi <strong>Computer Vision (MobileNetV3 ONNX / PHP GD CV Engine)</strong> dan layanan <strong>Node.js Express Microservice</strong> yang menghubungkan widget chatbot dengan <strong>Google Gemini Flash API</strong>.</p>
<p>Alur nilai utama produk mencakup: (1) pendaftaran &amp; verifikasi akun OTP; (2) pemindaian wajah melalui Smart Camera HUD untuk memperoleh skor 4 pilar sawar kulit; (3) pemeriksaan pemerataan tabir surya beserta visualisasi <em>Heatmap</em>; (4) pelacakan riwayat perkembangan kulit dan penerimaan kurasi produk skincare; serta (5) konsultasi interaktif dengan AI Assistant sadar-konteks.</p>

<h2>2.3 Pengguna dan Peran</h2>
<table class="data-table">
  <thead>
    <tr><th style="width:22%">Peran</th><th>Deskripsi dan Hak Akses</th></tr>
  </thead>
  <tbody>
    <tr>
      <td><strong>Guest (Tamu)</strong></td>
      <td>Pengunjung tanpa sesi login. Dapat melihat halaman beranda edukatif, ringkasan fitur 4 pilar, alur cara kerja, mendaftar akun pelanggan baru, memverifikasi OTP, masuk (login), serta mengajukan reset kata sandi. Tidak dapat mengakses rute portal pelanggan maupun admin.</td>
    </tr>
    <tr>
      <td><strong>Customer (Pelanggan)</strong></td>
      <td>Pengguna terdaftar (<code>role = 'customer'</code>). Dapat mengakses dasbor pribadi, menjalankan Face Scan HUD dan Sunscreen Check, melihat detail diagnostik dan citra heatmap miliknya sendiri, memantau riwayat progres, melihat rekomendasi produk, mengelola profil &amp; avatar, serta berkonsultasi dengan AI Skincare Assistant.</td>
    </tr>
    <tr>
      <td><strong>Admin (Eksekutif)</strong></td>
      <td>Pengelola sistem (<code>role = 'admin'</code>). Masuk melalui gerbang login admin terpisah, melihat KPI makro (total pelanggan, total scan, katalog produk, skor rata-rata global), memantau konsumsi token Google Gemini secara real-time, mengelola daftar customer dan katalog kosmetik, meninjau audit log pemindaian, berkonsultasi dengan Executive AI Analytics, serta mengelola konfigurasi API dinamis.</td>
    </tr>
  </tbody>
</table>

<h2>2.4 Lingkungan Operasi</h2>
<table class="data-table">
  <thead>
    <tr><th style="width:26%">Komponen</th><th>Spesifikasi Aktual</th></tr>
  </thead>
  <tbody>
    <tr><td>Web Server &amp; Core App</td><td>Apache 2.4 (XAMPP lokal) / Vercel Serverless PHP Runtime, PHP 8.1+ (ekstensi GD, PDO, PDO_MySQL, cURL, Mbstring).</td></tr>
    <tr><td>Basis Data</td><td>MySQL Server 8.0 (InnoDB, utf8mb4), database <code>skincheck</code> (8 tabel operasional).</td></tr>
    <tr><td>AI Vision Engine</td><td>Python 3.10+ dengan ONNX Runtime (<code>app/services/ai_engine/predict.py</code>, model MobileNetV3) + fallback PHP GD Computer Vision Engine.</td></tr>
    <tr><td>Chatbot Microservice</td><td>Node.js 18+ dengan Express (<code>chatbot-server/server.js</code>) dan REST integration ke Google Gemini Flash API.</td></tr>
    <tr><td>Peramban Klien</td><td>Chrome, Edge, Firefox, atau Safari modern dengan dukungan WebRTC (<code>getUserMedia</code>) dan HTML5 Canvas.</td></tr>
    <tr><td>Layanan Eksternal</td><td>Google Gemini API, Telegram Bot API, Resend Email API, Fonnte WhatsApp API (semua melalui HTTPS).</td></tr>
    <tr><td>CI/CD DevOps</td><td>GitHub Actions (<code>ubuntu-latest</code>, PHP 8.2, Node.js 18, Python 3.10) melalui <code>.github/workflows/main.yml</code>.</td></tr>
  </tbody>
</table>

<h2>2.5 Batasan Desain dan Implementasi</h2>
<ul>
  <li><strong>Non-Medical Screening Boundary</strong>: Sistem secara tegas membatasi seluruh narasi keluaran AI agar tidak mengeluarkan diagnosis penyakit klinis (<em>terminology compliance</em>), dan menampilkan sanggahan medis bahwa platform bukan pengganti dokter spesialis kulit (Sp.D.V.E.).</li>
  <li><strong>Estimasi Visual Non-UV</strong>: Pada perangkat konsumen berkamera RGB standar, pemeriksaan tabir surya menggunakan algoritma estimasi visual berbasis AI dan wajib menyertakan sanggahan eksplisit non-pengukuran UV langsung; abstraksi <code>UVSunscreenAnalysisProvider</code> disiapkan bila perangkat kamera UV tersedia.</li>
  <li><strong>Isolasi Berkas Citra Privat</strong>: Seluruh foto wajah hasil pemindaian dan peta panas <em>Heatmap</em> disimpan di direktori privat <code>storage/scans/</code> yang dilindungi <code>.htaccess</code> (deny direct access) dan hanya dapat dialirkan melalui endpoint PHP terkontrol kepemilikan.</li>
  <li><strong>Kontrak Antarmuka SOLID</strong>: Layanan analisis sawar kulit dan sunscreen diwajibkan bergantung pada abstraksi antarmuka (<code>SkinAnalysisProviderInterface</code>, <code>SunscreenAnalysisProviderInterface</code>) agar pergantian mesin AI tidak memengaruhi kontroler.</li>
</ul>

<h2>2.6 Asumsi dan Ketergantungan</h2>
<ul>
  <li>Pengguna memberikan izin akses kamera (webcam) pada peramban dan berada pada ruangan dengan pencahayaan depan yang cukup merata.</li>
  <li>Administrator telah mengisikan API Key yang valid (Google Gemini, Telegram Bot, Resend, atau Fonnte) pada halaman Pengaturan Sistem; bila kunci API Gemini tidak tersedia atau mengalami batas kuota (HTTP 429), sistem otomatis beralih ke mekanisme <em>graceful fallback</em>.</li>
  <li>Untuk inferensi ONNX lokal, runtime Python dan berkas model terlatih tersedia; jika lingkungan hosting shared (seperti InfinityFree / Vercel) tidak mengizinkan eksekusi subprocess Python, sistem secara otomatis mengandalkan <code>ComputerVisionSkinProvider</code> berbasis PHP GD.</li>
</ul>

<!-- ==================== BAB 3: KEBUTUHAN FUNGSIONAL ==================== -->
<h1 class="chapter-start">3. Kebutuhan Fungsional</h1>
<p>Kebutuhan fungsional berikut dirumuskan dalam format <em>User Story</em> dan <em>Kriteria Penerimaan</em> yang terukur. Kolom <strong>Status</strong> menyatakan kondisi implementasi aktual pada cabang <code>main</code> repositori GitHub.</p>

<h2>3.1 Autentikasi, OTP Multi-Kanal, dan Manajemen Akun</h2>
<table class="data-table">
  <thead>
    <tr><th style="width:7%">ID</th><th style="width:29%">User Story</th><th style="width:48%">Kriteria Penerimaan (Acceptance Criteria)</th><th style="width:16%">Status</th></tr>
  </thead>
  <tbody>
    <tr>
      <td><strong>F-01</strong></td>
      <td>Sebagai tamu, saya ingin melihat beranda produk beserta ringkasan 4 pilar kulit dan cara kerja, agar saya memahami manfaat SkinCheck sebelum mendaftar.</td>
      <td>Halaman utama (<code>/</code>) memuat bagian Hero, kartu 4 indikator (Jerawat, Kemerahan, Tekstur, Proteksi UV), 4 kartu fitur utama, panduan 3 langkah, dan tombol Masuk/Daftar Akun.</td>
      <td>Terimplementasi</td>
    </tr>
    <tr>
      <td><strong>F-02</strong></td>
      <td>Sebagai calon pelanggan, saya ingin mendaftar akun menggunakan nama, email, dan kata sandi, agar saya memiliki akun pribadi.</td>
      <td>Registrasi memvalidasi keunikan email, meng-hash kata sandi memakai <code>password_hash(..., PASSWORD_BCRYPT)</code>, menyimpan data pendaftaran sementara pada sesi, dan mengarahkan ke halaman verifikasi OTP.</td>
      <td>Terimplementasi</td>
    </tr>
    <tr>
      <td><strong>F-03</strong></td>
      <td>Sebagai pendaftar baru, saya ingin memverifikasi akun menggunakan kode OTP 6-digit melalui Bot Telegram, Email Resend, atau WhatsApp Fonnte, agar akun saya terlindungi.</td>
      <td>Kode OTP 6-digit acak diterbitkan dengan masa berlaku terkonfigurasi dan cooldown kirim ulang; mendukung verifikasi ketik kode maupun <em>1-click instant verification link</em> (<code>/auth/verifyDirect</code>).</td>
      <td>Terimplementasi</td>
    </tr>
    <tr>
      <td><strong>F-04</strong></td>
      <td>Sebagai pendaftar baru, saya ingin meminta pengiriman ulang OTP atau membatalkan pendaftaran jika salah memasukkan tujuan pengiriman.</td>
      <td>Endpoint <code>/auth/resendOtp</code> menegakkan cooldown pengiriman ulang; <code>/auth/cancelRegistration</code> membersihkan sesi registrasi tertunda secara aman.</td>
      <td>Terimplementasi</td>
    </tr>
    <tr>
      <td><strong>F-05</strong></td>
      <td>Sebagai pengguna terdaftar, saya ingin masuk ke akun saya menggunakan email dan kata sandi, agar dapat mengakses fitur sesuai peran saya.</td>
      <td>Login memverifikasi hash bcrypt via <code>password_verify()</code>, membuat sesi terautentikasi berdasarkan peran (<code>customer</code> ke <code>/customer/dashboard</code>; <code>admin</code> melalui <code>/admin/login</code> ke <code>/admin/dashboard</code>), serta mencatat entri pada <code>activity_logs</code>.</td>
      <td>Terimplementasi</td>
    </tr>
    <tr>
      <td><strong>F-06</strong></td>
      <td>Sebagai pengguna yang lupa kata sandi, saya ingin menerima tautan pemulihan kata sandi, agar dapat menyetel sandi baru dengan aman.</td>
      <td><code>/auth/forgotPassword</code> membangkitkan <code>reset_token</code> berjangka waktu (<code>reset_expires_at</code>) dan mengirimkannya via Email/Telegram; <code>/auth/resetPassword</code> memvalidasi token, memperbarui hash sandi, dan menghapus token lama.</td>
      <td>Terimplementasi</td>
    </tr>
    <tr>
      <td><strong>F-07</strong></td>
      <td>Sebagai pengguna terautentikasi, saya ingin keluar (logout) dari aplikasi, agar sesi saya tidak dapat digunakan pihak lain.</td>
      <td><code>/auth/logout</code> menghancurkan variabel sesi aktif dan cookie sesi, lalu mengarahkan pengguna kembali ke halaman login/beranda.</td>
      <td>Terimplementasi</td>
    </tr>
    <tr>
      <td><strong>F-08</strong></td>
      <td>Sebagai pelanggan, saya ingin memperbarui nama, email, Telegram Chat ID, serta foto profil (avatar), agar informasi akun saya selalu mutakhir.</td>
      <td><code>/customer/profile</code> memvalidasi format unggahan avatar (JPG/PNG/WEBP), menyimpannya ke <code>public/uploads/avatars/</code>, dan memperbarui kolom <code>profile_image</code> pada tabel <code>users</code>.</td>
      <td>Terimplementasi</td>
    </tr>
  </tbody>
</table>

<h2>3.2 Pemindaian Sawar Kulit (4 Pilar Klinis &amp; Smart Camera HUD)</h2>
<table class="data-table">
  <thead>
    <tr><th style="width:7%">ID</th><th style="width:29%">User Story</th><th style="width:48%">Kriteria Penerimaan (Acceptance Criteria)</th><th style="width:16%">Status</th></tr>
  </thead>
  <tbody>
    <tr>
      <td><strong>F-09</strong></td>
      <td>Sebagai pelanggan, saya ingin menggunakan kamera depan peramban yang dilengkapi panduan oval wajah dan tips pencahayaan, agar foto wajah saya memenuhi standar analisis.</td>
      <td>Halaman <code>/customer/scanFace</code> mengaktifkan WebRTC webcam, menampilkan <em>Face Oval HUD</em>, panduan 4 langkah (Aktifkan Kamera, Cahaya Depan Merata, Posisi Wajah Ideal 30–50 cm, Ambil &amp; Kirim Foto), dan validasi kelayakan cahaya.</td>
      <td>Terimplementasi</td>
    </tr>
    <tr>
      <td><strong>F-10</strong></td>
      <td>Sebagai sistem, saya ingin memvalidasi setiap citra unggahan sebelum dikirim ke model AI, agar gambar buram, rusak, atau tidak valid ditolak dengan ramah.</td>
      <td><code>ImageValidator</code> memeriksa tipe MIME nyata, batas ukuran berkas, dimensi minimum, dan kelayakan bacaan piksel sebelum menyimpan berkas melalui <code>ImageStorage</code>.</td>
      <td>Terimplementasi</td>
    </tr>
    <tr>
      <td><strong>F-11</strong></td>
      <td>Sebagai pelanggan, saya ingin mendapatkan evaluasi kuantitatif 4 pilar sawar kulit (Jerawat &amp; Pori, Kemerahan, Tekstur, Pigmentasi) beserta skor keseluruhan 0–100.</td>
      <td><code>SkinAnalysisService</code> memproses foto melalui <code>OnnxSkinAnalysisProvider</code> (MobileNetV3) atau <code>ComputerVisionSkinProvider</code> dan menyimpan <code>acne_score</code>, <code>redness_score</code>, <code>texture_score</code>, <code>hyperpigmentation_score</code>, dan <code>overall_score</code> ke tabel <code>analysis_results</code>.</td>
      <td>Terimplementasi</td>
    </tr>
    <tr>
      <td><strong>F-12</strong></td>
      <td>Sebagai pelanggan, saya ingin membaca ringkasan observasi visual AI yang mudah dipahami tanpa istilah vonis penyakit medis.</td>
      <td>Objek <code>AnalysisResult</code> membangkitkan indikasi visual dan catatan naratif edukatif yang lolos pengujian kepatuhan terminologi non-medis (bebas kata diagnosis penyakit klinis).</td>
      <td>Terimplementasi</td>
    </tr>
    <tr>
      <td><strong>F-13</strong></td>
      <td>Sebagai pelanggan, saya ingin dapat mengulang proses analisis jika terjadi gangguan sementara pada mesin AI, tanpa harus memotret ulang wajah saya.</td>
      <td><code>SkinAnalysisService</code> melakukan percobaan ulang otomatis (maks 2 kali); jika tetap gagal, status scan ditandai <code>failed</code> dan endpoint <code>/scan/retryAnalysis</code> memungkinkan eksekusi ulang atas citra tersimpan.</td>
      <td>Terimplementasi</td>
    </tr>
  </tbody>
</table>

<h2>3.3 Pemeriksaan Kerataan Sunscreen &amp; Visualisasi Heatmap</h2>
<table class="data-table">
  <thead>
    <tr><th style="width:7%">ID</th><th style="width:29%">User Story</th><th style="width:48%">Kriteria Penerimaan (Acceptance Criteria)</th><th style="width:16%">Status</th></tr>
  </thead>
  <tbody>
    <tr>
      <td><strong>F-14</strong></td>
      <td>Sebagai pelanggan, saya ingin mengambil foto wajah setelah mengoleskan sunscreen untuk mengevaluasi kerataan proteksinya.</td>
      <td>Halaman <code>/customer/sunscreenCheck</code> menyediakan pengambilan foto kamera HUD dan pengiriman payload ke <code>/sunscreen/submitCheck</code>.</td>
      <td>Terimplementasi</td>
    </tr>
    <tr>
      <td><strong>F-15</strong></td>
      <td>Sebagai pelanggan, saya ingin mengetahui persentase skor cakupan (0–100%), kategori kerataan, dan daftar area wajah yang kurang terlindungi.</td>
      <td><code>SunscreenAnalysisService</code> menghasilkan <code>coverage_score</code>, <code>coverage_status</code> (Sangat Merata / Cukup Merata / Kurang Merata), <code>low_coverage_areas</code>, dan skor <em>confidence</em>.</td>
      <td>Terimplementasi</td>
    </tr>
    <tr>
      <td><strong>F-16</strong></td>
      <td>Sebagai pelanggan, saya ingin melihat visualisasi peta panas (Heatmap) proteksi tabir surya pada wajah saya.</td>
      <td><code>RGBSunscreenAnalysisProvider</code> membangkitkan citra <em>Heatmap</em> gradien warna, menyimpannya ke direktori privat <code>storage/scans/{{userId}}/heatmaps/</code>, dan menayangkannya secara aman via <code>/sunscreen/serveHeatmap</code>.</td>
      <td>Terimplementasi</td>
    </tr>
    <tr>
      <td><strong>F-17</strong></td>
      <td>Sebagai sistem, saya ingin menyertakan sanggahan non-UV dan peringatan keyakinan rendah bila kualitas cahaya kurang mendukung.</td>
      <td>Hasil RGB wajib menampilkan sanggahan: <em>"Hasil ini merupakan estimasi visual berbasis AI dan bukan pengukuran UV langsung."</em> Bila <code>confidence &lt; 0.60</code>, flag <em>low confidence</em> aktif disertai saran pencahayaan.</td>
      <td>Terimplementasi</td>
    </tr>
  </tbody>
</table>

<h2>3.4 Riwayat Progres, Akses Citra Aman, dan Rekomendasi Produk</h2>
<table class="data-table">
  <thead>
    <tr><th style="width:7%">ID</th><th style="width:29%">User Story</th><th style="width:48%">Kriteria Penerimaan (Acceptance Criteria)</th><th style="width:16%">Status</th></tr>
  </thead>
  <tbody>
    <tr>
      <td><strong>F-18</strong></td>
      <td>Sebagai pelanggan, saya ingin melihat dasbor ringkasan skor kondisi kulit terakhir, status proteksi UV hari ini, dan 5 riwayat scan terbaru.</td>
      <td><code>/customer/dashboard</code> menampilkan skor lingkaran (0–100), 4 bar progres indikator, kartu Proteksi UV (SPF 30+ / PA++++ / 2 Jari), pintasan 4 menu cepat, dan tabel riwayat terbaru.</td>
      <td>Terimplementasi</td>
    </tr>
    <tr>
      <td><strong>F-19</strong></td>
      <td>Sebagai pelanggan, saya ingin menelusuri seluruh riwayat pemindaian lengkap dengan jaminan foto wajah saya tidak dapat diakses pengguna lain.</td>
      <td><code>/customer/history</code> menampilkan total pemeriksaan, rata-rata skor, dan daftar lengkap kronologis; endpoint <code>/scan/serveImage</code> memverifikasi kepemilikan <code>user_id</code> (atau peran admin) sebelum mengalirkan berkas citra.</td>
      <td>Terimplementasi</td>
    </tr>
    <tr>
      <td><strong>F-20</strong></td>
      <td>Sebagai pelanggan, saya ingin menerima rekomendasi produk skincare yang cocok dengan masalah kulit dominan pada hasil scan saya.</td>
      <td><code>Product::getProductsByConcern()</code> mencocokkan indikator terlemah hasil scan dengan katalog produk aktif di tabel <code>products</code> lengkap dengan merek, kandungan bahan aktif, dan panduan pemakaian.</td>
      <td>Terimplementasi</td>
    </tr>
  </tbody>
</table>

<h2>3.5 AI Skincare Assistant (Google Gemini Context-Aware Chatbot)</h2>
<table class="data-table">
  <thead>
    <tr><th style="width:7%">ID</th><th style="width:29%">User Story</th><th style="width:48%">Kriteria Penerimaan (Acceptance Criteria)</th><th style="width:16%">Status</th></tr>
  </thead>
  <tbody>
    <tr>
      <td><strong>F-21</strong></td>
      <td>Sebagai pelanggan, saya ingin berkonsultasi secara interaktif dengan asisten AI skincare melalui widget terapung maupun halaman Konsultasi AI.</td>
      <td>Widget chat (<code>templates/chatbot_widget.php</code>) dan halaman <code>/customer/aiAssistant</code> mengirim pertanyaan ke <code>/chatbot/chat</code> dan merender jawaban terformat secara real-time.</td>
      <td>Terimplementasi</td>
    </tr>
    <tr>
      <td><strong>F-22</strong></td>
      <td>Sebagai pelanggan, saya ingin asisten AI memahami hasil scan terakhir saya agar saran urutan skincare dan bahan aktif relevan dengan kondisi kulit saya.</td>
      <td><code>ChatbotController</code> menyuntikkan skor 4 pilar terbaru, skor keseluruhan, serta daftar produk relevan dari basis data ke dalam <em>System Prompt</em> sebelum memanggil Google Gemini API.</td>
      <td>Terimplementasi</td>
    </tr>
    <tr>
      <td><strong>F-23</strong></td>
      <td>Sebagai pelanggan, saya ingin mendapatkan jawaban edukatif yang aman dan tidak menyerupai resep obat medis keras.</td>
      <td><em>System Prompt</em> membatasi AI pada edukasi perawatan kulit non-klinis (mis. Niacinamide, Ceramide, Centella Asiatica, Hyaluronic Acid) dan mengarahkan kasus parah ke dokter spesialis kulit.</td>
      <td>Terimplementasi</td>
    </tr>
    <tr>
      <td><strong>F-24</strong></td>
      <td>Sebagai pengguna, saya ingin tetap menerima pesan yang santun dan jelas apabila kuota API Gemini habis atau server sedang terganggu.</td>
      <td>Sistem mendukung model cadangan (<em>fallback model</em>) pada konfigurasi <code>settings</code>; apabila semua model gagal atau terkena HTTP 429, chatbot mengembalikan pesan ramah tanpa menyebabkan error halaman.</td>
      <td>Terimplementasi</td>
    </tr>
  </tbody>
</table>

<h2>3.6 Panel Administrator Eksekutif, Token Monitor &amp; API Gateway</h2>
<table class="data-table">
  <thead>
    <tr><th style="width:7%">ID</th><th style="width:29%">User Story</th><th style="width:48%">Kriteria Penerimaan (Acceptance Criteria)</th><th style="width:16%">Status</th></tr>
  </thead>
  <tbody>
    <tr>
      <td><strong>F-25</strong></td>
      <td>Sebagai admin, saya ingin melihat dasbor eksekutif berisi KPI makro (Customers, Analysis Scans, Skincare Catalogue, dan Average Health Score).</td>
      <td><code>/admin/dashboard</code> menghitung dan menampilkan seluruh metrik KPI langsung dari basis data MySQL tanpa angka buatan.</td>
      <td>Terimplementasi</td>
    </tr>
    <tr>
      <td><strong>F-26</strong></td>
      <td>Sebagai admin, saya ingin mengelola daftar akun pelanggan terdaftar (tambah, lihat, ubah, hapus).</td>
      <td>Tab <em>Customer Registry</em> pada dasbor admin menampilkan ID, nama, email, tanggal registrasi, serta tombol tambah/edit/hapus akun pelanggan.</td>
      <td>Terimplementasi</td>
    </tr>
    <tr>
      <td><strong>F-27</strong></td>
      <td>Sebagai admin, saya ingin mengelola katalog produk skincare agar rekomendasi pelanggan selalu akurat.</td>
      <td>Tab <em>Cosmetics Catalog</em> menyediakan pengelolaan data merek, nama produk, kategori, target masalah kulit, tipe kulit, harga, bahan aktif, dan panduan pakai pada tabel <code>products</code>.</td>
      <td>Terimplementasi</td>
    </tr>
    <tr>
      <td><strong>F-28</strong></td>
      <td>Sebagai admin, saya ingin meninjau log audit pemindaian wajah seluruh pengguna untuk pemantauan kualitas sistem.</td>
      <td>Tab <em>Scan Audits</em> menampilkan riwayat seluruh sesi pemindaian pengguna beserta waktu, skor, dan status pemrosesan.</td>
      <td>Terimplementasi</td>
    </tr>
    <tr>
      <td><strong>F-29</strong></td>
      <td>Sebagai admin, saya ingin memantau konsumsi token Google Gemini API secara real-time (Total Token, Input/Prompt, Output/Response, dan Total Panggilan).</td>
      <td>Kartu <em>Google Gemini Token Monitor</em> pada dasbor membaca agregasi tabel <code>ai_token_usage</code> via <code>AiTokenUsage::getTokenMetrics()</code>, menampilkan model aktif (mis. <code>gemini-flash-lite-latest</code>), token hari ini, dan total panggilan.</td>
      <td>Terimplementasi</td>
    </tr>
    <tr>
      <td><strong>F-30</strong></td>
      <td>Sebagai admin, saya ingin berkonsultasi dengan Executive AI Assistant yang terhubung ke ringkasan database operasional untuk memperoleh wawasan bisnis cepat.</td>
      <td>Halaman <code>/admin/aiAssistant</code> menyediakan konsol AI berizin <em>ADMIN CLEARANCE</em> yang dapat menganalisis tren keluhan kulit pelanggan dan performa katalog produk.</td>
      <td>Terimplementasi</td>
    </tr>
    <tr>
      <td><strong>F-31</strong></td>
      <td>Sebagai admin, saya ingin mengonfigurasi API Key Google Gemini, model utama, model fallback, dan parameter temperatur langsung dari UI tanpa mengubah kode.</td>
      <td><code>/admin/settings</code> menyimpan konfigurasi pada tabel <code>settings</code> dengan penandaan <code>is_secret = 1</code> dan penyamaran nilai kunci (<em>masked</em>) di antarmuka.</td>
      <td>Terimplementasi</td>
    </tr>
    <tr>
      <td><strong>F-32</strong></td>
      <td>Sebagai admin, saya ingin mengatur Bot Token Telegram, Chat ID, Resend Email API, dan Fonnte WhatsApp API secara terpusat.</td>
      <td>Formulir pengaturan dikelompokkan per kategori (<code>telegram</code>, <code>email</code>, <code>fonnte</code>, <code>gemini</code>, <code>security</code>) melalui <code>Setting::getAllGrouped()</code>.</td>
      <td>Terimplementasi</td>
    </tr>
    <tr>
      <td><strong>F-33</strong></td>
      <td>Sebagai admin, saya ingin mengatur parameter keamanan OTP seperti masa kedaluwarsa dan durasi cooldown pengiriman ulang.</td>
      <td>Parameter keamanan tersimpan di grup <code>security</code> pada tabel <code>settings</code> dan dibaca dinamis oleh <code>AuthController</code>.</td>
      <td>Terimplementasi</td>
    </tr>
    <tr>
      <td><strong>F-34</strong></td>
      <td>Sebagai admin, saya ingin melakukan pengujian koneksi langsung (Live AJAX Test) terhadap Telegram, Resend, Fonnte, Gemini, dan NVIDIA sebelum menerapkannya.</td>
      <td>Endpoint <code>/admin/testApiKey</code> memanggil <code>Setting::testTelegram()</code>, <code>testResend()</code>, <code>testFonnte()</code>, <code>testGemini()</code>, dan <code>testNvidia()</code> serta menampilkan status berhasil/gagal secara instan.</td>
      <td>Terimplementasi</td>
    </tr>
  </tbody>
</table>

<!-- ==================== BAB 4: KEBUTUHAN NON-FUNGSIONAL ==================== -->
<h1 class="chapter-start">4. Kebutuhan Non-Fungsional</h1>
<p>Kebutuhan non-fungsional berikut ditetapkan dengan target kualitas yang terukur dan menjadi tolok ukur desain arsitektur serta pengujian sistem.</p>

<h2>4.1 Kinerja</h2>
<table class="data-table">
  <thead>
    <tr><th style="width:10%">ID</th><th style="width:26%">Kebutuhan</th><th>Target / Kriteria Uji</th></tr>
  </thead>
  <tbody>
    <tr><td><strong>NF-01</strong></td><td>Latensi inferensi sawar kulit</td><td>Pemrosesan citra oleh <code>ComputerVisionSkinProvider</code> / <code>OnnxSkinAnalysisProvider</code> selesai dalam &le; 1,5 detik pada perangkat standar lokal (terbukti 42 test suite berjalan total &lt; 2 detik).</td></tr>
    <tr><td><strong>NF-02</strong></td><td>Latensi estimasi sunscreen</td><td>Segmentasi warna RGB dan pembangkitan berkas citra <em>Heatmap</em> selesai dalam &le; 2,0 detik per pemindaian.</td></tr>
    <tr><td><strong>NF-03</strong></td><td>Waktu respons Chatbot Gemini</td><td>Respons pertama asisten AI melalui API Google Gemini Flash diterima dalam &le; 3,0 detik pada koneksi broadband standar (bergantung latensi API eksternal).</td></tr>
    <tr><td><strong>NF-04</strong></td><td>Waktu muat halaman utama</td><td>Halaman beranda, dasbor pelanggan, dan dasbor admin dirender server PHP dalam &lt; 1,0 detik pada server lokal Apache.</td></tr>
  </tbody>
</table>

<h2>4.2 Keamanan dan Privasi</h2>
<table class="data-table">
  <thead>
    <tr><th style="width:10%">ID</th><th style="width:26%">Kebutuhan</th><th>Target / Kriteria Uji</th></tr>
  </thead>
  <tbody>
    <tr><td><strong>NF-05</strong></td><td>Hashing kata sandi</td><td>Seluruh kata sandi pengguna disimpan menggunakan <code>PASSWORD_BCRYPT</code>; tidak pernah ada kata sandi plaintext di basis data maupun berkas log.</td></tr>
    <tr><td><strong>NF-06</strong></td><td>Perlindungan SQL Injection</td><td>Seluruh akses basis data melalui kelas <code>Database</code> wajib memakai PDO <em>Prepared Statements</em> dengan parameter binding.</td></tr>
    <tr><td><strong>NF-07</strong></td><td>Isolasi citra wajah privat</td><td>Berkas foto wajah di <code>storage/scans/</code> diblokir dari akses URL langsung via <code>.htaccess</code> dan hanya dapat dibaca melalui rute PHP tervalidasi kepemilikan (<code>serveImage</code> / <code>serveHeatmap</code>).</td></tr>
    <tr><td><strong>NF-08</strong></td><td>Kontrol akses berbasis peran</td><td><code>AuthGuard</code> memisahkan akses rute publik, pelanggan (<code>requireCustomer</code>), dan administrator (<code>requireAdmin</code>) secara ketat.</td></tr>
    <tr><td><strong>NF-09</strong></td><td>Penyamaran kredensial API</td><td>Seluruh kunci rahasia pada tabel <code>settings</code> (<code>is_secret = 1</code>) disamarkan pada tampilan formulir admin dan tidak diekspos ke kode JavaScript sisi klien.</td></tr>
    <tr><td><strong>NF-10</strong></td><td>Pencegahan XSS</td><td>Seluruh keluaran teks dinamis pada berkas View PHP disanitasi menggunakan <code>htmlspecialchars()</code> sebelum dirender ke DOM peramban.</td></tr>
  </tbody>
</table>

<h2>4.3 Keandalan dan Ketersediaan</h2>
<table class="data-table">
  <thead>
    <tr><th style="width:10%">ID</th><th style="width:26%">Kebutuhan</th><th>Target / Kriteria Uji</th></tr>
  </thead>
  <tbody>
    <tr><td><strong>NF-11</strong></td><td>Ketahanan kegagalan AI Vision</td><td><code>SkinAnalysisService</code> menerapkan percobaan ulang otomatis (maks 2 kali) dan menangkap <code>SkinAnalysisException</code> agar kegagalan inferensi tidak menghasilkan HTTP 500.</td></tr>
    <tr><td><strong>NF-12</strong></td><td>Graceful fallback LLM</td><td>Bila model Gemini utama gagal atau terkena batas kuota, sistem mencoba model fallback atau mengembalikan pesan informatif tanpa memutus sesi pengguna.</td></tr>
    <tr><td><strong>NF-13</strong></td><td>Integritas referensial data</td><td>Relasi antar-tabel MySQL dilindungi oleh <em>Foreign Key Constraints</em> (<code>ON DELETE CASCADE</code> untuk turunan scan, <code>ON DELETE SET NULL</code> untuk jejak audit).</td></tr>
  </tbody>
</table>

<h2>4.4 Kegunaan dan Kepatuhan Terminologi</h2>
<table class="data-table">
  <thead>
    <tr><th style="width:10%">ID</th><th style="width:26%">Kebutuhan</th><th>Target / Kriteria Uji</th></tr>
  </thead>
  <tbody>
    <tr><td><strong>NF-14</strong></td><td>Zero-install &amp; responsif</td><td>Seluruh fitur pemindaian, heatmap, dan konsultasi berjalan 100% di peramban desktop (1440px) maupun seluler (390px) tanpa plugin tambahan.</td></tr>
    <tr><td><strong>NF-15</strong></td><td>Kepatuhan terminologi non-medis</td><td>Seluruh catatan observasi kulit dan sanggahan sunscreen wajib lolos verifikasi otomatis (<em>Terminology Compliance</em>) agar bebas dari klaim diagnosis medis klinis.</td></tr>
  </tbody>
</table>

<h2>4.5 Keterpeliharaan, Prinsip SOLID, dan Keterujian</h2>
<table class="data-table">
  <thead>
    <tr><th style="width:10%">ID</th><th style="width:26%">Kebutuhan</th><th>Target / Kriteria Uji</th></tr>
  </thead>
  <tbody>
    <tr><td><strong>NF-16</strong></td><td>Kepatuhan arsitektur SOLID</td><td>Provider analisis wajah, sunscreen, dan deteksi wajah dipisahkan menggunakan 3 antarmuka PHP (<code>*Interface.php</code>) yang mendukung <em>Dependency Injection</em>.</td></tr>
    <tr><td><strong>NF-17</strong></td><td>Pengujian otomatis</td><td>Master runner <code>tests/run_all_tests.php</code> mengeksekusi minimal 42 kasus uji otomatis (21 analisis sawar kulit + 21 sunscreen) dengan status lulus 100%.</td></tr>
    <tr><td><strong>NF-18</strong></td><td>Pipeline CI/CD GitHub Actions</td><td>Workflow <code>.github/workflows/main.yml</code> menjalankan linting PHP 8.2, cek sintaks Node.js 18, kompilasi Python 3.10, pengujian otomatis, dan verifikasi artefak dokumen pada setiap push dengan status hijau.</td></tr>
  </tbody>
</table>

<h2>4.6 Portabilitas dan Kompatibilitas</h2>
<table class="data-table">
  <thead>
    <tr><th style="width:10%">ID</th><th style="width:26%">Kebutuhan</th><th>Target / Kriteria Uji</th></tr>
  </thead>
  <tbody>
    <tr><td><strong>NF-19</strong></td><td>Portabilitas lingkungan server</td><td>Aplikasi berjalan pada XAMPP Windows (lokal), hosting PHP shared (InfinityFree melalui <code>database_infinityfree.sql</code>), serta serverless (<code>vercel.json</code> &amp; <code>api/index.php</code>).</td></tr>
    <tr><td><strong>NF-20</strong></td><td>Konfigurasi non-hardcoded</td><td>Kredensial basis data dan API dipisahkan dari kode melalui <code>app/config/config.php</code>, berkas <code>.env</code> (diabaikan pada <code>.gitignore</code>), dan tabel <code>settings</code>.</td></tr>
  </tbody>
</table>

<!-- ==================== BAB 5: MODEL DATA ==================== -->
<h1 class="chapter-start">5. Model Data</h1>

<h2>5.1 Ringkasan Skema Basis Data</h2>
<p>Basis data SKINCHECK menggunakan <strong>MySQL 8.0 (InnoDB, utf8mb4_unicode_ci)</strong> dengan nama basis data <code>skincheck</code>. Instalasi lengkap mencakup <strong>8 tabel operasional</strong> dan <strong>4 foreign key aktif</strong>.</p>
<div class="callout">
  <strong>Catatan Ketelitian</strong>: Berkas <code>schema.sql</code> awal memuat 7 tabel inti, sedangkan berkas dump produksi <code>database_infinityfree.sql</code> dan model <code>AiTokenUsage.php</code> menambahkan tabel ke-8 yaitu <code>ai_token_usage</code> serta kolom tambahan pada tabel <code>users</code> (<code>profile_image</code>, <code>telegram_chat_id</code>, <code>telegram_username</code>, <code>reset_token</code>, <code>reset_expires_at</code>). Lampiran A dan ERD pada Dokumen SDD (Tahap 2) mendokumentasikan keseluruhan 8 tabel tersebut secara utuh.
</div>

<h2>5.2 Tabel per Domain</h2>
<table class="data-table">
  <thead>
    <tr><th style="width:24%">Domain</th><th style="width:32%">Tabel</th><th>Keterangan</th></tr>
  </thead>
  <tbody>
    <tr><td>Identitas &amp; Keamanan</td><td><code>users</code>, <code>activity_logs</code></td><td>Data akun pelanggan &amp; admin, profil, kredensial bcrypt, tautan Telegram, token reset sandi, dan jejak aktivitas audit IP.</td></tr>
    <tr><td>Pemindaian &amp; Visi AI</td><td><code>skin_scans</code>, <code>analysis_results</code>, <code>sunscreen_results</code></td><td>Rekaman sesi pemindaian wajah, skor 4 pilar sawar kulit + narasi AI, serta skor kerataan sunscreen + jalur citra heatmap.</td></tr>
    <tr><td>Katalog Rekomendasi</td><td><code>products</code></td><td>Katalog produk skincare terkurasi (merek, kategori, bahan aktif, target masalah kulit, tipe kulit, harga, dan panduan pakai).</td></tr>
    <tr><td>Telemetri AI &amp; Sistem</td><td><code>ai_token_usage</code>, <code>settings</code></td><td>Log konsumsi token Gemini API per permintaan (prompt/completion/total tokens, durasi ms) serta konfigurasi kunci API terpusat.</td></tr>
  </tbody>
</table>

<h2>5.3 Relasi Utama</h2>
<ul>
  <li><code>users</code> memiliki relasi <strong>1 : N</strong> ke <code>skin_scans</code> (<code>ON DELETE CASCADE</code>) dan <strong>1 : N</strong> ke <code>activity_logs</code> (<code>ON DELETE SET NULL</code> agar catatan audit tetap tersimpan meskipun akun dihapus).</li>
  <li><code>skin_scans</code> memiliki relasi <strong>1 : 0..1</strong> ke <code>analysis_results</code> dan <strong>1 : 0..1</strong> ke <code>sunscreen_results</code> (keduanya <code>ON DELETE CASCADE</code>).</li>
  <li><code>ai_token_usage</code> menyimpan referensi logis <code>user_id</code> dan <code>user_role</code> untuk menghitung statistik konsumsi token harian maupun kumulatif pada dasbor admin.</li>
</ul>

<!-- ==================== BAB 6: ANTARMUKA SISTEM ==================== -->
<h1 class="chapter-start">6. Antarmuka Sistem</h1>
<ul>
  <li><strong>6.1 Antarmuka Pengguna (UI)</strong>: Aplikasi web responsif berbahasa Indonesia dengan tema klinis bersih (latar terang <code>#F8FAFC</code>, aksen hijau zamrud klinis <code>#0D7366</code> &amp; teal <code>#14B8A6</code>), terdiri atas 18 halaman fungsional penuh dan 8 berkas template bersama (rincian inventaris layar dan spesifikasi Figma dijabarkan pada Bab 8 Dokumen SDD).</li>
  <li><strong>6.2 Antarmuka Perangkat Keras</strong>: Memanfaatkan kamera depan (webcam laptop atau kamera depan ponsel) melalui standar WebRTC peramban. Tidak memerlukan alat penganalisis kulit atau lampu UV eksternal pada mode RGB standar.</li>
  <li><strong>6.3 Antarmuka Perangkat Lunak</strong>: Driver PDO MySQL untuk basis data; subprocess CLI Python 3 untuk <code>predict.py</code> (ONNX Runtime); HTTP REST client (PHP cURL &amp; Node.js Express) untuk Google Gemini API, Telegram Bot API, Resend API, dan Fonnte API.</li>
  <li><strong>6.4 Antarmuka Komunikasi</strong>: Pertukaran data klien–server menggunakan HTTP/HTTPS dengan formulir standar, payload JSON AJAX (untuk chatbot, uji API admin, dan status OTP), serta streaming berkas biner terotorisasi untuk gambar scan dan heatmap.</li>
</ul>

<!-- ==================== BAB 7: BATASAN DAN ASUMSI ==================== -->
<h1 class="chapter-start">7. Batasan, Risiko, dan Pekerjaan Mendatang</h1>
<table class="data-table">
  <thead>
    <tr><th style="width:12%">Kode</th><th style="width:36%">Keterbatasan / Risiko yang Diketahui</th><th>Dampak &amp; Rencana Mitigasi Mendatang</th></tr>
  </thead>
  <tbody>
    <tr>
      <td><strong>B-01</strong></td>
      <td>Analisis sunscreen memakai kamera RGB standar (estimasi visual AI), bukan sensor kamera sinar UV.</td>
      <td>Akurasi dipengaruhi pencahayaan ruangan; dimitigasi dengan validasi <em>luminance</em>, sanggahan non-UV eksplisit, dan arsitektur siap-ekstensi <code>UVSunscreenAnalysisProvider</code>.</td>
    </tr>
    <tr>
      <td><strong>B-02</strong></td>
      <td><code>NullFaceDetector</code> menjadi implementasi bawaan saat pustaka deteksi wajah eksternal tidak dipasang di server PHP shared.</td>
      <td>Validasi orientasi mengandalkan panduan oval HUD di sisi klien; pada tahap lanjut dapat disambungkan ke MediaPipe Face Mesh di klien atau OpenCV Haar/DNN di backend.</td>
    </tr>
    <tr>
      <td><strong>B-03</strong></td>
      <td>Skrip uji <code>tests/test_email_service.php</code> dan <code>tests/test_chat.php</code> bergantung pada kredensial API asli dan basis data aktif sehingga tidak diikutsertakan dalam runner CI cloud.</td>
      <td>Runner CI menjalankan 42 kasus uji inti terisolasi melalui <code>tests/run_all_tests.php</code>; pengujian layanan eksternal dilakukan secara manual via tombol <em>Live AJAX Test</em> di panel admin.</td>
    </tr>
    <tr>
      <td><strong>B-04</strong></td>
      <td>Kunci API pada tabel <code>settings</code> disamarkan di UI (<code>is_secret = 1</code>), namun belum dienkripsi kolom-level (AES-256-GCM) di basis data.</td>
      <td>Menjadi prioritas peningkatan keamanan (<em>hardening</em>) sebelum peluncuran produksi berskala publik luas.</td>
    </tr>
  </tbody>
</table>

<!-- ==================== LAMPIRAN A, B, C ==================== -->
<h1 class="chapter-start">Lampiran A — Daftar Tabel Basis Data</h1>
<table class="data-table">
  <thead>
    <tr><th style="width:6%">No</th><th style="width:22%">Nama Tabel</th><th style="width:18%">Domain</th><th style="width:14%">Sumber</th><th>Keterangan</th></tr>
  </thead>
  <tbody>
    <tr><td>1</td><td><code>users</code></td><td>Identitas</td><td>schema.sql + dump</td><td>Akun pelanggan &amp; admin, hash bcrypt, avatar, Telegram ID, token reset sandi.</td></tr>
    <tr><td>2</td><td><code>skin_scans</code></td><td>Pemindaian</td><td>schema.sql</td><td>Sesi pemindaian wajah, jalur berkas privat, skor kualitas gambar, dan status.</td></tr>
    <tr><td>3</td><td><code>analysis_results</code></td><td>Visi AI</td><td>schema.sql</td><td>Skor 4 pilar sawar kulit (acne, redness, texture, hyperpigmentation), overall score, narasi AI.</td></tr>
    <tr><td>4</td><td><code>sunscreen_results</code></td><td>Visi AI</td><td>schema.sql</td><td>Persentase cakupan proteksi, status kerataan, path heatmap, area rawan, metode &amp; tipe hardware.</td></tr>
    <tr><td>5</td><td><code>products</code></td><td>Katalog</td><td>schema.sql</td><td>Katalog produk skincare aktif untuk mesin rekomendasi berbasis keluhan dominan.</td></tr>
    <tr><td>6</td><td><code>activity_logs</code></td><td>Audit</td><td>schema.sql</td><td>Jejak aktivitas autentikasi dan operasional beserta alamat IP pengguna.</td></tr>
    <tr><td>7</td><td><code>ai_token_usage</code></td><td>Telemetri AI</td><td>dump + Model</td><td>Riwayat pemakaian token Google Gemini (prompt, completion, total, durasi ms, model, status).</td></tr>
    <tr><td>8</td><td><code>settings</code></td><td>Konfigurasi</td><td>schema.sql</td><td>Pengaturan dinamis API Key (Gemini, Telegram, Resend, Fonnte) dan parameter keamanan OTP.</td></tr>
  </tbody>
</table>

<h1>Lampiran B — Glosarium</h1>
<table class="data-table">
  <thead>
    <tr><th style="width:26%">Istilah</th><th>Arti dalam Konteks SKINCHECK</th></tr>
  </thead>
  <tbody>
    <tr><td>4 Pilar Sawar Kulit</td><td>Empat indikator kuantitatif evaluasi wajah: Jerawat &amp; Pori, Kemerahan (Eritema), Kehalusan Tekstur, dan Hiperpigmentasi.</td></tr>
    <tr><td>Heatmap Proteksi</td><td>Citra pemetaan gradien warna yang menvisualisasikan tingkat sebaran lapisan sunscreen pada permukaan wajah.</td></tr>
    <tr><td>Context Injection</td><td>Teknik menyisipkan metadata skor scan terakhir pengguna ke dalam <em>System Prompt</em> LLM agar jawaban chatbot bersifat personal.</td></tr>
    <tr><td>Ownership-Scoped Stream</td><td>Mekanisme pengiriman berkas gambar melalui skrip PHP yang memeriksa kecocokan <code>user_id</code> pemilik sebelum menampilkan foto.</td></tr>
    <tr><td>Terminology Compliance</td><td>Aturan kepatuhan yang memastikan keluaran AI bersifat edukatif dan tidak mengeluarkan klaim diagnosis medis dokter.</td></tr>
  </tbody>
</table>

<h1 class="chapter-start">Lampiran C — Matriks Ketertelusuran Kebutuhan</h1>
<table class="data-table">
  <thead>
    <tr><th style="width:28%">Kelompok Kebutuhan</th><th>Sumber Verifikasi Aktual pada Repositori</th></tr>
  </thead>
  <tbody>
    <tr><td>F-01 (Beranda Publik)</td><td><code>app/controllers/HomeController.php</code>, <code>app/views/home/index.php</code>.</td></tr>
    <tr><td>F-02 – F-08 (Autentikasi, OTP &amp; Profil)</td><td><code>app/controllers/AuthController.php</code>, <code>app/models/User.php</code>, <code>app/services/TelegramService.php</code>, <code>EmailService.php</code>, <code>FonnteService.php</code>, <code>app/views/auth/*</code>.</td></tr>
    <tr><td>F-09 – F-13 (Scan Sawar Kulit 4 Pilar)</td><td><code>app/controllers/ScanController.php</code>, <code>app/services/SkinAnalysisService.php</code>, <code>OnnxSkinAnalysisProvider.php</code>, <code>ComputerVisionSkinProvider.php</code>, <code>ImageValidator.php</code>, <code>app/services/ai_engine/predict.py</code>.</td></tr>
    <tr><td>F-14 – F-17 (Sunscreen &amp; Heatmap)</td><td><code>app/controllers/SunscreenController.php</code>, <code>app/services/SunscreenAnalysisService.php</code>, <code>RGBSunscreenAnalysisProvider.php</code>, <code>UVSunscreenAnalysisProvider.php</code>.</td></tr>
    <tr><td>F-18 – F-20 (Dasbor, Riwayat &amp; Produk)</td><td><code>app/controllers/CustomerController.php</code>, <code>app/models/Scan.php</code>, <code>Product.php</code>, <code>ImageStorage.php</code>, <code>app/views/customer/*</code>.</td></tr>
    <tr><td>F-21 – F-24 (Chatbot AI Gemini)</td><td><code>app/controllers/ChatbotController.php</code>, <code>chatbot-server/server.js</code>, <code>app/views/templates/chatbot_widget.php</code>, <code>public/js/chatbot.js</code>.</td></tr>
    <tr><td>F-25 – F-34 (Panel Admin, Token &amp; API)</td><td><code>app/controllers/AdminController.php</code>, <code>app/models/AiTokenUsage.php</code>, <code>Setting.php</code>, <code>ActivityLog.php</code>, <code>app/views/admin/*</code>.</td></tr>
    <tr><td>NF-01 – NF-20 (Non-Fungsional &amp; CI/CD)</td><td><code>app/core/AuthGuard.php</code>, <code>Database.php</code>, <code>tests/run_all_tests.php</code>, <code>.github/workflows/main.yml</code>, <code>storage/scans/.htaccess</code>.</td></tr>
  </tbody>
</table>

<p class="end-marker">— Akhir Dokumen PRD / SRS —</p>
</body>
</html>
"""
