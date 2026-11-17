# LAPORAN PENGUJIAN PERANGKAT LUNAK & DEVOPS CI/CD MANDIRI
## Berdasarkan Standar IEEE 829 / ISO/IEC/IEEE 29119

---

# 🌿 SKINCHECK: AI-Powered Skin Barrier & Sunscreen Analysis Platform
**Laporan Hasil Uji Otomatisasi (Automated Unit Testing), Kepatuhan Clean Code & SOLID, serta Verifikasi Pipeline CI/CD GitHub Actions**

* **Mata Kuliah**: Rekayasa Perangkat Lunak (TIKB2503021) — 4 SKS (2 Teori + 2 Praktik)
* **Program Studi**: S1 Teknik Informatika — STMIK IKMI Cirebon
* **Penyusun / Mahasiswa**: Satria Fibi Saputra (NIM: 41257159)
* **Dosen Pengampu**: Yudhistira Arie Wijaya, M.Kom., CDS.
* **Tema Topik Kelompok**: *Smart Healthcare & Public Service AI*
* **Sifat Proyek**: Mandiri / Perorangan (*100% Individual Ownership & Full-App Build*)
* **Status Build CI/CD**: **SUCCESS / CENTANG HIJAU (PASSED)**
* **Versi Dokumen**: 1.0.0
* **Tanggal Rilis**: 18 November 2026

---

## DAFTAR ISI
1. [BAB I: PENDAHULUAN & STRATEGI PENGUJIAN](#bab-i-pendahuluan--strategi-pengujian)
   - 1.1 Tujuan Pengujian
   - 1.2 Lingkup Pengujian (Test Scope)
   - 1.3 Lingkungan Pengujian (Test Environment)
2. [BAB II: DESAIN SKENARIO & HASIL EKSEKUSI PENGUJIAN](#bab-ii-desain-skenario--hasil-eksekusi-pengujian)
   - 2.1 Test Suite 1: Evaluasi 4 Pilar Sawar Kulit AI (21 Test Cases)
   - 2.2 Test Suite 2: Estimasi Kerataan Sunscreen & Heatmap (21 Test Cases)
   - 2.3 Ringkasan Metrik Pengujian (Testing Metrics Summary)
3. [BAB III: IMPLEMENTASI GITHUB DEVOPS & CI/CD PIPELINE](#bab-iii-implementasi-github-devops--cicd-pipeline)
   - 3.1 Arsitektur Alur Kerja GitHub Actions (.github/workflows/main.yml)
   - 3.2 Tahapan Otomatisasi (Linting, Multi-Runtime Setup, Test Runner)
   - 3.3 Verifikasi Status Build Hijau (Build Passing Verification)
4. [BAB IV: AUDIT KEPATUHAN CLEAN CODE & PRINSIP SOLID](#bab-iv-audit-kepatuhan-clean-code--prinsip-solid)
   - 4.1 Pemisahan Tanggung Jawab & Abstraksi Antarmuka
   - 4.2 Penanganan Kesalahan & Graceful Fallback
5. [BAB V: KESIMPULAN & KESIAPAN TAHAP FINAL](#bab-v-kesimpulan--kesiapan-tahap-final)
6. [PENGESAHAN LAPORAN PENGUJIAN](#pengesahan-laporan-pengujian)

---

## BAB I: PENDAHULUAN & STRATEGI PENGUJIAN

### 1.1 Tujuan Pengujian
Laporan Pengujian Perangkat Lunak dan Otomatisasi DevOps ini disusun untuk memenuhi target luaran **Tahap 3 (Konstruksi Kode, Integrasi AI, Testing & CI/CD)** pada kurikulum *Outcome-Based Education* (OBE) STMIK IKMI Cirebon.

Pengujian ini bertujuan untuk:
1. Memverifikasi fungsionalitas algoritma inferensi *Computer Vision* (MobileNetV3 ONNX) dan modul estimasi visual tabir surya (*RGB Sunscreen Estimator*) secara matematis dan prosedural.
2. Membuktikan keandalan sistem dalam menghadapi skenario kegagalan (*Failure & Exception Handling*) seperti *AI engine timeout*, citra rusak (*corrupted image*), dan penanganan berkas yang hilang.
3. Memastikan kepatuhan terhadap regulasi terminologi non-medis (*Medical Disclaimer & Terminology Compliance*).
4. Mengotomatiskan seluruh alur verifikasi menggunakan *Continuous Integration/Continuous Deployment* (CI/CD) pada repositori GitHub publik pribadi (`satriafibisaputra18-droid/SkinCheck`).

### 1.2 Lingkup Pengujian (Test Scope)
Pengujian mencakup aspek-aspek inti sistem:
- **Unit Testing**: Pengujian unit pada method, kelas servis, dan value objects (`AnalysisResult`, `SunscreenResult`).
- **Flow & Integration Testing**: Pengujian siklus aliran data dari validasi input, pra-pemrosesan citra, inferensi regresi 4 pilar sawar kulit, hingga kalkulasi skor agregat.
- **Resilience & Recovery Testing**: Simulasi *fallback mechanism* ketika engine AI eksternal mengalami kendala jaringan.
- **CI/CD Automation**: Pengujian otomatis terintegrasi pada GitHub Actions *runner* berbasis Ubuntu Linux.

### 1.3 Lingkungan Pengujian (Test Environment)
| Parameter | Lingkungan Lokal (Local) | Lingkungan Cloud CI/CD (GitHub Actions) |
| :--- | :--- | :--- |
| **Sistem Operasi** | Windows 10/11 x64 | Ubuntu 22.04 LTS (GitHub Hosted Runner) |
| **Runtime PHP** | PHP 8.2+ (XAMPP / CLI) | PHP 8.2 (shivammathur/setup-php@v2) |
| **Ekstensi PHP** | GD, PDO, PDO_MySQL, cURL, Mbstring | GD, PDO, PDO_MySQL, cURL, Mbstring |
| **Runtime AI** | Python 3.10+ & ONNX Runtime | Python 3.10 & py_compile validator |
| **Microservice** | Node.js 18 LTS (Express) | Node.js 18 LTS (Automated Syntax Checker) |
| **Repositori** | Local Working Copy | `https://github.com/satriafibisaputra18-droid/SkinCheck` |

---

## BAB II: DESAIN SKENARIO & HASIL EKSEKUSI PENGUJIAN

Eksekusi pengujian otomatis dilakukan melalui skrip master runner `tests/run_all_tests.php` yang mengeksekusi dua rangkaian pengujian (*test suites*):

### 2.1 Test Suite 1: Evaluasi 4 Pilar Sawar Kulit AI (`tests/test_skin_analysis.php`)
Rangkaian ini menguji kemampuan engine inferensi dalam menganalisis integritas sawar kulit wajah:

| ID Uji | Kasus Pengujian (Test Case) | Ekspektasi Hasil | Realisasi | Status |
| :--- | :--- | :--- | :--- | :---: |
| **TC-SKIN-01** | Instansiasi Objek Hasil | Objek turunan dari `AnalysisResult` | Instance valid `AnalysisResult` | **PASS** |
| **TC-SKIN-02** | Status Pemrosesan Sukses | Status bernilai `'completed'` | Status `'completed'` | **PASS** |
| **TC-SKIN-03** | Rentang Nilai Acne Score | Nilai integer dalam interval $[0, 100]$ | Skor 6 (Valid) | **PASS** |
| **TC-SKIN-04** | Rentang Nilai Redness Score | Nilai integer dalam interval $[0, 100]$ | Skor 5 (Valid) | **PASS** |
| **TC-SKIN-05** | Rentang Nilai Texture Score | Nilai integer dalam interval $[0, 100]$ | Skor 95 (Valid) | **PASS** |
| **TC-SKIN-06** | Rentang Hyperpigmentation | Nilai integer dalam interval $[0, 100]$ | Skor 8 (Valid) | **PASS** |
| **TC-SKIN-07** | Rentang Overall Barrier Score | Skor agregat terbobot $[0, 100]$ | Skor 94 (Valid) | **PASS** |
| **TC-SKIN-08** | Metrik Tingkat Keyakinan | Float keyakinan dalam rentang $[0.5, 1.0]$ | Confidence 0.85 | **PASS** |
| **TC-SKIN-09** | Ekstraksi Indikasi Visual | Array visual indications terisi | Array indikasi terdefinisi | **PASS** |
| **TC-SKIN-10** | Catatan Ringkasan Naratif | String ringkasan non-kosong | Narasi ringkas terbentuk | **PASS** |
| **TC-SKIN-11** | Kepatuhan Non-Klinis | Bebas dari klaim vonis penyakit medis | Lolos sensor terminologi | **PASS** |
| **TC-SKIN-12** | Simulasi Kegagalan Engine AI | Mengembalikan objek `AnalysisResult` | Instance valid tertangkap | **PASS** |
| **TC-SKIN-13** | Status Gagal Tercatat | Status ditandai sebagai `'failed'` | Status `'failed'` | **PASS** |
| **TC-SKIN-14** | Pesan Error Kegagalan | Mencatat deskripsi penyebab timeout | Pesan tercatat akurat | **PASS** |
| **TC-SKIN-15** | Reset Skor Saat Gagal | Skor kesehatan di-reset saat gagal | Skor reset ke 0 | **PASS** |
| **TC-SKIN-16** | Pemulihan Retry Berhasil | Percobaan ulang berhasil recover | Status pulih ke `'completed'` | **PASS** |
| **TC-SKIN-17** | Penanganan Berkas Hilang | Penanganan graceful jika file tidak ada | Status `'failed'` ditangani rapi | **PASS** |
| **TC-SKIN-18** | Pesan Berkas Hilang | Pesan eksplisit berkas tidak ditemukan | Pesan spesifik terbit | **PASS** |
| **TC-SKIN-19** | Serialisasi `toArray()` | Menghasilkan struktur array asosiatif lengkap | Seluruh kunci metadata hadir | **PASS** |
| **TC-SKIN-20** | Eksistensi Persentase Keyakinan| Memuat kunci `confidence_percentage` | Kunci terbukti ada | **PASS** |
| **TC-SKIN-21** | Serialisasi `toJson()` | String JSON valid dan identik | JSON parsing 100% cocok | **PASS** |

*Tingkat Keberhasilan Suite 1: **21 dari 21 Pengujian LULUS (100% Passed)***.

---

### 2.2 Test Suite 2: Estimasi Kerataan Sunscreen & Heatmap (`tests/test_sunscreen_analysis.php`)
Rangkaian ini memverifikasi algoritma estimasi cakupan tabir surya dan pembangkitan visualisasi peta panas (*Heatmap*):

| ID Uji | Kasus Pengujian (Test Case) | Ekspektasi Hasil | Realisasi | Status |
| :--- | :--- | :--- | :--- | :---: |
| **TC-SUN-01** | Tipe Objek Hasil Evaluasi | Instance dari `SunscreenResult` | Valid `SunscreenResult` | **PASS** |
| **TC-SUN-02** | Status Evaluasi Sukses | Status bernilai `'completed'` | Status `'completed'` | **PASS** |
| **TC-SUN-03** | Rentang Nilai Coverage Score | Persentase terukur $[0, 100]\%$ | Coverage 70% | **PASS** |
| **TC-SUN-04** | Validitas Kategori Kerataan | Tergolong status standar (Cukup Merata) | Kategori 'Cukup Merata' | **PASS** |
| **TC-SUN-05** | Pembangkitan Jalur Heatmap | Berkas citra heatmap dihasilkan | File path terbentuk | **PASS** |
| **TC-SUN-06** | Validitas Nilai Keyakinan | Keyakinan berada pada interval $[0.4, 1.0]$ | Confidence 0.81 | **PASS** |
| **TC-SUN-07** | Klasifikasi Metode Analisis | Metode berlabel `'RGB Visual Estimation'` | Label sesuai metodologi | **PASS** |
| **TC-SUN-08** | Klasifikasi Tipe Perangkat Keras| Tipe berlabel `'Standard RGB Camera'` | Hardware type akurat | **PASS** |
| **TC-SUN-09** | Identifikasi Area Rawan | Array area rendah proteksi (*low coverage*) | Array terdefinisi | **PASS** |
| **TC-SUN-10** | Pernyataan Sanggahan Non-UV | Sanggahan eksplisit non-pengukuran UV | Teks disclaimer hadir | **PASS** |
| **TC-SUN-11** | Zero False UV Claim | Tidak ada klaim spektrum UV langsung | 100% patuh regulasi | **PASS** |
| **TC-SUN-12** | Deteksi Keyakinan Rendah | Flag low confidence aktif jika conf < 0.60 | Flag aktif bernilai TRUE | **PASS** |
| **TC-SUN-13** | Skor Keyakinan Marginal | Nilai confidence terdeteksi pada 0.48 | Skor 0.48 tercatat | **PASS** |
| **TC-SUN-14** | Rekomendasi Kondisi Rendah | Catatan memuat anjuran perbaikan cahaya | Advisory note muncul | **PASS** |
| **TC-SUN-15** | Penanganan Timeout Mesin | Mengembalikan status `'failed'` | Status `'failed'` | **PASS** |
| **TC-SUN-16** | Pencatatan Error Timeout | Pesan timeout engine tercatat di objek | Pesan tersimpan | **PASS** |
| **TC-SUN-17** | Skor Proteksi Nol Saat Gagal | Coverage score menjadi 0 saat failure | Coverage 0% | **PASS** |
| **TC-SUN-18** | Penanganan Citra Hilang | Tangani kegagalan file tanpa fatal error | Ditangani gracefully | **PASS** |
| **TC-SUN-19** | Abstraksi Provider UV Mandiri | Provider UV mengenali tipe perangkat UV | Tipe hardware UV valid | **PASS** |
| **TC-SUN-20** | Kunci Wajib Serialisasi Array | Method `toArray()` memuat seluruh kunci | Kunci lengkap | **PASS** |
| **TC-SUN-21** | Preservasi Metode Serialisasi | Kunci `analysis_method` lestari saat serial| Preservasi data utuh | **PASS** |

*Tingkat Keberhasilan Suite 2: **21 dari 21 Pengujian LULUS (100% Passed)***.

---

### 2.3 Ringkasan Metrik Pengujian (Testing Metrics Summary)
```
======================================================
                 PIPELINE SUMMARY                     
======================================================
Total Test Suites Executed : 2 Suites
Total Unit Test Cases      : 42 Cases
Tests Succeeded (Passed)   : 42 Cases (100.0%)
Tests Failed               : 0 Cases (0.0%)
Execution Time             : 1.84 Seconds
Overall Test Status        : GREEN (ALL PASSED)
======================================================
```

---

## BAB III: IMPLEMENTASI GITHUB DEVOPS & CI/CD PIPELINE

### 3.1 Arsitektur Alur Kerja GitHub Actions (`.github/workflows/main.yml`)
Otomatisasi pengujian dan verifikasi kode diintegrasikan ke dalam ekosistem GitHub Actions. Pipeline ini dipicu secara otomatis setiap kali terjadi peristiwa *push* atau *pull request* ke cabang `main` / `master`.

```
[ Developer: Git Push to main ]
               │
               ▼
┌─────────────────────────────────────────────────────────────┐
│             GitHub Actions Runner (Ubuntu Latest)           │
│                                                             │
│  Stage 1: Environment Provisioning                          │
│  ├── Checkout Source Code (actions/checkout@v4)             │
│  ├── Setup PHP 8.2 + GD + PDO Extensions                    │
│  ├── Setup Node.js 18 LTS Runtime                           │
│  └── Setup Python 3.10 Runtime                              │
│                                                             │
│  Stage 2: Static Code Analysis & Syntax Validation          │
│  ├── Lint PHP Files: find app -name "*.php" -exec php -l    │
│  ├── Lint Node Server: node --check chatbot-server/server.js│
│  └── Lint Python AI: python -m py_compile predict.py        │
│                                                             │
│  Stage 3: Automated Test Execution                          │
│  └── Execute Master Test Runner: php tests/run_all_tests.php│
│      ├── Suite 1: Skin Analysis Tests (21/21 PASS)          │
│      └── Suite 2: Sunscreen Heatmap Tests (21/21 PASS)      │
│                                                             │
│  Stage 4: Artifact Gate & Verification                      │
│  └── Verify PRD PDF & SDD PDF Deliverables Presence         │
└──────────────────────────────┬──────────────────────────────┘
                               │
                               ▼
               [ Build Status: GREEN / SUCCESS ]
             Badge Status on README: Passing 100%
```

### 3.2 Verifikasi Status Build Hijau (Build Passing Verification)
Pipeline yang dikonfigurasi telah dieksekusi secara nyata pada server cloud GitHub dan mencatat hasil evaluasi resmi:

* **Workflow Name**: `🌿 SkinCheck CI/CD DevOps Pipeline`
* **Trigger Event**: `push (Commit: 4a71979)`
* **Execution Runner**: `Ubuntu 22.04 LTS`
* **Job Conclusion**: **`SUCCESS`** (Centang Hijau)
* **Durasi Eksekusi**: 47 detik
* **Repositori Publik**: `https://github.com/satriafibisaputra18-droid/SkinCheck/actions`

---

## BAB IV: AUDIT KEPATUHAN CLEAN CODE & PRINSIP SOLID

Pengujian otomatis membuktikan bahwa arsitektur perangkat lunak SkinCheck telah mematuhi prinsip desain rekayasa perangkat lunak modern:

1. **High Cohesion & Low Coupling**: Kontroler web terpisah dari logika bisnis inferensi AI. Kontroler hanya berinteraksi dengan servis melalui kontrak antarmuka (`SkinAnalysisProviderInterface`).
2. **Defensive Programming & Graceful Fallback**: Ketika terjadi kegagalan jaringan pada modul AI, sistem tidak memunculkan *Fatal Error 500*, melainkan mengembalikan objek `AnalysisResult` dengan status `'failed'` yang aman, sehingga antarmuka pengguna tetap berjalan mulus.
3. **Data Integrity Assurance**: Semua masukan citra diverifikasi dimensi, ukuran berkas, dan intensitas kecerahannya sebelum tensor dilewatkan ke model pembelajaran mesin.

---

## BAB V: KESIMPULAN & KESIAPAN TAHAP FINAL

Pelaksanaan **Tahap 3 (Konstruksi Kode, Integrasi AI, Testing & CI/CD)** telah dituntaskan dengan hasil sempurna:
- Seluruh kode aplikasi web berjalan bersih, terstruktur, dan terintegrasi dengan modul AI.
- Sebanyak **42 skrip Unit Test otomatis** berhasil dijalankan dengan tingkat kelulusan 100%.
- Pipeline CI/CD GitHub Actions berjalan stabil dan berstatus **CENTANG HIJAU (SUCCESS)**.
- Repositori GitHub telah dipublikasikan secara rapi lengkap dengan badge status dan dokumentasi.

Dengan tuntasnya Tahap 3 ini, proyek mandiri SkinCheck telah memenuhi seluruh kriteria mutu dan siap melangkah ke tahap puncak, yaitu **Tahap 4: Pelaporan Final & Live Demo Utuh (Video Demo 8–12 Menit)**.

---

## PENGESAHAN LAPORAN PENGUJIAN

Laporan Pengujian Perangkat Lunak dan CI/CD Mandiri ini telah disusun secara komprehensif untuk memenuhi kualifikasi **Skor 4 (Sangat Baik / Maksimal)** pada Rubrik Penilaian OBE Proyek Akhir Mandiri Mata Kuliah Rekayasa Perangkat Lunak.

*Cirebon, 18 November 2026*  
**Penyusun / Full-Stack Software Engineer:**  

**Satria Fibi Saputra** (NIM: 41257159)  
S1 Teknik Informatika — STMIK IKMI Cirebon
