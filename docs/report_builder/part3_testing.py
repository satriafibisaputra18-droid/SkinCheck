# -*- coding: utf-8 -*-
"""
PART 3: LAPORAN PENGUJIAN & CI/CD (TESTING AND CI/CD REPORT)
Standard IEEE 829 / ISO/IEC/IEEE 29119 — SkinCheck
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

def build_testing_html(page_map: dict[str, str] = None) -> str:
    pm = page_map or {}
    def pg(key: str) -> str:
        return str(pm.get(key, "—"))

    toc_entries = [
        (1, "Riwayat Revisi", pg("Riwayat Revisi")),
        (1, "Daftar Isi", pg("Daftar Isi")),
        (1, "1. Pendahuluan", pg("1. Pendahuluan")),
        (2, "1.1 Tujuan & Ruang Lingkup", pg("1.1 Tujuan")),
        (2, "1.2 Referensi", pg("1.2 Referensi")),
        (1, "2. Strategi Pengujian", pg("2. Strategi Pengujian")),
        (2, "2.1 Prinsip & Lapisan Pengujian", pg("2.1 Prinsip")),
        (2, "2.2 Lingkungan Pengujian & Data Uji", pg("2.2 Lingkungan Pengujian")),
        (1, "3. Inventaris Skrip Pengujian & Hasil Eksekusi", pg("3. Inventaris Skrip Pengujian")),
        (2, "3.1 Ringkasan & Cara Menjalankan", pg("3.1 Ringkasan")),
        (2, "3.2 Rincian 21 Kasus Uji — Analisis Sawar Kulit AI", pg("3.2 Rincian 21 Kasus Uji — Analisis")),
        (2, "3.3 Rincian 21 Kasus Uji — Sunscreen & Heatmap", pg("3.3 Rincian 21 Kasus Uji — Sunscreen")),
        (2, "3.4 Skrip Pengujian Manual / Layanan Eksternal Lainnya", pg("3.4 Skrip Pengujian Manual")),
        (1, "4. Pipeline CI/CD GitHub Actions", pg("4. Pipeline CI/CD")),
        (2, "4.1 Gambaran & Langkah-Langkah Pipeline", pg("4.1 Gambaran")),
        (2, "4.2 Yang Sengaja Tidak Dijalankan di CI Cloud", pg("4.2 Yang Sengaja Tidak Dijalankan")),
        (1, "5. Riwayat Kendala & Perbaikan DevOps", pg("5. Riwayat Kendala")),
        (1, "6. Bukti Hasil Pengujian & Verifikasi CI Hijau", pg("6. Bukti Hasil Pengujian")),
        (1, "7. Keterbatasan & Daftar Tes Live Mendatang", pg("7. Keterbatasan")),
        (1, "Lampiran A — Ketertelusuran Pengujian → Kebutuhan", pg("Lampiran A")),
        (1, "Lampiran B — Glosarium Pengujian", pg("Lampiran B")),
    ]

    css = get_css("Laporan Pengujian & CI/CD — SKINCHECK | Rekayasa Perangkat Lunak (TIKB2503021)")

    return f"""<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Pengujian &amp; CI/CD — SKINCHECK (Satria Fibi Saputra - 41257159)</title>
<style>{css}</style>
</head>
<body>

<!-- ==================== COVER PAGE ==================== -->
<div class="cover-page">
  <p class="cover-inst">PROGRAM STUDI S1 TEKNIK INFORMATIKA</p>
  <p class="cover-inst">STMIK IKMI CIREBON</p>
  <hr class="cover-rule"/>

  <p class="cover-doctype">LAPORAN PENGUJIAN &amp; CI/CD</p>
  <p class="cover-doctype">TESTING AND CI/CD REPORT</p>
  <p class="cover-acronym">(Standar IEEE 829 / ISO/IEC/IEEE 29119)</p>

  <p class="cover-product">SKINCHECK</p>
  <p class="cover-subtitle">Platform Analisis Sawar Kulit &amp; Pemerataan Sunscreen Berbasis AI</p>
  <p class="cover-std">Tahap 3 &amp; 4 — Pengujian Otomatis, Integrasi Berkelanjutan (CI/CD), &amp; Bukti Hasil</p>

  <table class="meta-table">
    <thead>
      <tr><th colspan="2">Identitas Laporan Pengujian &amp; DevOps</th></tr>
    </thead>
    <tbody>
      <tr><td>Mata Kuliah</td><td>Rekayasa Perangkat Lunak (TIKB2503021) — 4 SKS, Semester 3</td></tr>
      <tr><td>Nama Mahasiswa</td><td>Satria Fibi Saputra</td></tr>
      <tr><td>NIM</td><td>41257159</td></tr>
      <tr><td>Program Studi</td><td>S1 Teknik Informatika — STMIK IKMI Cirebon</td></tr>
      <tr><td>Tema Topik Kelompok</td><td>Smart Healthcare &amp; Public Service AI</td></tr>
      <tr><td>Dosen Pengampu</td><td>Yudhistira Arie Wijaya, M.Kom., CDS.</td></tr>
      <tr><td>Repositori GitHub</td><td>github.com/satriafibisaputra18-droid/SkinCheck (publik)</td></tr>
      <tr><td>Pipeline CI/CD</td><td>.github/workflows/main.yml — Status: SUCCESS (HIJAU)</td></tr>
      <tr><td>Dokumen Acuan</td><td>PRD / SRS SKINCHECK v1.0 &amp; SDD SKINCHECK v1.0</td></tr>
      <tr><td>Versi &amp; Tanggal</td><td>1.0 — November 2026</td></tr>
    </tbody>
  </table>

  <p class="cover-note">
    Dokumen ini merupakan bagian ketiga dari berkas laporan akhir tunggal Proyek Akhir Mandiri mata kuliah Rekayasa Perangkat Lunak. Dokumen melaporkan strategi pengujian, 42 kasus uji otomatis terverifikasi, arsitektur pipeline CI/CD GitHub Actions berstatus hijau, riwayat kendala dan perbaikannya, serta keterbatasan yang dicatat secara transparan.
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
      <td>Laporan awal Tahap 3 &amp; 4. Inventaris seluruh skrip pengujian pada <code>tests/</code>, bukti eksekusi 42/42 lulus pada <code>tests/run_all_tests.php</code>, serta verifikasi status hijau GitHub Actions <code>.github/workflows/main.yml</code> (commit <code>ceee93c</code>).</td>
    </tr>
  </tbody>
</table>

<div class="callout">
  <strong>Prinsip Kejujuran Pengujian</strong>: Seluruh angka kelulusan yang dilaporkan pada dokumen ini merupakan hasil eksekusi nyata pada terminal lokal dan runner GitHub Actions. Keterbatasan teknis — termasuk skrip uji yang memerlukan kredensial API asli atau ketergantungan kelas yang ditemukan selama verifikasi — dicatat secara terbuka pada Bab 5 dan Bab 7.
</div>

<div class="toc-section">
  <h1>Daftar Isi</h1>
  {render_toc_items(toc_entries)}
</div>

<!-- ==================== BAB 1: PENDAHULUAN ==================== -->
<h1 class="chapter-start">1. Pendahuluan</h1>

<h2>1.1 Tujuan &amp; Ruang Lingkup</h2>
<p>Dokumen ini menjelaskan strategi, skrip, otomatisasi pipeline CI/CD, dan bukti hasil pengujian platform <strong>SKINCHECK</strong> sesuai persyaratan Tahap 3 dan Tahap 4 Panduan Proyek Akhir Mandiri Rekayasa Perangkat Lunak. Ruang lingkup pengujian meliputi: (1) validasi sintaks statis seluruh berkas PHP 8.2, Node.js 18, dan Python 3.10; (2) 42 kasus uji unit dan alur otomatis pada master runner <code>tests/run_all_tests.php</code>; (3) otomatisasi verifikasi melalui GitHub Actions (<code>.github/workflows/main.yml</code>); serta (4) pengujian manual fungsional antarmuka dan integrasi Google Gemini API secara live pada server lokal.</p>

<h2>1.2 Referensi</h2>
<ul>
  <li>Panduan Tugas Proyek Akhir Mandiri &amp; Rubrik Penilaian OBE (STMIK IKMI Cirebon) — Dimensi 4 (Pengujian &amp; GitHub DevOps CI/CD, Bobot 15%).</li>
  <li>PRD / SRS SKINCHECK v1.0 &amp; SDD SKINCHECK v1.0.</li>
  <li>Repositori publik <code>github.com/satriafibisaputra18-droid/SkinCheck</code> dan halaman log eksekusi <code>/actions</code>.</li>
</ul>

<!-- ==================== BAB 2: STRATEGI PENGUJIAN ==================== -->
<h1 class="chapter-start">2. Strategi Pengujian</h1>

<h2>2.1 Prinsip &amp; Lapisan Pengujian</h2>
<p>Pengujian SKINCHECK menerapkan empat lapisan verifikasi berlapis agar kesalahan sintaks, pelanggaran kontrak objek, hingga regresi logika AI tertangkap sebelum kode digabungkan:</p>
<table class="data-table">
  <thead>
    <tr><th style="width:22%">Lapisan</th><th style="width:28%">Alat / Perintah</th><th>Yang Dibuktikan</th><th style="width:16%">Lokasi</th></tr>
  </thead>
  <tbody>
    <tr><td>Linting Statis PHP</td><td><code>find app -name "*.php" -exec php -l</code></td><td>Nol kesalahan sintaks pada seluruh 7 Controller, 6 Model, 19 Service, dan 26 View PHP.</td><td>CI + Lokal</td></tr>
    <tr><td>Linting Node &amp; Python</td><td><code>node --check server.js</code> &amp; <code>py_compile predict.py</code></td><td>Kebenaran sintaks microservice chatbot Express dan skrip inferensi ONNX MobileNetV3.</td><td>CI + Lokal</td></tr>
    <tr><td>Automated Unit &amp; Flow Tests</td><td><code>php tests/run_all_tests.php</code></td><td>42 asersi logika sawar kulit 4 pilar, estimasi sunscreen, retry/fallback, terminologi non-medis, dan serialisasi JSON.</td><td>CI + Lokal</td></tr>
    <tr><td>Artifact Gate Check</td><td><code>test -f docs/PRD_*.pdf &amp;&amp; test -f docs/SDD_*.pdf</code></td><td>Memastikan seluruh luaran dokumen rekayasa perangkat lunak tersedia pada repositori.</td><td>CI Cloud</td></tr>
    <tr><td>Pengujian Live Manual</td><td>XAMPP + Browser + Live AJAX Test</td><td>Kamera WebRTC HUD, login/OTP nyata, tampilan UI 5 layar, dan pemakaian token Gemini (746.672 token tercatat).</td><td>Lokal</td></tr>
  </tbody>
</table>

<h2>2.2 Lingkungan Pengujian &amp; Data Uji</h2>
<p>Pengujian otomatis memanfaatkan citra wajah sintetis yang dibangkitkan secara dinamis menggunakan ekstensi <strong>PHP GD</strong> (<code>imagecreatetruecolor(400, 400)</code> dengan patch warna kulit natural dan simulasi area kemerahan) di direktori sementara OS (<code>sys_get_temp_dir()</code>), serta simulasi kegagalan jaringan (<em>mock timeout exception</em>) sehingga seluruh 42 asersi berjalan deterministik tanpa mengotori basis data produksi maupun menguras kuota API berbayar.</p>

<!-- ==================== BAB 3: INVENTARIS SKRIP PENGUJIAN ==================== -->
<h1 class="chapter-start">3. Inventaris Skrip Pengujian &amp; Hasil Eksekusi</h1>

<h2>3.1 Ringkasan &amp; Cara Menjalankan</h2>
<p>Direktori <code>tests/</code> berisi 6 skrip pengujian PHP: 1 master runner CI (<code>run_all_tests.php</code>), 2 suite pengujian otomatis terisolasi (<code>test_skin_analysis.php</code> &amp; <code>test_sunscreen_analysis.php</code>, total 42 kasus uji), serta 3 skrip uji integrasi lokal (<code>test_avatar_upload.php</code>, <code>test_chat.php</code>, <code>test_email_service.php</code>).</p>

<pre class="code-block"><code># Menjalankan seluruh 42 unit &amp; flow test otomatis sekaligus (exit code 0 = PASS)
php tests/run_all_tests.php

# Menjalankan masing-masing suite secara terpisah
php tests/test_skin_analysis.php
php tests/test_sunscreen_analysis.php</code></pre>

<h2>3.2 Rincian 21 Kasus Uji — Analisis Sawar Kulit AI (<code>tests/test_skin_analysis.php</code>)</h2>
<table class="data-table">
  <thead>
    <tr><th style="width:12%">ID Uji</th><th style="width:32%">Kasus Pengujian</th><th>Hasil Aktual yang Diverifikasi</th><th style="width:10%">Status</th></tr>
  </thead>
  <tbody>
    <tr><td>TC-SK-01..02</td><td>Instansiasi &amp; Status Sukses</td><td>Mengembalikan instance <code>AnalysisResult</code> dengan <code>status = 'completed'</code>.</td><td><strong>PASS</strong></td></tr>
    <tr><td>TC-SK-03..07</td><td>Rentang 4 Pilar &amp; Overall Score</td><td>Acne (6), Redness (5), Texture (95), Hyperpigmentation (8), Overall (94) seluruhnya dalam rentang $[0, 100]$.</td><td><strong>PASS</strong></td></tr>
    <tr><td>TC-SK-08..10</td><td>Confidence &amp; Narasi Visual</td><td>Confidence 0.85 dalam $[0.5, 1.0]$; daftar indikasi visual dan ringkasan catatan terisi penuh.</td><td><strong>PASS</strong></td></tr>
    <tr><td>TC-SK-11</td><td>Kepatuhan Terminologi Non-Medis</td><td>Terverifikasi bebas dari istilah vonis/diagnosis penyakit klinis (memenuhi NF-15).</td><td><strong>PASS</strong></td></tr>
    <tr><td>TC-SK-12..16</td><td>Simulasi Kegagalan AI &amp; Pemulihan</td><td>Timeout AI dicatat sebagai <code>failed</code>, skor di-reset ke 0, retry 2x berjalan, dan recovery pulih ke <code>completed</code>.</td><td><strong>PASS</strong></td></tr>
    <tr><td>TC-SK-17..18</td><td>Penanganan Berkas Gambar Hilang</td><td>Path tidak ditemukan ditangani secara aman (<em>graceful fail</em>) dengan pesan error eksplisit.</td><td><strong>PASS</strong></td></tr>
    <tr><td>TC-SK-19..21</td><td>Serialisasi <code>toArray()</code> &amp; <code>toJson()</code></td><td>Memuat seluruh kunci wajib termasuk <code>confidence_percentage</code> dan valid JSON.</td><td><strong>PASS</strong></td></tr>
  </tbody>
</table>

<h2>3.3 Rincian 21 Kasus Uji — Sunscreen &amp; Heatmap (<code>tests/test_sunscreen_analysis.php</code>)</h2>
<table class="data-table">
  <thead>
    <tr><th style="width:12%">ID Uji</th><th style="width:32%">Kasus Pengujian</th><th>Hasil Aktual yang Diverifikasi</th><th style="width:10%">Status</th></tr>
  </thead>
  <tbody>
    <tr><td>TC-SS-01..04</td><td>Estimasi Visual RGB &amp; Klasifikasi</td><td>Instance <code>SunscreenResult</code>, <code>status='completed'</code>, coverage 70% (valid 0–100%), status "Cukup Merata".</td><td><strong>PASS</strong></td></tr>
    <tr><td>TC-SS-05..09</td><td>Heatmap, Confidence &amp; Area Rawan</td><td>Berkas heatmap JPG dibangkitkan, confidence 0.81, metode "RGB Visual Estimation", array low coverage valid.</td><td><strong>PASS</strong></td></tr>
    <tr><td>TC-SS-10..11</td><td>Sanggahan Non-UV Wajib</td><td>Menyertakan disclaimer eksplisit non-UV dan nihil klaim pengukuran spektrum UV langsung.</td><td><strong>PASS</strong></td></tr>
    <tr><td>TC-SS-12..14</td><td>Kondisi Keyakinan Rendah (&lt; 0.60)</td><td>Flag <code>isLowConfidence</code> bernilai TRUE pada confidence 0.48 disertai catatan saran cahaya.</td><td><strong>PASS</strong></td></tr>
    <tr><td>TC-SS-15..18</td><td>Error Recovery &amp; Missing Image</td><td>Timeout dan citra hilang ditangani dengan status <code>failed</code> dan coverage score 0 tanpa crash.</td><td><strong>PASS</strong></td></tr>
    <tr><td>TC-SS-19..21</td><td>Abstraksi UV Provider &amp; Serialisasi</td><td><code>UVSunscreenAnalysisProvider</code> mendeklarasikan tipe hardware UV; <code>toArray()</code> menjaga metode analisis.</td><td><strong>PASS</strong></td></tr>
  </tbody>
</table>

<h2>3.4 Skrip Pengujian Manual / Layanan Eksternal Lainnya</h2>
<table class="data-table">
  <thead><tr><th style="width:28%">Berkas Skrip</th><th>Cakupan &amp; Status Eksekusi</th></tr></thead>
  <tbody>
    <tr><td><code>tests/test_avatar_upload.php</code></td><td>Menguji unggahan foto profil pelanggan terhadap tabel <code>users</code> MySQL lokal.</td></tr>
    <tr><td><code>tests/test_chat.php</code></td><td>Menguji alur respons chatbot terhadap endpoint Google Gemini API dengan kunci API asli.</td></tr>
    <tr><td><code>tests/test_email_service.php</code></td><td>Menguji konfigurasi Resend API; catatan temuan ketergantungan kelas <code>Setting</code> didokumentasikan pada Bab 5.</td></tr>
  </tbody>
</table>

<!-- ==================== BAB 4: PIPELINE CI/CD ==================== -->
<h1 class="chapter-start">4. Pipeline CI/CD GitHub Actions</h1>

<h2>4.1 Gambaran &amp; Langkah-Langkah Pipeline</h2>
<p>Pipeline Continuous Integration didefinisikan pada <code>.github/workflows/main.yml</code> dengan nama <strong>"🌿 SkinCheck CI/CD DevOps Pipeline"</strong> dan terdiri atas dua <em>job</em> berurutan (<code>build-and-test</code> dan <code>release-check</code>) yang otomatis aktif pada setiap <code>push</code>, <code>pull_request</code>, maupun pemicu manual (<code>workflow_dispatch</code>):</p>

<table class="data-table">
  <thead><tr><th style="width:6%">#</th><th style="width:26%">Tahap / Langkah</th><th style="width:30%">Perintah / Aksi</th><th>Yang Dibuktikan</th></tr></thead>
  <tbody>
    <tr><td>1</td><td>Checkout Repository</td><td><code>actions/checkout@v4</code></td><td>Kode sumber commit terbaru tersedia di runner Ubuntu.</td></tr>
    <tr><td>2</td><td>Setup PHP 8.2</td><td><code>shivammathur/setup-php@v2</code></td><td>Runtime PHP 8.2 beserta ekstensi <code>gd, pdo_mysql, curl, mbstring</code> aktif.</td></tr>
    <tr><td>3</td><td>PHP Syntax Lint</td><td><code>find app -name "*.php" -exec php -l</code></td><td>Nol kesalahan sintaks di seluruh berkas aplikasi PHP MVC.</td></tr>
    <tr><td>4–5</td><td>Setup &amp; Lint Node.js 18</td><td><code>node --check chatbot-server/server.js</code></td><td>Validasi sintaks microservice chatbot Express tanpa error.</td></tr>
    <tr><td>6–7</td><td>Setup &amp; Lint Python 3.10</td><td><code>python -m py_compile predict.py</code></td><td>Skrip inferensi ONNX MobileNetV3 terkompilasi bersih.</td></tr>
    <tr><td>8</td><td>Master Automated Tests</td><td><code>php tests/run_all_tests.php</code></td><td>Seluruh 42 kasus uji analisis sawar kulit &amp; sunscreen lulus 100% (exit code 0).</td></tr>
    <tr><td>9</td><td>Release Artifact Gate</td><td><code>test -f docs/PRD_*.pdf &amp;&amp; SDD_*.pdf</code></td><td>Memverifikasi kelengkapan berkas luaran PDF resmi pada repositori.</td></tr>
  </tbody>
</table>

<h2>4.2 Yang Sengaja Tidak Dijalankan di CI Cloud</h2>
<p>Pengujian yang membutuhkan kunci API produksi nyata (Google Gemini API, Telegram Bot Token, Resend API Key, dan Fonnte Token) secara sengaja tidak dijalankan pada runner publik GitHub Actions demi menjaga kerahasiaan kredensial dan menghindari pemborosan kuota gratis harian. Fungsionalitas layanan eksternal diverifikasi langsung melalui fitur <em>Live AJAX Test</em> pada <code>/admin/settings</code> serta demonstrasi live pada Video Final.</p>

<!-- ==================== BAB 5: RIWAYAT KENDALA & PERBAIKAN ==================== -->
<h1 class="chapter-start">5. Riwayat Kendala &amp; Perbaikan DevOps</h1>
<p>Selama proses pembangunan dan integrasi CI/CD, tercatat dua temuan nyata yang berhasil diidentifikasi dan diselesaikan secara terukur:</p>

<ol>
  <li><strong>Kendala 1 — Penolakan Push Workflow oleh GitHub OAuth Scope</strong>:
    <br/><em>Penyebab</em>: Saat mendorong berkas <code>.github/workflows/ci.yml</code> melalui terminal lokal, GitHub menolak push dengan pesan <code>refusing to allow an OAuth App to create or update workflow without workflow scope</code>.
    <br/><em>Perbaikan</em>: Commit dipisahkan sehingga seluruh kode aplikasi, dokumen PDF, dan skrip test berhasil didorong terlebih dahulu (commit <code>d136988</code>), kemudian berkas workflow <code>.github/workflows/main.yml</code> dibuat langsung melalui sesi terautentikasi GitHub Web (commit <code>4a71979</code>), disinkronkan ke lokal via <code>git pull</code>, dan berkas duplikat <code>ci.yml</code> dibersihkan.</li>
  <li><strong>Kendala 2 — Fatal Error Dependensi pada <code>tests/test_email_service.php</code></strong>:
    <br/><em>Penyebab</em>: Saat dijalankan terisolasi via CLI, skrip lama <code>test_email_service.php</code> melempar <code>Error: Class "Setting" not found</code> karena belum memuat <code>app/models/Setting.php</code> sebelum menginstansiasi <code>EmailService</code>.
    <br/><em>Mitigasi</em>: Dicatat secara terbuka sebagai temuan perbaikan pada skrip uji lokal, sementara pengujian koneksi Resend Email dilakukan via <code>Setting::testResend()</code> di panel admin.</li>
</ol>

<!-- ==================== BAB 6: BUKTI HASIL PENGUJIAN ==================== -->
<h1 class="chapter-start">6. Bukti Hasil Pengujian &amp; Verifikasi CI Hijau</h1>

<table class="data-table">
  <thead><tr><th style="width:26%">Item Verifikasi</th><th>Bukti Hasil Aktual</th></tr></thead>
  <tbody>
    <tr><td>Repositori Publik GitHub</td><td><code>https://github.com/satriafibisaputra18-droid/SkinCheck</code></td></tr>
    <tr><td>Workflow GitHub Actions</td><td><code>🌿 SkinCheck CI/CD DevOps Pipeline</code> (<code>.github/workflows/main.yml</code>)</td></tr>
    <tr><td>Commit Terverifikasi</td><td><code>4a71979</code> &amp; <code>ceee93c</code> pada cabang <code>main</code></td></tr>
    <tr><td>Status &amp; Kesimpulan CI</td><td><strong>Status: COMPLETED | Conclusion: SUCCESS (Centang Hijau 100%)</strong></td></tr>
    <tr><td>Ringkasan Unit Test</td><td><strong>42 / 42 Kasus Uji Lulus (100,0%)</strong> — 21 Skin Analysis + 21 Sunscreen Analysis</td></tr>
    <tr><td>Bukti Operasional Lokal</td><td>10 akun customer terdaftar, 34 riwayat scan tersimpan, 29 katalog produk skincare, skor kesehatan rata-rata 76,6/100, dan 746.672 token Google Gemini terpantau pada dasbor admin (Gambar 19 SDD).</td></tr>
  </tbody>
</table>

<pre class="code-block"><code>======================================================
    🌿 SkinCheck Automated CI/CD Test Pipeline
    Author: Satria Fibi Saputra (NIM: 41257159)
======================================================
▶ RUNNING TEST SUITE: Skin Condition 4-Pillar AI Analysis
   Tests Completed: 21 / 21 Passed  [SUCCESS]
▶ RUNNING TEST SUITE: Sunscreen Coverage &amp; Heatmap Engine
   Sunscreen Tests Completed: 21 / 21 Passed  [SUCCESS]
======================================================
🎉 ALL TEST SUITES PASSED! STATUS: GREEN (PASSED)</code></pre>

<!-- ==================== BAB 7: KETERBATASAN ==================== -->
<h1 class="chapter-start">7. Keterbatasan &amp; Daftar Tes Live Mendatang</h1>
<ul>
  <li><strong>Cakupan Pengujian Otomatis</strong>: Pengujian otomatis saat ini berfokus pada mesin inti visi komputer (sawar kulit 4 pilar, sunscreen, heatmap, fallback, terminologi, serialisasi). Pengujian HTTP end-to-end dengan database MySQL nyata pada runner CI (mis. login/OTP/admin CRUD) menjadi target penyempurnaan versi berikutnya.</li>
  <li><strong>Pengujian Beban (Load Testing)</strong>: Belum dilakukan pengujian beban konkuren multi-pengguna (mis. menggunakan k6 atau JMeter) pada server produksi.</li>
  <li><strong>Daftar Demonstrasi Live pada Video Final (Tahap 4)</strong>: (1) Registrasi &amp; login akun; (2) Pemindaian wajah live via kamera HUD; (3) Evaluasi kerataan sunscreen &amp; heatmap; (4) Obrolan langsung dengan AI Skincare Assistant; (5) Peninjauan Google Gemini Token Monitor di panel admin; serta (6) Eksekusi <code>php tests/run_all_tests.php</code> dan penunjukan status hijau GitHub Actions.</li>
</ul>

<!-- ==================== LAMPIRAN A & B ==================== -->
<h1 class="chapter-start">Lampiran A — Ketertelusuran Pengujian → Kebutuhan</h1>
<table class="data-table">
  <thead><tr><th style="width:32%">Skrip / Tahap Pengujian</th><th style="width:20%">Kebutuhan PRD</th><th>Cakupan Verifikasi</th></tr></thead>
  <tbody>
    <tr><td><code>tests/test_skin_analysis.php</code></td><td>F-10 – F-13, NF-01, NF-11, NF-15</td><td>Skor 4 pilar sawar kulit, retry/fallback, penanganan file hilang, terminologi non-medis, JSON.</td></tr>
    <tr><td><code>tests/test_sunscreen_analysis.php</code></td><td>F-14 – F-17, NF-02, NF-15, NF-16</td><td>Estimasi RGB, heatmap, sanggahan non-UV, low confidence advisory, abstraksi UV provider.</td></tr>
    <tr><td><code>.github/workflows/main.yml</code></td><td>NF-17, NF-18, NF-19</td><td>PHP 8.2 lint, Node 18 syntax, Python 3.10 compile, master runner 42 test, verifikasi artefak PDF.</td></tr>
    <tr><td>Live AJAX Test &amp; Manual Demo</td><td>F-01 – F-09, F-18 – F-34</td><td>Autentikasi OTP, kamera WebRTC HUD, riwayat privat, Chatbot Gemini, &amp; Token Monitor Admin.</td></tr>
  </tbody>
</table>

<h1>Lampiran B — Glosarium Pengujian</h1>
<table class="data-table">
  <thead><tr><th style="width:26%">Istilah</th><th>Definisi</th></tr></thead>
  <tbody>
    <tr><td>Assertion</td><td>Pernyataan logika pada skrip uji (<code>assertCondition</code>) untuk memastikan hasil aktual sesuai ekspektasi.</td></tr>
    <tr><td>Exit Code</td><td>Kode status keluaran proses CLI (<code>0</code> = lulus seluruhnya, bukan <code>0</code> = gagal) yang dibaca GitHub Actions.</td></tr>
    <tr><td>Graceful Fallback</td><td>Kemampuan sistem menangani kegagalan komponen eksternal tanpa crash dan tetap menyajikan respons terstruktur.</td></tr>
    <tr><td>Static Linting</td><td>Pemeriksaan kebenaran sintaks kode sumber secara statis tanpa mengeksekusi aplikasi penuh.</td></tr>
  </tbody>
</table>

<p class="end-marker">— Akhir Laporan Pengujian &amp; CI/CD —</p>
</body>
</html>
"""
