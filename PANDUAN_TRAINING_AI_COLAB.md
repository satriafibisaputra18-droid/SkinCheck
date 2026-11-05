# 📖 Panduan Lengkap: Melatih AI SkinCheck di Google Colab & Integrasi ke Antigravity

Panduan ini mendampingi file notebook **[`SkinCheck_AI_Trainer.ipynb`](file:///c:/xampp/htdocs/Skin%20Check/SkinCheck_AI_Trainer.ipynb)** yang telah disiapkan di direktori proyek Anda. Notebook ini menghubungkan Google Colab dengan akun Kaggle Anda (`satriafibisaputra`), memungkinkan Anda untuk menimbun dataset bertahap, melatih model AI dengan simulasi multi-posisi wajah, dan mengekspor hasilnya langsung ke proyek web SkinCheck.

---

## 📑 Daftar Isi
1. [Fitur Utama Notebook](#1-fitur-utama-notebook)
2. [Cara Membuka Notebook di Google Colab](#2-cara-membuka-notebook-di-google-colab)
3. [Alur Kerja Penimbunan Dataset ("Nimbun Dataset")](#3-alur-kerja-penimbunan-dataset-nimbun-dataset)
4. [Rekomendasi Dataset dari Kaggle](#4-rekomendasi-dataset-dari-kaggle)
5. [Konsep Augmentasi Multi-Sudut (Kanan, Kiri, Atas, Bawah, Dekat, Jauh)](#5-konsep-augmentasi-multi-sudut)
6. [Proses Training & Fitur Resume Checkpoint](#6-proses-training--fitur-resume-checkpoint)
7. [Pengujian Interaktif di Colab](#7-pengujian-interaktif-di-colab)
8. [Ekspor & Pasang Model ke Antigravity](#8-ekspor--pasang-model-ke-antigravity)

---

## 1. Fitur Utama Notebook

* 🔑 **Auto Kaggle Auth**: Kredensial akun Kaggle Anda (`satriafibisaputra` & API Token Anda) sudah tertanam otomatis di sel pertama. Tidak perlu upload `kaggle.json` manual lagi.
* 📥 **Universal Downloader**: Cukup copy-paste link/URL Kaggle apa saja dari browser, sistem otomatis mengunduh dan mengekstrak ZIP/TAR.
* 📦 **Sistem Penimbun Dataset (Deduplikasi MD5)**: Anda bisa mendownload 5 hingga 10 dataset secara terpisah. Sistem akan otomatis membersihkan file corrupt, memeriksa duplikat dengan checksum MD5, dan menyatukannya ke dalam Master Repository.
* 🔄 **Pose Invariant Augmentation**: Mensimulasikan pemindaian wajah multi-pose (hadap kanan, hadap kiri, hadap atas, hadap bawah, zoom dekat, dan portrait jauh) serta getaran gerak kamera.
* 🧠 **Arsitektur Multi-Task CNN**: Menghasilkan 4 skor indikator visual sekaligus:
  - **Jerawat (Acne)**: Skor 0–100
  - **Kemerahan (Redness)**: Skor 0–100
  - **Hyperpigmentasi (Dark Spots / Flek)**: Skor 0–100
  - **Tekstur Kulit (Pori / Kerutan / Roughness)**: Skor 0–100
* 🔁 **Pause & Resume Training**: Anda bisa melatih model, mengujinya, lalu menimbun dataset baru lagi dan melanjutkan training dari checkpoint sebelumnya (`RESUME = True`).
* ⚡ **1-Click Export ke Antigravity**: Model dikemas ke dalam format `.onnx` (sangat ringan untuk web) dan otomatis terunduh dalam format `.zip`.

---

## 2. Cara Membuka Notebook di Google Colab

1. Buka browser dan kunjungi **[Google Colab](https://colab.research.google.com/)**.
2. Di jendela pop-up yang muncul:
   - Pilih tab **Upload** (Unggah).
   - Klik **Browse** / Pilih Berkas.
   - Arahkan ke file: `c:\xampp\htdocs\Skin Check\SkinCheck_AI_Trainer.ipynb`.
3. **PENTING - Aktifkan Akselerasi GPU Gratis:**
   - Di menu atas Colab, klik **Runtime** ➔ **Change runtime type** (Ubah jenis runtime).
   - Pada opsi *Hardware accelerator*, pilih **T4 GPU**.
   - Klik **Save** (Simpan).
4. Sekarang notebook siap dijalankan sel per sel!

---

## 3. Alur Kerja Penimbunan Dataset ("Nimbun Dataset")

Konsep penimbunan dataset dirancang agar Anda bisa mengumpulkan data sedikit demi sedikit hingga dirasa cukup:

```
[Kaggle Dataset 1] ──> [Download (Tahap 2)] ──> [Bersihkan & Deduplikasi MD5 (Tahap 3)] ──┐
[Kaggle Dataset 2] ──> [Download (Tahap 2)] ──> [Bersihkan & Deduplikasi MD5 (Tahap 3)] ──┼──> [Master Dataset] ──> [Training AI]
[Kaggle Dataset 3] ──> [Download (Tahap 2)] ──> [Bersihkan & Deduplikasi MD5 (Tahap 3)] ──┘
```

### Langkah Praktis Menimbun Dataset:
1. Jalankan **Tahap 1** (Setup Environment).
2. Temukan dataset di Kaggle (lihat rekomendasi di Bab 4). Salin URL dari browser.
3. Tempel link tersebut pada form **Tahap 2** (`DATASET_URL_OR_SLUG`), lalu jalankan sel tersebut. Colab akan mendownload dan mengekstraknya.
4. Jalankan **Tahap 3** (Pembersih Cerdas & Akumulator).
   - Sistem akan memeriksa apakah foto corrupt.
   - Sistem mengecek hash MD5 agar foto yang sudah ada tidak dimasukkan dobel.
   - Sistem memetakan foto ke folder `acne`, `redness`, `hyperpigmentation`, `texture`, atau `normal_skin`.
   - Menampilkan grafik batang jumlah koleksi foto terkini Anda.
5. **Ingin nambah dataset lagi?**
   - Cukup ganti link URL di **Tahap 2**, jalankan kembali.
   - Jalankan lagi **Tahap 3**. Koleksi foto di Master Dataset akan terus bertambah (menimbun) secara otomatis!

---

## 4. Rekomendasi Dataset dari Kaggle

Anda tinggal menyalin salah satu link di bawah ini dan menempelkannya ke form **Tahap 2**:

1. **Acne / Jerawat**:
   - `https://www.kaggle.com/datasets/rutikbhartiya/acne-dataset`
   - `https://www.kaggle.com/datasets/faizalkarim/acne-classification`
2. **Kondisi Kulit & Kemerahan (Erythema/Rosacea)**:
   - `https://www.kaggle.com/datasets/subirkesharwani/skin-disease-dataset`
   - `https://www.kaggle.com/datasets/ismailpromus/skin-diseases-image-dataset`
3. **Hyperpigmentasi & Bintik Gelap**:
   - `https://www.kaggle.com/datasets/kmader/skin-cancer-mnist-ham10000`
4. **Tekstur & Kulit Sehat / Normal (Baseline)**:
   - `https://www.kaggle.com/datasets/arashnic/skin-burn-dataset`
   - `https://www.kaggle.com/datasets/farjanakabirsamanta/skin-diseases`

---

## 5. Konsep Augmentasi Multi-Sudut

Permintaan khusus Anda: *"User diminta hadap kanan, kiri, atas, bawah, dekat, dan jauh sehingga AI menjepret ribuan foto."*

Di **Tahap 4**, pipeline augmentasi secara matematis mensimulasikan:
1. **Yaw Rotation & Perspective Distortion**: Mensimulasikan kepala menoleh ke kanan dan ke kiri sudut hingga 25 derajat.
2. **Pitch Tilt & Vertical Shear**: Mensimulasikan kepala mendongak ke atas dan menunduk ke bawah.
3. **Random Resized Crop (Scale 0.45 – 1.15)**: Mensimulasikan pemotretan jarak makro (zoom dekat pada pori/jerawat) maupun pemotretan wajah utuh dari jarak jauh.
4. **Color & Lighting Jitter**: Mensimulasikan perbedaan lampu ruangan (lampu putih terang, lampu kuning hangat, kondisi redup).
5. **Gaussian Blur**: Mensimulasikan sedikit getaran tangan pengguna saat pemindaian berlangsung.

Hasilnya: Model AI yang dilatih tidak kaku dan tetap akurat mengenali kondisi kulit meskipun sudut wajah atau jarak kamera pengguna berubah-ubah!

---

## 6. Proses Training & Fitur Resume Checkpoint

1. Jalankan **Tahap 5** untuk membuat arsitektur Neural Network.
2. Jalankan **Tahap 6** untuk memulai pelatihan model.
3. **Fitur Resume**:
   - Setiap epoch, bobot terbaru disimpan ke `/content/skincheck_data/checkpoints/checkpoint_latest.pth`.
   - Jika suatu saat hasil pelatihan dirasa belum maksimal, Anda tidak perlu mengulang dari awal:
     1. Cari dataset baru di Kaggle dan download (Tahap 2).
     2. Bersihkan dan timbun (Tahap 3).
     3. Jalankan kembali **Tahap 6** dengan `RESUME_TRAINING = True`. Model akan otomatis melanjutkan pelatihan dari titik epoch terakhir!

---

## 7. Pengujian Interaktif di Colab

Di **Tahap 7**, Anda dapat langsung menguji performa model:
- Anda dapat memilih sampel dari dataset atau mengunggah foto wajah Anda sendiri.
- Output visual akan menampilkan:
  - Foto scan wajah.
  - Grafik batang skor kondisi kulit (0–100%).
  - Estimasi indikator non-medis yang rapi dan elegan.

---

## 8. Ekspor & Pasang Model ke Antigravity

Setelah Anda puas dengan hasil pelatihan:
1. Jalankan **Tahap 8** di Colab.
2. Colab akan otomatis mengompres model dan men-download file:
   📁 **`skincheck_ai_exported.zip`**
3. Buka file ZIP tersebut di komputer Anda, Anda akan menemukan 3 file:
   - `skincheck_model.onnx`
   - `skincheck_model.pt`
   - `model_metadata.json`
4. **Pindahkan ketiga file tersebut ke folder:**
   👉 `c:\xampp\htdocs\Skin Check\storage\models\`
5. Selesai! Web SkinCheck di Antigravity akan otomatis mendeteksi file model tersebut melalui [`OnnxSkinAnalysisProvider.php`](file:///c:/xampp/htdocs/Skin%20Check/app/services/OnnxSkinAnalysisProvider.php) dan menjalankan inferensi Deep Learning mutakhir untuk setiap customer yang melakukan face scan!
