# ⚡ Panduan Melatih AI SkinCheck di Kaggle Notebooks

Keputusan Tuan sangat tepat! Melatih model AI di **Kaggle Notebooks** jauh lebih cepat, stabil, dan ringan dibandingkan Google Colab:
* 🆓 **GPU T4 x2 / GPU P100 Gratis** (Kuota 30 jam/minggu, VRAM besar hingga 16GB).
* 💾 **RAM 20 GB - 30 GB** (Colab sering crash karena RAM habis, di Kaggle sangat lega).
* ⚡ **Fitur "+ Add Input" (0 Detik Download)**: Dataset yang ada di Kaggle tidak perlu di-download lewat internet lagi; langsung terpasang instan di server Kaggle!
* 🌐 Tidak mudah terputus koneksi (disconnect) seperti Google Colab.

---

## 📍 Berkas Notebook Khusus Kaggle:
👉 **[`SkinCheck_AI_Trainer_Kaggle.ipynb`](file:///c:/xampp/htdocs/Skin%20Check/SkinCheck_AI_Trainer_Kaggle.ipynb)**  
*(Juga tersedia di folder [`notebooks/SkinCheck_AI_Trainer_Kaggle.ipynb`](file:///c:/xampp/htdocs/Skin%20Check/notebooks/SkinCheck_AI_Trainer_Kaggle.ipynb))*

---

## 🚀 Langkah Demi Langkah Menggunakan Kaggle Notebooks:

### 1. Buka Kaggle & Buat Notebook Baru
1. Buka browser dan kunjungi: **[https://www.kaggle.com/code](https://www.kaggle.com/code)** (Pastikan sudah login dengan akun Tuan: `satriafibisaputra`).
2. Klik tombol biru **"New Notebook"** di pojok kanan atas.
3. Di dalam editor notebook baru:
   - Klik menu **File** (di kiri atas editor) ➔ Pilih **"Upload Notebook"**.
   - Pilih berkas dari komputer Tuan:  
     `c:\xampp\htdocs\Skin Check\SkinCheck_AI_Trainer_Kaggle.ipynb`
   - Klik **Upload**.

---

### 2. PENTING: Aktifkan 2 Pengaturan di Panel Kanan (Notebook Options)
Lihat bilah panel sebelah kanan (klik tanda panah `|◀` jika panel kanan tertutup):
1. **Accelerator**: Klik dan ubah dari *None* menjadi **GPU T4 x2** (atau **GPU P100**).
2. **Internet**: Geser saklar menjadi **Internet on** *(Wajib dinyalakan agar Kaggle bisa menginstal modul dan download data).*

---

### 3. Cara Menambahkan Dataset dengan Super Cepat (Fitur "+ Add Input")
Di Kaggle, Tuan punya 2 cara untuk memasukkan dataset:

#### Cara A: Paling Mudah & 0 Detik Download (Rekomendasi!)
1. Di panel sebelah kanan atas, klik tombol **"+ Add Input"**.
2. Di kolom pencarian, ketik kata kunci seperti:
   * `acne dataset`
   * `skin disease dataset`
   * `skin-cancer-mnist-ham10000`
3. Klik tombol **`+` (Add)** pada dataset yang diinginkan.
4. Dataset langsung terpasang di folder `/kaggle/input/` secara instan tanpa menunggu download!
5. Tuan bisa klik **"+ Add Input"** lagi untuk menambahkan 2, 3, atau lebih dataset sekaligus!

#### Cara B: Menggunakan Link URL di Tahap 2
Tuan juga tetap bisa menempelkan link URL Kaggle di sel **Tahap 2** (`DATASET_URL_OR_SLUG`). Sistem akan mendownloadnya secara otomatis via Kaggle API Tuan (`KGAT_ec...`).

---

### 4. Jalankan Sel 1 sampai 8 Secara Berurutan
Klik tombol Play `[▶]` atau tekan `Shift + Enter` dari atas ke bawah:
* **Tahap 1**: Menyiapkan environment Kaggle GPU & API key Tuan.
* **Tahap 2**: Download via URL (jika menggunakan link).
* **Tahap 3 (Timbun Dataset)**: Otomatis memindai semua dataset (dari input Kaggle maupun download), menyaring foto corrupt, memeriksa deduplikasi MD5, dan menyusunnya ke folder master.
* **Tahap 4**: Mengaktifkan augmentasi multi-sudut (wajah hadap kanan, kiri, atas, bawah, dekat, jauh).
* **Tahap 5**: Membangun arsitektur MobileNetV3 Multi-Task (Jerawat, Kemerahan, Hyperpigmentasi, Tekstur).
* **Tahap 6**: Melatih model dengan kecepatan penuh GPU Kaggle (T4 x2).
* **Tahap 7**: Menguji model langsung di notebook.
* **Tahap 8**: Ekspor ke format standar web Antigravity.

---

### 5. Cara Download Hasil Model dari Kaggle
Setelah menjalankan **Tahap 8**:
1. Akan muncul link teks biru: **`skincheck_ai_exported.zip`**. Klik link tersebut untuk langsung mengunduh ke komputer Tuan!
2. Atau, lihat panel sebelah kanan pada tab **Output** (`/kaggle/working/`):
   - Arahkan kursor ke file **`skincheck_ai_exported.zip`**.
   - Klik ikon **Download** (panah bawah).

---

### 6. Pasang ke Proyek SkinCheck di Komputer Tuan
Ekstrak file `skincheck_ai_exported.zip`, lalu pindahkan berkasnya ke:
👉 **[`c:\xampp\htdocs\Skin Check\storage\models\`](file:///c:/xampp/htdocs/Skin%20Check/storage/models/)**

Selesai! Web SkinCheck akan langsung mendeteksi model hasil pelatihan Kaggle Tuan dan siap digunakan untuk Face Scan customer!
