# Model Storage Directory

Tempatkan file model AI hasil ekspor dari Google Colab di folder ini:
1. `skincheck_model.onnx` (File model utama untuk inferensi cepat)
2. `skincheck_model.pt` (TorchScript checkpoint)
3. `model_metadata.json` (Konfigurasi parameter normalisasi & nama indikator)

File-file ini akan otomatis didownload dalam bentuk `skincheck_ai_exported.zip` saat Anda menjalankan Tahap 8 di notebook Colab (`SkinCheck_AI_Trainer.ipynb`). Cukup ekstrak isi zip ke folder ini!
