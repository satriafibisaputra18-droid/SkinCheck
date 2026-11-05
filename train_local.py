"""
SkinCheck AI Advanced Local Trainer - Multi-Tone & High-Diversity Edition
Melatih model MobileNetV3 dengan 2,500 sampel beraneka ragam (Fitzpatrick Skin Tones I-VI)
dan variasi realistis (acne, redness, hyperpigmentation, kehalusan tekstur kulit).
"""
import sys
import io
if sys.platform == "win32":
    try:
        sys.stdout.reconfigure(encoding='utf-8')
        sys.stderr.reconfigure(encoding='utf-8')
    except Exception:
        pass

import json
import time
import shutil
import random
from pathlib import Path
import numpy as np
import cv2
from PIL import Image

import torch
import torch.nn as nn
import torchvision.models as models
import torchvision.transforms as T
from torch.utils.data import Dataset, DataLoader, random_split
from torch.optim.lr_scheduler import CosineAnnealingLR

print("=" * 65)
print("  SkinCheck AI Advanced Trainer - 2,500 Diverse Dataset")
print("=" * 65)
print(f"  PyTorch : {torch.__version__}")
print(f"  Device  : CPU (Backbone Frozen, Multi-Head Fine-Tuning)")
print("=" * 65)

BASE = Path(__file__).resolve().parent
DS_DIR = BASE / "temp_training_data"
CKPT = BASE / "temp_ckpt"
OUT_DIR = BASE / "storage" / "models"

shutil.rmtree(DS_DIR, ignore_errors=True)
shutil.rmtree(CKPT, ignore_errors=True)
DS_DIR.mkdir(parents=True, exist_ok=True)
CKPT.mkdir(parents=True, exist_ok=True)
OUT_DIR.mkdir(parents=True, exist_ok=True)

# ── Fitzpatrick Skin Tone Baselines ──
SKIN_TONES = [
    (235, 198, 178), # Type I: Fair / Pale
    (222, 182, 155), # Type II: Light
    (198, 158, 130), # Type III: Medium / Asian
    (168, 130, 102), # Type IV: Olive
    (125,  88,  65), # Type V: Brown
    ( 88,  58,  42), # Type VI: Deep Dark
]

CATEGORIES = {
    "clear_healthy": 500,
    "acne_prone": 500,
    "sensitive_redness": 500,
    "hyperpigmentation": 500,
    "textured_rough": 500
}

print("\n[1/5] Menghasilkan 2,500 citra sintetik beraneka warna kulit & kondisi...")
t_gen_start = time.time()

samples_meta = []

for cat_name, count in CATEGORIES.items():
    cat_dir = DS_DIR / cat_name
    cat_dir.mkdir(parents=True, exist_ok=True)
    
    for i in range(count):
        # Pilih skin tone acak dengan variasi
        base_tone = np.array(random.choice(SKIN_TONES), dtype=np.float32)
        tone_jitter = np.random.uniform(-12, 12, size=3)
        tone = np.clip(base_tone + tone_jitter, 15, 255)
        
        # Buat canvas gradien lembut (seperti kontur wajah)
        canvas = np.zeros((224, 224, 3), dtype=np.float32)
        y, x = np.mgrid[0:224, 0:224]
        cx, cy = 112 + random.randint(-20, 20), 112 + random.randint(-20, 20)
        dist = np.sqrt((x - cx)**2 + (y - cy)**2) / 150.0
        gradient = 1.0 - np.clip(dist * 0.25, 0, 0.3)
        for c in range(3):
            canvas[:, :, c] = tone[c] * gradient

        # Default ground truth (Acne, Redness, Hyperpigmentation, Smoothness)
        # Indikator 4 adalah KEHALUSAN TEKSTUR (0 = sangat kasar, 100 = sangat mulus)
        acne_val = np.random.uniform(2, 12)
        redness_val = np.random.uniform(4, 15)
        hyper_val = np.random.uniform(2, 12)
        smoothness_val = np.random.uniform(82, 96) # Default kulit halus

        if cat_name == "clear_healthy":
            # Kulit sangat sehat, pori halus minimal noise
            noise = np.random.normal(0, 3, (224, 224, 3))
            canvas = np.clip(canvas + noise, 0, 255)
            # Nilai tetap rendah untuk masalah, tinggi untuk kehalusan

        elif cat_name == "acne_prone":
            num_acne = random.randint(8, 25)
            severity = np.random.uniform(0.5, 1.0)
            acne_val = np.clip(35 + num_acne * 2.2 + np.random.uniform(-5, 8), 45, 95)
            redness_val = np.clip(25 + num_acne * 1.5 + np.random.uniform(-5, 5), 30, 75)
            smoothness_val = np.clip(75 - num_acne * 1.8 + np.random.uniform(-6, 6), 25, 55)

            for _ in range(num_acne):
                px = random.randint(15, 208)
                py = random.randint(15, 208)
                r = random.randint(3, 8)
                # Bintik merah jerawat
                acne_color = [np.clip(tone[0] * 1.35 + 50, 140, 245),
                              np.clip(tone[1] * 0.45, 20, 110),
                              np.clip(tone[2] * 0.45, 20, 110)]
                cv2.circle(canvas, (px, py), r, acne_color, -1)
                # Pustula/inti putih di tengah sebagian jerawat
                if random.random() > 0.4:
                    cv2.circle(canvas, (px, py), max(1, r // 3), [240, 230, 215], -1)

            canvas = cv2.GaussianBlur(canvas, (3, 3), 0.8)

        elif cat_name == "sensitive_redness":
            red_intensity = np.random.uniform(0.5, 1.0)
            redness_val = np.clip(55 + red_intensity * 38 + np.random.uniform(-6, 6), 55, 95)
            smoothness_val = np.clip(78 - red_intensity * 25, 45, 75)
            # Patch kemerahan difus
            num_patches = random.randint(3, 7)
            for _ in range(num_patches):
                px = random.randint(30, 194)
                py = random.randint(30, 194)
                axes = (random.randint(25, 60), random.randint(20, 45))
                angle = random.randint(0, 180)
                red_tint = [np.clip(tone[0] * 1.3 + 40, 120, 240),
                            np.clip(tone[1] * 0.65, 20, 130),
                            np.clip(tone[2] * 0.65, 20, 130)]
                mask = np.zeros((224, 224), dtype=np.uint8)
                cv2.ellipse(mask, (px, py), axes, angle, 0, 360, 255, -1)
                mask = cv2.GaussianBlur(mask, (31, 31), 11) / 255.0
                for c in range(3):
                    canvas[:, :, c] = canvas[:, :, c] * (1 - mask * 0.5) + red_tint[c] * (mask * 0.5)

        elif cat_name == "hyperpigmentation":
            num_spots = random.randint(12, 35)
            hyper_val = np.clip(45 + num_spots * 1.4 + np.random.uniform(-5, 6), 50, 94)
            smoothness_val = np.clip(80 - num_spots * 0.6, 50, 78)
            for _ in range(num_spots):
                px = random.randint(20, 204)
                py = random.randint(20, 204)
                r = random.randint(2, 6)
                # Bintik melanin gelap
                dark_color = [np.clip(tone[0] * 0.55, 20, 110),
                              np.clip(tone[1] * 0.48, 15, 95),
                              np.clip(tone[2] * 0.42, 10, 85)]
                cv2.circle(canvas, (px, py), r, dark_color, -1)
            canvas = cv2.GaussianBlur(canvas, (3, 3), 0.6)

        elif cat_name == "textured_rough":
            rough_level = np.random.uniform(0.6, 1.0)
            smoothness_val = np.clip(18 + (1.0 - rough_level) * 30 + np.random.uniform(-5, 5), 15, 48)
            acne_val = np.random.uniform(10, 30)
            # Pori-pori dan tekstur kasar frekuensi tinggi
            num_pores = random.randint(40, 90)
            for _ in range(num_pores):
                px = random.randint(10, 214)
                py = random.randint(10, 214)
                depth = random.randint(-45, -15)
                canvas[px:px+2, py:py+2] = np.clip(canvas[px:px+2, py:py+2] + depth, 0, 255)
            # High frequency speckle noise
            spk = np.random.normal(0, 18 * rough_level, (224, 224, 3))
            canvas = np.clip(canvas + spk, 0, 255)

        # Simpan citra & label target
        img_arr = np.clip(canvas, 0, 255).astype(np.uint8)
        img_path = cat_dir / f"{cat_name}_{i:04d}.jpg"
        Image.fromarray(img_arr, mode="RGB").save(str(img_path), quality=92)
        
        target = [
            float(np.clip(acne_val, 0, 100)),
            float(np.clip(redness_val, 0, 100)),
            float(np.clip(hyper_val, 0, 100)),
            float(np.clip(smoothness_val, 0, 100))
        ]
        samples_meta.append((img_path, target))

print(f"    Selesai membuat {len(samples_meta)} citra dalam {time.time()-t_gen_start:.1f} detik.")

# ── PyTorch Dataset & Transforms ──
train_tf = T.Compose([
    T.RandomResizedCrop(224, scale=(0.85, 1.0)),
    T.RandomHorizontalFlip(),
    T.ColorJitter(brightness=0.15, contrast=0.15, saturation=0.15),
    T.ToTensor(),
    T.Normalize([0.485, 0.456, 0.406], [0.229, 0.224, 0.225]),
])

val_tf = T.Compose([
    T.Resize((224, 224)),
    T.ToTensor(),
    T.Normalize([0.485, 0.456, 0.406], [0.229, 0.224, 0.225]),
])

class SkinCheckMultiToneDS(Dataset):
    def __init__(self, data_list, tf):
        self.data = data_list
        self.tf = tf
    def __len__(self):
        return len(self.data)
    def __getitem__(self, idx):
        path, target = self.data[idx]
        img = Image.open(path).convert("RGB")
        return self.tf(img), torch.tensor(target, dtype=torch.float32)

random.seed(42)
random.shuffle(samples_meta)
n_val = int(len(samples_meta) * 0.15)
train_samples = samples_meta[:-n_val]
val_samples = samples_meta[-n_val:]

train_ds = SkinCheckMultiToneDS(train_samples, train_tf)
val_ds = SkinCheckMultiToneDS(val_samples, val_tf)

BATCH_SIZE = 32
train_loader = DataLoader(train_ds, batch_size=BATCH_SIZE, shuffle=True, drop_last=True)
val_loader = DataLoader(val_ds, batch_size=BATCH_SIZE, shuffle=False)

print(f"\n[2/5] Partisi Dataset: {len(train_ds)} Train, {len(val_ds)} Validation")

# ── Model Definition ──
print("\n[3/5] Menginisialisasi MobileNetV3-Large Multi-Task Network...")
class SkinCheckNet(nn.Module):
    def __init__(self):
        super().__init__()
        base = models.mobilenet_v3_large(weights=models.MobileNet_V3_Large_Weights.IMAGENET1K_V2)
        self.backbone = base.features
        # Bekukan backbone agar visual features ImageNet terjaga dan komputasi CPU super cepat
        for p in self.backbone.parameters():
            p.requires_grad = False
            
        self.pool = nn.AdaptiveAvgPool2d((1, 1))
        D = 960
        def make_head(dim_in):
            return nn.Sequential(
                nn.Linear(dim_in, 256),
                nn.LayerNorm(256),
                nn.Hardswish(),
                nn.Dropout(0.2),
                nn.Linear(256, 64),
                nn.ReLU(True),
                nn.Dropout(0.1),
                nn.Linear(64, 1),
                nn.Sigmoid() # Normalisasi 0.0 - 1.0 lalu dikali 100
            )
        self.h_acne = make_head(D)
        self.h_red = make_head(D)
        self.h_hyper = make_head(D)
        self.h_tex = make_head(D) # Kehalusan tekstur

    def forward(self, x):
        feat = torch.flatten(self.pool(self.backbone(x)), 1)
        return torch.cat([
            self.h_acne(feat) * 100.0,
            self.h_red(feat) * 100.0,
            self.h_hyper(feat) * 100.0,
            self.h_tex(feat) * 100.0,
        ], dim=1)

device = torch.device("cpu")
model = SkinCheckNet().to(device)

trainable_params = sum(p.numel() for p in model.parameters() if p.requires_grad)
print(f"    Trainable Head Parameters: {trainable_params:,}")

# ── Training Loop ──
EPOCHS = 14
print(f"\n[4/5] Melatih model {EPOCHS} epoch di CPU...")
loss_fn = nn.SmoothL1Loss(beta=3.0)
optimizer = torch.optim.AdamW(
    [p for p in model.parameters() if p.requires_grad],
    lr=5e-4, weight_decay=1e-3
)
scheduler = CosineAnnealingLR(optimizer, T_max=EPOCHS, eta_min=1e-5)

best_loss = float("inf")
best_ckpt_file = CKPT / "best.pth"
start_time = time.time()

for epoch in range(1, EPOCHS + 1):
    model.train()
    running_train_loss = 0.0
    for imgs, tgts in train_loader:
        imgs, tgts = imgs.to(device), tgts.to(device)
        optimizer.zero_grad(set_to_none=True)
        preds = model(imgs)
        loss = loss_fn(preds, tgts)
        loss.backward()
        nn.utils.clip_grad_norm_(model.parameters(), 1.0)
        optimizer.step()
        running_train_loss += loss.item() * imgs.size(0)
        
    train_loss = running_train_loss / len(train_ds)
    
    model.eval()
    running_val_loss = 0.0
    with torch.no_grad():
        for imgs, tgts in val_loader:
            imgs, tgts = imgs.to(device), tgts.to(device)
            preds = model(imgs)
            loss = loss_fn(preds, tgts)
            running_val_loss += loss.item() * imgs.size(0)
    val_loss = running_val_loss / len(val_ds)
    
    scheduler.step()
    lr_now = scheduler.get_last_lr()[0]
    is_best = val_loss < best_loss
    star = " <-- BEST" if is_best else ""
    print(f"  Epoch [{epoch:02d}/{EPOCHS}] Train={train_loss:.4f}  Val={val_loss:.4f}  LR={lr_now:.2e}{star}")
    
    if is_best:
        best_loss = val_loss
        torch.save({
            "epoch": epoch,
            "val_loss": val_loss,
            "state_dict": model.state_dict()
        }, best_ckpt_file)

print(f"\n    Training tuntas dalam {(time.time()-start_time)/60:.1f} menit | Best Val Loss: {best_loss:.4f}")

# ── Ekspor ONNX Model ──
print("\n[5/5] Mengekspor model terbaik ke format ONNX...")
ckpt_data = torch.load(best_ckpt_file, map_location="cpu")
model.load_state_dict(ckpt_data["state_dict"])
model.eval()

onnx_target = OUT_DIR / "skincheck_model.onnx"
dummy_input = torch.randn(1, 3, 224, 224)

torch.onnx.export(
    model, dummy_input, str(onnx_target),
    export_params=True, opset_version=18, do_constant_folding=True,
    input_names=["input_image"], output_names=["skin_indicators"],
    dynamic_axes={"input_image": {0: "batch"}, "skin_indicators": {0: "batch"}}
)
print(f"    Berkas ONNX berhasil diekspor: {onnx_target} ({onnx_target.stat().st_size / 1024:.1f} KB)")

# Validasi ONNX Checker
import onnx
onnx.checker.check_model(onnx.load(str(onnx_target)))
print("    Validasi ONNX Checker: VALID & LOLOS")

# Uji Inferensi ONNX Runtime
import onnxruntime as ort
session = ort.InferenceSession(str(onnx_target), providers=["CPUExecutionProvider"])
test_in = np.random.randn(1, 3, 224, 224).astype(np.float32)
sample_res = session.run(None, {"input_image": test_in})[0][0]
print(f"    Sample Output (Acne, Red, Hyper, Smoothness): {sample_res}")

# Simpan metadata
meta_info = {
    "model_name": "SkinCheck-MultiTask-Net-v2.1",
    "version": "2.1.0",
    "architecture": "MobileNetV3-Large MultiTask (Fitzpatrick Tones I-VI, 2500 Samples)",
    "epochs_trained": ckpt_data["epoch"],
    "best_val_loss": round(float(best_loss), 6),
    "input_size": [224, 224, 3],
    "input_mean": [0.485, 0.456, 0.406],
    "input_std": [0.229, 0.224, 0.225],
    "output_order": ["acne_score", "redness_score", "hyperpigmentation_score", "texture_score"],
    "indicators": [
        {"name": "acne", "label": "Jerawat", "scale": [0, 100], "direction": "lower_is_better"},
        {"name": "redness", "label": "Kemerahan", "scale": [0, 100], "direction": "lower_is_better"},
        {"name": "hyperpigmentation", "label": "Hyperpigmentasi", "scale": [0, 100], "direction": "lower_is_better"},
        {"name": "texture", "label": "Kehalusan Kulit", "scale": [0, 100], "direction": "higher_is_better"},
    ],
    "non_medical_notice": "Estimated cosmetic visual indicators only. NOT for medical diagnosis."
}

with open(OUT_DIR / "model_metadata.json", "w", encoding="utf-8") as f:
    json.dump(meta_info, f, indent=2)
print("    Berkas model_metadata.json berhasil diperbarui.")

# Bersihkan direktori temporary
shutil.rmtree(DS_DIR, ignore_errors=True)
shutil.rmtree(CKPT, ignore_errors=True)

print("\n" + "=" * 65)
print("  BERHASIL! Model dengan 2,500 sampel baru siap digunakan!")
print("=" * 65)
