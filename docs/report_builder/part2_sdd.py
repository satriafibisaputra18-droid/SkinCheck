# -*- coding: utf-8 -*-
"""
PART 2: SOFTWARE DESIGN DOCUMENT (SDD)
Standard IEEE 1016 / ISO/IEC/IEEE 42010 — SkinCheck
Includes 14 custom SVG UML/ERD diagrams + 5 actual SkinCheck UI screenshots + Figma Prototype Spec!
"""
import os
from styles import get_css
from diagrams import (
    svg_architecture,
    svg_usecase_overview,
    svg_usecase_guest,
    svg_usecase_customer,
    svg_usecase_admin,
    svg_activity_face_scan,
    svg_activity_sunscreen,
    svg_activity_chatbot,
    svg_class_domain,
    svg_class_ai_solid,
    svg_class_support,
    svg_sequence_scan,
    svg_sequence_chatbot,
    svg_erd_full,
)

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

def build_sdd_html(page_map: dict[str, str] = None) -> str:
    pm = page_map or {}
    def pg(key: str) -> str:
        return str(pm.get(key, "—"))

    ui_dir = os.path.abspath(os.path.join(os.path.dirname(__file__), "..", "assets", "ui")).replace("\\", "/")

    toc_entries = [
        (1, "Riwayat Revisi", pg("Riwayat Revisi")),
        (1, "Daftar Isi", pg("Daftar Isi")),
        (1, "Daftar Gambar", pg("Daftar Gambar")),
        (1, "1. Pendahuluan", pg("1. Pendahuluan")),
        (2, "1.1 Tujuan & Ruang Lingkup", pg("1.1 Tujuan")),
        (2, "1.2 Referensi & Konvensi", pg("1.2 Referensi")),
        (1, "2. Arsitektur Sistem", pg("2. Arsitektur Sistem")),
        (2, "2.1 Gambaran Arsitektur Berlapis", pg("2.1 Gambaran Arsitektur")),
        (2, "2.2 Tumpukan Teknologi", pg("2.2 Tumpukan Teknologi")),
        (2, "2.3 Struktur Proyek", pg("2.3 Struktur Proyek")),
        (2, "2.4 Pola Desain & Prinsip SOLID", pg("2.4 Pola Desain")),
        (2, "2.5 Aliran Data Utama", pg("2.5 Aliran Data Utama")),
        (1, "3. Diagram Use Case", pg("3. Diagram Use Case")),
        (2, "3.1 Aktor dan Ringkasan Keseluruhan", pg("3.1 Aktor dan Ringkasan")),
        (2, "3.2 Guest (Tamu)", pg("3.2 Guest")),
        (2, "3.3 Customer (Pelanggan)", pg("3.3 Customer")),
        (2, "3.4 Admin (Administrator Eksekutif)", pg("3.4 Admin")),
        (2, "3.5 Skenario Use Case Utama", pg("3.5 Skenario Use Case Utama")),
        (1, "4. Diagram Activity", pg("4. Diagram Activity")),
        (2, "4.1 Pemindaian Sawar Kulit & Inferensi AI", pg("4.1 Pemindaian Sawar Kulit")),
        (2, "4.2 Pemeriksaan Kerataan Sunscreen & Heatmap", pg("4.2 Pemeriksaan Kerataan Sunscreen")),
        (2, "4.3 Konsultasi Chatbot AI Gemini & Audit Token", pg("4.3 Konsultasi Chatbot AI")),
        (1, "5. Diagram Class", pg("5. Diagram Class")),
        (2, "5.1 Domain Inti & Entitas Model", pg("5.1 Domain Inti")),
        (2, "5.2 Arsitektur SOLID Servis AI & Provider", pg("5.2 Arsitektur SOLID")),
        (2, "5.3 Modul Pendukung & Komunikasi", pg("5.3 Modul Pendukung")),
        (1, "6. Diagram Sequence", pg("6. Diagram Sequence")),
        (2, "6.1 Analisis Sawar Kulit End-to-End", pg("6.1 Analisis Sawar Kulit")),
        (2, "6.2 Konsultasi Chatbot AI Gemini End-to-End", pg("6.2 Konsultasi Chatbot AI")),
        (1, "7. Entity Relationship Diagram (ERD)", pg("7. Entity Relationship Diagram")),
        (2, "7.1 Diagram Relasi 8 Tabel", pg("7.1 Diagram Relasi")),
        (2, "7.2 Daftar Lengkap Foreign Key & Integritas", pg("7.2 Daftar Lengkap Foreign Key")),
        (1, "8. Desain Antarmuka & Prototipe UI/UX Figma", pg("8. Desain Antarmuka")),
        (2, "8.1 Prinsip Desain & Design Tokens", pg("8.1 Prinsip Desain")),
        (2, "8.2 Inventaris Layar Aktual", pg("8.2 Inventaris Layar")),
        (2, "8.3 Alur Navigasi & Komponen Bersama", pg("8.3 Alur Navigasi")),
        (2, "8.4 Tangkapan Layar UI Aktual (Acuan Figma)", pg("8.4 Tangkapan Layar UI")),
        (2, "8.5 Spesifikasi Prototipe Interaktif Figma", pg("8.5 Spesifikasi Prototipe Interaktif Figma")),
        (1, "9. Keamanan & Keputusan Desain", pg("9. Keamanan & Keputusan Desain")),
        (1, "Lampiran A — Ketertelusuran PRD → SDD", pg("Lampiran A")),
        (1, "Lampiran B — Inventaris Rute & Endpoint Aplikasi", pg("Lampiran B")),
        (1, "Lampiran C — Glosarium Desain", pg("Lampiran C")),
    ]

    lof_entries = [
        (2, "Gambar 1 — Diagram Arsitektur Sistem SKINCHECK", pg("Gambar 1 —")),
        (2, "Gambar 2 — Diagram Use Case Keseluruhan — Sistem SKINCHECK", pg("Gambar 2 —")),
        (2, "Gambar 3 — Diagram Use Case — Guest (Tamu)", pg("Gambar 3 —")),
        (2, "Gambar 4 — Diagram Use Case — Customer (Pelanggan)", pg("Gambar 4 —")),
        (2, "Gambar 5 — Diagram Use Case — Admin (Administrator Eksekutif)", pg("Gambar 5 —")),
        (2, "Gambar 6 — Diagram Activity — Pemindaian Sawar Kulit & Inferensi AI", pg("Gambar 6 —")),
        (2, "Gambar 7 — Diagram Activity — Pemeriksaan Kerataan Sunscreen & Heatmap", pg("Gambar 7 —")),
        (2, "Gambar 8 — Diagram Activity — Konsultasi Chatbot AI Gemini & Audit Token", pg("Gambar 8 —")),
        (2, "Gambar 9 — Diagram Class — Domain Inti & Entitas Model", pg("Gambar 9 —")),
        (2, "Gambar 10 — Diagram Class — Arsitektur SOLID Servis AI & Provider", pg("Gambar 10 —")),
        (2, "Gambar 11 — Diagram Class — Modul Pendukung & Komunikasi", pg("Gambar 11 —")),
        (2, "Gambar 12 — Diagram Sequence — Analisis Sawar Kulit End-to-End", pg("Gambar 12 —")),
        (2, "Gambar 13 — Diagram Sequence — Konsultasi Chatbot AI Gemini End-to-End", pg("Gambar 13 —")),
        (2, "Gambar 14 — Entity Relationship Diagram (ERD) — 8 Tabel Basis Data SKINCHECK", pg("Gambar 14 —")),
        (2, "Gambar 15 — Antarmuka Beranda Publik (Landing Page) SKINCHECK", pg("Gambar 15 —")),
        (2, "Gambar 16 — Antarmuka Dasbor Pelanggan (Customer Dashboard)", pg("Gambar 16 —")),
        (2, "Gambar 17 — Antarmuka Pemindaian Wajah (Smart Camera HUD Viewfinder)", pg("Gambar 17 —")),
        (2, "Gambar 18 — Antarmuka Riwayat Pemindaian Kulit (History Tracking)", pg("Gambar 18 —")),
        (2, "Gambar 19 — Antarmuka Dasbor Admin Eksekutif & Google Gemini Token Monitor", pg("Gambar 19 —")),
    ]

    css = get_css("SDD — SKINCHECK | Rekayasa Perangkat Lunak (TIKB2503021)")

    return f"""<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>SDD — SKINCHECK (Satria Fibi Saputra - 41257159)</title>
<style>{css}</style>
</head>
<body>

<!-- ==================== COVER PAGE ==================== -->
<div class="cover-page">
  <p class="cover-inst">PROGRAM STUDI S1 TEKNIK INFORMATIKA</p>
  <p class="cover-inst">STMIK IKMI CIREBON</p>
  <hr class="cover-rule"/>

  <p class="cover-doctype">SOFTWARE DESIGN DOCUMENT</p>
  <p class="cover-acronym">(SDD)</p>

  <p class="cover-product">SKINCHECK</p>
  <p class="cover-subtitle">Platform Analisis Sawar Kulit &amp; Pemerataan Sunscreen Berbasis AI</p>
  <p class="cover-std">Tahap 2 — Desain Perangkat Lunak (UML, ERD, &amp; Desain Antarmuka UI/UX Figma)</p>

  <table class="meta-table">
    <thead>
      <tr><th colspan="2">Identitas Dokumen Desain Perangkat Lunak</th></tr>
    </thead>
    <tbody>
      <tr><td>Mata Kuliah</td><td>Rekayasa Perangkat Lunak (TIKB2503021) — 4 SKS, Semester 3</td></tr>
      <tr><td>Nama Mahasiswa</td><td>Satria Fibi Saputra</td></tr>
      <tr><td>NIM</td><td>41257159</td></tr>
      <tr><td>Program Studi</td><td>S1 Teknik Informatika — STMIK IKMI Cirebon</td></tr>
      <tr><td>Tema Topik Kelompok</td><td>Smart Healthcare &amp; Public Service AI</td></tr>
      <tr><td>Dosen Pengampu</td><td>Yudhistira Arie Wijaya, M.Kom., CDS.</td></tr>
      <tr><td>Repositori GitHub</td><td>github.com/satriafibisaputra18-droid/SkinCheck (publik)</td></tr>
      <tr><td>Dokumen Acuan</td><td>PRD / SRS SKINCHECK v1.0 (34 Kebutuhan Fungsional, 20 Non-Fungsional)</td></tr>
      <tr><td>Versi Dokumen</td><td>1.0</td></tr>
      <tr><td>Tanggal</td><td>November 2026</td></tr>
    </tbody>
  </table>

  <p class="cover-note">
    Dokumen ini disusun sebagai luaran Tahap 2 (Perancangan UML, ERD &amp; UI/UX) Proyek Akhir Mandiri mata kuliah Rekayasa Perangkat Lunak. Seluruh 14 diagram arsitektur/UML/ERD dan 5 antarmuka aktual diturunkan dari PRD / SRS SKINCHECK v1.0 serta diverifikasi langsung terhadap kode sumber pada repositori publik.
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
      <td>Dokumen desain lengkap Tahap 2. Memuat Diagram Arsitektur Berlapis, 4 Diagram Use Case (Keseluruhan + 3 Peran), 3 Diagram Activity, 3 Diagram Class berorientasi SOLID, 2 Diagram Sequence beserta tabel rincian pesan, ERD 8 tabel lengkap, 5 tangkapan layar UI aktual, serta Spesifikasi Prototipe Interaktif Figma.</td>
    </tr>
  </tbody>
</table>

<p>Konvensi penomoran: kode <strong>F-xx</strong> merujuk pada kebutuhan fungsional pada PRD / SRS SKINCHECK v1.0 dan <strong>NF-xx</strong> merujuk pada kebutuhan non-fungsional. Seluruh nama kelas, metode, rute, dan kolom tabel diambil secara akurat dari repositori kode sumber.</p>

<div class="toc-section">
  <h1>Daftar Isi</h1>
  {render_toc_items(toc_entries)}
</div>

<div class="toc-section">
  <h1>Daftar Gambar</h1>
  <p style="font-style: italic; color: #475569;">Seluruh diagram teknis (Gambar 1–14) dibangkitkan sebagai grafik vektor presisi dari struktur kode aktual, dan Gambar 15–19 merupakan tangkapan layar antarmuka aktual SKINCHECK yang menjadi acuan langsung prototipe Figma.</p>
  {render_toc_items(lof_entries)}
</div>

<!-- ==================== BAB 1: PENDAHULUAN ==================== -->
<h1 class="chapter-start">1. Pendahuluan</h1>

<h2>1.1 Tujuan &amp; Ruang Lingkup</h2>
<p>Dokumen Desain Perangkat Lunak (<em>Software Design Document / SDD</em>) ini memaparkan rancangan arsitektur, pemodelan perilaku dan struktur melalui empat diagram UML wajib (<em>Use Case, Activity, Class, Sequence</em>), pemodelan data relasional (<em>ERD</em>), serta rancangan antarmuka pengguna (<em>UI/UX Figma</em>) untuk sistem <strong>SKINCHECK</strong>. Rancangan ini menerjemahkan seluruh 34 kebutuhan fungsional (F-01–F-34) dan 20 kebutuhan non-fungsional (NF-01–NF-20) dari PRD/SRS v1.0 ke dalam cetak biru teknis yang konsisten dengan kode produksi.</p>
<p>Ruang lingkup desain mencakup: (1) inti aplikasi web PHP 8.1+ MVC (7 controller, 6 model, 19 servis SOLID, 26 berkas view); (2) modul visi komputer Python ONNX (MobileNetV3) dan PHP GD CV; (3) microservice chatbot Node.js Express yang terhubung ke Google Gemini Flash API; (4) basis data MySQL 8.0 (8 tabel, 4 foreign key); serta (5) integrasi notifikasi OTP multi-kanal (Telegram, Resend Email, Fonnte WhatsApp).</p>

<h2>1.2 Referensi &amp; Konvensi</h2>
<ul>
  <li>Panduan Tugas Proyek Akhir Mandiri &amp; Rubrik Penilaian OBE (STMIK IKMI Cirebon) — ketentuan Tahap 2 (4 Diagram UML, ERD, Prototipe UI/UX Figma).</li>
  <li>PRD / SRS SKINCHECK v1.0 (Bagian 1 dari berkas gabungan ini).</li>
  <li>IEEE Std 1016-2009, <em>IEEE Standard for Information Technology — Systems Design — Software Design Descriptions</em>.</li>
  <li>Repositori publik <code>github.com/satriafibisaputra18-droid/SkinCheck</code> (cabang <code>main</code>).</li>
</ul>

<!-- ==================== BAB 2: ARSITEKTUR SISTEM ==================== -->
<h1 class="chapter-start">2. Arsitektur Sistem</h1>

<h2>2.1 Gambaran Arsitektur Berlapis</h2>
<p>SKINCHECK menerapkan pola <strong>Layered MVC Architecture</strong> yang dipadukan dengan <strong>SOLID Service Decoupling</strong> pada sisi backend PHP serta <strong>Microservice Event-Driven Hub</strong> pada komponen Node.js chatbot. Pemisahan 5 lapisan ini memastikan bahwa presentasi, kendali akses, logika bisnis AI, dan persistensi data berdiri secara modular dan mudah diuji.</p>

<div class="figure-box">
  {svg_architecture()}
  <p class="figure-caption">Gambar 1 — Diagram Arsitektur Sistem SKINCHECK</p>
</div>

<h2>2.2 Tumpukan Teknologi</h2>
<table class="data-table">
  <thead>
    <tr><th style="width:22%">Lapisan</th><th style="width:28%">Teknologi</th><th>Versi / Peran dalam Sistem</th></tr>
  </thead>
  <tbody>
    <tr><td>Backend Web Core</td><td>PHP (Custom MVC)</td><td>PHP 8.1 / 8.2, PDO MySQL, GD Image Processing, cURL HTTPS.</td></tr>
    <tr><td>Frontend &amp; HUD</td><td>HTML5, CSS3, Vanilla JS</td><td>WebRTC Camera Viewfinder, Canvas Luminance Analyzer, Responsive Grid.</td></tr>
    <tr><td>Basis Data</td><td>MySQL Server (InnoDB)</td><td>MySQL 8.0, database <code>skincheck</code>, 8 tabel operasional, utf8mb4.</td></tr>
    <tr><td>Computer Vision AI</td><td>MobileNetV3 ONNX &amp; PHP GD</td><td><code>predict.py</code> (ONNX Runtime) + <code>ComputerVisionSkinProvider</code> &amp; <code>RGBSunscreenAnalysisProvider</code>.</td></tr>
    <tr><td>Generative AI</td><td>Google Gemini Flash API</td><td><code>gemini-flash-lite-latest</code> / model fallback melalui Node.js Express &amp; PHP REST.</td></tr>
    <tr><td>Komunikasi OTP</td><td>Telegram, Resend, Fonnte</td><td>Pengiriman OTP 6-digit dan tautan reset sandi multi-kanal.</td></tr>
    <tr><td>DevOps &amp; CI/CD</td><td>GitHub Actions</td><td>Workflow <code>.github/workflows/main.yml</code> (PHP Lint, Node Check, Python Compile, 42 Unit Tests).</td></tr>
  </tbody>
</table>

<h2>2.3 Struktur Proyek</h2>
<table class="data-table">
  <thead>
    <tr><th style="width:26%">Direktori / Berkas</th><th>Isi dan Tanggung Jawab</th></tr>
  </thead>
  <tbody>
    <tr><td><code>app/core/</code></td><td>Inti kerangka MVC: <code>App.php</code> (router), <code>AuthGuard.php</code> (RBAC), <code>Controller.php</code>, <code>Database.php</code> (PDO), <code>Model.php</code>.</td></tr>
    <tr><td><code>app/controllers/</code></td><td>7 kontroler domain: <code>Home</code>, <code>Auth</code>, <code>Customer</code>, <code>Scan</code>, <code>Sunscreen</code>, <code>Chatbot</code>, dan <code>Admin</code>.</td></tr>
    <tr><td><code>app/models/</code></td><td>6 model akses data: <code>User</code>, <code>Scan</code>, <code>Product</code>, <code>AiTokenUsage</code>, <code>ActivityLog</code>, dan <code>Setting</code>.</td></tr>
    <tr><td><code>app/services/</code></td><td>19 kelas layanan SOLID (Interface, Provider ONNX/CV/RGB/UV, Validator, Storage, Telegram, Email, Fonnte) + <code>ai_engine/predict.py</code>.</td></tr>
    <tr><td><code>app/views/</code></td><td>26 berkas tampilan PHP: 18 halaman penuh (home, auth, customer, admin) + 8 template bersama.</td></tr>
    <tr><td><code>chatbot-server/</code></td><td>Microservice Node.js Express (<code>server.js</code>) untuk pemrosesan percakapan Gemini AI.</td></tr>
    <tr><td><code>public/</code> &amp; <code>storage/</code></td><td>Aset publik CSS/JS/avatar (<code>public/</code>) dan penyimpanan privat foto scan + heatmap terproteksi <code>.htaccess</code> (<code>storage/scans/</code>).</td></tr>
    <tr><td><code>tests/</code> &amp; <code>.github/</code></td><td>Master runner <code>tests/run_all_tests.php</code> (42 unit test) dan pipeline CI <code>.github/workflows/main.yml</code>.</td></tr>
  </tbody>
</table>

<h2>2.4 Pola Desain &amp; Prinsip SOLID</h2>
<table class="data-table">
  <thead>
    <tr><th style="width:24%">Pola / Prinsip</th><th style="width:34%">Penerapan pada SKINCHECK</th><th>Manfaat Rekayasa</th></tr>
  </thead>
  <tbody>
    <tr><td><strong>Strategy / Provider Pattern (OCP &amp; DIP)</strong></td><td><code>SkinAnalysisProviderInterface</code> &amp; <code>SunscreenAnalysisProviderInterface</code> disuntikkan ke Service.</td><td>Memungkinkan perpindahan mesin ONNX ↔ PHP GD CV atau RGB ↔ UV tanpa mengubah <code>ScanController</code>.</td></tr>
    <tr><td><strong>Single Responsibility (SRP)</strong></td><td><code>ImageValidator</code> (validasi), <code>ImageStorage</code> (I/O privat), <code>SkinAnalysisService</code> (orkestrasi AI).</td><td>Setiap kelas terfokus, pendek, mudah diuji secara terisolasi.</td></tr>
    <tr><td><strong>Liskov Substitution (LSP)</strong></td><td><code>OnnxSkinAnalysisProvider</code> dan <code>ComputerVisionSkinProvider</code> keduanya mengembalikan <code>AnalysisResult</code>.</td><td>Kontrak data keluaran selalu identik sehingga kode pemanggil tidak pernah rusak saat fallback.</td></tr>
    <tr><td><strong>Interface Segregation (ISP)</strong></td><td>Pemisahan antarmuka <code>FaceDetectorInterface</code>, <code>SkinAnalysisProviderInterface</code>, dan <code>SunscreenAnalysisProviderInterface</code>.</td><td>Setiap implementasi hanya menyediakan metode yang benar-benar relevan dengan tugasnya.</td></tr>
    <tr><td><strong>Null Object Pattern</strong></td><td><code>NullFaceDetector</code> mengimplementasikan <code>FaceDetectorInterface</code>.</td><td>Menghindari pengecekan <code>null</code> berulang di kontroler saat detektor wajah backend opsional tidak aktif.</td></tr>
    <tr><td><strong>Value Object / DTO</strong></td><td><code>AnalysisResult</code> dan <code>SunscreenResult</code> dengan metode <code>toArray()</code> &amp; <code>toJson()</code>.</td><td>Menjamin struktur data tervalidasi dan konsisten lintas lapisan.</td></tr>
  </tbody>
</table>

<h2>2.5 Aliran Data Utama</h2>
<ul>
  <li><strong>Aliran Pemindaian Sawar Kulit</strong>: Browser HUD → <code>POST /scan/submitScan</code> → <code>ImageValidator</code> → <code>ImageStorage</code> (privat) → <code>skin_scans</code> (pending) → <code>SkinAnalysisService</code> (ONNX/CV, retry 2x) → <code>analysis_results</code> → <code>Product::getProductsByConcern</code> → halaman detail skor 4 pilar.</li>
  <li><strong>Aliran Pemeriksaan Sunscreen</strong>: Browser HUD → <code>POST /sunscreen/submitCheck</code> → <code>SunscreenAnalysisService</code> → <code>RGBSunscreenAnalysisProvider</code> (hitung coverage &amp; gambar heatmap) → <code>sunscreen_results</code> → streaming aman <code>/sunscreen/serveHeatmap</code>.</li>
  <li><strong>Aliran Chat AI &amp; Token Monitor</strong>: Widget Chat → <code>POST /chatbot/chat</code> → ambil skor scan terakhir &amp; produk dari MySQL → injeksi <em>System Prompt</em> → Node.js / Google Gemini API → catat ke <code>ai_token_usage</code> → tampilkan jawaban terformat.</li>
</ul>

<!-- ==================== BAB 3: USE CASE DIAGRAM ==================== -->
<h1 class="chapter-start">3. Diagram Use Case</h1>

<h2>3.1 Aktor dan Ringkasan Keseluruhan</h2>
<p>Sistem SKINCHECK melibatkan <strong>tiga aktor manusia</strong> (Guest, Customer, dan Admin) serta <strong>dua sistem layanan eksternal 24 jam</strong> (Layanan OTP Multi-Kanal dan Layanan AI Vision/LLM).</p>
<table class="data-table">
  <thead>
    <tr><th style="width:24%">Aktor</th><th>Deskripsi</th><th style="width:16%">Cakupan F-xx</th></tr>
  </thead>
  <tbody>
    <tr><td><strong>Guest (Tamu)</strong></td><td>Pengunjung tanpa login; membaca edukasi fitur, mendaftar akun, memverifikasi OTP, login, dan reset sandi.</td><td>F-01 – F-06</td></tr>
    <tr><td><strong>Customer (Pelanggan)</strong></td><td>Pengguna terdaftar yang memindai sawar kulit, mengecek kerataan sunscreen, melacak riwayat, dan berkonsultasi dengan Gemini AI.</td><td>F-05 – F-24</td></tr>
    <tr><td><strong>Admin (Eksekutif)</strong></td><td>Pengelola platform; memantau KPI, mengelola customer &amp; katalog skincare, mengaudit scan, memantau token Gemini, dan mengatur API Gateway.</td><td>F-05, F-07, F-25 – F-34</td></tr>
  </tbody>
</table>

<div class="landscape-wrap">
  <div class="figure-box">
    {svg_usecase_overview()}
    <p class="figure-caption">Gambar 2 — Diagram Use Case Keseluruhan — Sistem SKINCHECK</p>
  </div>
</div>

<h2>3.2 Guest (Tamu)</h2>
<div class="figure-box">
  {svg_usecase_guest()}
  <p class="figure-caption">Gambar 3 — Diagram Use Case — Guest (Tamu)</p>
</div>
<p>Tamu dapat meninjau halaman beranda edukatif (F-01), mendaftar akun baru (F-02), memverifikasi kode OTP melalui Telegram/Email/WhatsApp (F-03), masuk ke sistem (F-05), atau mengajukan pemulihan kata sandi (F-06). <code>AuthGuard</code> memblokir seluruh rute <code>/customer/*</code> dan <code>/admin/*</code> bagi tamu.</p>

<h2>3.3 Customer (Pelanggan)</h2>
<div class="figure-box">
  {svg_usecase_customer()}
  <p class="figure-caption">Gambar 4 — Diagram Use Case — Customer (Pelanggan)</p>
</div>
<p>Pelanggan merupakan aktor utama yang menjalankan siklus perawatan kulit berbasis data: memantau ringkasan skor di dasbor (F-18), melakukan pemindaian wajah melalui Smart Camera HUD (F-09–F-12), memeriksa kerataan sunscreen beserta peta panas <em>Heatmap</em> (F-14–F-17), melacak riwayat serta rekomendasi produk (F-19–F-20), dan berkonsultasi dengan asisten AI Gemini (F-21–F-24).</p>

<h2>3.4 Admin (Administrator Eksekutif)</h2>
<div class="figure-box">
  {svg_usecase_admin()}
  <p class="figure-caption">Gambar 5 — Diagram Use Case — Admin (Administrator Eksekutif)</p>
</div>
<p>Administrator mengakses panel manajemen melalui gerbang login terpisah untuk memantau metrik KPI global (F-25), mengelola data pelanggan dan katalog produk (F-26–F-27), meninjau log audit pemindaian (F-28), mengawasi konsumsi token Google Gemini secara langsung (F-29), menjalankan Executive AI Analytics (F-30), serta memperbarui dan menguji seluruh kunci API eksternal secara terpusat (F-31–F-34).</p>

<h2>3.5 Skenario Use Case Utama</h2>

<h3>UC-01 — Pemindaian Sawar Kulit 4 Pilar via Smart Camera HUD (F-09 s.d. F-13)</h3>
<table class="data-table">
  <thead><tr><th style="width:22%">Aspek</th><th>Rincian Skenario</th></tr></thead>
  <tbody>
    <tr><td>Aktor</td><td>Customer (Pelanggan)</td></tr>
    <tr><td>Pra-kondisi</td><td>Customer telah login; peramban mengizinkan akses kamera (WebRTC).</td></tr>
    <tr><td>Alur Utama</td><td>1) Customer membuka menu Face Scan (<code>/customer/scanFace</code>) dan menekan "Aktifkan Kamera".<br>2) Sistem menampilkan viewfinder kamera beserta panduan oval wajah dan tips cahaya.<br>3) Customer mengambil foto dan menekan "Kirim Scan" menuju <code>/scan/submitScan</code>.<br>4) <code>ImageValidator</code> memverifikasi MIME, ukuran, dan resolusi; <code>ImageStorage</code> menyimpan berkas ke <code>storage/scans/</code>.<br>5) <code>SkinAnalysisService</code> memanggil provider AI (MobileNetV3 ONNX / CV) untuk menghitung skor Acne, Redness, Texture, Hyperpigmentation, dan Overall Score.<br>6) Hasil disimpan pada <code>analysis_results</code> dan halaman detail diagnostik beserta rekomendasi skincare ditampilkan.</td></tr>
    <tr><td>Alur Alternatif</td><td>Validasi citra gagal → tampilkan pesan perbaikan. Provider AI timeout → retry otomatis maks 2x; bila tetap gagal, status dicatat <code>failed</code> dan tombol Retry (F-13) tersedia.</td></tr>
    <tr><td>Pasca-kondisi</td><td>Record <code>skin_scans</code> berstatus <code>completed</code>, skor 4 pilar tersimpan, dan foto hanya dapat diakses pemilik.</td></tr>
  </tbody>
</table>

<h3>UC-02 — Pemeriksaan Kerataan Sunscreen &amp; Heatmap (F-14 s.d. F-17)</h3>
<table class="data-table">
  <thead><tr><th style="width:22%">Aspek</th><th>Rincian Skenario</th></tr></thead>
  <tbody>
    <tr><td>Aktor</td><td>Customer (Pelanggan)</td></tr>
    <tr><td>Pra-kondisi</td><td>Customer telah mengoleskan tabir surya dan membuka menu Sunscreen Check.</td></tr>
    <tr><td>Alur Utama</td><td>1) Customer memotret wajah melalui kamera HUD dan mengirim ke <code>/sunscreen/submitCheck</code>.<br>2) <code>RGBSunscreenAnalysisProvider</code> melakukan segmentasi kontur wajah, menghitung <code>coverage_score</code> (0–100%), menentukan status kerataan, dan mendeteksi <code>low_coverage_areas</code>.<br>3) Sistem membangkitkan berkas citra <em>Heatmap</em> gradien warna dan menyimpannya ke direktori privat.<br>4) Sistem menampilkan persentase proteksi, citra Heatmap via <code>/sunscreen/serveHeatmap</code>, dan sanggahan non-UV wajib.</td></tr>
    <tr><td>Alur Alternatif</td><td>Bila skor keyakinan (<em>confidence</em>) &lt; 0,60 → sistem menyalakan peringatan keyakinan rendah dan menyarankan pencahayaan depan yang lebih merata.</td></tr>
    <tr><td>Pasca-kondisi</td><td>Data tersimpan pada tabel <code>sunscreen_results</code> dan terhubung ke dasbor pelanggan.</td></tr>
  </tbody>
</table>

<h3>UC-03 — Konsultasi AI Skincare Assistant Sadar-Konteks (F-21 s.d. F-24, F-29)</h3>
<table class="data-table">
  <thead><tr><th style="width:22%">Aspek</th><th>Rincian Skenario</th></tr></thead>
  <tbody>
    <tr><td>Aktor</td><td>Customer / Admin</td></tr>
    <tr><td>Pra-kondisi</td><td>Pengguna telah login; kunci API Google Gemini terkonfigurasi di tabel <code>settings</code>.</td></tr>
    <tr><td>Alur Utama</td><td>1) Pengguna mengirim pertanyaan melalui widget chat atau halaman Konsultasi AI.<br>2) <code>ChatbotController</code> memuat hasil scan terakhir &amp; katalog produk (untuk customer) atau KPI makro (untuk admin).<br>3) Sistem menyusun <em>System Prompt</em> terinjeksi konteks dan mengirimkannya ke Google Gemini Flash.<br>4) Jawaban AI dikembalikan ke browser dan metadata token dicatat ke <code>ai_token_usage</code>.</td></tr>
    <tr><td>Alur Alternatif</td><td>Provider utama terkena batas kuota (429) → beralih ke model cadangan (fallback) atau tampilkan pesan layanan ramah tanpa crash.</td></tr>
    <tr><td>Pasca-kondisi</td><td>Pengguna memperoleh saran edukatif non-klinis; metrik token pada dasbor admin bertambah secara real-time.</td></tr>
  </tbody>
</table>

<h3>UC-04 — Pengelolaan API Gateway &amp; Uji Koneksi Live AJAX (F-31 s.d. F-34)</h3>
<table class="data-table">
  <thead><tr><th style="width:22%">Aspek</th><th>Rincian Skenario</th></tr></thead>
  <tbody>
    <tr><td>Aktor</td><td>Admin (Administrator Eksekutif)</td></tr>
    <tr><td>Pra-kondisi</td><td>Admin telah masuk melalui <code>/admin/login</code> dan membuka <code>/admin/settings</code>.</td></tr>
    <tr><td>Alur Utama</td><td>1) Admin memperbarui token Telegram, Resend, Fonnte, atau Gemini API Key beserta model utama/fallback.<br>2) Admin menekan tombol uji koneksi (<code>POST /admin/testApiKey</code>).<br>3) <code>Setting</code> menjalankan pengujian live ke endpoint eksternal terkait dan mengembalikan status JSON berhasil/gagal.<br>4) Admin menyimpan perubahan ke tabel <code>settings</code> tanpa perlu me-restart atau mengubah kode sumber.</td></tr>
    <tr><td>Pasca-kondisi</td><td>Kunci rahasia tersimpan dengan flag <code>is_secret = 1</code> dan langsung berlaku pada permintaan berikutnya.</td></tr>
  </tbody>
</table>

<!-- ==================== BAB 4: ACTIVITY DIAGRAM ==================== -->
<h1 class="chapter-start">4. Diagram Activity</h1>

<h2>4.1 Pemindaian Sawar Kulit &amp; Inferensi AI</h2>
<div class="figure-box">
  {svg_activity_face_scan()}
  <p class="figure-caption">Gambar 6 — Diagram Activity — Pemindaian Sawar Kulit &amp; Inferensi AI</p>
</div>
<p>Diagram Activity dengan tiga <em>swimlane</em> (Gambar 6) memperlihatkan koordinasi antara validasi pencahayaan pada peramban klien, verifikasi keamanan berkas pada <code>ScanController</code>, serta ketahanan inferensi AI (<em>automatic retry</em> maks 2 kali dan dukungan <em>manual retry</em> apabila gagal).</p>

<h2>4.2 Pemeriksaan Kerataan Sunscreen &amp; Heatmap</h2>
<div class="figure-box">
  {svg_activity_sunscreen()}
  <p class="figure-caption">Gambar 7 — Diagram Activity — Pemeriksaan Kerataan Sunscreen &amp; Heatmap</p>
</div>

<h2>4.3 Konsultasi Chatbot AI Gemini &amp; Audit Token</h2>
<div class="figure-box">
  {svg_activity_chatbot()}
  <p class="figure-caption">Gambar 8 — Diagram Activity — Konsultasi Chatbot AI Gemini &amp; Audit Token</p>
</div>

<!-- ==================== BAB 5: CLASS DIAGRAM ==================== -->
<h1 class="chapter-start">5. Diagram Class</h1>
<p>Diagram Class SKINCHECK dipecah menjadi tiga tampilan agar setiap atribut, metode, dan penerapan prinsip <strong>SOLID</strong> terbaca dengan jernih tanpa tumpang tindih.</p>

<h2>5.1 Domain Inti &amp; Entitas Model</h2>
<div class="figure-box">
  {svg_class_domain()}
  <p class="figure-caption">Gambar 9 — Diagram Class — Domain Inti &amp; Entitas Model</p>
</div>

<h2>5.2 Arsitektur SOLID Servis AI &amp; Provider</h2>
<div class="figure-box">
  {svg_class_ai_solid()}
  <p class="figure-caption">Gambar 10 — Diagram Class — Arsitektur SOLID Servis AI &amp; Provider</p>
</div>
<p>Gambar 10 menegaskan kepatuhan penuh terhadap kelima prinsip <strong>SOLID</strong>: pemisahan antarmuka (ISP), penyuntikan dependensi berbasis kontrak abstrak (DIP), keterbukaan penambahan provider baru tanpa memodifikasi service/controller (OCP), substitusi mulus antara ONNX dan PHP GD CV (LSP), serta tanggung jawab tunggal per kelas (SRP).</p>

<h2>5.3 Modul Pendukung &amp; Komunikasi</h2>
<div class="figure-box">
  {svg_class_support()}
  <p class="figure-caption">Gambar 11 — Diagram Class — Modul Pendukung &amp; Komunikasi</p>
</div>

<!-- ==================== BAB 6: SEQUENCE DIAGRAM ==================== -->
<h1 class="chapter-start">6. Diagram Sequence</h1>

<h2>6.1 Analisis Sawar Kulit End-to-End</h2>
<div class="figure-box">
  {svg_sequence_scan()}
  <p class="figure-caption">Gambar 12 — Diagram Sequence — Analisis Sawar Kulit End-to-End</p>
</div>

<table class="data-table">
  <thead><tr><th style="width:6%">No</th><th style="width:28%">Pengirim → Penerima</th><th style="width:28%">Pesan / Operasi</th><th>Keterangan</th></tr></thead>
  <tbody>
    <tr><td>1</td><td>Browser → ScanController</td><td><code>POST /scan/submitScan</code></td><td>Mengirim data gambar hasil tangkapan kamera HUD.</td></tr>
    <tr><td>2–3</td><td>ScanController ↔ ImageValidator</td><td><code>validate(imagePayload)</code></td><td>Memeriksa tipe MIME asli, batas ukuran, dan dimensi citra.</td></tr>
    <tr><td>4–5</td><td>ScanController ↔ ImageStorage</td><td><code>saveScan(userId, bytes)</code></td><td>Menyimpan citra ke folder privat terisolasi <code>storage/scans/</code>.</td></tr>
    <tr><td>6–7</td><td>ScanController ↔ MySQL</td><td><code>INSERT skin_scans</code></td><td>Mencatat sesi scan berstatus awal <code>pending</code> dan mengembalikan <code>scanId</code>.</td></tr>
    <tr><td>8–11</td><td>ScanController → Service → ONNX/CV</td><td><code>analyzeScan() → analyze()</code></td><td>Menjalankan inferensi 4 pilar sawar kulit (termasuk blok <code>alt</code> retry maks 2x).</td></tr>
    <tr><td>12–13</td><td>ScanController ↔ MySQL</td><td><code>INSERT analysis_results</code></td><td>Menyimpan 4 skor pilar + overall score dan memperbarui status <code>completed</code>.</td></tr>
    <tr><td>14</td><td>ScanController → Browser</td><td><code>Redirect /customer/scanDetail</code></td><td>Menampilkan halaman detail diagnostik dan kurasi produk skincare.</td></tr>
  </tbody>
</table>

<h2>6.2 Konsultasi Chatbot AI Gemini End-to-End</h2>
<div class="figure-box">
  {svg_sequence_chatbot()}
  <p class="figure-caption">Gambar 13 — Diagram Sequence — Konsultasi Chatbot AI Gemini End-to-End</p>
</div>

<table class="data-table">
  <thead><tr><th style="width:6%">No</th><th style="width:28%">Pengirim → Penerima</th><th style="width:28%">Pesan / Operasi</th><th>Keterangan</th></tr></thead>
  <tbody>
    <tr><td>1</td><td>Browser Widget → ChatbotController</td><td><code>POST /chatbot/chat</code></td><td>Mengirim pesan pertanyaan pengguna dalam format JSON.</td></tr>
    <tr><td>2–4</td><td>ChatbotController ↔ MySQL</td><td><code>SELECT skor scan &amp; produk</code></td><td>Mengambil konteks klinis terbaru pengguna &amp; konfigurasi API key dari <code>settings</code>.</td></tr>
    <tr><td>5–7</td><td>Controller → Node.js → Gemini API</td><td><code>POST generateContent</code></td><td>Memanggil Google Gemini Flash (dengan <code>alt</code> model fallback bila terjadi 429/timeout).</td></tr>
    <tr><td>8–10</td><td>ChatbotController ↔ AiTokenUsage</td><td><code>recordUsage(tokens, ms)</code></td><td>Mencatat <code>prompt_tokens</code>, <code>completion_tokens</code>, dan <code>total_tokens</code> untuk dasbor admin.</td></tr>
    <tr><td>11</td><td>ChatbotController → Browser</td><td><code>JSON Markdown Reply</code></td><td>Menampilkan saran bahan aktif skincare secara interaktif pada widget obrolan.</td></tr>
  </tbody>
</table>

<!-- ==================== BAB 7: ERD ==================== -->
<h1 class="chapter-start">7. Entity Relationship Diagram (ERD)</h1>

<h2>7.1 Diagram Relasi 8 Tabel</h2>
<p>Skema basis data <code>skincheck</code> terdiri dari <strong>8 tabel operasional</strong> dan <strong>4 foreign key aktif</strong> yang diverifikasi langsung dari <code>schema.sql</code> dan <code>database_infinityfree.sql</code>.</p>

<div class="figure-box">
  {svg_erd_full()}
  <p class="figure-caption">Gambar 14 — Entity Relationship Diagram (ERD) — 8 Tabel Basis Data SKINCHECK</p>
</div>

<h2>7.2 Daftar Lengkap Foreign Key &amp; Integritas</h2>
<table class="data-table">
  <thead><tr><th style="width:6%">No</th><th style="width:30%">Tabel Asal (Kolom FK)</th><th style="width:24%">Tabel Tujuan (Kolom PK)</th><th style="width:18%">ON DELETE</th><th>Tujuan Integritas</th></tr></thead>
  <tbody>
    <tr><td>1</td><td><code>skin_scans (user_id)</code></td><td><code>users (id)</code></td><td>CASCADE</td><td>Menghapus riwayat scan terkait saat akun pengguna dihapus.</td></tr>
    <tr><td>2</td><td><code>analysis_results (skin_scan_id)</code></td><td><code>skin_scans (id)</code></td><td>CASCADE</td><td>Menjamin setiap hasil 4 pilar terikat sah pada sesi pemindaian induknya.</td></tr>
    <tr><td>3</td><td><code>sunscreen_results (skin_scan_id)</code></td><td><code>skin_scans (id)</code></td><td>CASCADE</td><td>Menjamin data cakupan dan heatmap terikat sah pada sesi scan induknya.</td></tr>
    <tr><td>4</td><td><code>activity_logs (user_id)</code></td><td><code>users (id)</code></td><td>SET NULL</td><td>Menjaga catatan audit keamanan tetap utuh meskipun akun pengguna dihapus.</td></tr>
  </tbody>
</table>
<p>Tabel <code>products</code>, <code>settings</code>, dan <code>ai_token_usage</code> dirancang tanpa <em>hard foreign key constraint</em> ke <code>users</code> agar katalog produk, konfigurasi API, serta statistik agregat token historis tetap konsisten dan berkecepatan tulis tinggi.</p>

<!-- ==================== BAB 8: DESAIN ANTARMUKA & FIGMA ==================== -->
<h1 class="chapter-start">8. Desain Antarmuka &amp; Prototipe UI/UX Figma</h1>

<h2>8.1 Prinsip Desain &amp; Design Tokens</h2>
<ul>
  <li><strong>Estetika Klinis Modern (Clinical Health System)</strong>: Mengusung latar bersih terang (<code>#F8FAFC</code> / <code>#FFFFFF</code>) dengan aksen utama hijau zamrud klinis (<code>#0D7366</code>) dan teal bercahaya (<code>#14B8A6</code>) yang merepresentasikan kebersihan dermatologi serta kepercayaan medis.</li>
  <li><strong>Hierarki Skor Visual Instan</strong>: Skor kesehatan 0–100 divisualisasikan memakai cincin progres (<em>circular gauge</em>) dan bar indikator berkode warna: Jerawat (<code>#EF4444</code>), Kemerahan (<code>#F59E0B</code>), Tekstur (<code>#3B82F6</code>), dan Pigmentasi (<code>#8B5CF6</code>).</li>
  <li><strong>Navigasi Terpisah per Peran</strong>: Pelanggan menggunakan <em>top pill navigation bar</em> yang ringkas (Dashboard, Face Scan, Sunscreen, Riwayat, Konsultasi), sedangkan Administrator menggunakan <em>left sidebar navigation</em> profesional.</li>
  <li><strong>Akses Cepat Asisten AI</strong>: Tombol widget chatbot terapung (<em>floating teal pill</em>) selalu tersedia di pojok kanan bawah seluruh halaman.</li>
</ul>

<h2>8.2 Inventaris Layar Aktual (26 Berkas View: 18 Halaman + 8 Template)</h2>
<table class="data-table">
  <thead><tr><th style="width:26%">Berkas View / Rute</th><th style="width:24%">Nama Layar</th><th>Komponen &amp; Fungsi Utama</th></tr></thead>
  <tbody>
    <tr><td><code>home/index.php</code> (<code>/</code>)</td><td>Beranda Publik</td><td>Hero SkinCheck, 4 kartu indikator, 4 kartu fitur utama, panduan 3 langkah, CTA Mulai Scan.</td></tr>
    <tr><td><code>auth/login.php</code> &amp; <code>register.php</code></td><td>Masuk &amp; Daftar Akun</td><td>Formulir autentikasi email/password, validasi input, tautan registrasi dan lupa sandi.</td></tr>
    <tr><td><code>auth/verify_otp.php</code></td><td>Verifikasi OTP Multi-Kanal</td><td>Input 6 digit OTP, hitung mundur cooldown 60 detik, status verifikasi Telegram/Email/WA.</td></tr>
    <tr><td><code>auth/forgot_password.php</code> &amp; <code>reset_password.php</code></td><td>Pemulihan Kata Sandi</td><td>Pengajuan token pemulihan dan formulir penetapan kata sandi baru.</td></tr>
    <tr><td><code>customer/dashboard.php</code></td><td>Dasbor Pelanggan</td><td>Skor keseluruhan lingkaran, 4 bar pilar, kartu Proteksi UV, 4 pintasan cepat, 5 riwayat terbaru.</td></tr>
    <tr><td><code>customer/scan_face.php</code></td><td>Face Scan (Smart HUD)</td><td>Viewfinder kamera WebRTC, tombol Aktifkan Kamera, panel 4 Tips Scan Akurat &amp; badge cahaya.</td></tr>
    <tr><td><code>customer/scan_detail.php</code></td><td>Detail Diagnostik Scan</td><td>Foto aman terotorisasi, rincian 4 pilar, catatan AI, tombol Retry (bila gagal), rekomendasi produk.</td></tr>
    <tr><td><code>customer/sunscreen_check.php</code> &amp; <code>sunscreen_result.php</code></td><td>Sunscreen Check &amp; Heatmap</td><td>Kamera cek sunscreen, persentase coverage, klasifikasi status, peta panas Heatmap, disclaimer non-UV.</td></tr>
    <tr><td><code>customer/history.php</code></td><td>Riwayat Pemindaian</td><td>Kartu KPI (Total Pemeriksaan, Rata-rata Skor, Proteksi Optimal) &amp; tabel riwayat lengkap.</td></tr>
    <tr><td><code>customer/ai_assistant.php</code> &amp; <code>profile.php</code></td><td>Konsultasi AI &amp; Profil</td><td>Layar obrolan penuh Gemini AI sadar-konteks serta pengaturan profil, Telegram ID, &amp; avatar.</td></tr>
    <tr><td><code>admin/login.php</code> &amp; <code>dashboard.php</code></td><td>Login &amp; Dasbor Admin</td><td>4 Kartu KPI, kartu Live Google Gemini Token Monitor, tab Customer Registry, Cosmetics Catalog, Scan Audits.</td></tr>
    <tr><td><code>admin/ai_assistant.php</code> &amp; <code>settings.php</code></td><td>Executive AI &amp; Pengaturan</td><td>Konsol analitik AI level admin serta konfigurasi API Key dinamis + uji koneksi Live AJAX.</td></tr>
    <tr><td><code>templates/*.php</code> (8 berkas)</td><td>Komponen Bersama</td><td>Header/Footer publik, customer, admin, <code>chatbot_widget.php</code>, dan <code>toast.php</code>.</td></tr>
  </tbody>
</table>

<h2>8.3 Alur Navigasi &amp; Komponen Bersama</h2>
<ul>
  <li><strong>Alur Guest</strong>: Beranda (<code>/</code>) → Daftar Akun (<code>/auth/register</code>) → Verifikasi OTP (<code>/auth/verifyOtp</code>) → Dasbor Pelanggan.</li>
  <li><strong>Alur Customer</strong>: Dasbor → Face Scan (<code>/customer/scanFace</code>) → Detail Diagnostik (<code>/customer/scanDetail</code>) → Rekomendasi Produk &amp; Konsultasi AI → Cek Sunscreen &amp; Heatmap → Riwayat Progres.</li>
  <li><strong>Alur Admin</strong>: Login Admin (<code>/admin/login</code>) → Dasbor Eksekutif &amp; Token Monitor → Customer Registry / Cosmetics Catalog / Scan Audits → Executive AI Assistant → Pengaturan Sistem &amp; Uji API Live.</li>
</ul>

<h2 class="chapter-start" style="page-break-before: always;">8.4 Tangkapan Layar UI Aktual (Acuan Langsung Prototipe Figma)</h2>
<p>Berikut adalah lima tangkapan layar utama antarmuka aktual platform SKINCHECK yang telah dibangun dan menjadi acuan presisi 1-ke-1 bagi penyusunan frame prototipe interaktif UI/UX di Figma:</p>

<div class="figure-box">
  <img class="ui-shot" src="file:///{ui_dir}/ui_landing_clean.png" alt="Landing Page SkinCheck"/>
  <p class="figure-caption">Gambar 15 — Antarmuka Beranda Publik (Landing Page) SKINCHECK</p>
</div>

<div class="figure-box" style="page-break-before: always;">
  <img class="ui-shot" src="file:///{ui_dir}/ui_customer_dashboard.png" alt="Customer Dashboard SkinCheck"/>
  <p class="figure-caption">Gambar 16 — Antarmuka Dasbor Pelanggan (Customer Dashboard — 4 Pilar &amp; Proteksi UV)</p>
</div>

<div class="figure-box" style="page-break-before: always;">
  <img class="ui-shot" src="file:///{ui_dir}/ui_face_scan.png" alt="Face Scan HUD SkinCheck"/>
  <p class="figure-caption">Gambar 17 — Antarmuka Pemindaian Wajah (Smart Camera HUD Viewfinder &amp; Tips Scan Akurat)</p>
</div>

<div class="figure-box" style="page-break-before: always;">
  <img class="ui-shot" src="file:///{ui_dir}/ui_history.png" alt="Riwayat Pemindaian SkinCheck"/>
  <p class="figure-caption">Gambar 18 — Antarmuka Riwayat Pemindaian Kulit (History Tracking &amp; Metrik Progres)</p>
</div>

<div class="figure-box" style="page-break-before: always;">
  <img class="ui-shot" src="file:///{ui_dir}/ui_admin_dashboard.png" alt="Admin Dashboard SkinCheck"/>
  <p class="figure-caption">Gambar 19 — Antarmuka Dasbor Admin Eksekutif &amp; Google Gemini Token Monitor</p>
</div>

<h2 style="page-break-before: always;">8.5 Spesifikasi Prototipe Interaktif Figma</h2>
<p>Bagian ini merumuskan spesifikasi teknis lengkap siap-bangun untuk berkas prototipe interaktif <strong>"SKINCHECK — UI/UX Interactive Prototype"</strong> pada Figma, selaras dengan 18 layar aktual pada Bab 8.2 dan 5 tangkapan layar referensi (Gambar 15–19).</p>

<table class="data-table">
  <thead><tr><th style="width:26%">Aspek Design System</th><th>Spesifikasi Token Figma</th></tr></thead>
  <tbody>
    <tr><td>Ukuran Frame</td><td>Desktop: <strong>1440 × 900 px</strong> (18 frame penuh); Mobile Responsif: <strong>390 × 844 px</strong> (Beranda, Login, Dashboard, Face Scan, Heatmap).</td></tr>
    <tr><td>Palet Warna (Color Styles)</td><td>Primary Clinical Teal: <code>#0D7366</code>; Secondary Mint: <code>#14B8A6</code>; Background Surface: <code>#F8FAFC</code>; Card Surface: <code>#FFFFFF</code>; Border Subtle: <code>#E2E8F0</code>; Indikator: Acne <code>#EF4444</code>, Redness <code>#F59E0B</code>, Texture <code>#3B82F6</code>, Pigment <code>#8B5CF6</code>.</td></tr>
    <tr><td>Tipografi (Text Styles)</td><td>Font Family: <strong>Plus Jakarta Sans / Inter</strong>; H1 Display 36–48px Bold; H2 Section 24px Bold; Card Title 16px SemiBold; Body 14px Regular; Metric KPI 28px ExtraBold.</td></tr>
    <tr><td>Radius &amp; Elevasi</td><td>Card Radius: <code>16px</code>; Pill Nav &amp; Primary Button: <code>9999px</code> (full pill) dengan soft drop-shadow <code>0 4px 14px rgba(13, 115, 102, 0.18)</code>.</td></tr>
    <tr><td>Komponen Reusable (Variants)</td><td>Top Customer Navbar (5 state aktif), Admin Left Sidebar, Metric KPI Card, 4-Pillar Progress Gauge, Face Scan HUD Viewfinder, Data Table Row, Badge Status UV, Floating Chatbot Widget (Closed / Open).</td></tr>
  </tbody>
</table>

<h3>Alur Interaktif Wajib pada Prototype Links Figma (Minimal 5 Alur Klik):</h3>
<ol>
  <li><strong>Alur Registrasi &amp; OTP</strong>: Beranda → tombol "Daftar Akun" → Form Registrasi → Layar Verifikasi OTP 6-digit → Dasbor Pelanggan.</li>
  <li><strong>Alur Face Scan 4 Pilar</strong>: Dasbor → tombol "Mulai Scan Wajah" → Face Scan HUD ("Aktifkan Kamera" → overlay oval aktif) → Detail Diagnostik 4 Pilar &amp; Rekomendasi Produk.</li>
  <li><strong>Alur Sunscreen &amp; Heatmap</strong>: Dasbor → "Cek Sunscreen" → Pemotretan HUD → Hasil Persentase Coverage &amp; Visualisasi Heatmap.</li>
  <li><strong>Alur Riwayat &amp; Konsultasi AI</strong>: Navbar "Riwayat" → klik "Detail Diagnostik" → klik tombol Floating Chatbot di pojok kanan bawah → panel obrolan Gemini AI terbuka.</li>
  <li><strong>Alur Eksekutif Admin</strong>: Login Admin → Dasbor Admin (KPI + Google Gemini Token Monitor) → klik tab "Cosmetics Catalog" / "Scan Audits" → klik menu "Pengaturan Sistem" → tombol Uji Koneksi AJAX.</li>
</ol>

<div class="callout">
  <strong>Daftar Periksa Penyelesaian Figma</strong>: (1) Buat berkas Figma <em>"SKINCHECK — UI/UX Prototype"</em> dengan 18 frame desktop + 5 frame mobile; (2) Impor color &amp; text tokens sesuai tabel di atas; (3) Hubungkan kelima alur interaksi menggunakan Figma Prototype Connectors (Smart Animate 250ms Ease-Out); (4) Atur izin berbagi ke <em>"Anyone with the link can view"</em> dan cantumkan tautannya pada <code>README.md</code> repositori.
</div>

<!-- ==================== BAB 9: KEAMANAN & KEPUTUSAN DESAIN ==================== -->
<h1 class="chapter-start">9. Keamanan &amp; Keputusan Desain</h1>
<table class="data-table">
  <thead><tr><th style="width:26%">Keputusan Desain</th><th style="width:26%">Alternatif yang Ditolak</th><th>Alasan Rekayasa</th></tr></thead>
  <tbody>
    <tr><td>Arsitektur PHP MVC + Node.js Microservice</td><td>Rewrite penuh ke framework berat / monolit tunggal</td><td>PHP MVC ringan untuk XAMPP &amp; shared hosting, sementara Node.js Express optimal untuk komunikasi asinkron Google Gemini API.</td></tr>
    <tr><td>Dual Vision Engine (MobileNetV3 ONNX + Fallback PHP GD CV)</td><td>Hanya bergantung pada Python ONNX lokal</td><td>Menjamin aplikasi tetap fungsional 100% baik di server lokal (Python ONNX) maupun hosting shared tanpa subprocess Python (PHP GD).</td></tr>
    <tr><td>Ownership-Scoped Image Streaming via PHP</td><td>Menyimpan foto wajah di folder <code>public/</code></td><td>Mencegah pencurian foto wajah pribadi melalui tebakan URL langsung (pencegahan IDOR &amp; kebocoran privasi).</td></tr>
    <tr><td>Konfigurasi API Terpusat di Tabel <code>settings</code></td><td>Hardcoding API key di berkas PHP</td><td>Memudahkan rotasi kunci Gemini, Telegram, Resend, dan Fonnte dari UI admin lengkap dengan tombol uji live AJAX.</td></tr>
  </tbody>
</table>

<!-- ==================== LAMPIRAN A, B, C ==================== -->
<h1 class="chapter-start">Lampiran A — Ketertelusuran PRD → SDD</h1>
<table class="data-table">
  <thead><tr><th style="width:12%">Kode</th><th style="width:28%">Kebutuhan PRD</th><th style="width:18%">Status</th><th>Elemen Desain pada Dokumen SDD</th></tr></thead>
  <tbody>
    <tr><td>F-01</td><td>Beranda publik &amp; edukasi</td><td>Terimplementasi</td><td>UC Guest §3.2; Layar <code>home/index.php</code> (Gambar 15).</td></tr>
    <tr><td>F-02–F-04</td><td>Registrasi &amp; OTP multi-kanal</td><td>Terimplementasi</td><td>UC Guest §3.2; Class Pendukung §5.3 (Telegram, Email, Fonnte); <code>AuthController</code>.</td></tr>
    <tr><td>F-05–F-08</td><td>Login, reset sandi, logout, profil</td><td>Terimplementasi</td><td>UC §3.2–3.4; Class Domain §5.1 (<code>User</code>); <code>AuthGuard</code>; ERD <code>users</code>.</td></tr>
    <tr><td>F-09–F-13</td><td>Scan HUD &amp; 4 pilar sawar kulit</td><td>Terimplementasi</td><td>UC Customer §3.3 (UC-01); Activity §4.1; Class SOLID §5.2; Sequence §6.1; Gambar 17.</td></tr>
    <tr><td>F-14–F-17</td><td>Sunscreen Check &amp; Heatmap</td><td>Terimplementasi</td><td>UC Customer §3.3 (UC-02); Activity §4.2; Class SOLID §5.2; ERD <code>sunscreen_results</code>.</td></tr>
    <tr><td>F-18–F-20</td><td>Dasbor, riwayat &amp; produk</td><td>Terimplementasi</td><td>UC Customer §3.3; Class Domain §5.1 (<code>Scan</code>, <code>Product</code>); Gambar 16 &amp; 18.</td></tr>
    <tr><td>F-21–F-24</td><td>Chatbot AI Gemini sadar-konteks</td><td>Terimplementasi</td><td>UC Customer §3.3 (UC-03); Activity §4.3; Sequence §6.2; <code>chatbot-server/server.js</code>.</td></tr>
    <tr><td>F-25–F-28</td><td>Dasbor Admin, customer, katalog, audit</td><td>Terimplementasi</td><td>UC Admin §3.4; ERD <code>users</code>, <code>products</code>, <code>activity_logs</code>; Gambar 19.</td></tr>
    <tr><td>F-29–F-30</td><td>Live Token Monitor &amp; Executive AI</td><td>Terimplementasi</td><td>UC Admin §3.4; Class <code>AiTokenUsage</code>; Sequence §6.2; Gambar 19.</td></tr>
    <tr><td>F-31–F-34</td><td>API Gateway &amp; Live AJAX Test</td><td>Terimplementasi</td><td>UC Admin §3.4 (UC-04); Class <code>Setting</code>; ERD <code>settings</code>; <code>/admin/testApiKey</code>.</td></tr>
  </tbody>
</table>

<h1 class="chapter-start">Lampiran B — Inventaris Rute &amp; Endpoint Aplikasi</h1>
<table class="data-table">
  <thead><tr><th style="width:14%">Metode</th><th style="width:36%">Rute / Endpoint</th><th>Controller &amp; Kegunaan</th></tr></thead>
  <tbody>
    <tr><td>GET</td><td><code>/</code></td><td><code>HomeController::index</code> — Halaman beranda publik (F-01).</td></tr>
    <tr><td>GET, POST</td><td><code>/auth/login</code>, <code>/auth/register</code></td><td><code>AuthController</code> — Autentikasi masuk &amp; registrasi (F-02, F-05).</td></tr>
    <tr><td>GET, POST</td><td><code>/auth/verifyOtp</code>, <code>/auth/verifyDirect</code>, <code>/auth/resendOtp</code></td><td><code>AuthController</code> — Verifikasi OTP 6-digit &amp; 1-click link (F-03, F-04).</td></tr>
    <tr><td>GET, POST</td><td><code>/auth/forgotPassword</code>, <code>/auth/resetPassword</code>, <code>/auth/logout</code></td><td><code>AuthController</code> — Pemulihan kata sandi &amp; keluar sesi (F-06, F-07).</td></tr>
    <tr><td>GET</td><td><code>/customer/dashboard</code>, <code>/customer/history</code>, <code>/customer/scanDetail</code></td><td><code>CustomerController</code> — Dasbor 4 pilar, riwayat, detail diagnostik (F-18–F-20).</td></tr>
    <tr><td>GET, POST</td><td><code>/customer/scanFace</code>, <code>/customer/profile</code>, <code>/customer/aiAssistant</code></td><td><code>CustomerController</code> — Layar HUD, profil &amp; avatar, konsultasi AI (F-08, F-09, F-21).</td></tr>
    <tr><td>POST, GET</td><td><code>/scan/submitScan</code>, <code>/scan/retryAnalysis</code>, <code>/scan/serveImage</code></td><td><code>ScanController</code> — Inferensi 4 pilar, retry gagal, streaming gambar privat (F-10–F-13, F-19).</td></tr>
    <tr><td>GET, POST</td><td><code>/sunscreen/check</code>, <code>/sunscreen/submitCheck</code>, <code>/sunscreen/serveHeatmap</code></td><td><code>SunscreenController</code> — Analisis kerataan sunscreen &amp; streaming heatmap (F-14–F-17).</td></tr>
    <tr><td>POST</td><td><code>/chatbot/chat</code></td><td><code>ChatbotController::chat</code> — Endpoint obrolan AI sadar-konteks Customer &amp; Admin (F-21–F-24, F-30).</td></tr>
    <tr><td>GET, POST</td><td><code>/admin/login</code>, <code>/admin/dashboard</code>, <code>/admin/aiAssistant</code></td><td><code>AdminController</code> — Portal manajemen, KPI, Token Monitor, Executive AI (F-25–F-30).</td></tr>
    <tr><td>GET, POST</td><td><code>/admin/settings</code>, <code>/admin/testApiKey</code></td><td><code>AdminController</code> — Pusat pengaturan API &amp; pengujian koneksi live AJAX (F-31–F-34).</td></tr>
  </tbody>
</table>

<h1>Lampiran C — Glosarium Desain</h1>
<table class="data-table">
  <thead><tr><th style="width:26%">Istilah</th><th>Definisi</th></tr></thead>
  <tbody>
    <tr><td>Dependency Injection</td><td>Pola penyuntikan dependensi (mis. objek provider AI) melalui konstruktor service agar kode mudah diuji dan diganti.</td></tr>
    <tr><td>Front Controller</td><td>Pola perutean tunggal (<code>app/core/App.php</code>) yang memetakan seluruh permintaan URL ke pasangan Controller dan Method.</td></tr>
    <tr><td>Swimlane</td><td>Kolom pemisah pada Activity Diagram yang mengelompokkan setiap aktivitas menurut aktor atau komponen penanggung jawabnya.</td></tr>
    <tr><td>Lifeline</td><td>Garis vertikal putus-putus pada Sequence Diagram yang melambangkan siklus hidup suatu objek selama pertukaran pesan berlangsung.</td></tr>
  </tbody>
</table>

<p class="end-marker">— Akhir Dokumen SDD —</p>
</body>
</html>
"""
