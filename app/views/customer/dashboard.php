<?php
// Helpers for score and sunscreen badges
function scoreClass($score)
{
    if ($score === null) return '';
    if ($score >= 75) return 'good';
    if ($score >= 50) return 'fair';
    return 'poor';
}

function scoreStatusLabel($score)
{
    if ($score === null) return 'Belum Dianalisis';
    if ($score >= 80) return 'Sangat Baik (Optimal)';
    if ($score >= 65) return 'Baik (Terkontrol)';
    if ($score >= 50) return 'Cukup (Perlu Perawatan)';
    return 'Memerlukan Perhatian Khusus';
}

function sunBadgeClass($status)
{
    $map = [
        'Protected' => 'protected',
        'Partially Protected' => 'partial',
        'Unprotected' => 'unprotected',
        'Belum Dicek' => 'neutral'
    ];
    return $map[$status] ?? 'neutral';
}

function sunIcon($status)
{
    if ($status === 'Protected') return '<i class="fa-solid fa-circle-check"></i>';
    if ($status === 'Partially Protected') return '<i class="fa-solid fa-shield-halved"></i>';
    if ($status === 'Unprotected') return '<i class="fa-solid fa-circle-exclamation"></i>';
    return '<i class="fa-regular fa-clock"></i>';
}

$ls = $latest_scan; // shorthand for latest scan

require APPROOT . '/views/templates/customer_header.php';
?>



<!-- ── Clean Welcome Header ───────────────────────────────── -->
<div class="sc-dashboard-hero">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
            <h1 class="h3 fw-bold mb-1 text-dark" style="letter-spacing:-0.02em;">
                Halo, <span class="sc-highlight-gold"><?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?></span>
            </h1>
            <p class="text-muted mb-0 small">
                <?php if ($ls): ?>
                    Pemindaian terakhir: <strong class="text-dark"><?php echo date('d M Y, H:i', strtotime($ls->created_at)); ?> WIB</strong>
                <?php else: ?>
                    Siap melakukan pemeriksaan kulit pertama Anda hari ini.
                <?php endif; ?>
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo URLROOT; ?>/scan/faceScan" class="btn sc-btn-gold rounded-pill px-4 py-2 small">
                <i class="fa-solid fa-camera me-2"></i>Mulai Scan Wajah
            </a>
        </div>
    </div>
</div>

<!-- ── Quick Actions ──────────────────────────────────────── -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3 d-flex">
        <a href="<?php echo URLROOT; ?>/scan/faceScan" class="sc-action-card text-decoration-none w-100">
            <div class="sc-action-icon" style="background:rgba(15, 118, 110, 0.1); color:#0f766e;">
                <i class="fa-solid fa-camera"></i>
            </div>
            <div class="flex-grow-1 overflow-hidden">
                <h6 class="text-truncate">Scan Wajah</h6>
                <p class="text-truncate">Analisis 4 indikator</p>
            </div>
            <i class="fa-solid fa-chevron-right sc-action-arrow"></i>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-xl-3 d-flex">
        <a href="<?php echo URLROOT; ?>/sunscreen/check" class="sc-action-card text-decoration-none w-100">
            <div class="sc-action-icon" style="background:rgba(217, 119, 6, 0.1); color:#b45309;">
                <i class="fa-solid fa-sun"></i>
            </div>
            <div class="flex-grow-1 overflow-hidden">
                <h6 class="text-truncate">Cek Sunscreen</h6>
                <p class="text-truncate">Kerataan lapisan UV</p>
            </div>
            <i class="fa-solid fa-chevron-right sc-action-arrow"></i>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-xl-3 d-flex">
        <a href="<?php echo URLROOT; ?>/customer/aiAssistant" class="sc-action-card text-decoration-none w-100">
            <div class="sc-action-icon" style="background:rgba(14, 165, 233, 0.1); color:#0284c7;">
                <i class="fa-solid fa-comment-medical"></i>
            </div>
            <div class="flex-grow-1 overflow-hidden">
                <h6 class="text-truncate">Konsultasi AI</h6>
                <p class="text-truncate">Tanya produk &amp; kulit</p>
            </div>
            <i class="fa-solid fa-chevron-right sc-action-arrow"></i>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-xl-3 d-flex">
        <a href="<?php echo URLROOT; ?>/customer/history" class="sc-action-card text-decoration-none w-100">
            <div class="sc-action-icon" style="background:rgba(139, 92, 246, 0.1); color:#7c3aed;">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div class="flex-grow-1 overflow-hidden">
                <h6 class="text-truncate">Riwayat</h6>
                <p class="text-truncate"><?php echo $scan_count; ?> rekaman</p>
            </div>
            <i class="fa-solid fa-chevron-right sc-action-arrow"></i>
        </a>
    </div>
</div>

<!-- ── Primary Analytics & Sunscreen Protection Grid ─────── -->
<div class="row g-4 mb-4">

    <!-- Latest Skin Condition -->
    <div class="col-lg-8">
        <div class="sc-card h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="sc-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-microscope text-primary"></i>
                        <h5 class="mb-0 fw-bold text-dark">Kondisi Kulit Terakhir</h5>
                    </div>
                    <?php if ($ls): ?>
                        <span class="badge sc-badge-date small">
                            <?php echo date('d M Y, H:i', strtotime($ls->created_at)); ?> WIB
                        </span>
                    <?php endif; ?>
                </div>
                
                <div class="sc-card-body">
                    <?php if ($ls): ?>
                        <div class="row align-items-center g-4">
                            <div class="col-md-auto text-center px-md-3">
                                <?php $sc = scoreClass($ls->overall_score); ?>
                                <div class="sc-score-circle <?php echo $sc; ?> mx-auto mb-2">
                                    <?php echo $ls->overall_score ?? '--'; ?>
                                </div>
                                <div class="fw-bold text-dark" style="font-size:0.88rem;">
                                    Skor Keseluruhan
                                </div>
                                <div class="text-muted" style="font-size:0.75rem;">
                                    <?php echo scoreStatusLabel($ls->overall_score); ?>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="sc-metric-bar">
                                    <div class="sc-metric-bar-header">
                                        <span class="sc-metric-bar-label fw-semibold">
                                            <i class="fa-solid fa-circle" style="color:var(--color-acne);font-size:0.5rem;"></i>Jerawat &amp; Pori
                                        </span>
                                        <span class="sc-metric-bar-value" style="color:var(--color-acne);"><?php echo $ls->acne_score ?? 0; ?> / 100</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" style="width:<?php echo $ls->acne_score ?? 0; ?>%;background:var(--color-acne);"></div>
                                    </div>
                                </div>
                                <div class="sc-metric-bar">
                                    <div class="sc-metric-bar-header">
                                        <span class="sc-metric-bar-label fw-semibold">
                                            <i class="fa-solid fa-circle" style="color:var(--color-redness);font-size:0.5rem;"></i>Kemerahan
                                        </span>
                                        <span class="sc-metric-bar-value" style="color:var(--color-redness);"><?php echo $ls->redness_score ?? 0; ?> / 100</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" style="width:<?php echo $ls->redness_score ?? 0; ?>%;background:var(--color-redness);"></div>
                                    </div>
                                </div>
                                <div class="sc-metric-bar">
                                    <div class="sc-metric-bar-header">
                                        <span class="sc-metric-bar-label fw-semibold">
                                            <i class="fa-solid fa-circle" style="color:var(--color-texture);font-size:0.5rem;"></i>Tekstur Kulit
                                        </span>
                                        <span class="sc-metric-bar-value" style="color:var(--color-texture);"><?php echo $ls->texture_score ?? 0; ?> / 100</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" style="width:<?php echo $ls->texture_score ?? 0; ?>%;background:var(--color-texture);"></div>
                                    </div>
                                </div>
                                <div class="sc-metric-bar">
                                    <div class="sc-metric-bar-header">
                                        <span class="sc-metric-bar-label fw-semibold">
                                            <i class="fa-solid fa-circle" style="color:var(--color-hyperpig);font-size:0.5rem;"></i>Pigmentasi
                                        </span>
                                        <span class="sc-metric-bar-value" style="color:var(--color-hyperpig);"><?php echo $ls->hyperpigmentation_score ?? 0; ?> / 100</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" style="width:<?php echo $ls->hyperpigmentation_score ?? 0; ?>%;background:var(--color-hyperpig);"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="sc-empty-state py-4 text-center">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-3 text-primary" style="width:56px;height:56px;background:var(--primary-tint);">
                                <i class="fa-solid fa-camera fs-5"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Belum Ada Hasil Scan</h6>
                            <p class="text-muted small mb-3">Ambil foto wajah Anda untuk melihat 4 skor indikator kulit.</p>
                            <a href="<?php echo URLROOT; ?>/scan/faceScan" class="btn sc-btn-gold btn-sm rounded-pill px-4">
                                Mulai Scan
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($ls): ?>
                <div class="px-4 py-3 border-top bg-light bg-opacity-50 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span class="text-muted small">Lihat observasi &amp; catatan lengkap pemindaian ini.</span>
                    <a href="<?php echo URLROOT; ?>/customer/scanDetail/<?php echo $ls->id; ?>" class="btn sc-btn-outline-gold btn-sm rounded-pill px-3">
                        Detail Lengkap <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sunscreen Protection Card -->
    <div class="col-lg-4">
        <div class="sc-card h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="sc-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-sun text-warning"></i>
                        <h5 class="mb-0 fw-bold text-dark">Proteksi UV</h5>
                    </div>
                </div>
                
                <div class="sc-card-body">
                    <?php if ($ls && $ls->sunscreen_coverage_score !== null): ?>
                        <?php $sunStatus = $ls->sunscreen_status ?? 'Unprotected'; ?>
                        <div class="text-center py-1">
                            <div class="position-relative mx-auto mb-2" style="width:104px;height:104px;">
                                <svg viewBox="0 0 36 36" style="transform:rotate(-90deg);width:100%;height:100%;">
                                    <circle cx="18" cy="18" r="15.9155" fill="none" stroke="#e6efec" stroke-width="3" />
                                    <circle cx="18" cy="18" r="15.9155" fill="none"
                                        stroke="<?php echo $sunStatus === 'Protected' ? 'var(--color-score-good)' : ($sunStatus === 'Partially Protected' ? 'var(--color-score-fair)' : 'var(--color-score-poor)'); ?>"
                                        stroke-width="3.2"
                                        stroke-dasharray="<?php echo $ls->sunscreen_coverage_score; ?> <?php echo 100 - $ls->sunscreen_coverage_score; ?>"
                                        stroke-linecap="round" />
                                </svg>
                                <div class="position-absolute top-50 start-50 translate-middle fw-bold text-primary"
                                    style="font-size:1.35rem;font-family:'Outfit',sans-serif;">
                                    <?php echo $ls->sunscreen_coverage_score; ?>%
                                </div>
                            </div>

                            <div class="mb-3">
                                <span class="sc-sun-badge <?php echo sunBadgeClass($sunStatus); ?>">
                                    <?php echo sunIcon($sunStatus); ?> <?php echo htmlspecialchars($sunStatus); ?>
                                </span>
                            </div>

                            <div class="sc-uv-stats-grid mb-0">
                                <div class="sc-uv-stat-pill">
                                    <div class="val"><?php echo $ls->sunscreen_coverage_score; ?>%</div>
                                    <div class="lbl">Cakupan</div>
                                </div>
                                <div class="sc-uv-stat-pill">
                                    <div class="val">~2.5 Jam</div>
                                    <div class="lbl">Durasi</div>
                                </div>
                                <div class="sc-uv-stat-pill">
                                    <div class="val">SPF 30+</div>
                                    <div class="lbl">Standar</div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <div class="position-relative mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:92px;height:92px;border-radius:50%;background:var(--primary-tint-soft);border:2px dashed rgba(15,118,110,0.3);">
                                <i class="fa-solid fa-shield-halved text-primary" style="font-size:2rem;"></i>
                            </div>
                            <p class="fw-semibold text-dark small mb-2">Belum Dicek Hari Ini</p>
                            <div class="sc-uv-stats-grid mb-0">
                                <div class="sc-uv-stat-pill">
                                    <div class="val">SPF 30+</div>
                                    <div class="lbl">UV-B</div>
                                </div>
                                <div class="sc-uv-stat-pill">
                                    <div class="val">PA++++</div>
                                    <div class="lbl">UV-A</div>
                                </div>
                                <div class="sc-uv-stat-pill">
                                    <div class="val">2 Jari</div>
                                    <div class="lbl">Takaran</div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="p-3 pt-0">
                <a href="<?php echo URLROOT; ?>/sunscreen/check" class="btn sc-btn-outline-gold btn-sm w-100 rounded-pill py-2">
                    <i class="fa-solid fa-sun me-2"></i>Cek Sunscreen
                </a>
            </div>
        </div>
    </div>

</div>

<!-- ── Recent History Table ──────────────────────────────── -->
<div class="sc-card">
    <div class="sc-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="fa-solid fa-clock-rotate-left text-primary"></i>
            <h5 class="mb-0 fw-bold text-dark">Riwayat Terbaru</h5>
            <span class="badge sc-badge-date px-2 py-1 rounded-pill small">
                <?php echo $scan_count; ?>
            </span>
        </div>
        <a href="<?php echo URLROOT; ?>/customer/history" class="btn sc-btn-outline-gold btn-sm rounded-pill px-3">
            Semua Riwayat <i class="fa-solid fa-arrow-right ms-1"></i>
        </a>
    </div>

    <?php if (!empty($recent_scans)): ?>
        <div class="table-responsive">
            <table class="table sc-history-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jerawat</th>
                        <th>Kemerahan</th>
                        <th>Proteksi UV</th>
                        <th>Skor</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_scans as $s):
                        $sunSt = !empty($s->sunscreen_status) ? $s->sunscreen_status : 'Belum Dicek';
                        $sc = scoreClass($s->overall_score);
                    ?>
                        <tr>
                            <td>
                                <div class="fw-semibold text-dark"><?php echo date('d M Y', strtotime($s->created_at)); ?></div>
                                <small class="text-muted"><?php echo date('H:i', strtotime($s->created_at)); ?> WIB</small>
                            </td>
                            <td><span class="badge sc-badge-metric"><?php echo $s->acne_score ?? '--'; ?></span></td>
                            <td><span class="badge sc-badge-metric"><?php echo $s->redness_score ?? '--'; ?></span></td>
                            <td>
                                <span class="sc-sun-badge <?php echo sunBadgeClass($sunSt); ?>">
                                    <?php echo sunIcon($sunSt); ?> <?php echo htmlspecialchars($sunSt); ?>
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold" style="color:var(--<?php echo $sc === 'good' ? 'color-score-good' : ($sc === 'fair' ? 'color-score-fair' : 'color-score-poor'); ?>);">
                                    <?php echo $s->overall_score ?? '--'; ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="<?php echo URLROOT; ?>/customer/scanDetail/<?php echo $s->id; ?>" class="btn sc-btn-outline-glass btn-sm rounded-pill px-3">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="sc-empty-state py-4 text-center">
            <i class="fa-regular fa-folder-open mb-2 fs-3 text-muted"></i>
            <p class="text-muted small mb-0">Belum ada riwayat pemindaian.</p>
        </div>
    <?php endif; ?>
</div>

<?php require APPROOT . '/views/templates/customer_footer.php'; ?>