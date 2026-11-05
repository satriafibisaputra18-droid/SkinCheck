<?php require APPROOT . '/views/templates/header.php'; ?>

<!-- Dedicated In-Page Luxury Dark Styles -->
<style>
/* Scoped Dark Auth Architecture */
.sc-auth-section {
    background: #080f0c !important;
    background-image: 
        radial-gradient(at 0% 0%, rgba(191, 161, 95, 0.1) 0px, transparent 50%),
        radial-gradient(at 100% 100%, rgba(26, 74, 64, 0.18) 0px, transparent 50%),
        radial-gradient(at 50% 50%, rgba(31, 107, 87, 0.12) 0px, transparent 65%) !important;
    min-height: 85vh;
}

.sc-auth-box {
    background: #0d1b16 !important;
    border: 1px solid rgba(223, 198, 139, 0.22) !important;
    border-radius: 20px !important;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.08) !important;
    position: relative;
    overflow: hidden;
    color: #e2ede8 !important;
    max-width: 460px;
    margin: 0 auto;
}

.sc-auth-box::before {
    content: '';
    position: absolute;
    top: 0;
    left: 10%;
    right: 10%;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(223, 198, 139, 0.7), transparent);
    pointer-events: none;
}

/* Icon & Header */
.sc-auth-icon-wrap {
    width: 62px;
    height: 62px;
    margin: 0 auto 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(191, 161, 95, 0.35) 0%, rgba(13, 38, 30, 0.95) 100%) !important;
    border: 1.5px solid rgba(223, 198, 139, 0.5) !important;
    color: #dfc68b !important;
    box-shadow: 0 8px 25px rgba(191, 161, 95, 0.25);
}

.sc-auth-icon-wrap i {
    font-size: 1.75rem;
    filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.4));
}

.sc-auth-heading {
    color: #ffffff !important;
    font-family: 'Outfit', sans-serif !important;
    font-weight: 700 !important;
    letter-spacing: -0.02em;
}

.sc-auth-subtext {
    color: #94a3b8 !important;
    font-size: 0.85rem;
}

/* Action Pill: Buka Telegram Web */
.sc-btn-tele-pill {
    background: rgba(42, 171, 238, 0.12) !important;
    border: 1px solid rgba(42, 171, 238, 0.4) !important;
    color: #38bdf8 !important;
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

.sc-btn-tele-pill:hover {
    background: rgba(42, 171, 238, 0.22) !important;
    color: #ffffff !important;
    border-color: rgba(42, 171, 238, 0.65) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(42, 171, 238, 0.25);
}

/* Icon Info Pop-up Trigger (Clean round icon button) */
.sc-icon-notice-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(223, 198, 139, 0.1) !important;
    border: 1px solid rgba(223, 198, 139, 0.35) !important;
    color: #dfc68b !important;
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
    background: rgba(223, 198, 139, 0.22) !important;
    border-color: rgba(223, 198, 139, 0.7) !important;
    color: #ffffff !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(191, 161, 95, 0.3);
}

.sc-icon-notice-btn .sc-pulse-beacon {
    position: absolute;
    top: 2px;
    right: 2px;
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background-color: #dfc68b;
    animation: beaconPulse 1.8s infinite;
}

/* Pulsing Beacon Dot */
.sc-pulse-beacon {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: #dfc68b;
    display: inline-block;
    box-shadow: 0 0 0 0 rgba(223, 198, 139, 0.7);
    animation: beaconPulse 1.8s infinite;
}

.sc-pulse-beacon.active-sent {
    background-color: #10b981 !important;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7) !important;
    animation: beaconPulseGreen 1.8s infinite !important;
}

@keyframes beaconPulse {
    0% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(223, 198, 139, 0.7);
    }
    70% {
        transform: scale(1);
        box-shadow: 0 0 0 7px rgba(223, 198, 139, 0);
    }
    100% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(223, 198, 139, 0);
    }
}

@keyframes beaconPulseGreen {
    0% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    }
    70% {
        transform: scale(1);
        box-shadow: 0 0 0 7px rgba(16, 185, 129, 0);
    }
    100% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
    }
}

/* 6-Digit OTP Boxes */
.sc-otp-box {
    background: rgba(255, 255, 255, 0.05) !important;
    border: 2px solid rgba(223, 198, 139, 0.35) !important;
    color: #dfc68b !important;
    font-family: 'Outfit', monospace, sans-serif !important;
    font-size: 1.55rem !important;
    font-weight: 700 !important;
    border-radius: 12px !important;
    width: 46px !important;
    height: 52px !important;
    transition: all 0.2s ease !important;
    text-shadow: 0 0 10px rgba(223, 198, 139, 0.3);
}

.sc-otp-box:focus {
    background: rgba(255, 255, 255, 0.1) !important;
    border-color: #dfc68b !important;
    color: #ffffff !important;
    box-shadow: 0 0 16px rgba(223, 198, 139, 0.45) !important;
    transform: translateY(-2px);
    outline: none !important;
}

/* Primary Gold Action Button */
.sc-btn-gold {
    background: linear-gradient(135deg, #dfc68b 0%, #c4a968 100%) !important;
    border: 1px solid rgba(223, 198, 139, 0.5) !important;
    color: #05120f !important;
    font-weight: 700 !important;
    font-size: 0.95rem !important;
    border-radius: 999px !important;
    padding: 0.75rem 1.6rem !important;
    box-shadow: 0 4px 18px rgba(191, 161, 95, 0.35) !important;
    transition: all 0.25s ease !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    cursor: pointer;
}

.sc-btn-gold:hover {
    background: #eed8a1 !important;
    color: #05120f !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 24px rgba(191, 161, 95, 0.5) !important;
}

/* Modal Pop-up Styling */
.sc-modal-dark {
    background: #0d1b16 !important;
    border: 1px solid rgba(223, 198, 139, 0.35) !important;
    border-radius: 18px !important;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.8) !important;
    color: #e2ede8 !important;
}

.sc-modal-icon {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: rgba(223, 198, 139, 0.15);
    border: 1px solid rgba(223, 198, 139, 0.35);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #dfc68b;
}

.sc-modal-bot-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
}

.sc-guide-num {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: rgba(223, 198, 139, 0.2);
    border: 1px solid rgba(223, 198, 139, 0.4);
    color: #dfc68b;
    font-size: 0.75rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.sc-btn-tele-modal {
    background: linear-gradient(135deg, #2AABEE 0%, #229ED9 100%) !important;
    border: 1px solid rgba(255, 255, 255, 0.25) !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    font-size: 0.9rem !important;
    border-radius: 999px !important;
    padding: 0.65rem 1.25rem !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none !important;
    transition: all 0.2s ease !important;
}

.sc-btn-tele-modal:hover {
    background: linear-gradient(135deg, #38bdf8 0%, #25a0dc 100%) !important;
    color: #ffffff !important;
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(34, 158, 217, 0.4);
}
</style>

<section class="py-5 sc-auth-section d-flex align-items-center position-relative overflow-hidden">
    <!-- Ambient Backdrop Glow -->
    <div class="position-absolute top-50 start-50 translate-middle rounded-circle" style="width: 580px; height: 580px; background: radial-gradient(circle, rgba(31, 107, 87, 0.22) 0%, rgba(191, 161, 95, 0.1) 45%, transparent 70%); filter: blur(85px); pointer-events: none;"></div>

    <div class="container position-relative z-1">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5">
                
                <div class="card sc-auth-box border-0 p-4 p-sm-4 text-center">
                    
                    <!-- Icon Header -->
                    <div class="mb-3">
                        <div class="sc-auth-icon-wrap">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <h2 class="h3 fw-bold mb-1 sc-auth-heading">Verifikasi OTP</h2>
                        <p class="sc-auth-subtext small mb-3">
                            Masukkan 6 digit kode yang dikirimkan ke Telegram Anda.
                        </p>

                        <!-- Baris: Buka Telegram Web & Icon Info Pop-up -->
                        <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                            <a href="<?php echo htmlspecialchars($data['telegram_web_link'] ?? 'https://web.telegram.org'); ?>" target="_blank" class="sc-btn-tele-pill" id="scBtnOpenTeleWeb">
                                <i class="fa-brands fa-telegram me-1 fs-6"></i>
                                <span>Buka Telegram Web</span>
                                <i class="fa-solid fa-arrow-up-right-from-square ms-1" style="font-size:0.65rem; opacity:0.85;"></i>
                            </a>

                            <!-- Icon Info Pop-up (Hanya icon tanpa teks) -->
                            <button type="button" class="sc-icon-notice-btn" data-bs-toggle="modal" data-bs-target="#otpNoticeModal" id="btnOtpNoticeDot" title="Pemberitahuan Kode OTP">
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
                            <i class="fa-regular fa-clock" style="color: #dfc68b;"></i>
                            <span>Kode berlaku:</span>
                            <strong id="scOtpTimer" class="font-monospace fs-6" style="color: #dfc68b;">05:00</strong>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid mb-3">
                            <button type="submit" id="scOtpSubmitBtn" class="sc-btn-gold">
                                <i class="fa-solid fa-check-circle me-2"></i> Verifikasi
                            </button>
                        </div>
                    </form>

                    <!-- Resend & Alternative Links -->
                    <div class="pt-3 border-top d-flex flex-column align-items-center gap-2" style="border-color: rgba(255, 255, 255, 0.08) !important;">
                        <form action="<?php echo URLROOT; ?>/auth/resendOtp" method="POST" id="scResendForm" class="d-inline">
                            <button type="submit" id="scResendBtn" class="btn btn-sm btn-link text-decoration-none p-0" <?php echo ($data['can_resend_in'] > 0) ? 'disabled' : ''; ?> style="color: #dfc68b; font-size: 0.85rem;">
                                <i class="fa-solid fa-arrows-rotate me-1"></i>
                                <span id="scResendText">
                                    <?php if ($data['can_resend_in'] > 0) : ?>
                                        Kirim Ulang Kode (<?php echo $data['can_resend_in']; ?>s)
                                    <?php else : ?>
                                        Kirim Ulang Kode OTP
                                    <?php endif; ?>
                                </span>
                            </button>
                        </form>

                        <a href="<?php echo URLROOT; ?>/auth/cancelRegistration" class="sc-auth-subtext text-decoration-none small mt-1" onclick="return confirm('Batalkan proses pendaftaran ini dan ganti data registrasi?');" style="transition: color 0.2s ease;" onmouseover="this.style.color='#dfc68b'" onmouseout="this.style.color='#94a3b8'">
                            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Pendaftaran
                        </a>
                    </div>

                </div>

            </div>
        </div>
    </div>
</section>

<!-- Pop-up Modal Pemberitahuan Kode OTP (Dipicu oleh Titik Pemberitahuan) -->
<div class="modal fade" id="otpNoticeModal" tabindex="-1" aria-labelledby="otpNoticeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content sc-modal-dark p-3">
            <div class="modal-header border-0 pb-2">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="sc-modal-icon">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="otpNoticeModalLabel" style="font-family:'Outfit',sans-serif; font-size: 1.15rem;">Pemberitahuan Kode OTP</h5>
                        <span class="small text-secondary" style="font-size:0.75rem;">Sistem Verifikasi Akun SkinCheck</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body py-2">
                <!-- Bot Connection Badge -->
                <div class="sc-modal-bot-card p-3 rounded-3 mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small text-uppercase fw-semibold" style="color: #94a3b8; font-size: 0.72rem; letter-spacing: 0.05em;">Bot Telegram Resmi</span>
                        <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2 py-1 small" style="font-size:0.7rem;">
                            <i class="fa-solid fa-circle-check me-1"></i> Aktif Terhubung
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2.5">
                        <i class="fa-brands fa-telegram text-info fs-3"></i>
                        <div>
                            <strong class="text-white d-block" style="font-family:'Outfit',sans-serif; font-size: 0.95rem;"><?php echo htmlspecialchars($data['telegram_bot_name'] ?? 'KodeOTPSkinCheckBot'); ?></strong>
                            <small class="text-secondary" style="font-size:0.75rem;">@Kode_OTP_SkinCheckBot</small>
                        </div>
                    </div>
                </div>

                <!-- Step by Step Guide -->
                <div class="sc-guide-list mb-3">
                    <div class="d-flex gap-3 mb-2.5">
                        <div class="sc-guide-num">1</div>
                        <div class="small text-white-50" style="line-height: 1.5;">
                            Buka Telegram Web dengan mengklik tombol biru di bawah atau di panel OTP.
                        </div>
                    </div>
                    <div class="d-flex gap-3 mb-2.5">
                        <div class="sc-guide-num">2</div>
                        <div class="small text-white-50" style="line-height: 1.5;">
                            Di dalam chat bot <strong class="text-white"><?php echo htmlspecialchars($data['telegram_bot_name'] ?? 'KodeOTPSkinCheckBot'); ?></strong>, tekan tombol <strong class="text-info">START</strong>.
                        </div>
                    </div>
                    <div class="d-flex gap-3 mb-2.5">
                        <div class="sc-guide-num">3</div>
                        <div class="small text-white-50" style="line-height: 1.5;">
                            Bot akan langsung mengirimkan <strong class="text-warning">6 digit kode OTP</strong> rahasia Anda.
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="sc-guide-num">4</div>
                        <div class="small text-white-50" style="line-height: 1.5;">
                            Ketik 6 digit kode tersebut ke formulir verifikasi sebelum masa kedaluwarsa 5 menit berakhir.
                        </div>
                    </div>
                </div>

                <!-- Quick Open Button Inside Modal -->
                <div class="d-grid gap-2">
                    <a href="<?php echo htmlspecialchars($data['telegram_web_link'] ?? 'https://web.telegram.org'); ?>" target="_blank" class="sc-btn-tele-modal">
                        <i class="fa-brands fa-telegram me-2 fs-5"></i> Buka Telegram Web (<?php echo htmlspecialchars($data['telegram_bot_name'] ?? 'KodeOTPSkinCheckBot'); ?>)
                        <i class="fa-solid fa-arrow-up-right-from-square ms-1" style="font-size:0.7rem;"></i>
                    </a>
                    <a href="<?php echo htmlspecialchars($data['telegram_bot_link'] ?? 'https://t.me/Kode_OTP_SkinCheckBot'); ?>" target="_blank" class="btn btn-sm btn-link text-secondary text-decoration-none small py-1">
                        <i class="fa-solid fa-desktop me-1"></i> Buka via Aplikasi Telegram
                    </a>
                </div>
            </div>

            <div class="modal-footer border-0 pt-2 pb-0">
                <button type="button" class="btn btn-sm sc-btn-gold px-4 py-2 w-100" data-bs-dismiss="modal">
                    <i class="fa-solid fa-check me-1.5"></i> Mengerti, Masukkan Kode OTP
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
    const beaconDot = document.getElementById('beaconDot');
    const beaconIcon = document.getElementById('beaconIcon');

    // Auto-polling silently to receive OTP status from Telegram
    let isChecking = false;
    let pollInterval = setInterval(function() {
        if (isChecking) return;
        isChecking = true;

        fetch('<?php echo URLROOT; ?>/auth/checkOtpStatus')
            .then(res => res.json())
            .then(data => {
                isChecking = false;
                if (data && data.otp_sent) {
                    clearInterval(pollInterval);
                    // Update dot beacon & icon to active emerald green (no text)
                    if (beaconDot) {
                        beaconDot.classList.add('active-sent');
                    }
                    if (beaconIcon) {
                        beaconIcon.style.color = '#10b981';
                    }
                    if (window.showToast) {
                        window.showToast(data.message || 'Kode OTP telah terkirim ke Telegram Anda', 'info');
                    }
                    if (boxes.length > 0 && !boxes[0].value) {
                        boxes[0].focus();
                    }
                }
            })
            .catch(err => {
                isChecking = false;
            });
    }, 2500);

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
            this.style.borderColor = '#dfc68b';
            this.style.boxShadow = '0 0 16px rgba(223, 198, 139, 0.45)';
        });

        box.addEventListener('blur', function () {
            this.style.borderColor = 'rgba(223, 198, 139, 0.35)';
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
            submitBtn.style.boxShadow = '0 0 25px rgba(191, 161, 95, 0.7)';
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
                resendText.textContent = 'Kirim Ulang Kode OTP';
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
