<?php require APPROOT . '/views/templates/customer_header.php'; ?>

<!-- ── Breadcrumb ────────────────────────────────────────── -->
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="<?php echo URLROOT; ?>/customer/dashboard" class="btn btn-sm btn-outline-primary rounded-pill px-3">
        <i class="fa-solid fa-arrow-left me-1"></i>Dashboard
    </a>
    <span class="text-muted small">/ Face Scan</span>
</div>

<!-- ── Main Layout ──────────────────────────────────────── -->
<div class="row g-4 align-items-start">

    <!-- ═══ Left: Camera Panel ═══════════════════════════════════════ -->
    <div class="col-lg-7">
        <div class="sc-card">
            <div class="sc-card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="mb-0"><i class="fa-solid fa-camera me-2 text-primary"></i>Face Scan</h5>
                    <span id="sc-quality-badge" class="badge d-none"></span>
                </div>
                <span class="badge sc-badge-secure rounded-pill px-2 py-1" style="font-size:.74rem;">
                    <i class="fa-solid fa-lock me-1"></i>Privat
                </span>
            </div>

            <div class="sc-card-body">

                <!-- Permission error panel (hidden by default) -->
                <div id="sc-perm-error" class="d-none mb-3">
                    <div class="alert alert-danger border-0 mb-0">
                        <div class="d-flex gap-3 align-items-start">
                            <i class="fa-solid fa-video-slash fs-4 mt-1 flex-shrink-0"></i>
                            <div>
                                <p class="fw-bold mb-1 sc-perm-title">Camera Error</p>
                                <p class="mb-2 small sc-perm-msg">Unknown error.</p>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button class="btn btn-danger btn-sm" onclick="window.__faceScanApp.startCamera()">
                                        <i class="fa-solid fa-rotate-right me-1"></i>Try Again
                                    </button>
                                    <a href="https://support.google.com/chrome/answer/2693767" target="_blank" rel="noopener"
                                       class="btn btn-outline-danger btn-sm">
                                        <i class="fa-solid fa-circle-question me-1"></i>Help
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Validation result banner -->
                <div id="sc-validation-result" class="d-none"></div>

                <!-- ── Viewport ─────────────────────────────────────── -->
                <div class="sc-cam-viewport mb-3" id="sc-viewport">

                    <!-- Placeholder (idle state) -->
                    <div id="sc-placeholder" class="sc-cam-placeholder">
                        <i class="fa-solid fa-video-slash"></i>
                        <p class="fw-semibold mb-1">Kamera Belum Aktif</p>
                        <small>Klik "Aktifkan Kamera" untuk memulai</small>
                    </div>

                    <!-- Live video feed (hidden until camera starts) -->
                    <video id="sc-video"
                           class="sc-cam-video d-none"
                           autoplay playsinline muted></video>

                    <!-- Captured image preview (hidden until captured) -->
                    <img id="sc-preview"
                         class="sc-cam-video d-none"
                         alt="Captured scan preview">

                    <!-- HUD overlay: face guide + laser (shown during live) -->
                    <div id="sc-hud" class="sc-cam-hud d-none">
                        <!-- Face oval guide -->
                        <svg class="sc-face-guide" viewBox="0 0 100 100" preserveAspectRatio="none">
                            <ellipse cx="50" cy="50" rx="30" ry="40"
                                     fill="none"
                                     stroke="rgba(20,184,166,0.85)"
                                     stroke-width="0.8"
                                     stroke-dasharray="4 2"/>
                        </svg>
                        <!-- Corner brackets -->
                        <div class="sc-bracket sc-bracket-tl"></div>
                        <div class="sc-bracket sc-bracket-tr"></div>
                        <div class="sc-bracket sc-bracket-bl"></div>
                        <div class="sc-bracket sc-bracket-br"></div>
                        <!-- Instruction overlay -->
                        <div class="sc-cam-instruction">
                            <i class="fa-solid fa-face-smile me-1"></i>
                            Posisikan wajah di tengah frame
                        </div>
                        <!-- Scan laser -->
                        <div id="sc-laser" class="sc-cam-laser"></div>
                    </div>

                    <!-- Hidden canvas (used to capture frame) -->
                    <canvas id="sc-canvas" class="d-none"></canvas>
                </div>

                <!-- ── Upload progress ───────────────────────────── -->
                <div id="sc-upload-progress" class="d-none mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Mengunggah…</span>
                        <span id="sc-progress-pct" class="text-muted"></span>
                    </div>
                    <div class="progress" style="height:8px;border-radius:99px;">
                        <div id="sc-progress-bar"
                             class="progress-bar progress-bar-striped progress-bar-animated"
                             role="progressbar"
                             style="width:0%;background:var(--primary-color);"
                             aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                </div>

                <!-- ── Status message ────────────────────────────── -->
                <p id="sc-status-msg" class="sc-status-msg small text-muted text-center mt-2 mb-3">
                    Klik <strong>Aktifkan Kamera</strong> untuk memulai sesi scan.
                </p>

                <!-- ── Control buttons ───────────────────────────── -->
                <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap">

                    <button id="sc-btn-start" class="btn btn-primary px-4">
                        <i class="fa-solid fa-video me-2"></i>Aktifkan Kamera
                    </button>

                    <button id="sc-btn-capture" class="btn btn-secondary px-4 d-none">
                        <i class="fa-solid fa-camera me-2"></i>Ambil Foto
                    </button>

                    <button id="sc-btn-retake" class="btn btn-outline-secondary d-none">
                        <i class="fa-solid fa-rotate-left me-1"></i>Ulangi
                    </button>

                    <button id="sc-btn-submit" class="btn btn-primary px-4 d-none">
                        <i class="fa-solid fa-paper-plane me-2"></i>Kirim Scan
                    </button>

                    <button id="sc-btn-stop" class="btn btn-outline-danger btn-sm d-none">
                        <i class="fa-solid fa-video-slash me-1"></i>Stop
                    </button>

                </div>



            </div><!-- /.sc-card-body -->
        </div><!-- /.sc-card -->
    </div>

    <!-- ═══ Right: Compact Guide ══════════════════════════════════ -->
    <div class="col-lg-5">

        <div class="sc-card">
            <div class="sc-card-header">
                <h5 class="mb-0 text-dark"><i class="fa-solid fa-list-check me-2 text-primary"></i>Tips Scan Akurat</h5>
            </div>
            <div class="sc-card-body">
                <ol class="list-unstyled mb-3">
                    <?php
                    $steps = [
                        ['Aktifkan Kamera', 'Izinkan akses kamera pada browser Anda.'],
                        ['Cahaya Depan Merata', 'Hindari ruangan gelap atau cahaya dari belakang (backlight).'],
                        ['Posisi Wajah Ideal', 'Pasang wajah di dalam frame oval dengan jarak 30–50 cm.'],
                        ['Ambil & Kirim Foto', 'Tahan 1–2 detik saat memotret lalu klik Kirim Scan.'],
                    ];
                    foreach ($steps as $i => $step):
                    ?>
                        <li class="d-flex align-items-start gap-2 <?php echo $i === count($steps) - 1 ? 'mb-0' : 'mb-2'; ?>">
                            <div class="sc-step-num"><?php echo $i + 1; ?></div>
                            <div>
                                <p class="fw-semibold mb-0 small text-dark"><?php echo $step[0]; ?></p>
                                <p class="text-muted mb-0" style="font-size:.78rem;"><?php echo $step[1]; ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ol>

                <div class="d-flex flex-wrap gap-2 pt-3 border-top small">
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 fw-medium">
                        <i class="fa-solid fa-check me-1"></i>Cahaya depan rata
                    </span>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1 fw-medium">
                        <i class="fa-solid fa-xmark me-1"></i>Backlight / gelap
                    </span>
                </div>
            </div>
        </div>

    </div><!-- /.col-lg-5 -->
</div><!-- /.row -->

<!-- ── Custom CSS for this page ──────────────────────────── -->
<style>
/* Camera viewport */
.sc-cam-viewport {
    position: relative;
    width: 100%;
    aspect-ratio: 4 / 3;
    background: #0d1117;
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--shadow-md);
}
.sc-cam-video {
    position: absolute; inset: 0;
    width: 100%; height: 100%;
    object-fit: cover;
    transform: scaleX(-1);
    border-radius: inherit;
}
img.sc-cam-video { transform: none; } /* preview not mirrored */

/* Placeholder */
.sc-cam-placeholder {
    position: absolute; inset: 0;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    color: rgba(255,255,255,.45);
    gap: .4rem;
    font-size: .85rem;
}
.sc-cam-placeholder i { font-size: 2.8rem; opacity: .4; }

/* HUD overlay */
.sc-cam-hud {
    position: absolute; inset: 0;
    pointer-events: none;
}

/* Face oval guide (SVG fills the hud) */
.sc-face-guide {
    position: absolute; inset: 0;
    width: 100%; height: 100%;
}

/* Corner brackets */
.sc-bracket {
    position: absolute;
    width: 22px; height: 22px;
    border-color: rgba(20,184,166,.92);
    border-style: solid;
}
.sc-bracket-tl { top: 10%; left: 10%; border-width: 3px 0 0 3px; border-radius: 4px 0 0 0; }
.sc-bracket-tr { top: 10%; right: 10%; border-width: 3px 3px 0 0; border-radius: 0 4px 0 0; }
.sc-bracket-bl { bottom: 10%; left: 10%; border-width: 0 0 3px 3px; border-radius: 0 0 0 4px; }
.sc-bracket-br { bottom: 10%; right: 10%; border-width: 0 3px 3px 0; border-radius: 0 0 4px 0; }

/* Instruction text on HUD */
.sc-cam-instruction {
    position: absolute;
    bottom: 14px; left: 50%; transform: translateX(-50%);
    background: rgba(12, 28, 25, 0.56);
    border: 1px solid rgba(255, 255, 255, 0.28);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.24), inset 0 1px 0 rgba(255, 255, 255, 0.25);
    color: #fff;
    font-size: .75rem;
    font-weight: 600;
    padding: .36rem .95rem;
    border-radius: 99px;
    white-space: nowrap;
    backdrop-filter: blur(14px) saturate(170%);
    -webkit-backdrop-filter: blur(14px) saturate(170%);
}

/* Scan laser */
.sc-cam-laser {
    position: absolute; left: 0; width: 100%; height: 3px;
    background: linear-gradient(transparent, rgba(20,184,166,.95) 50%, transparent);
    box-shadow: 0 0 10px rgba(20,184,166,.85);
    animation: sc-laser 2.5s ease-in-out infinite;
    display: none; /* shown via JS during capture */
}
@keyframes sc-laser { 0%{ top:0 } 50%{ top:100% } 100%{ top:0 } }

/* Step numbers */
.sc-step-num {
    width: 28px; height: 28px;
    border-radius: 50%;
    background: var(--primary-color);
    color: #fff;
    font-size: .8rem; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}

/* Status message */
.sc-status-msg { min-height: 1.3rem; }

/* Floating Dynamic Notification Toast */
.sc-floating-toast {
    position: fixed;
    top: 24px;
    left: 50%;
    transform: translateX(-50%) translateY(-120px);
    z-index: 1070;
    transition: transform 0.45s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s ease;
    opacity: 0;
    pointer-events: none;
    cursor: pointer;
    max-width: 92vw;
    width: 460px;
}
.sc-floating-toast.show {
    transform: translateX(-50%) translateY(0);
    opacity: 1;
    pointer-events: auto;
}
.sc-toast-inner {
    background: rgba(255, 255, 255, 0.86);
    backdrop-filter: blur(22px) saturate(185%);
    -webkit-backdrop-filter: blur(22px) saturate(185%);
    border: 1px solid rgba(255, 255, 255, 0.94);
    box-shadow: 0 18px 42px rgba(15, 41, 36, 0.14), inset 0 1.5px 0 #ffffff;
    border-radius: 999px;
    padding: 0.65rem 1rem 0.65rem 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: var(--dark-neutral);
}
.sc-toast-bell-wrap {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #e6f4f1;
    border: 1px solid #cce6df;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
    font-size: 1.1rem;
    flex-shrink: 0;
}
.sc-bell-shake {
    animation: scBellRing 1.8s infinite ease-in-out;
}
@keyframes scBellRing {
    0%, 100% { transform: rotate(0); }
    10%, 30% { transform: rotate(-16deg); }
    20%, 40% { transform: rotate(16deg); }
    50% { transform: rotate(0); }
}
.sc-toast-body {
    flex-grow: 1;
    min-width: 0;
}
.sc-toast-title {
    font-size: 0.88rem;
    font-weight: 700;
    color: var(--primary-color);
    line-height: 1.2;
}
.sc-toast-sub {
    font-size: 0.75rem;
    color: var(--text-muted);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.sc-toast-btn {
    font-size: 0.78rem;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(15, 118, 110, 0.15);
}

/* ── Real Scan Complete Popup Modal ────────────────────── */
#scScanCompleteModal {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    z-index: 99999999 !important;
    display: none;
    align-items: center;
    justify-content: center;
    background: rgba(4, 10, 8, 0.88) !important;
    backdrop-filter: blur(12px) !important;
    -webkit-backdrop-filter: blur(12px) !important;
    padding: 1.25rem;
    overflow-y: auto;
}
#scScanCompleteModal.show {
    display: flex !important;
    animation: scModalPopIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
@keyframes scModalPopIn {
    from {
        opacity: 0;
        transform: scale(0.92);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}
#scScanCompleteModal .modal-dialog {
    max-width: 460px;
    width: 100%;
    margin: auto;
    position: relative;
    z-index: 10;
}
#scScanCompleteModal .modal-content {
    border-radius: 28px !important;
    border: 1px solid rgba(255, 255, 255, 0.92) !important;
    box-shadow: 0 28px 70px rgba(15, 41, 36, 0.22), inset 0 1.5px 0 rgba(255, 255, 255, 0.96) !important;
    background: rgba(255, 255, 255, 0.86) !important;
    backdrop-filter: blur(28px) saturate(190%);
    -webkit-backdrop-filter: blur(28px) saturate(190%);
    color: var(--body-text) !important;
    overflow: hidden;
    position: relative;
}
#scScanCompleteModal .modal-content::before {
    content: '';
    position: absolute;
    top: 0;
    left: 16%;
    right: 16%;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.98), transparent);
    pointer-events: none;
}
.sc-modal-icon-badge {
    width: 68px;
    height: 68px;
    border-radius: 50%;
    background: #e6f4f1 !important;
    border: 2px solid #cce6df !important;
    color: var(--primary-color) !important;
    font-size: 1.85rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 20px rgba(15, 118, 110, 0.12);
}
.sc-modal-score-wrap {
    background: #f6f9f8 !important;
    border: 1px solid var(--border-subtle) !important;
    border-radius: 18px !important;
    padding: 1.15rem 1rem !important;
}
.sc-modal-score-num {
    font-size: 3.2rem;
    font-weight: 800;
    color: var(--primary-color);
    line-height: 1;
    font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif;
}
.sc-metric-mini-item {
    background: #f8fbfa !important;
    border: 1px solid var(--border-subtle) !important;
    border-radius: 12px !important;
    padding: 0.65rem 0.75rem !important;
    text-align: center;
}
</style>

<!-- ── Floating Dynamic Notification Toast ────────────────────── -->
<div id="sc-floating-toast" class="sc-floating-toast" onclick="window.__faceScanApp && window.__faceScanApp.goToReport()">
    <div class="sc-toast-inner">
        <div class="sc-toast-bell-wrap">
            <i class="fa-solid fa-bell sc-bell-shake"></i>
        </div>
        <div class="sc-toast-body">
            <div class="sc-toast-title">Analisis Wajah Selesai!</div>
            <div class="sc-toast-sub">Skor Kulit: <strong id="sc-toast-score">--</strong>/100 · Ketuk untuk melihat laporan</div>
        </div>
        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-bold sc-toast-btn">
            Buka <i class="fa-solid fa-arrow-right ms-1"></i>
        </button>
    </div>
</div>

<!-- ── Scan Complete Real Popup Modal ────────────────────── -->
<div class="modal fade" id="scScanCompleteModal" tabindex="-1" aria-labelledby="scScanCompleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-4 p-md-5 text-center">
                <!-- Success Icon -->
                <div class="mb-3">
                    <div class="sc-modal-icon-badge">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                </div>

                <h3 class="fw-bold mb-1 text-dark" id="scScanCompleteModalLabel">Analisis Wajah Selesai</h3>
                <p class="small mb-3 text-muted">Evaluasi kondisi kulit wajah Anda berhasil diproses oleh sistem AI.</p>



                <!-- Score Highlight Box -->
                <div class="sc-modal-score-wrap mb-3 text-center">
                    <span class="text-uppercase fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.08em;">Skor Kesehatan Kulit</span>
                    <div class="d-flex align-items-baseline justify-content-center gap-1 my-1">
                        <span id="sc-modal-score-val" class="sc-modal-score-num">--</span>
                        <span class="fw-semibold text-primary" style="font-size: 1.2rem;">/100</span>
                    </div>
                    <span id="sc-modal-score-badge" class="badge bg-success-subtle text-success px-3 py-1 rounded-pill fw-semibold" style="font-size: 0.76rem;">Kondisi Terverifikasi</span>
                </div>

                <!-- 4 Metrics Breakdown -->
                <div class="row g-2 mb-4 text-start">
                    <div class="col-6">
                        <div class="sc-metric-mini-item">
                            <span class="d-block text-muted" style="font-size: 0.72rem;">Jerawat</span>
                            <span class="fw-bold text-dark" id="sc-modal-acne">--</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="sc-metric-mini-item">
                            <span class="d-block text-muted" style="font-size: 0.72rem;">Kemerahan</span>
                            <span class="fw-bold text-dark" id="sc-modal-redness">--</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="sc-metric-mini-item">
                            <span class="d-block text-muted" style="font-size: 0.72rem;">Tekstur</span>
                            <span class="fw-bold text-dark" id="sc-modal-texture">--</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="sc-metric-mini-item">
                            <span class="d-block text-muted" style="font-size: 0.72rem;">Pigmentasi</span>
                            <span class="fw-bold text-dark" id="sc-modal-pigment">--</span>
                        </div>
                    </div>
                </div>

                <!-- CTA Button -->
                <a href="#" id="sc-modal-btn-view" class="sc-btn-gold w-100 py-2 fw-bold rounded-pill shadow-sm mb-2 text-decoration-none" style="display:inline-flex; align-items:center; justify-content:center; font-size: 0.98rem;" onclick="window.__faceScanApp && window.__faceScanApp.goToReport(); return false;">
                    <i class="fa-solid fa-file-lines me-2"></i> Buka Laporan Lengkap
                </a>

                <div class="small mt-2 text-muted" style="font-size: 0.76rem;" id="sc-modal-countdown-wrap">
                    <i class="fa-solid fa-arrows-rotate fa-spin me-1 text-primary"></i> Mengalihkan otomatis dalam <span id="sc-modal-countdown" class="fw-bold text-dark">15</span> detik...
                </div>

                <div>
                    <button type="button" class="btn btn-link text-muted btn-sm text-decoration-none mt-2" onclick="window.__faceScanApp && window.__faceScanApp.closeModal()">
                        Tutup & Tetap di Sini
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Embedded Audio Element for 100% Reliable HTML5 Playback -->
<audio id="sc-audio-player" preload="auto" src="<?php echo URLROOT; ?>/public/audio/notification.wav?v=<?php echo time(); ?>"></audio>

<!-- Embedded Base64 Audio URI for Zero-Network Latency Sound -->
<script>
window.__SC_CHIME_B64 = <?php echo json_encode(trim(file_get_contents(APPROOT . '/../public/audio/notification_b64.txt'))); ?>;
</script>

<!-- Bootstrap JS + face-scan module with Cache Buster -->
<script>
// Pass server-generated values to the JS module safely
const SC_CONFIG = {
    uploadUrl:  <?php echo json_encode(URLROOT . '/scan/submitScan'); ?>,
    csrfToken:  <?php echo json_encode($_SESSION['csrf_token'] ?? ''); ?>,
    redirectTo: <?php echo json_encode(URLROOT . '/customer/dashboard'); ?>,
    audioUrl:   <?php echo json_encode(URLROOT . '/public/audio/notification.wav?v=' . time()); ?>,
};
</script>
<script>
window.testAudioDirect = function() {
    if (window.__faceScanApp && typeof window.__faceScanApp.testCompleteFlow === 'function') {
        window.__faceScanApp.testCompleteFlow();
    } else {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (AudioCtx) {
                const ctx = new AudioCtx();
                const now = ctx.currentTime;
                [
                    { f: 880,   o: 0,    d: 0.60, g: 0.85 },
                    { f: 1318.5,o: 0.15, d: 0.75, g: 0.90 },
                    { f: 1760,  o: 0.30, d: 0.90, g: 0.75 }
                ].forEach(n => {
                    const osc = ctx.createOscillator();
                    const g = ctx.createGain();
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(n.f, now + n.o);
                    g.gain.setValueAtTime(0.0001, now + n.o);
                    g.gain.linearRampToValueAtTime(n.g, now + n.o + 0.015);
                    g.gain.exponentialRampToValueAtTime(0.0001, now + n.o + n.d);
                    osc.connect(g);
                    g.connect(ctx.destination);
                    osc.start(now + n.o);
                    osc.stop(now + n.o + n.d + 0.05);
                });
            }
        } catch(e) {}
        const m = document.getElementById('scScanCompleteModal');
        if (m) {
            if (m.parentElement !== document.body) document.body.appendChild(m);
            m.style.display = 'flex';
            m.classList.add('show');
            const sc = document.getElementById('sc-modal-score-val');
            if (sc) sc.textContent = '94';
        }
    }
};
</script>
<script src="<?php echo URLROOT; ?>/public/js/face-scan.js?v=<?php echo time(); ?>"></script>
<script>
function initApp() {
    if (!window.__faceScanApp) {
        window.__faceScanApp = new FaceScanApp(SC_CONFIG);
    }
}
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initApp);
} else {
    initApp();
}
</script>

<?php require APPROOT . '/views/templates/customer_footer.php'; ?>
