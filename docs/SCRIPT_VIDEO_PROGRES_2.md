# NASKAH & PANDUAN REKAMAN VIDEO PROGRES 2
## Proyek Akhir Rekayasa Perangkat Lunak (TIKB2503021) — STMIK IKMI Cirebon
**Topik Presentasi**: Pemodelan 4 Diagram UML Utama (SDD), Kepatuhan SOLID, ERD, & Walkthrough UI/UX  
**Produk**: SkinCheck — AI-Powered Skin Barrier & Sunscreen Analysis Platform  
**Penyaji / Mahasiswa**: Satria Fibi Saputra (NIM: 41257159)  
**Target Durasi**: **5 s.d. 7 Menit** (Estimasi Naskah: $\approx$ 6 Menit)  
**Bobot Nilai**: 30% dari Total Nilai Video Presentasi (Target: Skor 4 / Sangat Baik)

---

## 🛠️ PANDUAN TEKNIS SEBELUM REKAMAN:
1. **Perekam Layar**: OBS Studio / Loom (Resolusi disarankan 1080p).
2. **Webcam Wajib Aktif**: Wajah Tuan tampil jelas di pojok layar sepanjang presentasi.
3. **Materi di Layar**: Buka berkas [`docs/SDD_UML_SkinCheck.pdf`](file:///c:/xampp/htdocs/Skin%20Check/docs/SDD_UML_SkinCheck.pdf) dalam mode layar penuh (tekan **F11** di browser/PDF reader).
4. **Alur Scrolling**: Scroll perlahan mengikuti nomor halaman pada naskah ini.

---

## ⏱️ TIMELINE & NASKAH LENGKAP VIDEO PROGRES 2

### 📍 SEGMEN 1: Pembukaan & Gambaran Arsitektur (00:00 – 00:45 | 45 Detik)
📄 **Halaman PDF**: **Halaman 1 (Cover) & Halaman 3 (Bab I: Arsitektur)**

> **🗣️ Kalimat yang Tuan Baca:**  
> "Assalamu’alaikum Warahmatullahi Wabarakatuh, salam sejahtera untuk kita semua.  
> 
> Yang terhormat Bapak Yudhistira Arie Wijaya, M.Kom., CDS. selaku dosen pengampu mata kuliah Rekayasa Perangkat Lunak. Berjumpa kembali dengan saya, **Satria Fibi Saputra**, NIM **41257159**, mahasiswa S1 Teknik Informatika STMIK IKMI Cirebon.  
> 
> Melanjutkan keberhasilan Tahap 1, pada kesempatan kali ini saya akan mempresentasikan **Laporan Progres Tahap 2: Perancangan Arsitektur Perangkat Lunak (SDD)** untuk aplikasi mandiri saya, yaitu **SkinCheck: AI-Powered Skin Barrier & Sunscreen Analysis Platform**.  
> 
> Pada dokumen SDD ini, saya memodelkan 4 diagram UML utama yang terikat kuat dengan kode implementasi, menerapkan prinsip Clean Code & SOLID, serta merancang basis data relasional dan arsitektur UI/UX."

---

### 📍 SEGMEN 2: Bedah Diagram UML 1 — Use Case Diagram & Skenario (00:45 – 02:00 | 75 Detik)
📄 **Halaman PDF**: **Halaman 5 – 7 (Bab II: Use Case Diagram & Skenario)**

> **🗣️ Kalimat yang Tuan Baca:**  
> "Kita mulai dari **Diagram UML pertama, yaitu Use Case Diagram**.  
> 
> Sistem SkinCheck mendefinisikan dua aktor utama:  
> 1. **Customer (Pelanggan)** yang berinteraksi dengan fitur inti seperti pemindaian sawar kulit via smart HUD, pemeriksaan kerataan tabir surya, pelacakan riwayat perkembangan kulit, dan konsultasi interaktif dengan AI.  
> 2. **Executive Administrator** yang memiliki wewenang untuk memantau dashboard KPI bisnis, mengaudit log pemindaian, mengelola katalog kosmetik, dan memantau pemakaian token AI Gemini secara langsung.  
> 
> Sebagai contoh skenario mendalam pada **UC-03**, ketika pelanggan melakukan pemindaian wajah, sistem tidak langsung memproses citra mentah. Terdapat pra-kondisi berupa validasi pencahayaan dan resolusi pada HUD. Jika kondisi optimal tercapai, barulah alur komputasi inferensi dijalankan untuk mengekstrak skor 4 pilar sawar kulit secara terstruktur."

---

### 📍 SEGMEN 3: Bedah Diagram UML 2 — Activity Diagram Alur Bisnis & AI (02:00 – 03:15 | 75 Detik)
📄 **Halaman PDF**: **Halaman 8 – 9 (Bab III: Activity Diagram)**

> **🗣️ Kalimat yang Tuan Baca:**  
> "Selanjutnya, kita masuk ke **Diagram UML kedua: Activity Diagram**. Di sini saya memodelkan dua alur komputasi penting:  
> 
> Pertama, **Alur Pemindaian Wajah & Pipeline AI**. Dimulai saat pengguna membuka kamera, sistem melakukan *heuristic check* terhadap tingkat pencahayaan (*luminance*). Setelah foto diambil, `ScanController` meneruskan citra ke `ImageValidator` untuk memastikan keaslian wajah.  
> 
> Jika valid, citra disimpan ke direktori privat dan dieksekusi oleh `SkinAnalysisService` menggunakan model **MobileNetV3 ONNX**. Model melakukan *forward pass* ke empat *regression heads* sekaligus untuk menghitung skor Jerawat, Eritema, Tekstur, dan Hiperpigmentasi dalam waktu di bawah 1,5 detik.  
> 
> Kedua, **Alur Chatbot Gemini Sadar-Konteks**. Ketika pengguna mengirimkan pesan konsultasi, sistem terlebih dahulu mengambil data riwayat skor scan terakhir pengguna dari basis data, lalu menyuntikkannya ke dalam *system prompt* Google Gemini Flash melalui microservice Node.js. Respon yang dihasilkan menjadi sangat personal dan berbasis data klinis pengguna."

---

### 📍 SEGMEN 4: Bedah Diagram UML 3 — Class Diagram & Kepatuhan SOLID (03:15 – 04:30 | 75 Detik)
📄 **Halaman PDF**: **Halaman 10 – 11 (Bab IV: Class Diagram & SOLID)**

> **🗣️ Kalimat yang Tuan Baca:**  
> "Diagram UML ketiga adalah **Class Diagram berorientasi objek yang dirancang patuh terhadap 5 prinsip SOLID**:  
> 
> 1. **Single Responsibility (SRP)**: Terlihat dari pemisahan tugas yang tegas. Kelas `ImageValidator` murni memvalidasi citra, `ImageStorage` mengamankan berkas, dan `SkinAnalysisService` mengurus inferensi.  
> 2. **Open/Closed Principle (OCP)**: Saya menerapkan abstraksi kontrak antarmuka `SkinAnalysisProviderInterface` dan `SunscreenAnalysisProviderInterface`. Jika di masa depan sistem ingin beralih dari model ONNX lokal ke Cloud API, kita cukup membuat kelas baru tanpa perlu mengubah kode yang ada pada controller.  
> 3. **Liskov Substitution (LSP)**: Kelas `OnnxSkinAnalysisProvider` dan `ComputerVisionSkinProvider` dapat saling menggantikan secara transparan karena keduanya mengembalikan kontrak objek `AnalysisResult` yang konsisten.  
> 4. **Interface Segregation (ISP)**: Pemisahan antarmuka deteksi wajah, analisis kulit, dan sunscreen dilakukan secara independen agar kelas tidak dipaksa mengimplementasikan metode yang tidak relevan.  
> 5. **Dependency Inversion (DIP)**: Controller tingkat tinggi tidak bergantung langsung pada skrip inferensi Python, melainkan bergantung pada abstraksi antarmuka servis."

---

### 📍 SEGMEN 5: Bedah Sequence Diagram & ERD Basis Data (04:30 – 05:45 | 75 Detik)
📄 **Halaman PDF**: **Halaman 12 – 13 (Bab V & Bab VI: Sequence Diagram & ERD)**

> **🗣️ Kalimat yang Tuan Baca:**  
> "Diagram UML keempat adalah **Sequence Diagram** yang memetakan pertukaran pesan antar-komponen secara kronologis:  
> 
> Terlihat bagaimana Browser melakukan HTTP POST ke `ScanController`, lalu didelegasikan ke `ImageValidator`, `ImageStorage`, hingga memanggil `OnnxSkinProvider`. Setelah kalkulasi selesai, skor disimpan secara relasional ke basis data MySQL sebelum mengembalikan halaman hasil ke pengguna.  
> 
> Pada **Bab VI (Entity-Relationship Diagram / ERD)**, sistem mengelola 7 tabel relasional:  
> - Tabel `users` berelasi *one-to-many* dengan `skin_scans`.  
> - Setiap record pemindaian berelasi *one-to-one* dengan tabel `analysis_results` untuk rincian 4 pilar skor, serta `sunscreen_results` untuk menyimpan nilai *coverage* dan path citra *Heatmap*.  
> - Integritas data dijaga ketat dengan *Foreign Key Constraints ON DELETE CASCADE*, sementara konfigurasi kunci API terpusat pada tabel `settings` dengan atribut *is_secret* agar tersamarkan."

---

### 📍 SEGMEN 6: Walkthrough UI/UX Figma & Penutup (05:45 – 06:45 | 60 Detik)
📄 **Halaman PDF**: **Halaman 14 (Bab VII: Sitemap & UI/UX) & Halaman 15 (Pengesahan)**  
*(Opsional: Tuan bisa *switch window* 10–15 detik memperlihatkan aplikasi SkinCheck yang berjalan di browser)*

> **🗣️ Kalimat yang Tuan Baca:**  
> "Pada **Bab VII**, perancangan UI/UX disusun dengan hierarki sitemap yang intuitif dan *mobile-responsive*.  
> 
> Pengguna disuguhkan antarmuka kamera HUD dengan garis oval pemandu wajah dan animasi pemindai laser. Pada halaman detail hasil, pengguna dapat melihat visualisasi perbandingan *side-by-side* foto wajah dengan peta panas *Heatmap* sunscreen, grafik progress 4 pilar sawar kulit, rekomendasi produk yang cocok, serta konsol pemantau token AI di portal admin.  
> 
> Dokumen SDD ini membuktikan bahwa arsitektur sistem SkinCheck telah dirancang secara matang, konsisten, dan siap diimplementasikan secara utuh.  
> 
> Pada tahap selanjutnya, yaitu **Tahap 3**, saya akan menuntaskan pengujian otomatis Unit Test dan konfigurasi otomatisasi CI/CD pipeline pada GitHub Actions.  
> 
> Sekian presentasi Progres Tahap 2 dari saya. Terima kasih banyak atas perhatian Bapak Yudhistira Arie Wijaya.  
> Wassalamu’alaikum Warahmatullahi Wabarakatuh."

---

## 💡 TIPS SUKSES MERAIH SKOR 4 VIDEO 2:
1. **Patuhi Batas Waktu**: Targetkan durasi rekaman tuntas di kisaran **5 menit 30 detik s.d. 6 menit 30 detik** (sangat aman di rentang wajib 5–7 menit).
2. **Kuasai Istilah SOLID**: Sebutkan kelima prinsip SOLID secara tegas dan lancar saat membahas Halaman 10–11 (dosen sangat menilai aspek kedalaman arsitektur ini!).
3. **Perekaman Wajah**: Pastikan webcam tetap aktif di pojok layar dari awal hingga akhir presentasi.
