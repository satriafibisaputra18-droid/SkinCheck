<?php require APPROOT . '/views/templates/admin_header.php'; ?>

<!-- Clean Styles for Admin Settings Console -->
<style>
.sc-settings-card {
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.sc-settings-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
}

.sc-section-label {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #64748b;
    margin-bottom: 0.6rem;
}

.sc-card-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 0.15rem;
}

.sc-card-subtitle {
    font-size: 0.78rem;
    color: #64748b;
    margin-bottom: 0;
}

.sc-secret-input-wrap {
    position: relative;
}

.sc-secret-toggle-btn {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    color: #94a3b8;
    padding: 4px;
    cursor: pointer;
    font-size: 0.85rem;
    line-height: 1;
    transition: color 0.15s ease;
}

.sc-secret-toggle-btn:hover {
    color: #334155;
}

.sc-test-result-box {
    font-size: 0.82rem;
    border-radius: 6px;
    padding: 0.55rem 0.8rem;
    display: none;
    transition: all 0.2s ease;
}

.sc-test-btn {
    font-size: 0.78rem;
    padding: 0.28rem 0.75rem;
    border-radius: 6px;
    font-weight: 500;
}
</style>

<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 bg-white p-3 p-md-4 rounded-3 border border-secondary border-opacity-10 mb-4 shadow-sm">
    <div>
        <h1 class="h5 fw-bold mb-1 text-dark">Pengaturan Sistem & Kunci API</h1>
        <p class="text-muted small mb-0">Kelola kredensial API dan parameter aplikasi.</p>
    </div>
    
    <div>
        <a href="<?php echo URLROOT; ?>/admin/dashboard" class="btn btn-outline-secondary btn-sm px-3 rounded-2">
            Kembali ke Dashboard
        </a>
    </div>
</div>

<?php
// Extract settings for clean direct access
$aiSettings = [];
if (!empty($groupedSettings['ai'])) {
    foreach ($groupedSettings['ai'] as $item) {
        $aiSettings[$item->setting_key] = $item;
    }
}

$nvidiaSettings = [];
if (!empty($groupedSettings['nvidia'])) {
    foreach ($groupedSettings['nvidia'] as $item) {
        $nvidiaSettings[$item->setting_key] = $item;
    }
}

$emailSettings = [];
if (!empty($groupedSettings['email'])) {
    foreach ($groupedSettings['email'] as $item) {
        $emailSettings[$item->setting_key] = $item;
    }
}

$generalSettings = [];
if (!empty($groupedSettings['general'])) {
    foreach ($groupedSettings['general'] as $item) {
        $generalSettings[$item->setting_key] = $item;
    }
}

$telegramSettings = [];
if (!empty($groupedSettings['telegram'])) {
    foreach ($groupedSettings['telegram'] as $item) {
        $telegramSettings[$item->setting_key] = $item;
    }
}

$fonnteSettings = [];
if (!empty($groupedSettings['fonnte'])) {
    foreach ($groupedSettings['fonnte'] as $item) {
        $fonnteSettings[$item->setting_key] = $item;
    }
}
$fonnteToken = $fonnteSettings['fonnte_token']->setting_value ?? Setting::getVal('fonnte_token', 'mu2K7LxWSs8KNRLBqJ3v');

// Gemini values
$geminiApiKey = $aiSettings['gemini_api_key']->setting_value ?? '';
$geminiPrimaryModel = $aiSettings['gemini_primary_model']->setting_value ?? 'gemini-flash-lite-latest';
$geminiFallbackModels = $aiSettings['gemini_fallback_models']->setting_value ?? 'gemini-3.8-flash,gemini-3.5-flash,gemini-flash-latest';
$geminiTemp = $aiSettings['gemini_temperature']->setting_value ?? '0.7';

// NVIDIA values
$nvidiaApiKey = $nvidiaSettings['nvidia_api_key']->setting_value ?? '';
$nvidiaModel = $nvidiaSettings['nvidia_model']->setting_value ?? 'nvidia/nemotron-3.5-bolt-e4b';
$nvidiaFallback = $nvidiaSettings['nvidia_fallback_models']->setting_value ?? 'poolside/laguna-xs-2.1,nvidia/ising-calibration-1.5-31b';

// Priority
$aiProvider = Setting::getVal('ai_provider', 'gemini');
?>

<!-- Main Settings Form -->
<form action="<?php echo URLROOT; ?>/admin/settings" method="POST" id="scSettingsForm">

    <!-- 1. BAGIAN: MESIN KECERDASAN BUATAN (AI ENGINE & FALLBACK) -->
    <div class="mb-4">
        <div class="sc-section-label">Mesin Kecerdasan Buatan (AI Engine & Fallback)</div>

        <!-- Provider Priority Bar -->
        <div class="card sc-settings-card p-3 mb-3">
            <div class="row align-items-center g-2">
                <div class="col-md-5">
                    <label for="setting_ai_provider" class="form-label small fw-semibold text-dark mb-0">
                        Prioritas Penyedia AI
                    </label>
                    <div class="text-muted small" style="font-size: 0.76rem;">
                        Penyedia cadangan akan aktif otomatis jika model utama mengalami batas limit atau gangguan.
                    </div>
                </div>
                <div class="col-md-7">
                    <select name="settings[ai_provider]" id="setting_ai_provider" class="form-select form-select-sm">
                        <option value="gemini" <?php echo ($aiProvider === 'gemini') ? 'selected' : ''; ?>>
                            Google Gemini (Utama) → NVIDIA NIM (Cadangan)
                        </option>
                        <option value="nvidia" <?php echo ($aiProvider === 'nvidia') ? 'selected' : ''; ?>>
                            NVIDIA NIM (Utama) → Google Gemini (Cadangan)
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <!-- AI Cards Grid -->
        <div class="row g-3">
            
            <!-- Google Gemini Card -->
            <div class="col-lg-6">
                <div class="card sc-settings-card h-100 p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                        <div>
                            <div class="sc-card-title">Google Gemini</div>
                            <p class="sc-card-subtitle">Layanan utama konsultasi klinis SkinCheck</p>
                        </div>
                        <button type="button" class="btn btn-outline-secondary sc-test-btn" id="btnTestGemini" onclick="testApiKey('gemini', 'setting_gemini_api_key', 'resGeminiTest', 'setting_gemini_primary_model')">
                            Uji Koneksi
                        </button>
                    </div>

                    <div id="resGeminiTest" class="sc-test-result-box mb-3"></div>

                    <div class="row g-3">
                        <!-- API Key -->
                        <div class="col-12">
                            <label for="setting_gemini_api_key" class="form-label small fw-medium text-dark mb-1">
                                API Key
                            </label>
                            <div class="sc-secret-input-wrap">
                                <input type="password" 
                                       name="settings[gemini_api_key]" 
                                       id="setting_gemini_api_key" 
                                       class="form-control form-control-sm pe-5" 
                                       value="<?php echo htmlspecialchars($geminiApiKey); ?>"
                                       placeholder="AIzaSy..."
                                       required>
                                <button type="button" class="sc-secret-toggle-btn" onclick="toggleSecretVisibility('setting_gemini_api_key', this)" title="Tampilkan/Sembunyikan">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Primary Model -->
                        <div class="col-12">
                            <label for="scPrimaryModelSelect" class="form-label small fw-medium text-dark mb-1">
                                Model Utama
                            </label>
                            <select id="scPrimaryModelSelect" class="form-select form-select-sm mb-2" onchange="onPrimaryModelChange(this.value)">
                                <option value="gemini-flash-lite-latest" <?php echo ($geminiPrimaryModel === 'gemini-flash-lite-latest') ? 'selected' : ''; ?>>
                                    gemini-flash-lite-latest (Default)
                                </option>
                                <option value="gemini-3.8-flash" <?php echo ($geminiPrimaryModel === 'gemini-3.8-flash') ? 'selected' : ''; ?>>
                                    gemini-3.8-flash
                                </option>
                                <option value="gemini-3.5-flash" <?php echo ($geminiPrimaryModel === 'gemini-3.5-flash') ? 'selected' : ''; ?>>
                                    gemini-3.5-flash
                                </option>
                                <option value="gemini-flash-latest" <?php echo ($geminiPrimaryModel === 'gemini-flash-latest') ? 'selected' : ''; ?>>
                                    gemini-flash-latest
                                </option>
                                <option value="gemini-3.5-flash-lite" <?php echo ($geminiPrimaryModel === 'gemini-3.5-flash-lite') ? 'selected' : ''; ?>>
                                    gemini-3.5-flash-lite
                                </option>
                                <option value="gemini-2.5-flash" <?php echo ($geminiPrimaryModel === 'gemini-2.5-flash') ? 'selected' : ''; ?>>
                                    gemini-2.5-flash
                                </option>
                                <option value="gemini-2.5-pro" <?php echo ($geminiPrimaryModel === 'gemini-2.5-pro') ? 'selected' : ''; ?>>
                                    gemini-2.5-pro
                                </option>
                                <option value="custom" <?php echo (!in_array($geminiPrimaryModel, ['gemini-flash-lite-latest','gemini-3.8-flash','gemini-3.5-flash','gemini-flash-latest','gemini-3.5-flash-lite','gemini-2.5-flash','gemini-2.5-pro'])) ? 'selected' : ''; ?>>
                                    Kustom...
                                </option>
                            </select>
                            <input type="text" 
                                   name="settings[gemini_primary_model]" 
                                   id="setting_gemini_primary_model" 
                                   class="form-control form-control-sm" 
                                   value="<?php echo htmlspecialchars($geminiPrimaryModel); ?>"
                                   placeholder="gemini-flash-lite-latest"
                                   required>
                        </div>

                        <!-- Fallback Models Chain -->
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label for="setting_gemini_fallback_models" class="form-label small fw-medium text-dark mb-0">
                                    Model Cadangan (Fallback Chain)
                                </label>
                                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none small text-muted" onclick="applyRecommendedFallback()" style="font-size: 0.75rem;">
                                    Reset Default
                                </button>
                            </div>
                            <input type="text" 
                                   name="settings[gemini_fallback_models]" 
                                   id="setting_gemini_fallback_models" 
                                   class="form-control form-control-sm" 
                                   value="<?php echo htmlspecialchars($geminiFallbackModels); ?>"
                                   placeholder="gemini-3.8-flash,gemini-3.5-flash"
                                   required>
                        </div>

                        <!-- Temperature -->
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label for="setting_gemini_temperature" class="form-label small fw-medium text-dark mb-0">
                                    Temperature
                                </label>
                                <span id="tempValueBadge" class="badge bg-light text-dark border small"><?php echo htmlspecialchars($geminiTemp); ?></span>
                            </div>
                            <input type="range" 
                                   class="form-range" 
                                   id="tempRangeSlider" 
                                   min="0.0" 
                                   max="1.0" 
                                   step="0.05" 
                                   value="<?php echo htmlspecialchars($geminiTemp); ?>"
                                   oninput="onTempSliderChange(this.value)">
                            <input type="hidden" 
                                   name="settings[gemini_temperature]" 
                                   id="setting_gemini_temperature" 
                                   value="<?php echo htmlspecialchars($geminiTemp); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- NVIDIA NIM Card -->
            <div class="col-lg-6">
                <div class="card sc-settings-card h-100 p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                        <div>
                            <div class="sc-card-title">NVIDIA NIM</div>
                            <p class="sc-card-subtitle">Layanan AI cadangan saat kuota Gemini tercapai</p>
                        </div>
                        <button type="button" class="btn btn-outline-secondary sc-test-btn" id="btnTestNvidia" onclick="testApiKey('nvidia', 'setting_nvidia_api_key', 'resNvidiaTest', 'setting_nvidia_model')">
                            Uji Koneksi
                        </button>
                    </div>

                    <div id="resNvidiaTest" class="sc-test-result-box mb-3"></div>

                    <div class="row g-3">
                        <!-- API Key -->
                        <div class="col-12">
                            <label for="setting_nvidia_api_key" class="form-label small fw-medium text-dark mb-1">
                                API Key
                            </label>
                            <div class="sc-secret-input-wrap">
                                <input type="password" 
                                       name="settings[nvidia_api_key]" 
                                       id="setting_nvidia_api_key" 
                                       class="form-control form-control-sm pe-5" 
                                       value="<?php echo htmlspecialchars($nvidiaApiKey); ?>"
                                       placeholder="nvapi-..."
                                       required>
                                <button type="button" class="sc-secret-toggle-btn" onclick="toggleSecretVisibility('setting_nvidia_api_key', this)" title="Tampilkan/Sembunyikan">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Primary Model -->
                        <div class="col-12">
                            <label for="scNvidiaModelSelect" class="form-label small fw-medium text-dark mb-1">
                                Model Utama
                            </label>
                            <select id="scNvidiaModelSelect" class="form-select form-select-sm mb-2" onchange="onNvidiaModelChange(this.value)">
                                <option value="nvidia/nemotron-3.5-bolt-e4b" <?php echo ($nvidiaModel === 'nvidia/nemotron-3.5-bolt-e4b') ? 'selected' : ''; ?>>
                                    nvidia/nemotron-3.5-bolt-e4b (Direkomendasikan)
                                </option>
                                <option value="poolside/laguna-xs-2.1" <?php echo ($nvidiaModel === 'poolside/laguna-xs-2.1') ? 'selected' : ''; ?>>
                                    poolside/laguna-xs-2.1
                                </option>
                                <option value="nvidia/ising-calibration-1.5-31b" <?php echo ($nvidiaModel === 'nvidia/ising-calibration-1.5-31b') ? 'selected' : ''; ?>>
                                    nvidia/ising-calibration-1.5-31b
                                </option>
                                <option value="custom" <?php echo (!in_array($nvidiaModel, ['nvidia/nemotron-3.5-bolt-e4b','poolside/laguna-xs-2.1','nvidia/ising-calibration-1.5-31b'])) ? 'selected' : ''; ?>>
                                    Kustom...
                                </option>
                            </select>
                            <input type="text" 
                                   name="settings[nvidia_model]" 
                                   id="setting_nvidia_model" 
                                   class="form-control form-control-sm" 
                                   value="<?php echo htmlspecialchars($nvidiaModel); ?>"
                                   placeholder="nvidia/nemotron-3.5-bolt-e4b"
                                   required>
                        </div>

                        <!-- Fallback Models Chain -->
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label for="setting_nvidia_fallback_models" class="form-label small fw-medium text-dark mb-0">
                                    Model Cadangan (Fallback Chain)
                                </label>
                                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none small text-muted" onclick="applyRecommendedNvidiaFallback()" style="font-size: 0.75rem;">
                                    Reset Default
                                </button>
                            </div>
                            <input type="text" 
                                   name="settings[nvidia_fallback_models]" 
                                   id="setting_nvidia_fallback_models" 
                                   class="form-control form-control-sm" 
                                   value="<?php echo htmlspecialchars($nvidiaFallback); ?>"
                                   placeholder="poolside/laguna-xs-2.1,nvidia/ising-calibration-1.5-31b"
                                   required>
                        </div>

                        <!-- Note Info -->
                        <div class="col-12">
                            <div class="p-2 rounded bg-light border small text-muted" style="font-size: 0.78rem;">
                                Model NVIDIA menggunakan protokol standar OpenAI dan siap menggantikan Gemini secara otomatis saat kuota harian habis.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- 2. BAGIAN: LAYANAN KOMUNIKASI (WHATSAPP GATEWAY - FONNTE) -->
    <div class="mb-4">
        <div class="sc-section-label">Layanan Komunikasi & Verifikasi OTP</div>

        <div class="row g-3">
            
            <!-- Fonnte WhatsApp Gateway Card -->
            <div class="col-12">
                <div class="card sc-settings-card p-3 p-md-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pb-3 mb-3 border-bottom">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 44px; height: 44px; border-radius: 12px; background: #ecfdf5; border: 1px solid #a7f3d0; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                                <i class="fa-brands fa-whatsapp"></i>
                            </div>
                            <div>
                                <div class="sc-card-title d-flex align-items-center gap-2">
                                    <span>Fonnte WhatsApp Gateway</span>
                                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle small fw-semibold" style="font-size: 0.72rem;">
                                        <i class="fa-solid fa-circle-check me-1"></i> Aktif sebagai Mesin OTP
                                    </span>
                                </div>
                                <p class="sc-card-subtitle mb-0">Pengiriman kode OTP 6-digit verifikasi akun pelanggan secara instan via WhatsApp.</p>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-secondary sc-test-btn" id="btnTestFonnte" onclick="testApiKey('fonnte', 'setting_fonnte_token', 'resFonnteTest')">
                            <i class="fa-solid fa-tower-broadcast me-1"></i> Uji Koneksi
                        </button>
                    </div>

                    <div id="resFonnteTest" class="sc-test-result-box mb-3"></div>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="setting_fonnte_token" class="form-label small fw-medium text-dark mb-1">
                                Token API Fonnte
                            </label>
                            <div class="sc-secret-input-wrap">
                                <input type="password" 
                                       name="settings[fonnte_token]" 
                                       id="setting_fonnte_token" 
                                       class="form-control form-control-sm pe-5" 
                                       value="<?php echo htmlspecialchars($fonnteToken); ?>"
                                       placeholder="Masukkan token akun Fonnte..."
                                       required>
                                <button type="button" class="sc-secret-toggle-btn" onclick="toggleSecretVisibility('setting_fonnte_token', this)" title="Tampilkan/Sembunyikan">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted mt-1 d-block" style="font-size: 0.75rem;">
                                <i class="fa-solid fa-circle-info text-primary me-1"></i> Token ini digunakan untuk mengautentikasi pengiriman pesan via <code>https://api.fonnte.com/send</code>.
                            </small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-medium text-dark mb-1">
                                Status Integrasi
                            </label>
                            <div class="p-2 rounded-2 border bg-light small d-flex flex-column gap-1">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Metode OTP:</span>
                                    <strong class="text-success">WhatsApp</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Target Nomor:</span>
                                    <span class="font-monospace text-dark">08... / 628...</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Status Engine:</span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle py-0" style="font-size:0.7rem;">Siap Pakai</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- 3. BAGIAN: SISTEM & KEAMANAN OTP -->
    <div class="mb-4">
        <div class="sc-section-label">Parameter Aplikasi & Keamanan OTP</div>

        <div class="card sc-settings-card p-3 p-md-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="setting_site_name" class="form-label small fw-medium text-dark mb-1">
                        Nama Aplikasi / Brand
                    </label>
                    <input type="text" 
                           name="settings[site_name]" 
                           id="setting_site_name" 
                           class="form-control form-control-sm" 
                           value="<?php echo htmlspecialchars($generalSettings['site_name']->setting_value ?? 'SkinCheck'); ?>"
                           required>
                </div>

                <div class="col-md-4">
                    <label for="setting_otp_expiry_minutes" class="form-label small fw-medium text-dark mb-1">
                        Masa Berlaku OTP (Menit)
                    </label>
                    <input type="number" 
                           min="1" 
                           name="settings[otp_expiry_minutes]" 
                           id="setting_otp_expiry_minutes" 
                           class="form-control form-control-sm" 
                           value="<?php echo htmlspecialchars($generalSettings['otp_expiry_minutes']->setting_value ?? '5'); ?>"
                           required>
                </div>

                <div class="col-md-4">
                    <label for="setting_otp_resend_cooldown" class="form-label small fw-medium text-dark mb-1">
                        Cooldown Kirim Ulang OTP (Detik)
                    </label>
                    <input type="number" 
                           min="1" 
                           name="settings[otp_resend_cooldown]" 
                           id="setting_otp_resend_cooldown" 
                           class="form-control form-control-sm" 
                           value="<?php echo htmlspecialchars($generalSettings['otp_resend_cooldown']->setting_value ?? '60'); ?>"
                           required>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="card sc-settings-card p-3 mb-5">
        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
            <div class="small text-muted">
                Perubahan konfigurasi tersimpan langsung ke database sistem.
            </div>
            <div class="d-flex gap-2">
                <button type="reset" class="btn btn-outline-secondary btn-sm px-3 rounded-2">
                    Reset
                </button>
                <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold rounded-2 shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>

</form>

<!-- Scripts for Dynamic Selects and Testing -->
<script>
// Toggle Password/Secret Visibility
function toggleSecretVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;

    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Change Primary Gemini Model from Select
function onPrimaryModelChange(val) {
    const input = document.getElementById('setting_gemini_primary_model');
    if (!input) return;
    if (val !== 'custom') {
        input.value = val;
    } else {
        input.value = '';
        input.focus();
    }
}

// Reset Gemini fallback to recommended default
function applyRecommendedFallback() {
    const input = document.getElementById('setting_gemini_fallback_models');
    if (input) {
        input.value = 'gemini-3.8-flash,gemini-3.5-flash,gemini-flash-latest';
    }
}

// Change NVIDIA Model from Select
function onNvidiaModelChange(val) {
    const input = document.getElementById('setting_nvidia_model');
    if (!input) return;
    if (val !== 'custom') {
        input.value = val;
    } else {
        input.value = '';
        input.focus();
    }
}

// Reset NVIDIA fallback to recommended default
function applyRecommendedNvidiaFallback() {
    const input = document.getElementById('setting_nvidia_fallback_models');
    if (input) {
        input.value = 'poolside/laguna-xs-2.1,nvidia/ising-calibration-1.5-31b';
    }
}

// Temperature slider change handler
function onTempSliderChange(val) {
    const badge = document.getElementById('tempValueBadge');
    const hidden = document.getElementById('setting_gemini_temperature');
    if (badge) badge.textContent = val;
    if (hidden) hidden.value = val;
}

// AJAX Tester for API Keys
function testApiKey(service, keyInputId, resultBoxId, extraInputId = null) {
    const keyInput = document.getElementById(keyInputId);
    const resultBox = document.getElementById(resultBoxId);
    if (!keyInput || !resultBox) return;

    const keyVal = keyInput.value.trim();
    if (!keyVal) {
        showResultBox(resultBox, false, 'Kunci API / Token tidak boleh kosong.');
        return;
    }

    let extraVal = '';
    if (extraInputId) {
        const extraInput = document.getElementById(extraInputId);
        if (extraInput) extraVal = extraInput.value.trim();
    }

    // Loading State
    resultBox.style.display = 'block';
    resultBox.className = 'sc-test-result-box mb-3 bg-light text-muted border';
    resultBox.innerHTML = 'Menghubungi endpoint ' + service.toUpperCase() + '...';

    let loadToast = null;
    if (window.toast && typeof window.toast.loading === 'function') {
        loadToast = window.toast.loading('Menguji koneksi ' + service.toUpperCase() + '...', {
            description: 'Memeriksa validitas API Key dan latensi respon.'
        });
    }

    const formData = new FormData();
    formData.append('service', service);
    formData.append('key', keyVal);
    if (extraVal) formData.append('extra', extraVal);

    fetch('<?php echo URLROOT; ?>/admin/testApiKey', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (loadToast && typeof loadToast.dismiss === 'function') loadToast.dismiss();
        showResultBox(resultBox, data.success, data.message);
        if (window.toast) {
            if (data.success) {
                window.toast.success('Koneksi ' + service.toUpperCase() + ' Berhasil!', {
                    description: data.message
                });
            } else {
                window.toast.error('Uji Koneksi ' + service.toUpperCase() + ' Gagal', {
                    description: data.message
                });
            }
        }
    })
    .catch(err => {
        if (loadToast && typeof loadToast.dismiss === 'function') loadToast.dismiss();
        showResultBox(resultBox, false, 'Gagal terhubung: ' + err.message);
        if (window.toast) {
            window.toast.error('Gagal Menguji Koneksi', {
                description: err.message
            });
        }
    });
}

function showResultBox(box, isSuccess, msg) {
    box.style.display = 'block';
    if (isSuccess) {
        box.className = 'sc-test-result-box mb-3 bg-success-subtle text-success border border-success border-opacity-25';
        box.textContent = msg;
    } else {
        box.className = 'sc-test-result-box mb-3 bg-danger-subtle text-danger border border-danger border-opacity-25';
        box.textContent = msg;
    }
}
</script>

<?php require APPROOT . '/views/templates/admin_footer.php'; ?>
