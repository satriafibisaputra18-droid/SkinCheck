<?php require APPROOT . '/views/templates/header.php'; ?>

<!-- Scoped OTP Clinical Extensions -->
<style>
.sc-auth-box {
    max-width: 460px;
    margin: 0 auto;
}

.sc-btn-wa-pill {
    background: #ecfdf5 !important;
    border: 1px solid #a7f3d0 !important;
    color: #059669 !important;
    font-size: 0.85rem !important;
    font-weight: 600 !important;
    border-radius: 999px !important;
    padding: 0.45rem 1.15rem !important;
    display: inline-flex;
    align-items: center;
    height: 36px;
    text-decoration: none !important;
    transition: all 0.22s ease !important;
}

.sc-btn-wa-pill:hover {
    background: #059669 !important;
    color: #ffffff !important;
    border-color: #059669 !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);
}

.sc-wa-target-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #166534;
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 0.85rem;
}

.sc-icon-notice-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--primary-tint) !important;
    border: 1px solid rgba(15, 118, 110, 0.28) !important;
    color: var(--primary-color) !important;
    font-size: 1rem !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.22s ease !important;
    position: relative;
    padding: 0 !important;
    flex-shrink: 0;
}

.sc-icon-notice-btn:hover {
    background: var(--primary-color) !important;
    color: #ffffff !important;
    transform: translateY(-1px);
}

.sc-icon-notice-btn .sc-pulse-beacon {
    position: absolute;
    top: 2px;
    right: 2px;
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background-color: #10b981;
    animation: beaconPulseGreen 1.8s infinite;
}

@keyframes beaconPulseGreen {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6); }
    70% { transform: scale(1); box-shadow: 0 0 0 7px rgba(16, 185, 129, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

.sc-otp-box {
    width: 46px !important;
    height: 52px !important;
    padding: 0 !important;
    font-size: 1.25rem !important;
    font-weight: 700 !important;
    border-radius: 12px !important;
    border: 1.5px solid #dce8e4 !important;
    color: #0f766e !important;
    transition: all 0.2s ease !important;
}

.sc-modal-dark {
    background: rgba(255, 255, 255, 0.94) !important;
    backdrop-filter: blur(26px) saturate(190%) !important;
    -webkit-backdrop-filter: blur(26px) saturate(190%) !important;
    border: 1px solid rgba(255, 255, 255, 0.92) !important;
    border-radius: 24px !important;
    box-shadow: 0 26px 65px rgba(15, 41, 36, 0.18), inset 0 1.5px 0 rgba(255, 255, 255, 0.96) !important;
    color: #1f3b35 !important;
}

.sc-modal-icon {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #059669;
}

.sc-modal-bot-card {
    background: rgba(240, 253, 244, 0.85);
    border: 1px solid #bbf7d0;
}

.sc-guide-num {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    color: #059669;
    font-size: 0.75rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
</style>

<section class="py-5 sc-auth-section d-flex align-items-center position-relative overflow-hidden">
    <div class="container position-relative z-1">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5">
                
                <div class="card sc-auth-box border-0 p-4 p-sm-4 text-center">
                    
                    <!-- Icon Header -->
                    <div class="mb-3">
                        <div class="sc-auth-icon-wrap" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff;">
                            <i class="fa-brands fa-whatsapp fs-3"></i>
                        </div>
                        <h2 class="h3 fw-bold mb-1 sc-auth-heading">Verifikasi OTP WhatsApp</h2>
                        <p class="sc-auth-subtext small mb-2">
                            Masukkan 6 digit kode yang dikirimkan ke nomor WhatsApp Anda:
                        </p>

                        <!-- Nomor WhatsApp Target -->
                        <div class="sc-wa-target-pill mb-3">
                            <i class="fa-brands fa-whatsapp text-success fs-5"></i>
                            <span>Terkirim ke: <strong><?php echo htmlspecialchars($data['phone_masked'] ?? $data['phone'] ?? ''); ?></strong></span>
                        </div>

                        <!-- Baris: Buka WhatsApp Web & Icon Info Pop-up -->
                        <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                            <a href="https://web.whatsapp.com" target="_blank" class="sc-btn-wa-pill" id="scBtnOpenWaWeb">
                                <i class="fa-brands fa-whatsapp me-1 fs-6"></i>
                                <span>Buka WhatsApp Web</span>
                                <i class="fa-solid fa-arrow-up-right-from-square ms-1" style="font-size:0.65rem; opacity:0.85;"></i>
                            </a>

                            <!-- Icon Info Pop-up -->
                            <button type="button" class="sc-icon-notice-btn" data-bs-toggle="modal" data-bs-target="#otpNoticeModal" id="btnOtpNoticeDot" title="Panduan Kode OTP">
                                <i class="fa-solid fa-circle-info" id="beaconIcon"></i>
                                <span class="sc-pulse-beacon" id="beaconDot"></span>
                            </button>
                        </div>
                    </div>

                    <?php if (!empty($data['otp_err'])) : ?>
                        <script>document.addEventListener('DOMContentLoaded', () => { if (window.showToast) window.showToast(<?php echo json_encode($data['otp_err']); ?>, 'error'); });</script>
                    <?php endif; ?>

                    <!-- OTP Form -->
                    <form action="<?php echo URLROOT; ?>/auth/verifyOtp" method="POST" id="scOtpForm">
                        
                        <!-- 6 Digit Input Boxes -->
                        <div class="d-flex justify-content-center gap-2 mb-3 mt-2">
                            <?php for ($i = 0; $i < 6; $i++) : ?>
                                <input type="text" 
                                       name="otp_digit[]" 
                                       class="form-control text-center sc-otp-box" 
                                       maxlength="1" 
                                       pattern="[0-9]"
                                       inputmode="numeric" 
                                       autocomplete="one-time-code"
                                       required>
                            <?php endfor; ?>
                        </div>

                        <!-- Expiry Countdown -->
                        <div class="d-flex align-items-center justify-content-center gap-2 mb-3 sc-auth-subtext small">
                            <i class="fa-regular fa-clock text-primary"></i>
                            <span>Kode berlaku:</span>
                            <strong id="scOtpTimer" class="font-monospace fs-6 text-primary">05:00</strong>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid mb-3">
                            <button type="submit" id="scOtpSubmitBtn" class="sc-btn-gold">
                                <i class="fa-solid fa-check-circle me-2"></i> Verifikasi Akun
                            </button>
                        </div>
                    </form>

                    <!-- Resend & Alternative Links -->
                    <div class="pt-3 border-top d-flex flex-column align-items-center gap-2">
                        <form action="<?php echo URLROOT; ?>/auth/resendOtp" method="POST" id="scResendForm" class="d-inline">
                            <button type="submit" id="scResendBtn" class="btn btn-sm btn-link text-decoration-none p-0 sc-link-gold" <?php echo ($data['can_resend_in'] > 0) ? 'disabled' : ''; ?> style="font-size: 0.85rem;">
                                <i class="fa-solid fa-arrows-rotate me-1"></i>
                                <span id="scResendText">
                                    <?php if ($data['can_resend_in'] > 0) : ?>
                                        Kirim Ulang Kode (<?php echo $data['can_resend_in']; ?>s)
                                    <?php else : ?>
                                        Kirim Ulang Kode OTP ke WhatsApp
                                    <?php endif; ?>
                                </span>
                            </button>
                        </form>

                        <a href="<?php echo URLROOT; ?>/auth/cancelRegistration" class="sc-auth-subtext text-decoration-none small mt-1" onclick="return confirm('Batalkan proses pendaftaran ini dan ganti data registrasi?');">
                            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Pendaftaran
                        </a>
                    </div>

                </div>

            </div>
        </div>
    </div>
</section>

<!-- Pop-up Modal Panduan Kode OTP WhatsApp -->
<div class="modal fade" id="otpNoticeModal" tabindex="-1" aria-labelledby="otpNoticeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content sc-modal-dark p-3">
            <div class="modal-header border-0 pb-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="sc-modal-icon">
                        <i class="fa-brands fa-whatsapp fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="otpNoticeModalLabel" style="font-family:'Outfit',sans-serif; font-size: 1.15rem;">Panduan Verifikasi WhatsApp</h5>
                        <span class="small text-muted" style="font-size:0.75rem;">Sistem Gateway SkinCheck AI</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body py-2">
                <!-- Gateway Connection Badge -->
                <div class="sc-modal-bot-card p-3 rounded-3 mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="small text-uppercase fw-semibold text-muted" style="font-size: 0.72rem; letter-spacing: 0.05em;">Layanan Pengiriman</span>
                        <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2 py-1 small" style="font-size:0.7rem;">
                            <i class="fa-solid fa-circle-check me-1"></i> WhatsApp Gateway Aktif
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <i class="fa-brands fa-whatsapp text-success fs-3"></i>
                        <div>
                            <strong class="text-dark d-block" style="font-family:'Outfit',sans-serif; font-size: 0.95rem;">SkinCheck AI Security</strong>
                            <small class="text-muted" style="font-size:0.75rem;">Pesan OTP dikirim langsung ke nomor WhatsApp Anda</small>
                        </div>
                    </div>
                </div>

                <!-- Step by Step Guide -->
                <div class="sc-guide-list mb-3">
                    <div class="d-flex align-items-start gap-2 mb-2">
                        <div class="sc-guide-num">1</div>
                        <div class="small text-muted" style="line-height: 1.5;">
                            Buka aplikasi <strong class="text-dark">WhatsApp</strong> di ponsel Anda atau klik tombol WhatsApp Web.
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-2 mb-2">
                        <div class="sc-guide-num">2</div>
                        <div class="small text-muted" style="line-height: 1.5;">
                            Cari pesan masuk terbaru berisi <strong class="text-success">6 Digit Kode OTP</strong> dari SkinCheck.
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-2 mb-2">
                        <div class="sc-guide-num">3</div>
                        <div class="small text-muted" style="line-height: 1.5;">
                            Ketikkan 6 digit angka kode OTP ke dalam kotak formulir di halaman ini.
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-2">
                        <div class="sc-guide-num">4</div>
                        <div class="small text-muted" style="line-height: 1.5;">
                            Kode ini berlaku selama 5 menit. Jika belum menerima pesan, gunakan tombol <strong class="text-dark">Kirim Ulang Kode</strong>.
                        </div>
                    </div>
                </div>

                <!-- Quick Open Button Inside Modal -->
                <div class="d-grid gap-2">
                    <a href="https://web.whatsapp.com" target="_blank" class="sc-btn-wa-pill justify-content-center py-2 h-auto">
                        <i class="fa-brands fa-whatsapp me-2 fs-5"></i> Buka WhatsApp Web
                        <i class="fa-solid fa-arrow-up-right-from-square ms-1" style="font-size:0.7rem;"></i>
                    </a>
                </div>
            </div>

            <div class="modal-footer border-0 pt-2 pb-0">
                <button type="button" class="btn btn-sm sc-btn-gold px-4 py-2 w-100" data-bs-dismiss="modal">
                    <i class="fa-solid fa-check me-2"></i> Mengerti, Masukkan Kode OTP
                </button>
            </div>
        </div>
    </div>
</div>

<!-- OTP Interaction Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const boxes = document.querySelectorAll('.sc-otp-box');
    const timerElem = document.getElementById('scOtpTimer');
    const resendBtn = document.getElementById('scResendBtn');
    const resendText = document.getElementById('scResendText');
    const submitBtn = document.getElementById('scOtpSubmitBtn');
    const form = document.getElementById('scOtpForm');

    // Auto focus first input box
    if (boxes.length > 0) {
        boxes[0].focus();
    }

    // Handle Input, Backspace, Arrow keys
    boxes.forEach((box, index) => {
        box.addEventListener('input', function (e) {
            const val = this.value;
            // Only allow digits
            if (!/^\d$/.test(val)) {
                this.value = '';
                return;
            }

            // Move to next box if available
            if (val.length === 1 && index < boxes.length - 1) {
                boxes[index + 1].focus();
                boxes[index + 1].select();
            }

            // Auto submit check
            checkAllFilled();
        });

        box.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace') {
                if (this.value === '' && index > 0) {
                    boxes[index - 1].focus();
                    boxes[index - 1].value = '';
                }
            } else if (e.key === 'ArrowLeft' && index > 0) {
                boxes[index - 1].focus();
            } else if (e.key === 'ArrowRight' && index < boxes.length - 1) {
                boxes[index + 1].focus();
            }
        });

        box.addEventListener('focus', function () {
            this.style.borderColor = '#10b981';
            this.style.boxShadow = '0 0 0 4px rgba(16, 185, 129, 0.16)';
        });

        box.addEventListener('blur', function () {
            this.style.borderColor = '#dce8e4';
            this.style.boxShadow = 'none';
        });

        // Paste support: user pastes 6 digit code directly
        box.addEventListener('paste', function (e) {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData).getData('text').trim();
            const digits = pasted.replace(/\D/g, '').slice(0, 6);
            if (digits.length > 0) {
                for (let i = 0; i < digits.length; i++) {
                    if (boxes[i]) {
                        boxes[i].value = digits[i];
                    }
                }
                const focusIdx = Math.min(digits.length, boxes.length - 1);
                boxes[focusIdx].focus();
                checkAllFilled();
            }
        });
    });

    function checkAllFilled() {
        let allFilled = true;
        boxes.forEach(b => {
            if (!b.value || !/^\d$/.test(b.value)) allFilled = false;
        });
        if (allFilled) {
            submitBtn.style.boxShadow = '0 8px 24px rgba(16, 185, 129, 0.38)';
        }
    }

    // ── Expiry Timer Countdown ──
    let expiresInSec = <?php echo (int)($data['expires_in'] ?? 300); ?>;
    function updateExpiryTimer() {
        if (expiresInSec <= 0) {
            timerElem.textContent = '00:00 (Kedaluwarsa)';
            timerElem.style.color = '#ef4444';
            boxes.forEach(b => b.disabled = true);
            submitBtn.disabled = true;
            return;
        }

        const mins = Math.floor(expiresInSec / 60);
        const secs = expiresInSec % 60;
        timerElem.textContent = String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
        expiresInSec--;
        setTimeout(updateExpiryTimer, 1000);
    }
    updateExpiryTimer();

    // ── Resend Cooldown Countdown ──
    let canResendIn = <?php echo (int)($data['can_resend_in'] ?? 0); ?>;
    function updateResendCooldown() {
        if (canResendIn <= 0) {
            if (resendBtn) {
                resendBtn.disabled = false;
                resendText.textContent = 'Kirim Ulang Kode OTP ke WhatsApp';
            }
            return;
        }

        if (resendText) {
            resendText.textContent = `Kirim Ulang Kode (${canResendIn}s)`;
        }
        canResendIn--;
        setTimeout(updateResendCooldown, 1000);
    }
    if (canResendIn > 0) {
        updateResendCooldown();
    }
});
</script>

<?php require APPROOT . '/views/templates/footer.php'; ?>
