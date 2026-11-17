# NASKAH & PANDUAN REKAMAN VIDEO PROGRES 1
## Proyek Akhir Rekayasa Perangkat Lunak (TIKB2503021) — STMIK IKMI Cirebon
**Topik Presentasi**: Inisiasi Proyek, Identifikasi Masalah, PRD/SRS & Peran Strategis Fitur AI  
**Produk**: SkinCheck — AI-Powered Skin Barrier & Sunscreen Analysis Platform  
**Target Durasi**: **3 s.d. 5 Menit** (Estimasi Naskah: ~4 Menit)  
**Bobot Nilai**: 20% dari Total Nilai Video Presentasi (Target: Skor 4 / Sangat Baik)

---

## 🛠️ CHECKLIST TEKNIS SEBELUM MENEKAN TOMBOL "RECORD":
1. **Perekam Layar**: Gunakan **OBS Studio**, **Loom**, atau software perekam layar favorit Tuan.
2. **Webcam Wajib Aktif**: Posisikan kamera wajah Tuan di pojok kanan bawah atau pojok kanan atas layar (wajah terlihat jelas dan pencahayaan terang).
3. **Audio**: Gunakan headset/mic yang jernih, minimalkan suara bising di sekitar.
4. **Materi di Layar**: Tampilkan Slide Ringkasan (PowerPoint/Canva) atau gulir berkas `PRD_SRS_SkinCheck.md`.
5. **Sikap**: Tatap kamera secara berkala, sampaikan dengan intonasi percaya diri, tegas, dan natural.

---

## ⏱️ TIMELINE & NASKAH LENGKAP PRESENTASI

### 📍 SEGMEN 1: Pembukaan & Identitas Diri (00:00 – 00:30 | 30 Detik)
**Tampilan Layar**: *Slide 1: Judul Proyek, Nama Mahasiswa, NIM, Mata Kuliah, Dosen Pengampu (Bpk. Yudhistira Arie Wijaya, M.Kom., CDS.), & Tema Kelompok.*

> **Teks Narasi Tuan:**  
> "Assalamu’alaikum Warahmatullahi Wabarakatuh, salam sejahtera untuk kita semua.  
> 
> Yang terhormat Bapak Yudhistira Arie Wijaya selaku dosen pengampu mata kuliah Rekayasa Perangkat Lunak. Perkenalkan, nama saya **Satria Fibi Saputra**, NIM **41257159**, dari program studi S1 Teknik Informatika STMIK IKMI Cirebon.  
> 
> Pada kesempatan kali ini, di bawah koridor tema kelompok *'Smart Healthcare & Public Service AI'*, saya akan mempresentasikan laporan **Progres Tahap 1: Inisiasi Proyek dan Penyusunan Dokumen PRD / SRS Mandiri** untuk aplikasi yang saya bangun secara utuh, yaitu **SkinCheck: AI-Powered Skin Barrier & Sunscreen Analysis Platform**."

---

### 📍 SEGMEN 2: Latar Belakang Masalah & Urgensi Nyata (00:30 – 01:15 | 45 Detik)
**Tampilan Layar**: *Slide 2: Poin Masalah Nyata (3 Problem Utama di Masyarakat).*

> **Teks Narasi Tuan:**  
> "Proyek ini dilatarbelakangi oleh permasalahan nyata di masyarakat terkait kesehatan sawar kulit atau *skin barrier*. Sawar kulit adalah garda terdepan pertahanan wajah dari polusi, radiasi ultraviolet, dan bakteri. Namun, ada tiga kendala besar yang dihadapi masyarakat saat ini:  
> 
> Pertama, **akses konsultasi dermatologi klinis yang relatif mahal** dan tidak terjangkau bagi sebagian besar pelajar dan mahasiswa.  
> Kedua, **sulitnya deteksi dini kerusakan kulit secara mandiri**. Kerusakan mikroskopis sering kali tidak disadari sebelum berkembang menjadi jerawat parah atau flek hitam permanen.  
> Dan ketiga, **tingginya angka kesalahan pemakaian tabir surya (*under-application of sunscreen*)**, di mana lebih dari 70% pengguna tidak mengaplikasikan tabir surya secara merata sehingga meninggalkan area wajah yang rentan terpapar UV.  
> 
> Berangkat dari masalah inilah, saya menginisiasi **SkinCheck** sebagai platform evaluasi mandiri yang cepat, presisi, dan mudah diakses langsung melalui peramban web."

---

### 📍 SEGMEN 3: Nilai Guna & Solusi Produk SkinCheck (01:15 – 02:00 | 45 Detik)
**Tampilan Layar**: *Slide 3: Gambaran Produk & Fitur Unggulan SkinCheck.*

> **Teks Narasi Tuan:**  
> "**SkinCheck** hadir menjembatani kebutuhan tersebut dengan keunggulan utama:  
> 
> 1. **Zero-Installation & Browser-First**: Pengguna tidak perlu mengunduh aplikasi native dari Play Store. Cukup buka browser, kamera webcam dengan panduan HUD (*Heads-Up Display*) oval langsung aktif memandu posisi wajah pengguna.  
> 2. **Evaluasi 4 Pilar Klinis Sawar Kulit**: Sistem mengukur skor kuantitatif 0 sampai 100 untuk Jerawat dan Pori, Tingkat Kemerahan atau Eritema, Kehalusan Tekstur, serta Hiperpigmentasi.  
> 3. **Sunscreen Coverage Check & Heatmap**: Mengevaluasi seberapa merata tabir surya diaplikasikan dan menghasilkan peta gradien warna *Heatmap* untuk memperlihatkan area wajah yang terlewat.  
> 4. **AI Skincare Assistant Interaktif**: Asisten konsultasi cerdas yang memahami langsung riwayat scan pengguna untuk memberikan rekomendasi bahan aktif yang ilmiah dan aman."

---

### 📍 SEGMEN 4: Kebutuhan Fungsional & Non-Fungsional (PRD/SRS) (02:00 – 02:50 | 50 Detik)
**Tampilan Layar**: *Slide 4: Dokumen PRD IEEE 830, Tabel User Stories, & NFR Matrix.*

> **Teks Narasi Tuan:**  
> "Dalam dokumen PRD berstandar **IEEE 830** yang telah saya susun, sistem SkinCheck terbagi menjadi dua ranah persona: Portal Pelanggan (*Customer*) dan Portal Manajemen Eksekutif (*Admin*).  
> 
> Beberapa kebutuhan fungsional kunci yang telah didefinisikan meliputi:  
> - **Autentikasi Aman Multi-Kanal**: Pendaftaran akun dilindungi OTP 6-digit via Bot Telegram dan Email Resend API.  
> - **Validasi Citra Cerdas**: Kamera otomatis memvalidasi resolusi dan tingkat pencahayaan sebelum citra diproses.  
> - **Manajemen Token & Admin Console**: Administrator dapat memantau kuota token Gemini API secara real-time dan mengelola konfigurasi kunci API secara terpusat tanpa menyentuh kode program.  
> 
> Dari sisi **Kebutuhan Non-Fungsional**, sistem menetapkan standar latensi inferensi Computer Vision di bawah 1,5 detik, hashing sandi dengan standar Bcrypt, serta isolasi keamanan berkas citra wajah agar tidak dapat diakses publik secara sembarangan."

---

### 📍 SEGMEN 5: Peran Strategis Fitur AI yang Ditanamkan (02:50 – 03:40 | 50 Detik)
**Tampilan Layar**: *Slide 5: Diagram Arsitektur AI (MobileNetV3 ONNX + Google Gemini Flash).*

> **Teks Narasi Tuan:**  
> "Hal yang menjadi diferensiasi utama dan kekuatan dari SkinCheck adalah **penanaman dua mesin Kecerdasan Artifisial yang saling melengkapi**:  
> 
> Yang pertama adalah **Computer Vision berbasis MobileNetV3 yang diekspor ke format ONNX**. Arsitektur ini menggunakan *multi-task regression heads* untuk mengekstraksi fitur visual wajah dan menghasilkan skor 4 pilar sawar kulit serta memetakan matriks distribusi tabir surya secara lokal berkecepatan tinggi.  
> 
> Yang kedua adalah **Large Language Model menggunakan Google Gemini Flash** yang dihubungkan melalui microservice Node.js. Fitur AI ini memiliki *context-awareness*, artinya prompt yang dikirim ke Gemini secara otomatis diinjeksi dengan skor scan terakhir pengguna, sehingga AI dapat bertindak layaknya konsultan dermatologi pribadi yang merekomendasikan bahan aktif seperti Niacinamide, Ceramide, atau Centella Asiatica secara tepat sasaran."

---

### 📍 SEGMEN 6: Penutup & Rencana Tahap Selanjutnya (03:40 – 04:05 | 25 Detik)
**Tampilan Layar**: *Slide 6: Penutup, Rencana Tahap 2 (Pemodelan UML SDD & Figma), & Ucapan Terima Kasih.*

> **Teks Narasi Tuan:**  
> "Dokumen PRD/SRS lengkap berstandar IEEE 830 telah selesai saya dokumentasikan pada repositori proyek.  
> 
> Pada tahap berikutnya, yaitu **Tahap 2**, saya akan melanjutkan ke perancangan arsitektur perangkat lunak (SDD) yang memodelkan 4 diagram UML utama—Use Case, Activity, Class berorientasi SOLID, dan Sequence Diagram—beserta prototipe interaktif UI/UX di Figma.  
> 
> Demikian presentasi Progres Tahap 1 proyek mandiri SkinCheck ini. Terima kasih atas perhatian Bapak Yudhistira Arie Wijaya.  
> Wassalamu’alaikum Warahmatullahi Wabarakatuh."

---

## 💡 TIPS SUKSES MERAIH SKOR 4 DARI DOSEN:
1. **Jangan Membaca Kaku**: Naskah di atas adalah panduan alur. Tuan dapat menggunakan gaya bahasa sendiri yang santai namun tetap formal dan percaya diri.
2. **Perhatikan Timer**: Pasang *stopwatch* di samping laptop. Pastikan durasi rekaman video berada di antara **3 menit 30 detik s.d. 4 menit 30 detik**.
3. **Pencahayaan Webcam**: Pastikan wajah Tuan tidak *backlight* agar dosen dapat memverifikasi keaslian pengerjaan mandiri Tuan secara jelas.
