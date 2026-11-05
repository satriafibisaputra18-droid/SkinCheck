<?php
function sunBadgeClass($st) {
    $m = ['Sangat Merata' => 'protected', 'Protected' => 'protected', 'Cukup Merata' => 'partial', 'Partially Protected' => 'partial', 'Kurang Merata' => 'unprotected', 'Unprotected' => 'unprotected'];
    return $m[$st] ?? 'unprotected';
}

function sunIcon($st) {
    if (in_array($st, ['Sangat Merata', 'Protected'], true)) return '<i class="fa-solid fa-shield-check"></i>';
    if (in_array($st, ['Cukup Merata', 'Partially Protected'], true)) return '<i class="fa-solid fa-shield-halved"></i>';
    return '<i class="fa-solid fa-shield-slash"></i>';
}

$ls = $latest_scan ?? null;

require APPROOT . '/views/templates/customer_header.php';
?>

<!-- ── Breadcrumb ────────────────────────────────────────── -->
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="<?php echo URLROOT; ?>/customer/dashboard" class="btn btn-sm btn-outline-primary rounded-pill px-3">
        <i class="fa-solid fa-arrow-left me-1"></i>Dashboard
    </a>
    <span class="text-muted small">/ Sunscreen Check</span>
</div>

<div class="row g-4 align-items-start">

    <!-- ═══ Left: Camera Console ═════════════════════════════════ -->
    <div class="col-lg-7">
        <div class="sc-card">
            <div class="sc-card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="mb-0"><i class="fa-solid fa-sun me-2 text-warning"></i>Pemeriksaan Sunscreen</h5>
                </div>
                <span class="badge sc-badge-gold rounded-pill px-2.5 py-1" style="font-size:.74rem;">
                    <i class="fa-solid fa-camera me-1"></i>RGB Visual Estimation
                </span>
            </div>

            <div class="sc-card-body">

                <!-- Permission error panel -->
                <div id="sc-sun-perm-error" class="d-none mb-3">
                    <div class="alert alert-danger border-0 mb-0">
                        <div class="d-flex gap-3 align-items-start">
                            <i class="fa-solid fa-camera-slash fs-4 mt-1 flex-shrink-0"></i>
                            <div>
                                <p class="fw-bold mb-1 sc-perm-title">Akses Kamera Diperlukan</p>
                                <p class="mb-2 small sc-perm-msg">Tidak dapat mengakses kamera.</p>
                                <button class="btn btn-danger btn-sm" onclick="window.__sunscreenCheckApp.startCamera()">
                                    <i class="fa-solid fa-rotate-right me-1"></i>Coba Lagi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Validation banner -->
                <div id="sc-sun-validation-result" class="d-none"></div>

                <!-- ── Camera Viewport ──────────────────────────────── -->
                <div class="sc-cam-viewport mb-3" id="sc-sun-viewport">

                    <!-- Placeholder -->
                    <div id="sc-sun-placeholder" class="sc-cam-placeholder">
                        <i class="fa-solid fa-sun fs-1 mb-2" style="color:var(--secondary-color); opacity:0.8;"></i>
                        <p class="fw-semibold mb-1 text-white">Kamera Belum Aktif</p>
                        <small class="text-white-50">Klik "Aktifkan Kamera" untuk memeriksa pemerataan sunscreen</small>
                    </div>

                    <!-- Live Video Feed -->
                    <video id="sc-sun-video"
                           class="sc-cam-video d-none"
                           autoplay playsinline muted></video>

                    <!-- Preview of captured image -->
                    <img id="sc-sun-preview"
                         class="sc-cam-video d-none"
                         alt="Preview foto sunscreen">

                    <!-- HUD Overlay: Face framing guide & zones -->
                    <div id="sc-sun-hud" class="sc-cam-hud d-none">
                        <!-- Oval guide -->
                        <svg class="sc-face-guide" viewBox="0 0 100 100" preserveAspectRatio="none">
                            <ellipse cx="50" cy="50" rx="30" ry="40"
                                     fill="none"
                                     stroke="rgba(191,161,95,0.85)"
                                     stroke-width="0.9"
                                     stroke-dasharray="4 2"/>
                        </svg>
                        <!-- Corner brackets -->
                        <div class="sc-bracket sc-bracket-tl"></div>
                        <div class="sc-bracket sc-bracket-tr"></div>
                        <div class="sc-bracket sc-bracket-bl"></div>
                        <div class="sc-bracket sc-bracket-br"></div>
                        <!-- HUD instruction label -->
                        <div class="sc-cam-instruction">
                            <i class="fa-solid fa-face-smile me-1"></i>
                            Posisikan wajah di dalam area oval
                        </div>
                    </div>

                    <canvas id="sc-sun-canvas" class="d-none"></canvas>
                </div>

                <!-- ── Upload Progress ──────────────────────────────── -->
                <div id="sc-sun-upload-progress" class="d-none mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Mengestimasi pemerataan sunscreen…</span>
                    </div>
                    <div class="progress" style="height:8px; border-radius:99px;">
                        <div id="sc-sun-progress-bar"
                             class="progress-bar progress-bar-striped progress-bar-animated"
                             role="progressbar"
                             style="width:0%; background:var(--secondary-color);"
                             aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                </div>

                <!-- ── Status Message ───────────────────────────────── -->
                <p id="sc-sun-status-msg" class="sc-status-msg small text-muted text-center mt-2 mb-3">
                    Pastikan sunscreen telah diaplikasikan sebelum mengambil foto.
                </p>

                <!-- ── Action Buttons ───────────────────────────────── -->
                <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap">
                    <button id="sc-sun-btn-start" class="btn btn-primary px-4">
                        <i class="fa-solid fa-video me-2"></i>Aktifkan Kamera
                    </button>

                    <button id="sc-sun-btn-capture" class="btn btn-secondary px-4 d-none">
                        <i class="fa-solid fa-camera me-2"></i>Ambil Foto
                    </button>

                    <button id="sc-sun-btn-retake" class="btn btn-outline-secondary d-none">
                        <i class="fa-solid fa-rotate-left me-1"></i>Ulangi
                    </button>

                    <button id="sc-sun-btn-submit" class="btn btn-primary px-4 d-none">
                        <i class="fa-solid fa-sun me-2"></i>Periksa Sunscreen
                    </button>

                    <button id="sc-sun-btn-stop" class="btn btn-outline-danger btn-sm d-none">
                        <i class="fa-solid fa-video-slash me-1"></i>Stop
                    </button>
                </div>

            </div><!-- /.sc-card-body -->
        </div><!-- /.sc-card -->

        <!-- ═══ Technical Limitation Notice ═══════════════════════ -->
        <div class="sc-advisory-banner mt-3">
            <div class="d-flex gap-3 align-items-start">
                <i class="fa-solid fa-circle-info fs-5 mt-1" style="color: #dfc68b; flex-shrink: 0;"></i>
                <div style="font-size: 0.82rem; line-height: 1.55;">
                    <div>
                        <strong class="sc-advisory-title">Metodologi Pemeriksaan:</strong>
                        <span style="color: #c9ded5;"> AI-based visual sunscreen coverage estimation (Standard RGB Camera).</span>
                    </div>
                    <div class="sc-advisory-quote">
                        "Hasil ini merupakan estimasi visual berbasis AI dan bukan pengukuran UV langsung."
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ Right: Lighting Guidance & Latest Summary ════════════ -->
    <div class="col-lg-5">

        <!-- Latest Sunscreen Coverage Card -->
        <?php if ($ls && $ls->sunscreen_coverage_score !== null) :
            $sunSt = $ls->sunscreen_status ?? 'Cukup Merata';
        ?>
            <div class="sc-card mb-4">
                <div class="sc-card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fa-solid fa-clock-rotate-left me-2" style="color:var(--secondary-color);"></i>Hasil Terakhir</h5>
                    <a href="<?php echo URLROOT; ?>/sunscreen/result/<?php echo $ls->id; ?>" class="btn btn-sm btn-outline-secondary">Detail</a>
                </div>
                <div class="sc-card-body text-center p-3">
                    <div class="d-flex align-items-center justify-content-center gap-4">
                        <div class="position-relative" style="width:84px; height:84px;">
                            <svg viewBox="0 0 36 36" style="transform:rotate(-90deg);width:100%;height:100%;">
                                <circle cx="18" cy="18" r="15.9155" fill="none" stroke="#eef0ee" stroke-width="3.5"/>
                                <circle cx="18" cy="18" r="15.9155" fill="none"
                                    stroke="<?php echo in_array($sunSt, ['Sangat Merata', 'Protected'], true) ? '#27ae60' : (in_array($sunSt, ['Cukup Merata', 'Partially Protected'], true) ? '#f39c12' : '#e74c3c'); ?>"
                                    stroke-width="3.5"
                                    stroke-dasharray="<?php echo $ls->sunscreen_coverage_score; ?> <?php echo 100 - $ls->sunscreen_coverage_score; ?>"
                                    stroke-linecap="round"/>
                            </svg>
                            <div class="position-absolute top-50 start-50 translate-middle fw-bold" style="font-size:1.1rem; color:var(--primary-color);">
                                <?php echo $ls->sunscreen_coverage_score; ?>%
                            </div>
                        </div>
                        <div class="text-start">
                            <span class="sc-sun-badge <?php echo sunBadgeClass($sunSt); ?> mb-1">
                                <?php echo sunIcon($sunSt); ?> <?php echo htmlspecialchars($sunSt); ?>
                            </span>
                            <p class="text-muted mb-0 small" style="font-size:0.75rem;">
                                Diperiksa: <?php echo date('d M Y, H:i', strtotime($ls->created_at)); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Lighting Guidance Card -->
        <div class="sc-card mb-4">
            <div class="sc-card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0 text-white"><i class="fa-solid fa-lightbulb me-2" style="color:var(--secondary-color);"></i>Panduan Pencahayaan</h5>
                <span class="badge" style="background: rgba(191,161,95,0.15); color: #dfc68b; font-size: 0.72rem; border: 1px solid rgba(191,161,95,0.3);">Standar Klinis</span>
            </div>
            <div class="sc-card-body">
                <p class="small mb-3" style="color: #94a3b8; font-size: 0.8rem;">
                    Pencahayaan yang merata penting agar estimasi pantulan visual sunscreen dapat dihitung secara akurat.
                </p>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="sc-lighting-tip good">
                            <div class="sc-lighting-header">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Pencahayaan Baik</span>
                            </div>
                            <ul class="sc-lighting-list">
                                <li class="sc-lighting-item">
                                    <i class="fa-solid fa-sun"></i>
                                    <span>Cahaya alami dari arah depan</span>
                                </li>
                                <li class="sc-lighting-item">
                                    <i class="fa-solid fa-lightbulb"></i>
                                    <span>Lampu ruangan menyebar merata</span>
                                </li>
                                <li class="sc-lighting-item">
                                    <i class="fa-solid fa-arrows-to-eye"></i>
                                    <span>Posisi wajah tegak lurus kamera</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="sc-lighting-tip bad">
                            <div class="sc-lighting-header">
                                <i class="fa-solid fa-circle-xmark"></i>
                                <span>Hindari</span>
                            </div>
                            <ul class="sc-lighting-list">
                                <li class="sc-lighting-item">
                                    <i class="fa-solid fa-moon"></i>
                                    <span>Ruangan redup atau gelap</span>
                                </li>
                                <li class="sc-lighting-item">
                                    <i class="fa-solid fa-circle-half-stroke"></i>
                                    <span>Cahaya samping (bayangan tajam)</span>
                                </li>
                                <li class="sc-lighting-item">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                    <span>Membelakangi jendela (backlight)</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- How Sunscreen Evenness Check Works -->
        <div class="sc-card">
            <div class="sc-card-header">
                <h5><i class="fa-solid fa-circle-question me-2" style="color:var(--primary-color);"></i>Cara Kerja Pemeriksaan</h5>
            </div>
            <div class="sc-card-body">
                <ol class="list-unstyled mb-0">
                    <?php
                    $steps = [
                        ['fa-pump-soap', 'Aplikasikan Sunscreen', 'Gunakan sunscreen minimal 2 jari dan ratakan ke seluruh area wajah.'],
                        ['fa-camera',    'Ambil Foto Wajah',     'Posisikan wajah di dalam frame oval dengan cahaya depan.'],
                        ['fa-microchip', 'Estimasi Visual AI',   'AI menganalisis profil kilau dan pantulan permukaan pada 6 zona wajah.'],
                        ['fa-map',       'Peta Heatmap & Skor',  'Dapatkan skor persentase cakupan serta area yang perlu ditebalkan.'],
                    ];
                    foreach ($steps as $i => $step) : ?>
                        <li class="d-flex gap-3 mb-3">
                            <div class="sc-step-num"><?php echo $i + 1; ?></div>
                            <div>
                                <p class="fw-semibold mb-0 small"><?php echo $step[1]; ?></p>
                                <p class="text-muted mb-0" style="font-size:.76rem;"><?php echo $step[2]; ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>

    </div><!-- /.col-lg-5 -->

</div><!-- /.row -->

<!-- ── Custom Styles ─────────────────────────────────────── -->
<style>
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
img.sc-cam-video { transform: none; }
.sc-cam-placeholder {
    position: absolute; inset: 0;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    color: rgba(255,255,255,.45);
    gap: .4rem;
    font-size: .85rem;
}
.sc-cam-hud { position: absolute; inset: 0; pointer-events: none; }
.sc-face-guide { position: absolute; inset: 0; width: 100%; height: 100%; }
.sc-bracket { position: absolute; width: 22px; height: 22px; border-color: rgba(191,161,95,.9); border-style: solid; }
.sc-bracket-tl { top: 10%; left: 10%; border-width: 3px 0 0 3px; border-radius: 4px 0 0 0; }
.sc-bracket-tr { top: 10%; right: 10%; border-width: 3px 3px 0 0; border-radius: 0 4px 0 0; }
.sc-bracket-bl { bottom: 10%; left: 10%; border-width: 0 0 3px 3px; border-radius: 0 0 0 4px; }
.sc-bracket-br { bottom: 10%; right: 10%; border-width: 0 3px 3px 0; border-radius: 0 0 4px 0; }
.sc-cam-instruction {
    position: absolute;
    bottom: 12px; left: 50%; transform: translateX(-50%);
    background: rgba(0,0,0,.6);
    color: #fff;
    font-size: .75rem;
    font-weight: 600;
    padding: .3rem .85rem;
    border-radius: 99px;
    white-space: nowrap;
    backdrop-filter: blur(4px);
}
.sc-step-num {
    width: 26px; height: 26px;
    border-radius: 50%;
    background: var(--primary-color);
    color: #fff;
    font-size: .78rem; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
.sc-status-msg { min-height: 1.3rem; }
</style>

<!-- Bootstrap JS + sunscreen-check module -->
<script>
const SUNSCREEN_CONFIG = {
    uploadUrl:  <?php echo json_encode(URLROOT . '/sunscreen/submitCheck'); ?>,
    csrfToken:  <?php echo json_encode($_SESSION['csrf_token'] ?? ''); ?>,
    redirectTo: <?php echo json_encode(URLROOT . '/customer/dashboard'); ?>,
};
</script>
<script src="<?php echo URLROOT; ?>/public/js/sunscreen-check.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    window.__sunscreenCheckApp = new SunscreenCheckApp(SUNSCREEN_CONFIG);
});
</script>

<?php require APPROOT . '/views/templates/customer_footer.php'; ?>
