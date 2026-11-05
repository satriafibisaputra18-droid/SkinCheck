<?php require APPROOT . '/views/templates/admin_header.php'; ?>

<!-- Custom Styles for Admin Settings Console -->
<style>
.sc-settings-card {
    border-radius: 14px;
    border: 1px solid rgba(0, 0, 0, 0.08);
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
    background: #ffffff;
    transition: all 0.25s ease;
}

.sc-settings-card:hover {
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.07);
}

.sc-card-header-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.sc-secret-input-wrap {
    position: relative;
}

.sc-secret-toggle-btn {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    color: #64748b;
    padding: 4px 8px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: color 0.2s ease;
}

.sc-secret-toggle-btn:hover {
    color: #0f172a;
}

.sc-test-result-box {
    font-size: 0.85rem;
    border-radius: 8px;
    padding: 0.6rem 0.9rem;
    display: none;
    transition: all 0.3s ease;
}

.sc-badge-secret {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
    border: 1px solid rgba(239, 68, 68, 0.2);
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
}
</style>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 bg-white p-3 p-md-4 rounded-3 shadow-sm border border-secondary border-opacity-10">
            <div>
                <h2 class="h5 fw-bold mb-1 text-dark">Pengaturan Sistem & Kunci API</h2>
                <p class="text-muted small mb-0">Kelola kredensial API dan parameter aplikasi.</p>
            </div>
            
            <div class="d-flex align-items-center gap-2">
                <a href="<?php echo URLROOT; ?>/admin/dashboard" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="fa-solid fa-arrow-left me-1"></i> Dashboard
                </a>
            </div>
        </div>
    </div>
</div>



<!-- Main Settings Form -->
<form action="<?php echo URLROOT; ?>/admin/settings" method="POST" id="scSettingsForm">
    <div class="row g-4">
        
        <!-- 1. TELEGRAM BOT INTEGRATION -->
        <div class="col-lg-6">
            <div class="card sc-settings-card h-100 p-4">
                <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <div class="sc-card-header-icon" style="background: rgba(42, 171, 238, 0.12); color: #229ED9;">
                            <i class="fa-brands fa-telegram fs-4"></i>
                        </div>
                        <div>
                            <h3 class="h6 fw-bold mb-0 text-dark">Telegram Bot</h3>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" id="btnTestTelegram" onclick="testApiKey('telegram', 'setting_telegram_bot_token', 'resTelegramTest')">
                        <i class="fa-solid fa-plug me-1"></i> Uji Bot
                    </button>
                </div>

                <div id="resTelegramTest" class="sc-test-result-box mb-3"></div>

                <div class="row g-3">
                    <?php if (!empty($groupedSettings['telegram'])) : ?>
                        <?php foreach ($groupedSettings['telegram'] as $s) : ?>
                            <div class="col-12">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <label for="setting_<?php echo $s->setting_key; ?>" class="form-label small fw-semibold text-dark mb-0">
                                        <?php echo htmlspecialchars($s->setting_label); ?>
                                    </label>
                                    <?php if ($s->is_secret) : ?>
                                        <span class="sc-badge-secret">Sensitif</span>
                                    <?php endif; ?>
                                </div>
                                <div class="<?php echo $s->is_secret ? 'sc-secret-input-wrap' : ''; ?>">
                                    <input type="<?php echo $s->is_secret ? 'password' : 'text'; ?>" 
                                           name="settings[<?php echo $s->setting_key; ?>]" 
                                           id="setting_<?php echo $s->setting_key; ?>" 
                                           class="form-control form-control-sm <?php echo $s->is_secret ? 'pe-5' : ''; ?>" 
                                           value="<?php echo htmlspecialchars($s->setting_value ?? ''); ?>"
                                           placeholder="<?php echo htmlspecialchars($s->setting_label); ?>"
                                           required>
                                    <?php if ($s->is_secret) : ?>
                                        <button type="button" class="sc-secret-toggle-btn" onclick="toggleSecretVisibility('setting_<?php echo $s->setting_key; ?>', this)" title="Lihat/Sembunyikan">
                                            <i class="fa-regular fa-eye"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- 2. GOOGLE GEMINI AI CONFIGURATION -->
        <div class="col-lg-6">
            <div class="card sc-settings-card h-100 p-4">
                <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <div class="sc-card-header-icon" style="background: rgba(234, 179, 8, 0.12); color: #ca8a04;">
                            <i class="fa-solid fa-brain fs-4"></i>
                        </div>
                        <div>
                            <h3 class="h6 fw-bold mb-0 text-dark">Google Gemini AI</h3>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" id="btnTestGemini" onclick="testApiKey('gemini', 'setting_gemini_api_key', 'resGeminiTest', 'setting_gemini_primary_model')">
                        <i class="fa-solid fa-plug me-1"></i> Uji AI
                    </button>
                </div>

                <div id="resGeminiTest" class="sc-test-result-box mb-3"></div>

                <div class="row g-3">
                    <?php
                    $aiSettings = [];
                    if (!empty($groupedSettings['ai'])) {
                        foreach ($groupedSettings['ai'] as $item) {
                            $aiSettings[$item->setting_key] = $item;
                        }
                    }
                    $currentPrimaryModel = $aiSettings['gemini_primary_model']->setting_value ?? 'gemini-flash-lite-latest';
                    $currentFallbackModels = $aiSettings['gemini_fallback_models']->setting_value ?? 'gemini-3.8-flash,gemini-3.5-flash,gemini-flash-latest';
                    $currentTemp = $aiSettings['gemini_temperature']->setting_value ?? '0.7';
                    $currentApiKey = $aiSettings['gemini_api_key']->setting_value ?? '';
                    ?>

                    <!-- API Key -->
                    <div class="col-12">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label for="setting_gemini_api_key" class="form-label small fw-semibold text-dark mb-0">
                                Google Gemini API Key
                            </label>
                            <span class="sc-badge-secret">Sensitif</span>
                        </div>
                        <div class="sc-secret-input-wrap">
                            <input type="password" 
                                   name="settings[gemini_api_key]" 
                                   id="setting_gemini_api_key" 
                                   class="form-control form-control-sm pe-5" 
                                   value="<?php echo htmlspecialchars($currentApiKey); ?>"
                                   placeholder="AIzaSy..."
                                   required>
                            <button type="button" class="sc-secret-toggle-btn" onclick="toggleSecretVisibility('setting_gemini_api_key', this)" title="Lihat/Sembunyikan">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Primary Model Selector -->
                    <div class="col-12">
                        <label for="scPrimaryModelSelect" class="form-label small fw-semibold text-dark mb-1">
                            Model Utama (Primary)
                        </label>
                        <select id="scPrimaryModelSelect" class="form-select form-select-sm mb-1.5" onchange="onPrimaryModelChange(this.value)">
                            <option value="gemini-flash-lite-latest" <?php echo ($currentPrimaryModel === 'gemini-flash-lite-latest') ? 'selected' : ''; ?>>
                                gemini-flash-lite-latest (Default)
                            </option>
                            <option value="gemini-3.8-flash" <?php echo ($currentPrimaryModel === 'gemini-3.8-flash') ? 'selected' : ''; ?>>
                                gemini-3.8-flash
                            </option>
                            <option value="gemini-3.5-flash" <?php echo ($currentPrimaryModel === 'gemini-3.5-flash') ? 'selected' : ''; ?>>
                                gemini-3.5-flash
                            </option>
                            <option value="gemini-flash-latest" <?php echo ($currentPrimaryModel === 'gemini-flash-latest') ? 'selected' : ''; ?>>
                                gemini-flash-latest
                            </option>
                            <option value="gemini-3.5-flash-lite" <?php echo ($currentPrimaryModel === 'gemini-3.5-flash-lite') ? 'selected' : ''; ?>>
                                gemini-3.5-flash-lite
                            </option>
                            <option value="gemini-2.5-flash" <?php echo ($currentPrimaryModel === 'gemini-2.5-flash') ? 'selected' : ''; ?>>
                                gemini-2.5-flash
                            </option>
                            <option value="gemini-2.5-pro" <?php echo ($currentPrimaryModel === 'gemini-2.5-pro') ? 'selected' : ''; ?>>
                                gemini-2.5-pro
                            </option>
                            <option value="custom" <?php echo (!in_array($currentPrimaryModel, ['gemini-flash-lite-latest','gemini-3.8-flash','gemini-3.5-flash','gemini-flash-latest','gemini-3.5-flash-lite','gemini-2.5-flash','gemini-2.5-pro'])) ? 'selected' : ''; ?>>
                                Kustom...
                            </option>
                        </select>
                        <input type="text" 
                               name="settings[gemini_primary_model]" 
                               id="setting_gemini_primary_model" 
                               class="form-control form-control-sm" 
                               value="<?php echo htmlspecialchars($currentPrimaryModel); ?>"
                               placeholder="Nama model (contoh: gemini-flash-lite-latest)"
                               required>
                    </div>

                    <!-- Fallback / Backup Models Chain -->
                    <div class="col-12">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label for="setting_gemini_fallback_models" class="form-label small fw-semibold text-dark mb-0">
                                Model Cadangan (Fallback Chain)
                            </label>
                            <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none small text-muted" onclick="applyRecommendedFallback()" style="font-size: 0.78rem;">
                                Reset Default
                            </button>
                        </div>
                        <input type="text" 
                               name="settings[gemini_fallback_models]" 
                               id="setting_gemini_fallback_models" 
                               class="form-control form-control-sm mb-1.5" 
                               value="<?php echo htmlspecialchars($currentFallbackModels); ?>"
                               placeholder="gemini-3.8-flash,gemini-3.5-flash..."
                               required>
                        
                        <!-- Quick Add Badges -->
                        <div class="d-flex flex-wrap gap-1 align-items-center">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill small" style="font-size: 0.72rem;" onclick="toggleFallbackModel('gemini-3.8-flash')">+ 3.8-flash</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill small" style="font-size: 0.72rem;" onclick="toggleFallbackModel('gemini-3.5-flash')">+ 3.5-flash</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill small" style="font-size: 0.72rem;" onclick="toggleFallbackModel('gemini-flash-latest')">+ flash-latest</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill small" style="font-size: 0.72rem;" onclick="toggleFallbackModel('gemini-flash-lite-latest')">+ flash-lite</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill small" style="font-size: 0.72rem;" onclick="toggleFallbackModel('gemini-3.5-flash-lite')">+ 3.5-lite</button>
                        </div>
                    </div>

                    <!-- AI Temperature -->
                    <div class="col-12">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label for="setting_gemini_temperature" class="form-label small fw-semibold text-dark mb-0">
                                Temperature
                            </label>
                            <span id="tempValueBadge" class="badge bg-secondary-subtle text-dark small fw-bold"><?php echo htmlspecialchars($currentTemp); ?></span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <input type="range" 
                                   class="form-range" 
                                   id="tempRangeSlider" 
                                   min="0.0" 
                                   max="1.0" 
                                   step="0.05" 
                                   value="<?php echo htmlspecialchars($currentTemp); ?>"
                                   oninput="onTempSliderChange(this.value)">
                            <input type="hidden" 
                                   name="settings[gemini_temperature]" 
                                   id="setting_gemini_temperature" 
                                   value="<?php echo htmlspecialchars($currentTemp); ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. RESEND EMAIL API CONFIGURATION -->
        <div class="col-lg-6">
            <div class="card sc-settings-card h-100 p-4">
                <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <div class="sc-card-header-icon" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                            <i class="fa-solid fa-envelope fs-4"></i>
                        </div>
                        <div>
                            <h3 class="h6 fw-bold mb-0 text-dark">Resend Email</h3>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" id="btnTestResend" onclick="testApiKey('resend', 'setting_resend_api_key', 'resResendTest')">
                        <i class="fa-solid fa-plug me-1"></i> Uji Resend
                    </button>
                </div>

                <div id="resResendTest" class="sc-test-result-box mb-3"></div>

                <div class="row g-3">
                    <?php if (!empty($groupedSettings['email'])) : ?>
                        <?php foreach ($groupedSettings['email'] as $s) : ?>
                            <div class="col-12">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <label for="setting_<?php echo $s->setting_key; ?>" class="form-label small fw-semibold text-dark mb-0">
                                        <?php echo htmlspecialchars($s->setting_label); ?>
                                    </label>
                                    <?php if ($s->is_secret) : ?>
                                        <span class="sc-badge-secret">Sensitif</span>
                                    <?php endif; ?>
                                </div>
                                <div class="<?php echo $s->is_secret ? 'sc-secret-input-wrap' : ''; ?>">
                                    <input type="<?php echo $s->is_secret ? 'password' : 'text'; ?>" 
                                           name="settings[<?php echo $s->setting_key; ?>]" 
                                           id="setting_<?php echo $s->setting_key; ?>" 
                                           class="form-control form-control-sm <?php echo $s->is_secret ? 'pe-5' : ''; ?>" 
                                           value="<?php echo htmlspecialchars($s->setting_value ?? ''); ?>"
                                           placeholder="<?php echo htmlspecialchars($s->setting_label); ?>"
                                           required>
                                    <?php if ($s->is_secret) : ?>
                                        <button type="button" class="sc-secret-toggle-btn" onclick="toggleSecretVisibility('setting_<?php echo $s->setting_key; ?>', this)" title="Lihat/Sembunyikan">
                                            <i class="fa-regular fa-eye"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- 4. GENERAL SYSTEM & SECURITY PARAMETERS -->
        <div class="col-lg-6">
            <div class="card sc-settings-card h-100 p-4">
                <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <div class="sc-card-header-icon" style="background: rgba(99, 102, 241, 0.12); color: #4f46e5;">
                            <i class="fa-solid fa-sliders fs-4"></i>
                        </div>
                        <div>
                            <h3 class="h6 fw-bold mb-0 text-dark">Sistem & Keamanan OTP</h3>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <?php if (!empty($groupedSettings['general'])) : ?>
                        <?php foreach ($groupedSettings['general'] as $s) : ?>
                            <div class="<?php echo ($s->setting_type === 'number') ? 'col-sm-6' : 'col-12'; ?>">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <label for="setting_<?php echo $s->setting_key; ?>" class="form-label small fw-semibold text-dark mb-0">
                                        <?php echo htmlspecialchars($s->setting_label); ?>
                                    </label>
                                </div>
                                <div>
                                    <input type="<?php echo ($s->setting_type === 'number') ? 'number' : 'text'; ?>" 
                                           <?php echo ($s->setting_type === 'number') ? 'min="1"' : ''; ?>
                                           name="settings[<?php echo $s->setting_key; ?>]" 
                                           id="setting_<?php echo $s->setting_key; ?>" 
                                           class="form-control form-control-sm" 
                                           value="<?php echo htmlspecialchars($s->setting_value ?? ''); ?>"
                                           placeholder="<?php echo htmlspecialchars($s->setting_label); ?>"
                                           required>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

    <!-- Sticky / Bottom Action Bar -->
    <div class="row mt-4 mb-5">
        <div class="col-12">
            <div class="p-3 bg-white rounded-3 shadow-sm border border-secondary border-opacity-10 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                <div class="small text-muted">
                    <i class="fa-solid fa-circle-check text-success me-1"></i>
                    Perubahan konfigurasi tersimpan langsung ke database.
                </div>
                <div class="d-flex gap-2">
                    <button type="reset" class="btn btn-outline-secondary px-3 py-2 btn-sm rounded-pill">
                        Reset
                    </button>
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold btn-sm rounded-pill shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Client-side Testing & Visibility Toggle Script -->
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

// Change Primary Model from Select
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

// Quick apply recommended fallback order
function applyRecommendedFallback() {
    const input = document.getElementById('setting_gemini_fallback_models');
    if (input) {
        input.value = 'gemini-3.8-flash,gemini-3.5-flash,gemini-flash-latest,gemini-flash-lite-latest,gemini-3.5-flash-lite';
    }
}

// Toggle individual fallback model in comma-separated list
function toggleFallbackModel(modelName) {
    const input = document.getElementById('setting_gemini_fallback_models');
    if (!input) return;
    let parts = input.value.split(',').map(s => s.trim()).filter(Boolean);
    const idx = parts.indexOf(modelName);
    if (idx >= 0) {
        parts.splice(idx, 1);
    } else {
        parts.push(modelName);
    }
    input.value = parts.join(',');
}

// Temperature slider change handler
function onTempSliderChange(val) {
    const badge = document.getElementById('tempValueBadge');
    const hidden = document.getElementById('setting_gemini_temperature');
    if (badge) badge.textContent = val;
    if (hidden) hidden.value = val;
}

// Live AJAX Tester for API Keys
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
    resultBox.className = 'sc-test-result-box mb-3 bg-light text-muted border border-secondary border-opacity-25';
    resultBox.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Menghubungi endpoint API ' + service.toUpperCase() + '...';

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
        showResultBox(resultBox, data.success, data.message);
    })
    .catch(err => {
        showResultBox(resultBox, false, 'Gagal melakukan request cURL: ' + err.message);
    });
}

function showResultBox(box, isSuccess, msg) {
    box.style.display = 'block';
    if (isSuccess) {
        box.className = 'sc-test-result-box mb-3 bg-success-subtle text-success border border-success border-opacity-25';
        box.innerHTML = '<i class="fa-solid fa-circle-check me-1.5"></i> ' + msg;
    } else {
        box.className = 'sc-test-result-box mb-3 bg-danger-subtle text-danger border border-danger border-opacity-25';
        box.innerHTML = '<i class="fa-solid fa-circle-xmark me-1.5"></i> ' + msg;
    }
}
</script>

<?php require APPROOT . '/views/templates/admin_footer.php'; ?>
