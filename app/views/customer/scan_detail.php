<?php
// Visual scoring helper
function scoreClass($s) {
    if ($s === null || $s === 0) return 'poor';
    return $s >= 75 ? 'good' : ($s >= 50 ? 'fair' : 'poor');
}

function sunBadgeClass($st) {
    $m = ['Protected' => 'protected', 'Partially Protected' => 'partial', 'Unprotected' => 'unprotected'];
    return $m[$st] ?? 'unprotected';
}

function sunIcon($st) {
    if ($st === 'Protected') return '<i class="fa-solid fa-shield-check"></i>';
    if ($st === 'Partially Protected') return '<i class="fa-solid fa-shield-halved"></i>';
    return '<i class="fa-solid fa-shield-slash"></i>';
}

$status = $scan->status ?? 'completed';
$isFailed = ($status === 'failed');
$isProcessing = ($status === 'processing' || $status === 'pending');
$confidencePct = !empty($scan->confidence) ? round($scan->confidence * 100) : null;

require APPROOT . '/views/templates/customer_header.php';
?>

<!-- ── Breadcrumb & Action Toolbar ──────────────────────── -->
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div class="d-flex align-items-center gap-2">
        <a href="<?php echo URLROOT; ?>/customer/history" class="btn btn-sm btn-outline-primary rounded-pill px-3">
            <i class="fa-solid fa-arrow-left me-1"></i>Riwayat Scan
        </a>
        <span class="text-muted small">/ Detail Scan #<?php echo $scan->id; ?></span>
    </div>
    <div class="d-flex gap-2">
        <?php if ($isFailed) : ?>
            <form action="<?php echo URLROOT; ?>/scan/retryAnalysis/<?php echo $scan->id; ?>" method="POST" class="d-inline">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <button type="submit" class="btn btn-sm btn-warning">
                    <i class="fa-solid fa-rotate-right me-1"></i>Analisis Ulang (Retry)
                </button>
            </form>
        <?php endif; ?>
        <a href="<?php echo URLROOT; ?>/scan/faceScan" class="btn btn-sm btn-primary">
            <i class="fa-solid fa-camera me-1"></i>Scan Baru
        </a>
    </div>
</div>



<!-- ── Failed State Warning Banner ──────────────────────── -->
<?php if ($isFailed) : ?>
    <div class="alert alert-danger border-0 mb-4 p-4 shadow-sm" style="border-radius: var(--border-radius);">
        <div class="d-flex gap-3 align-items-start">
            <div class="rounded-circle bg-danger text-white p-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;flex-shrink:0;">
                <i class="fa-solid fa-triangle-exclamation fs-5"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="fw-bold text-danger mb-1">Analisis Visual AI Mengalami Kendala</h5>
                <p class="mb-2 small text-dark">
                    <?php echo htmlspecialchars($scan->error_message ?? 'Sistem tidak dapat menyelesaikan estimasi indikator visual pada foto ini.'); ?>
                </p>
                <form action="<?php echo URLROOT; ?>/scan/retryAnalysis/<?php echo $scan->id; ?>" method="POST" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                    <button type="submit" class="btn btn-danger btn-sm px-3">
                        <i class="fa-solid fa-rotate-right me-1"></i>Coba Analisis Ulang
                    </button>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- ── Main Grid Layout ──────────────────────────────────── -->
<div class="row g-4">

    <!-- ═══ Left Column: Scanned Photo & Summary ════════════════ -->
    <div class="col-lg-4">

        <!-- Scanned Photo Card -->
        <div class="sc-card mb-4 overflow-hidden">
            <div class="sc-card-header">
                <h5><i class="fa-solid fa-image me-2" style="color:var(--primary-color);"></i>Foto Scan</h5>
                <span class="badge <?php echo $status === 'completed' ? 'bg-success-subtle text-success border border-success-subtle' : ($isFailed ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle'); ?>">
                    <?php echo ucfirst($status); ?>
                </span>
            </div>
            <div class="sc-card-body text-center p-3">
                <?php if (!empty($scan->image_path)) : ?>
                    <div class="position-relative rounded overflow-hidden shadow-sm bg-dark mx-auto" style="max-height: 280px; max-width: 100%;">
                        <img src="<?php echo URLROOT; ?>/scan/serveImage/<?php echo $scan->id; ?>"
                             class="w-100 h-100 object-fit-cover"
                             style="max-height:280px;"
                             alt="Foto Wajah Scan #<?php echo $scan->id; ?>">
                        <div class="position-absolute bottom-0 start-0 end-0 p-2 bg-dark bg-opacity-50 text-white small text-start d-flex justify-content-between">
                            <span><i class="fa-solid fa-calendar me-1"></i><?php echo date('d M Y, H:i', strtotime($scan->created_at)); ?></span>
                            <?php if ($confidencePct !== null) : ?>
                                <span class="badge bg-success"><i class="fa-solid fa-shield-check me-1"></i>Keyakinan: <?php echo $confidencePct; ?>%</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="sc-empty-state py-4">
                        <i class="fa-solid fa-image-slash"></i>
                        <p class="small">File foto tidak tersedia</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Overall Score Card -->
        <div class="sc-card mb-4">
            <div class="sc-card-body text-center">
                <?php $sc = scoreClass($scan->overall_score); ?>
                <div class="mb-2" style="font-size:0.75rem;color:#7a8a84;font-weight:700;text-transform:uppercase;letter-spacing:.06em;">
                    Skor Indikator Keseluruhan
                </div>
                
                <div class="sc-detail-score <?php echo $sc; ?>"
                     style="color:var(--<?php echo $sc === 'good' ? 'color-score-good' : ($sc === 'fair' ? 'color-score-fair' : 'color-score-poor'); ?>);">
                    <?php echo ($status === 'completed' && $scan->overall_score) ? $scan->overall_score : '--'; ?>
                </div>
                <div class="text-muted mb-3" style="font-size:0.78rem;">skala estimasi 0 - 100</div>

                <div class="sc-score-circle <?php echo $sc; ?> mx-auto mb-3" style="width:70px;height:70px;font-size:1.3rem;">
                    <?php echo ($status === 'completed' && $scan->overall_score) ? $scan->overall_score : '--'; ?>
                </div>

                <?php if ($confidencePct !== null) : ?>
                    <div class="sc-badge-confidence py-2 px-3 mb-3">
                        <i class="fa-solid fa-microchip me-1" style="color: #60a5fa;"></i>Tingkat Keyakinan: <strong><?php echo $confidencePct; ?>%</strong>
                    </div>
                <?php endif; ?>

                <hr class="my-3">
                <div class="d-flex justify-content-between small mb-2">
                    <span class="text-muted">Tanggal Scan</span>
                    <span class="fw-semibold"><?php echo date('d M Y, H:i', strtotime($scan->created_at)); ?></span>
                </div>
                <div class="d-flex justify-content-between small mb-2">
                    <span class="text-muted">Kualitas Citra</span>
                    <span class="fw-semibold"><?php echo $scan->image_quality_score !== null ? $scan->image_quality_score . '%' : 'N/A'; ?></span>
                </div>
                <div class="d-flex justify-content-between small">
                    <span class="text-muted">Engine Analisis</span>
                    <span class="text-muted small"><?php echo htmlspecialchars($scan->provider_name ?? 'SkinCheck AI'); ?></span>
                </div>
            </div>
        </div>

        <!-- Sunscreen Detail -->
        <div class="sc-card">
            <div class="sc-card-header">
                <h5 style="font-size:0.9rem;"><i class="fa-solid fa-sun me-2" style="color:var(--secondary-color);"></i>Proteksi Sunscreen</h5>
            </div>
            <div class="sc-card-body text-center">
                <?php $sunSt = $scan->sunscreen_status ?? null; ?>
                <?php if ($sunSt && $scan->sunscreen_coverage_score !== null) : ?>
                    <div class="position-relative mb-3 mx-auto" style="width:80px;height:80px;">
                        <svg viewBox="0 0 36 36" style="transform:rotate(-90deg);width:100%;height:100%;">
                            <circle cx="18" cy="18" r="15.9155" fill="none" stroke="#eef0ee" stroke-width="3.5"/>
                            <circle cx="18" cy="18" r="15.9155" fill="none"
                                stroke="<?php echo $sunSt === 'Protected' ? '#27ae60' : ($sunSt === 'Partially Protected' ? '#f39c12' : '#e74c3c'); ?>"
                                stroke-width="3.5"
                                stroke-dasharray="<?php echo $scan->sunscreen_coverage_score ?? 0; ?> <?php echo 100 - ($scan->sunscreen_coverage_score ?? 0); ?>"
                                stroke-linecap="round"/>
                        </svg>
                        <div class="position-absolute top-50 start-50 translate-middle fw-bold" style="font-size:1rem;font-family:'Outfit',sans-serif;color:var(--primary-color);">
                            <?php echo $scan->sunscreen_coverage_score ?? 0; ?>%
                        </div>
                    </div>
                    <span class="sc-sun-badge <?php echo sunBadgeClass($sunSt); ?> mb-2 d-inline-flex">
                        <?php echo sunIcon($sunSt); ?> <?php echo htmlspecialchars($sunSt); ?>
                    </span>
                    <?php if (!empty($scan->low_coverage_areas) && $scan->low_coverage_areas !== 'N/A') : ?>
                        <p class="small text-muted mt-2 mb-0">Area minim coverage: <?php echo htmlspecialchars($scan->low_coverage_areas); ?></p>
                    <?php endif; ?>
                <?php else : ?>
                    <p class="text-muted small mb-0">Belum ada data pemeriksaan sunscreen pada scan ini.</p>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- ═══ Right Column: Visual Skin Indicators Breakdown ══════ -->
    <div class="col-lg-8">

        <!-- Visual Indicators Breakdown Card -->
        <div class="sc-card mb-4">
            <div class="sc-card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa-solid fa-chart-simple me-2" style="color:var(--primary-color);"></i>Indikator Kondisi Kulit (Visual)</h5>
                <span class="badge bg-secondary-subtle text-secondary small">Estimasi AI</span>
            </div>
            <div class="sc-card-body">

                <!-- 1. Acne Indicator -->
                <div class="sc-metric-bar mb-4">
                    <div class="sc-metric-bar-header d-flex justify-content-between align-items-center">
                        <span class="sc-metric-bar-label fw-semibold">
                            <i class="fa-solid fa-circle me-1" style="color:var(--color-acne);font-size:0.55rem;vertical-align:middle;"></i>
                            Indikasi Jerawat & Noda (Acne Indication)
                        </span>
                        <span class="sc-metric-bar-value fw-bold" style="color:var(--color-acne); font-size:1rem;">
                            <?php echo $scan->acne_score ?? 0; ?><small class="text-muted">/100</small>
                        </span>
                    </div>
                    <div class="progress my-1" style="height:10px; border-radius: 99px;">
                        <div class="progress-bar" style="width:<?php echo $scan->acne_score ?? 0; ?>%; background:var(--color-acne);"></div>
                    </div>
                    <div class="d-flex justify-content-between text-muted" style="font-size:0.75rem;">
                        <span>0: Minimal / Bersih</span>
                        <span>Estimasi visual bintik/titik kemerahan aktif</span>
                        <span>100: Padat</span>
                    </div>
                </div>

                <!-- 2. Redness Indicator -->
                <div class="sc-metric-bar mb-4">
                    <div class="sc-metric-bar-header d-flex justify-content-between align-items-center">
                        <span class="sc-metric-bar-label fw-semibold">
                            <i class="fa-solid fa-circle me-1" style="color:var(--color-redness);font-size:0.55rem;vertical-align:middle;"></i>
                            Indikasi Rona Kemerahan (Redness Indication)
                        </span>
                        <span class="sc-metric-bar-value fw-bold" style="color:var(--color-redness); font-size:1rem;">
                            <?php echo $scan->redness_score ?? 0; ?><small class="text-muted">/100</small>
                        </span>
                    </div>
                    <div class="progress my-1" style="height:10px; border-radius: 99px;">
                        <div class="progress-bar" style="width:<?php echo $scan->redness_score ?? 0; ?>%; background:var(--color-redness);"></div>
                    </div>
                    <div class="d-flex justify-content-between text-muted" style="font-size:0.75rem;">
                        <span>0: Rona Seimbang</span>
                        <span>Estimasi saturasi kemerahan pada permukaan</span>
                        <span>100: Kemerahan Tinggi</span>
                    </div>
                </div>

                <!-- 3. Texture Score -->
                <div class="sc-metric-bar mb-4">
                    <div class="sc-metric-bar-header d-flex justify-content-between align-items-center">
                        <span class="sc-metric-bar-label fw-semibold">
                            <i class="fa-solid fa-circle me-1" style="color:var(--color-texture);font-size:0.55rem;vertical-align:middle;"></i>
                            Skor Kehalusan Tekstur Kulit (Texture Smoothness Score)
                        </span>
                        <span class="sc-metric-bar-value fw-bold" style="color:var(--color-texture); font-size:1rem;">
                            <?php echo $scan->texture_score ?? 0; ?><small class="text-muted">/100</small>
                        </span>
                    </div>
                    <div class="progress my-1" style="height:10px; border-radius: 99px;">
                        <div class="progress-bar" style="width:<?php echo $scan->texture_score ?? 0; ?>%; background:var(--color-texture);"></div>
                    </div>
                    <div class="d-flex justify-content-between text-muted" style="font-size:0.75rem;">
                        <span>0: Tekstur Kasar/Tidak Rata</span>
                        <span>Tingkat kehalusan visual permukaan kulit</span>
                        <span>100: Halus & Merata</span>
                    </div>
                </div>

                <!-- 4. Hyperpigmentation Indicator -->
                <div class="sc-metric-bar mb-2">
                    <div class="sc-metric-bar-header d-flex justify-content-between align-items-center">
                        <span class="sc-metric-bar-label fw-semibold">
                            <i class="fa-solid fa-circle me-1" style="color:var(--color-hyperpig);font-size:0.55rem;vertical-align:middle;"></i>
                            Indikasi Hiperpigmentasi & Bintik Gelap (Hyperpigmentation)
                        </span>
                        <span class="sc-metric-bar-value fw-bold" style="color:var(--color-hyperpig); font-size:1rem;">
                            <?php echo $scan->hyperpigmentation_score ?? 0; ?><small class="text-muted">/100</small>
                        </span>
                    </div>
                    <div class="progress my-1" style="height:10px; border-radius: 99px;">
                        <div class="progress-bar" style="width:<?php echo $scan->hyperpigmentation_score ?? 0; ?>%; background:var(--color-hyperpig);"></div>
                    </div>
                    <div class="d-flex justify-content-between text-muted" style="font-size:0.75rem;">
                        <span>0: Warna Rata</span>
                        <span>Estimasi variasi bintik gelap & pigmentasi</span>
                        <span>100: Kontras Gelap Tinggi</span>
                    </div>
                </div>

            </div>
        </div>

        <!-- Qualitative Visual Observations Card -->
        <?php if (!empty($scan->visual_indications) || !empty($scan->notes_text)) : ?>
            <div class="sc-card mb-4">
                <div class="sc-card-header">
                    <h5><i class="fa-solid fa-eye me-2" style="color:var(--secondary-color);"></i>Observasi Indikasi Visual</h5>
                </div>
                <div class="sc-card-body">
                    <?php if (!empty($scan->visual_indications)) : ?>
                        <ul class="list-unstyled mb-3">
                            <?php foreach ($scan->visual_indications as $ind) : ?>
                                <li class="d-flex gap-2 mb-2 small">
                                    <i class="fa-solid fa-circle-check text-success mt-1" style="font-size:0.8rem; flex-shrink:0;"></i>
                                    <span><?php echo htmlspecialchars($ind); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <?php if (!empty($scan->notes_text)) : ?>
                        <div class="sc-analysis-notes p-3 rounded" style="white-space:pre-line;">
                            <?php echo htmlspecialchars($scan->notes_text); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- ═══ Mandatory Medical Disclaimer Banner ═════════════════ -->
        <div class="sc-advisory-banner mb-4">
            <div class="d-flex gap-3 align-items-start">
                <i class="fa-solid fa-shield-heart fs-4 mt-1" style="color: #dfc68b; flex-shrink: 0;"></i>
                <div>
                    <h6 class="fw-bold mb-1 sc-advisory-title">Pemberitahuan & Disclaimer Medis</h6>
                    <div class="sc-advisory-quote">
                        "SkinCheck memberikan informasi dan indikasi visual berbasis AI. Hasil ini bukan diagnosis medis dan bukan pengganti konsultasi dengan tenaga kesehatan profesional."
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Links -->
        <div class="d-flex gap-2">
            <a href="<?php echo URLROOT; ?>/customer/history" class="btn btn-outline-primary">
                <i class="fa-solid fa-arrow-left me-2"></i>Kembali ke Riwayat
            </a>
            <a href="<?php echo URLROOT; ?>/scan/faceScan" class="btn btn-primary">
                <i class="fa-solid fa-camera me-2"></i>Ambil Scan Baru
            </a>
        </div>

    </div><!-- /.col-lg-8 -->

</div><!-- /.row -->

<?php require APPROOT . '/views/templates/customer_footer.php'; ?>
