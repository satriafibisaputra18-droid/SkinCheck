# DOKUMEN SPESIFIKASI KEBUTUHAN PERANGKAT LUNAK (SRS / PRD)
## Berdasarkan Standar IEEE 830 / ISO/IEC/IEEE 29148

---

# 🌿 SKINCHECK: AI-Powered Skin Barrier & Sunscreen Analysis Platform
**Sistem Informasi Berbasis Web untuk Evaluasi Kesehatan Sawar Kulit & Proteksi Tabir Surya Berbasis Artificial Intelligence**

* **Mata Kuliah**: Rekayasa Perangkat Lunak (TIKB2503021) — 4 SKS
* **Penyusun / Mahasiswa**: Satria Fibi Saputra (NIM: 41257159)
* **Program Studi**: S1 Teknik Informatika — STMIK IKMI Cirebon
* **Dosen Pengampu**: Yudhistira Arie Wijaya, M.Kom., CDS.
* **Tema Topik Kelompok**: *Smart Healthcare & Public Service AI*
* **Sifat Proyek**: Mandiri / Perorangan (*100% Individual Ownership & Full-App Build*)
* **Versi Dokumen**: 1.0.0
* **Tanggal Rilis**: 15 November 2026

---

## DAFTAR ISI
1. [BAB I: PENDAHULUAN](#bab-i-pendahuluan)
   - 1.1 Tujuan Dokumen
   - 1.2 Ruang Lingkup Produk
   - 1.3 Definisi, Akronim, dan Singkatan
   - 1.4 Referensi Standar
   - 1.5 Gambaran Umum Dokumen
2. [BAB II: DESKRIPSI UMUM SISTEM](#bab-ii-deskripsi-umum-sistem)
   - 2.1 Latar Belakang Masalah
   - 2.2 Perspektif Produk & Arsitektur Tingkat Tinggi
   - 2.3 Karakteristik & Persona Pengguna
   - 2.4 Batasan Sistem, Asumsi, dan Ketergantungan
3. [BAB III: SPESIFIKASI KEBUTUHAN FUNGSIONAL (USER STORIES & ACCEPTANCE CRITERIA)](#bab-iii-spesifikasi-kebutuhan-fungsional)
   - 3.1 Modul 1: Autentikasi Multi-Kanal & Keamanan OTP
   - 3.2 Modul 2: Smart Camera HUD Viewfinder & Validasi Citra Wajah
   - 3.3 Modul 3: Evaluasi Klinis 4 Pilar Sawar Kulit (Skin Barrier)
   - 3.4 Modul 4: Pemeriksaan Kerataan Sunscreen (Sunscreen Check & Heatmap)
   - 3.5 Modul 5: AI Skincare Assistant Interaktif (Google Gemini Context-Aware)
   - 3.6 Modul 6: Riwayat Pemeriksaan & Pelacakan Progres Kesehatan Kulit
   - 3.7 Modul 7: Kurasi Rekomendasi Produk Skincare Berbasis Masalah
   - 3.8 Modul 8: Executive Admin Dashboard & Audit Log Pengguna
   - 3.9 Modul 9: Live Gemini AI Token & Engine Monitor Console
   - 3.10 Modul 10: Centralized Dynamic Settings & API Gateway Console
4. [BAB IV: SPESIFIKASI KEBUTUHAN NON-FUNGSIONAL (NFR)](#bab-iv-spesifikasi-kebutuhan-non-fungsional-nfr)
   - 4.1 Kinerja dan Efisiensi Waktu (Performance)
   - 4.2 Keamanan dan Privasi Data (Security)
   - 4.3 Reliabilitas dan Ketersediaan (Reliability & Availability)
   - 4.4 Usabilitas dan Aksesibilitas (Usability & Portability)
   - 4.5 Maintainability dan Kepatuhan Prinsip SOLID
5. [BAB V: SPESIFIKASI REKAYASA FITUR ARTIFICIAL INTELLIGENCE](#bab-v-spesifikasi-rekayasa-fitur-artificial-intelligence)
   - 5.1 Pipeline Computer Vision (MobileNetV3 Multi-Task ONNX)
   - 5.2 Integrasi Generative AI (Google Gemini Flash & Node.js Engine)
   - 5.3 Manajemen Token & Audit Konsumsi AI Real-Time
6. [BAB VI: MATRIKS KETERLACAKAN KEBUTUHAN (TRACEABILITY MATRIX)](#bab-vi-matriks-keterlacakan-kebutuhan)

---

## BAB I: PENDAHULUAN

### 1.1 Tujuan Dokumen
Dokumen *Product Requirement Document* (PRD) yang merangkap sebagai *Software Requirements Specification* (SRS) ini disusun berdasarkan format standar **IEEE 830** dan **ISO/IEC/IEEE 29148**. Dokumen ini bertujuan untuk:
1. Mendefinisikan secara menyeluruh kebutuhan fungsional dan non-fungsional dari platform **SkinCheck**.
2. Menguraikan arsitektur dan spesifikasi teknis penanaman fitur *Artificial Intelligence* (AI) berbasis *Computer Vision* dan *Generative AI*.
3. Menjadi acuan verifikasi, perancangan diagram UML (SDD), konstruksi kode, pengujian otomatis (*Unit Testing*), konfigurasi CI/CD, serta live demonstrasi pada Proyek Akhir Mandiri Mata Kuliah Rekayasa Perangkat Lunak.

### 1.2 Ruang Lingkup Produk
**SkinCheck** adalah platform aplikasi web pintar (*AI-Powered HealthTech/BeautyTech*) mandiri yang dirancang untuk menganalisis integritas sawar kulit (*skin barrier*) wajah dan mengevaluasi kerataan aplikasi tabir surya (*sunscreen*) secara langsung melalui browser (tanpa perlu mengunduh aplikasi native dari app store). 

Sistem ini mengintegrasikan:
- **Computer Vision (MobileNetV3 ONNX & Algoritma Visi Komputer RGB)** untuk mendeteksi 4 pilar sawar kulit (Jerawat/Pori, Eritema/Kemerahan, Kehalusan Tekstur, dan Hiperpigmentasi) serta pemetaan *Heatmap* proteksi tabir surya.
- **Large Language Model (Google Gemini Flash API)** sebagai asisten konsultasi interaktif yang sadar-konteks (*context-aware*) terhadap hasil scan riwayat pengguna, serta konsol analitik cerdas untuk level eksekutif/administrator.
- **Microservices Chatbot Engine (Node.js/Express)** yang menjembatani komunikasi data real-time dengan server inti PHP MVC.

### 1.3 Definisi, Akronim, dan Singkatan
| Istilah / Akronim | Definisi |
| :--- | :--- |
| **SRS / PRD** | *Software Requirements Specification* / *Product Requirement Document* |
| **Skin Barrier** | Lapisan pelindung terluar kulit (stratum korneum) yang menjaga hidrasi dan menangkal patogen eksternal. |
| **Eritema (Redness)** | Kemerahan pada kulit akibat pelebaran pembuluh darah kapiler yang menandakan iritasi atau peradangan. |
| **Computer Vision** | Bidang AI yang memungkinkan komputer mengekstrak dan memahami informasi visual dari citra digital. |
| **ONNX** | *Open Neural Network Exchange*, format representasi model machine learning terbuka untuk inferensi performa tinggi. |
| **LLM** | *Large Language Model* (dalam sistem ini menggunakan Google Gemini Flash). |
| **OTP** | *One-Time Password*, kode verifikasi keamanan sekali pakai 6-digit. |
| **HUD** | *Heads-Up Display*, overlay visual panduan pemosisian wajah pada viewfinder kamera peramban. |
| **MVC** | *Model-View-Controller*, pola arsitektur perangkat lunak yang memisahkan logika data, presentasi, dan alur aplikasi. |
| **SOLID** | Lima prinsip desain berorientasi objek: *Single Responsibility, Open/Closed, Liskov Substitution, Interface Segregation, Dependency Inversion*. |

### 1.4 Referensi Standar
1. **IEEE Std 830-1998**: *IEEE Recommended Practice for Software Requirements Specifications*.
2. **ISO/IEC/IEEE 29148:2018**: *Systems and software engineering — Life cycle processes — Requirements engineering*.
3. **Clean Code & SOLID Principles**: Robert C. Martin (2008), *Clean Code: A Handbook of Agile Software Craftsmanship*.
4. **Google AI Gemini Developer Documentation**: *Gemini Flash Inference & Context Protocol*.

### 1.5 Gambaran Umum Dokumen
Dokumen ini terbagi ke dalam 6 bab utama. Bab II mendeskripsikan konteks permasalahan dan persona pengguna. Bab III merinci seluruh kebutuhan fungsional dalam format *User Stories* dan *Acceptance Criteria*. Bab IV menjelaskan kebutuhan kualitas non-fungsional. Bab V membedah spesifikasi arsitektur AI secara matematis dan prosedural, dan Bab VI merangkum matriks keterlacakan (*traceability matrix*).

---

## BAB II: DESKRIPSI UMUM SISTEM

### 2.1 Latar Belakang Masalah
Kesehatan kulit wajah, khususnya sawar kulit (*skin barrier*), memegang peranan krusial dalam melindungi tubuh dari polusi lingkungan, infeksi bakteri, dan radiasi sinar ultraviolet (UV). Berdasarkan data dermatologi, kerusakan sawar kulit sering kali dipicu oleh penggunaan produk perawatan kulit yang tidak sesuai, over-eksfoliasi, serta kurangnya perlindungan tabir surya.

Namun, terdapat 3 kesenjangan utama di masyarakat:
1. **Tingginya Biaya & Keterbatasan Akses Konsultasi Klinis**: Akses ke dokter spesialis kulit atau klinik estetika berbiaya tinggi dan tidak terjangkau bagi sebagian besar masyarakat umum, pelajar, maupun mahasiswa.
2. **Subjektivitas & Sulitnya Deteksi Dini Kerusakan Kulit**: Kebanyakan individu tidak mampu menilai secara objektif apakah kulit mereka mengalami iritasi mikroskopis, kerusakan tekstur, atau ketidakmerataan tabir surya sebelum masalah berkembang menjadi lesi jerawat parah atau flek hitam menetap.
3. **Kesalahan Penggunaan Sunscreen (Under-application)**: Lebih dari 70% pengguna sunscreen tidak mengaplikasikan tabir surya secara merata, meninggalkan area rawan seperti sudut hidung, kelopak mata, dan batas rahang tanpa perlindungan UV memadai.

**SkinCheck** hadir sebagai solusi teknologi *Smart Healthcare AI* berbasis web yang menghadirkan evaluasi klinis sawar kulit mandiri yang cepat, presisi, gratis, dan dapat diakses dari perangkat apa pun secara langsung melalui peramban web.

### 2.2 Perspektif Produk & Arsitektur Tingkat Tinggi
SkinCheck dirancang sebagai sistem terdistribusi modular yang mengedepankan prinsip *Separation of Concerns* (SoC):

```
┌────────────────────────────────────────────────────────┐
│                   Pengguna / Browser                   │
│   (Webcam HUD, Dashboard, Konsul Chatbot, Scan History) │
└───────────────────────────┬────────────────────────────┘
                            │ HTTP / JSON AJAX
┌───────────────────────────▼────────────────────────────┐
│                    Web Server Apache                   │
│             SkinCheck Core (PHP 8.1+ MVC)              │
│   [Controllers: Auth, Scan, Sunscreen, Chatbot, Admin] │
│   [Services: ImageValidator, Storage, Email, Telegram] │
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

### 2.3 Karakteristik & Persona Pengguna
Sistem mengakomodasi 2 aktor utama:

1. **Pelanggan / Pengguna Umum (Customer)**
   - Mahasiswa, profesional, atau masyarakat umum yang peduli terhadap kesehatan kulit.
   - Menggunakan ponsel cerdas atau laptop dengan kamera terintegrasi.
   - Memerlukan kemudahan pemindaian tanpa instalasi aplikasi rumit, visualisasi hasil yang mudah dipahami (skor 0-100), rekomendasi produk yang cocok, dan asisten konsultasi yang ramah.

2. **Administrator Eksekutif (Admin)**
   - Pengelola platform dan sistem analis kesehatan.
   - Membutuhkan visibilitas terhadap metrik sistem: volume scan, registrasi pengguna, inventaris katalog kosmetik, pemantauan konsumsi token API Gemini secara real-time, serta asisten analitik data tingkat eksekutif.

### 2.4 Batasan Sistem, Asumsi, dan Ketergantungan
- **Batasan**: SkinCheck merupakan instrumen skrining dan evaluasi mandiri (*screening tool*), **bukan pengganti diagnosis medis resmi dokter spesialis kulit**. Terdapat klausul sangkalan (*medical disclaimer*) wajib di setiap hasil analisis.
- **Asumsi**: Pengguna memiliki koneksi internet aktif dan peramban modern (Chrome/Firefox/Edge/Safari) yang mendukung WebRTC (*getUserMedia API*) serta HTML5 Canvas.
- **Ketergantungan**: Layanan bergantung pada ketersediaan API Google Gemini untuk fungsionalitas asisten cerdas, serta dependensi Python/ONNX Runtime untuk pipeline inferensi *Computer Vision*.

---

## BAB III: SPESIFIKASI KEBUTUHAN FUNGSIONAL

### 3.1 Modul 1: Autentikasi Multi-Kanal & Keamanan OTP
* **Deskripsi**: Sistem mengelola registrasi, login, dan pemulihan akun dengan perlindungan verifikasi dua langkah (2FA/OTP) menggunakan saluran Telegram dan Email.

#### User Story 1.1: Registrasi Akun dengan Verifikasi OTP
* **Sebagai**: Calon pengguna SkinCheck,
* **Saya ingin**: Mendaftar akun dengan nama, email, dan password yang diverifikasi menggunakan kode OTP 6-digit,
* **Agar**: Akun saya aman, terhindar dari bot, dan riwayat analisis kulit saya tersimpan secara privat.

* **Acceptance Criteria (AC-1.1)**:
  - *Given* pengguna berada di halaman registrasi,
  - *When* pengguna mengisi data valid dan menekan tombol "Daftar",
  - *Then* sistem meng-hash password menggunakan `PASSWORD_BCRYPT`, membuat token OTP 6 digit acak dengan masa kedaluwarsa 10 menit, menyimpannya di basis data, dan mengirimkannya via Bot Telegram / Email Resend API,
  - *And* pengguna dialihkan ke halaman verifikasi OTP yang dilengkapi penghitung mundur (*cooldown timer*) 60 detik sebelum tombol kirim ulang aktif.

#### User Story 1.2: Pemulihan Password via Instant Verification Link
* **Sebagai**: Pengguna yang lupa kata sandi,
* **Saya ingin**: Menerima tautan reset sandi instan atau kode OTP pemulihan via email,
* **Agar**: Saya dapat mengatur ulang kata sandi dan mengakses kembali akun saya dengan aman.

---

### 3.2 Modul 2: Smart Camera HUD Viewfinder & Validasi Citra Wajah
* **Deskripsi**: Antarmuka kamera berbasis web yang memandu pengguna memosisikan wajah secara presisi dan memvalidasi kelayakan citra sebelum diproses oleh modul AI.

#### User Story 2.1: Panduan Pemosisian Wajah (HUD Face Oval Guide)
* **Sebagai**: Pengguna yang akan melakukan pemindaian wajah,
* **Saya ingin**: Melihat garis panduan berbentuk oval (*Face Oval HUD*) dan efek visual *laser scanner* di layar webcam,
* **Agar**: Posisi wajah saya simetris, pas di tengah bingkai, dan memenuhi jarak ideal untuk analisis mikroskopis.

#### User Story 2.2: Validasi Kelayakan Citra Otomatis
* **Sebagai**: Mesin inferensi SkinCheck,
* **Saya ingin**: Melakukan pra-validasi terhadap citra yang ditangkap (kecerahan, resolusi minimal 640x480, dan deteksi wajah),
* **Agar**: Model AI tidak memproses gambar yang terlalu gelap, buram (*blur*), atau citra non-wajah yang dapat merusak akurasi skor.

* **Acceptance Criteria (AC-2.2)**:
  - *Given* kamera menangkap frame gambar,
  - *When* rata-rata nilai intensitas piksel (*luminance*) di bawah ambang batas (terlalu gelap) atau melebihi ambang batas (over-exposure),
  - *Then* sistem menampilkan pesan peringatan interaktif pada HUD (*"Pencahayaan Kurang! Arahkan wajah ke sumber cahaya"*) dan menonaktifkan tombol pemindaian hingga kondisi optimal tercapai.

---

### 3.3 Modul 3: Evaluasi Klinis 4 Pilar Sawar Kulit (Skin Barrier)
* **Deskripsi**: Ekstraksi dan evaluasi kuantitatif terhadap 4 indikator utama integritas sawar kulit wajah menggunakan model MobileNetV3 ONNX dan algoritma visi komputer.

#### User Story 3.1: Evaluasi Komprehensif Sawar Kulit
* **Sebagai**: Pengguna SkinCheck,
* **Saya ingin**: Menerima skor kuantitatif (0–100) untuk 4 indikator: Jerawat/Pori, Kemerahan/Eritema, Kehalusan Tekstur, dan Hiperpigmentasi, serta Skor Kesehatan Kulit Keseluruhan (*Overall Score*),
* **Agar**: Saya mengetahui kondisi spesifik sawar kulit saya dan tingkat keparahan peradangan secara objektif.

* **Acceptance Criteria (AC-3.1)**:
  - *Given* citra wajah yang valid telah diunggah,
  - *When* proses inferensi AI dijalankan oleh `SkinAnalysisService`,
  - *Then* sistem mengembalikan data terstruktur berupa:
    1. `acne_score` (Skor Jerawat & Kepadatan Pori, 0–100)
    2. `redness_score` (Skor Eritema & Iritasi Vaskular, 0–100)
    3. `texture_score` (Skor Kehalusan Tekstur Permukaan, 0–100)
    4. `hyperpigmentation_score` (Skor Noda Hitam & Melanin, 0–100)
    5. `overall_score` (Skor Agregat Integritas Sawar Kulit, 0–100)
    6. `ai_response` (Penjelasan naratif ringkas kondisi kulit)
  - *And* waktu komputasi inferensi tidak melebihi 2.0 detik.

---

### 3.4 Modul 4: Pemeriksaan Kerataan Sunscreen (Sunscreen Check & Heatmap)
* **Deskripsi**: Analisis distribusi tabir surya pada wajah untuk mendeteksi area yang terlewat (*missed spots*) serta memvisualisasikan peta sebaran panas (*protection heatmap*).

#### User Story 4.1: Analisis Cakupan Sunscreen & Visualisasi Heatmap
* **Sebagai**: Pengguna yang telah mengaplikasikan sunscreen,
* **Saya ingin**: Mengetahui persentase kerataan tabir surya (*Coverage Score 0–100%*), status kerataan, area rawan yang belum tercover, dan melihat gambar *Heatmap*,
* **Agar**: Saya dapat mengaplikasikan ulang sunscreen pada bagian wajah yang belum terproteksi optimal sebelum beraktivitas di bawah sinar matahari.

* **Acceptance Criteria (AC-4.1)**:
  - *Given* foto wajah pengguna setelah memakai tabir surya,
  - *When* pengguna menekan "Periksa Kerataan Sunscreen",
  - *Then* `SunscreenAnalysisService` menghasilkan:
    - `coverage_score`: Nilai persentase 0-100%.
    - `coverage_status`: Klasifikasi (*Sangat Merata* $\ge 85\%$, *Cukup Merata* $70-84\%$, *Kurang Merata* $< 70\%$).
    - `heatmap_path`: Citra representasi visual gradien warna (area hijau = proteksi tinggi, kuning = sedang, merah = terlewat).
    - `low_coverage_areas`: Daftar teks area yang kurang terlindungi (contoh: "Sudut Hidung Kiri", "Garis Rahang").

---

### 3.5 Modul 5: AI Skincare Assistant Interaktif (Google Gemini Context-Aware)
* **Deskripsi**: Layanan chatbot cerdas berbasis LLM yang tertanam di peramban, memiliki kesadaran konteks terhadap riwayat kesehatan kulit pengguna, dan mampu merekomendasikan bahan aktif yang tepat.

#### User Story 5.1: Konsultasi Interaktif Sadar Konteks Riwayat Scan
* **Sebagai**: Pengguna SkinCheck,
* **Saya ingin**: Berkonsultasi via obrolan teks interaktif dengan AI mengenai cara memperbaiki sawar kulit saya,
* **Agar**: Saya mendapatkan rekomendasi bahan aktif skincare (misal: Ceramide, Hyaluronic Acid, Centella Asiatica) yang disesuaikan secara presisi dengan skor scan terakhir saya.

* **Acceptance Criteria (AC-5.1)**:
  - *Given* pengguna membuka widget AI Assistant,
  - *When* pengguna mengirimkan pertanyaan tentang keluhan kulitnya,
  - *Then* `ChatbotController` menginjeksi metadata scan terbaru pengguna (*acne score*, *redness*, *overall health*) ke dalam *system prompt* Google Gemini Flash,
  - *And* AI memberikan respon edukatif, empatik, berbasis bukti dermatologi, tanpa memberikan resep obat keras (disertai disclaimer bahwa jawaban bersifat edukatif).

---

### 3.6 Modul 6: Riwayat Pemeriksaan & Pelacakan Progres Kesehatan Kulit
* **Deskripsi**: Modul pencatatan historis dan perbandingan metrik kesehatan kulit dari waktu ke waktu.

#### User Story 6.1: Pelacakan Progres dan Keamanan Akses Citra
* **Sebagai**: Pengguna terdaftar,
* **Saya ingin**: Melihat linimasa seluruh riwayat pemindaian saya lengkap dengan foto, tanggal, grafik skor, dan detail hasil,
* **Agar**: Saya dapat mengevaluasi apakah kondisi sawar kulit saya membaik setelah menggunakan produk perawatan tertentu.

* **Acceptance Criteria (AC-6.1)**:
  - Akses terhadap berkas citra scan dienkapsulasi menggunakan *ownership-scoped streaming route* (`/scan/image?id=X`), sehingga pengguna lain tidak dapat mengintip foto pengguna berbeda meskipun menebak URL gambar.

---

### 3.7 Modul 7: Kurasi Rekomendasi Produk Skincare Berbasis Masalah
* **Deskripsi**: Sistem pencocokan otomatis (*rule-based matcher*) antara masalah dominan pengguna dengan database katalog produk.

#### User Story 7.1: Rekomendasi Produk yang Cocok Secara Otomatis
* **Sebagai**: Pengguna yang baru saja selesai melakukan pemindaian,
* **Saya ingin**: Melihat daftar rekomendasi produk skincare lokal/resmi (nama, kategori, bahan aktif, panduan pakai) yang sesuai dengan indikator skor terlemah saya,
* **Agar**: Saya tidak bingung memilih produk yang tepat untuk memulihkan sawar kulit saya.

---

### 3.8 Modul 8: Executive Admin Dashboard & Audit Log Pengguna
* **Deskripsi**: Panel administrasi tingkat tinggi untuk memantau KPI sistem secara menyeluruh.

#### User Story 8.1: Pemantauan KPI Platform
* **Sebagai**: Administrator Eksekutif,
* **Saya ingin**: Melihat dashboard dengan ringkasan metrik: Total Pengguna, Total Pemindaian Wajah, Katalog Produk Aktif, dan Rata-rata Skor Kesehatan Kulit Global,
* **Agar**: Saya memiliki gambaran makro mengenai adopsi platform dan kondisi umum kesehatan kulit para pengguna.

---

### 3.9 Modul 9: Live Gemini AI Token & Engine Monitor Console
* **Deskripsi**: Konsol observabilitas real-time untuk memantau konsumsi token, latensi inferensi, dan status operasional kuota API Google Gemini.

#### User Story 9.1: Pemantauan Konsumsi Token Gemini Real-time
* **Sebagai**: Administrator Eksekutif,
* **Saya ingin**: Memantau statistik pemakaian token Gemini API (Input Tokens, Output Tokens, Total Tokens Today, dan Volume Request),
* **Agar**: Biaya operasional API terkontrol dan sistem terhindar dari *rate limit quota exhaustion*.

* **Acceptance Criteria (AC-9.1)**:
  - Konsol menampilkan metrik akumulatif harian dan status tier mesin (*Free Tier / Active Tier*), diperbarui secara langsung melalui pencatatan log pada tabel `activity_logs` / `settings`.

---

### 3.10 Modul 10: Centralized Dynamic Settings & API Gateway Console
* **Deskripsi**: Pusat kendali konfigurasi kunci API eksternal dan parameter sistem secara dinamis dari basis data tanpa perlu mengubah kode sumber (*zero-downtime reconfiguration*).

#### User Story 10.1: Manajemen Konfigurasi Kunci API & Uji Koneksi Live
* **Sebagai**: Administrator Eksekutif,
* **Saya ingin**: Mengubah token bot Telegram, API Key Google Gemini, dan API Key Resend Email secara langsung dari UI, serta melakukan uji koneksi (*Live AJAX Test*),
* **Agar**: Sistem dapat dialihkan ke API Key cadangan dengan cepat tanpa perlu deploy ulang kode aplikasi.

---

## BAB IV: SPESIFIKASI KEBUTUHAN NON-FUNGSIONAL (NFR)

### 4.1 Kinerja dan Efisiensi Waktu (Performance)
* **NFR-01 (Inference Latency)**: Waktu pemrosesan citra oleh pipeline Computer Vision (MobileNetV3 ONNX) harus selesai dalam waktu $\le 1.5$ detik pada server standar lokal (Intel Core i5 / 8 GB RAM).
* **NFR-02 (Chatbot Response Latency)**: Waktu respon pertama (*Time-to-First-Token*) dari integrasi Google Gemini API melalui microservice Node.js tidak boleh melebihi 2.5 detik pada koneksi internet berkecepatan 10 Mbps.
* **NFR-03 (Page Load Time)**: Halaman antarmuka utama pengguna (*Customer Dashboard*) harus memuat secara tuntas dalam waktu $\le 2.0$ detik.

### 4.2 Keamanan dan Privasi Data (Security)
* **NFR-04 (Password Hashing)**: Seluruh kata sandi pengguna wajib di-hash menggunakan algoritma `PASSWORD_BCRYPT` dengan cost factor standar PHP ($cost \ge 10$).
* **NFR-05 (Image Access Isolation)**: Citra wajah pengguna disimpan pada direktori terproteksi di luar jangkauan langsung peramban (*web-root isolation*) dan hanya dapat diakses melalui mekanisme *Session Ownership Verification*.
* **NFR-06 (API Key Masking & Secret Protection)**: Kunci API eksternal (Gemini, Telegram, Resend) disimpan dalam tabel `settings` dengan atribut `is_secret = 1` dan disamarkan (*masked*, contoh: `AIzaSy...XXXX`) pada antarmuka admin untuk mencegah *credential sniffing*.
* **NFR-07 (Input Sanitization & CSRF Prevention)**: Setiap input formulir wajib dibersihkan (*sanitized*) dari entitas berbahaya untuk mencegah serangan *Cross-Site Scripting* (XSS) dan *SQL Injection* (menggunakan PDO Prepared Statements).

### 4.3 Reliabilitas dan Ketersediaan (Reliability & Availability)
* **NFR-08 (Graceful AI Degradation / Fallback)**: Apabila layanan eksternal Google Gemini API mengalami *downtime* atau *rate limit (HTTP 429)*, sistem tidak boleh crash, melainkan otomatis beralih ke *Fallback Engine* atau menampilkan pesan ramah (*"Asisten AI sedang dalam pemeliharaan berkala, silakan periksa skor hasil scan Anda"*).
* **NFR-09 (Database Data Integrity)**: Integritas relasional antar-tabel (Users, Scans, Analysis Results) dilindungi menggunakan *Foreign Key Constraints* dengan mode `ON DELETE CASCADE` atau `ON DELETE SET NULL`.

### 4.4 Usabilitas dan Aksesibilitas (Usability & Portability)
* **NFR-10 (Zero-Install Requirement)**: Seluruh fungsionalitas kamera dan pemindaian berjalan 100% di dalam peramban web modern tanpa memerlukan instalasi aplikasi tambahan atau plugin pihak ketiga.
* **NFR-11 (Responsive Layout)**: Tampilan antarmuka beradaptasi mulus (*mobile-first responsive design*) pada layar smartphone (resolusi minimal 360x640) hingga layar monitor desktop (1920x1080).

### 4.5 Maintainability dan Kepatuhan Prinsip SOLID
* **NFR-12 (Decoupled Architecture)**: Modul analisis sawar kulit dan sunscreen wajib mengimplementasikan interface terpisah (`SkinAnalysisProviderInterface`, `SunscreenAnalysisProviderInterface`) sehingga provider inferensi baru (misal: migrasi dari ONNX ke Cloud API) dapat ditambahkan tanpa memodifikasi kode kontroler (*Open/Closed Principle*).

---

## BAB V: SPESIFIKASI REKAYASA FITUR ARTIFICIAL INTELLIGENCE

### 5.1 Pipeline Computer Vision (MobileNetV3 Multi-Task ONNX)
Model *Computer Vision* pada platform SkinCheck dirancang untuk menyelesaikan tugas regresi multi-output secara simultan dari satu citra wajah masukan:

```
[Citra Wajah Input: 640x480 RGB]
           │
           ▼
[Preprocessing & Normalisasi]
- Resize ke Tensor masukan: (1, 3, 224, 224)
- Normalisasi ImageNet (Mean: [0.485, 0.456, 0.406], Std: [0.229, 0.224, 0.225])
           │
           ▼
[MobileNetV3 Backbone Feature Extractor]
- Inverted Residual Blocks & Squeeze-and-Excitation (SE) Units
           │
           ▼
[Multi-Task Regression Heads]
┌──────────────┬──────────────┬──────────────┬──────────────┐
│  Acne Head   │ Redness Head │ Texture Head │ Hyperpig Head│
│ (Dense Layer)│ (Dense Layer)│ (Dense Layer)│ (Dense Layer)│
└──────┬───────┴──────┬───────┴──────┬───────┴──────┬───────┘
       ▼              ▼              ▼              ▼
[Acne Score]   [Redness Score][Texture Score][Hyperpig Score]
 (0 - 100)        (0 - 100)      (0 - 100)      (0 - 100)
       │              │              │              │
       └──────────────┴──────┬───────┴──────────────┘
                             ▼
               [Overall Skin Barrier Score]
           Formula: Skor Agregat Tertimbang (0 - 100)
```

#### Pipeline Sunscreen Coverage & Heatmap Generation:
1. **Ekstraksi Saluran Warna & Konversi Ruang Warna (RGB $\rightarrow$ LAB / HSV)**: Memisahkan komponen reflektansi UV dan absorpsi tabir surya pada permukaan epidermis.
2. **Segmentasi Area Wajah**: Menerapkan masker wajah oval (*Face Contour Mask*) untuk mengeliminasi latar belakang non-kulit.
3. **Thresholding Kepadatan Tabir Surya**: Menghitung rasio piksel terlindungi terhadap total piksel area wajah:
   $$\text{Coverage Score} = \left( \frac{\sum \text{Piksel Terproteksi}}{\sum \text{Total Piksel Wajah}} \right) \times 100\%$$
4. **Pembangkitan Heatmap Proteksi**: Mentransformasikan matriks densitas ke dalam color map gradien (*Jet/Turbo Color Gradient*) yang disimpan sebagai citra visual PNG transparan untuk ditampilkan kembali kepada pengguna.

### 5.2 Integrasi Generative AI (Google Gemini Flash & Node.js Engine)
Layanan konsultasi cerdas memanfaatkan model **Google Gemini Flash** yang dioptimalkan untuk latensi rendah (*low-latency streaming inference*).

#### Struktur System Prompt & Injeksi Konteks Klinis:
```text
System Role: Anda adalah Konsultan Sawar Kulit & Skincare Cerdas (SkinCheck AI Assistant).
Context Payload:
- Nama Pengguna: {{user_name}}
- Skor Kesehatan Sawar Kulit: {{overall_score}}/100
- Skor Jerawat/Pori: {{acne_score}}/100
- Skor Eritema/Kemerahan: {{redness_score}}/100
- Skor Tekstur: {{texture_score}}/100
- Skor Hiperpigmentasi: {{hyperpigmentation_score}}/100
- Riwayat Cakupan Sunscreen: {{sunscreen_coverage}}%

Instruksi Khusus:
1. Berikan tanggapan yang empatik, ilmiah, ringkas, dan mudah dipahami oleh awam.
2. Fokuskan rekomendasi bahan aktif (misal: Niacinamide, Ceramide, Cica, Zinc PCA) yang relevan dengan skor terlemah pengguna.
3. Selalu cantumkan disclaimer bahwa saran ini bersifat edukatif dan bukan pengganti resep dokter spesialis.
```

### 5.3 Manajemen Token & Audit Konsumsi AI Real-Time
Untuk menjaga transparansi biaya dan performa:
- Setiap pemanggilan Gemini API mengekstrak metadata respon: `promptTokenCount`, `candidatesTokenCount`, dan `totalTokenCount`.
- Nilai token diagregasi ke dalam tabel statistik sistem secara asinkron.
- Administrator dapat memantau grafik konsumsi harian serta mengatur limit penghentian darurat jika kuota token mendekati ambang batas maksimal.

---

## BAB VI: MATRIKS KETERLACAKAN KEBUTUHAN (TRACEABILITY MATRIX)

Tabel berikut memetakan keterkaitan langsung antara Kebutuhan Bisnis/Pengguna dengan Modul Kode dan Metode Pengujian:

| ID Kebutuhan | Deskripsi Singkat | File Modul / Controller | Komponen AI Terlibat | Metode Pengujian (Unit / CI-CD) |
| :--- | :--- | :--- | :--- | :--- |
| **FR-01** | Autentikasi & Verifikasi OTP | `app/controllers/AuthController.php` | Telegram Bot API / Resend | `tests/AuthTest.php` |
| **FR-02** | Smart Camera HUD & Validasi | `public/js/main.js`, `ImageValidator.php` | Client Canvas Heuristics | E2E Browser Testing |
| **FR-03** | Evaluasi 4 Pilar Sawar Kulit | `ScanController.php`, `SkinAnalysisService.php` | MobileNetV3 ONNX (`predict.py`) | `tests/SkinAnalysisTest.php` |
| **FR-04** | Analisis Kerataan Sunscreen | `SunscreenController.php`, `RGBSunscreenProvider.php` | CV Heatmap Pipeline | `tests/SunscreenAnalysisTest.php` |
| **FR-05** | Asisten Chatbot Skincare | `ChatbotController.php`, `server.js` | Google Gemini Flash LLM | `tests/ChatbotApiTest.php` |
| **FR-06** | Riwayat & Scoped Image Access | `CustomerController.php`, `ImageStorage.php` | - | Security Boundary Test |
| **FR-07** | Rekomendasi Produk Skincare | `CustomerController.php`, `Product.php` | Rule-based Engine | `tests/ProductCatalogTest.php` |
| **FR-08** | Executive Admin Dashboard | `AdminController.php` | Analytics Aggregator | Dashboard Integration Test |
| **FR-09** | Live AI Token & Engine Monitor | `AdminController.php`, `Setting.php` | Gemini Usage Metadata | Token Metrics Verification |
| **FR-10** | Centralized Dynamic Settings | `AdminController.php`, `Setting.php` | Dynamic Key Ingestion | Settings Live AJAX Test |

---

## PENGESAHAN DOKUMEN

Dokumen Kebutuhan Perangkat Lunak (PRD/SRS) ini telah disusun secara komprehensif untuk memenuhi kualifikasi **Skor 4 (Sangat Baik)** pada Rubrik Penilaian OBE Proyek Akhir Mandiri Mata Kuliah Rekayasa Perangkat Lunak.

*Cirebon, 15 November 2026*  
**Penyusun / Full-Stack Software Engineer:**  
**Satria Fibi Saputra** (NIM: 41257159)  
S1 Teknik Informatika — STMIK IKMI Cirebon
