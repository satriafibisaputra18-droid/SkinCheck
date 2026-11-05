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

$score = $scan->sunscreen_coverage_score ?? 0;
$status = $scan->sunscreen_status ?? 'Cukup Merata';
$lowAreas = $scan->low_coverage_areas_list ?? [];
$confidencePct = !empty($scan->confidence) ? round($scan->confidence * 100) : 85;
$isLowConfidence = ($confidencePct < 60);

require APPROOT . '/views/templates/customer_header.php';
?>

<!-- ── Breadcrumb & Action Toolbar ──────────────────────── -->
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div class="d-flex align-items-center gap-2">
        <a href="<?php echo URLROOT; ?>/sunscreen/check" class="btn btn-sm btn-outline-primary">
            <i class="fa-solid fa-arrow-left me-1"></i>Pemeriksaan Sunscreen
        </a>
        <span class="text-muted small">/ Hasil Pemerataan #<?php echo $scan->id; ?></span>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo URLROOT; ?>/customer/scanDetail/<?php echo $scan->id; ?>" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-chart-simple me-1"></i>Analisis Kulit Lengkap
        </a>
        <a href="<?php echo URLROOT; ?>/sunscreen/check" class="btn btn-sm btn-primary">
            <i class="fa-solid fa-sun me-1"></i>Pemeriksaan Baru
        </a>
    </div>
</div>

<!-- ── Low Confidence Notice if applicable ──────────────── -->
<?php if ($isLowConfidence) : ?>
    <div class="alert alert-warning border-0 mb-4 p-4 shadow-sm" style="border-radius: var(--border-radius);">
        <div class="d-flex gap-3 align-items-start">
            <i class="fa-solid fa-triangle-exclamation fs-3 text-warning flex-shrink-0 mt-1"></i>
            <div>
                <h6 class="fw-bold text-dark mb-1">Hasil Kurang Meyakinkan (Keyakinan: <?php echo $confidencePct; ?>%)</h6>
                <p class="mb-2 small text-dark">
                    Hasil kurang meyakinkan akibat pencahayaan yang tidak seimbang atau terlalu redup. Silakan ambil gambar ulang dengan pencahayaan merata dari arah depan.
                </p>
                <a href="<?php echo URLROOT; ?>/sunscreen/check" class="btn btn-sm btn-warning">
                    <i class="fa-solid fa-rotate-right me-1"></i>Ambil Foto Ulang
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- ── Main Layout ──────────────────────────────────────── -->
<div class="row g-4">

    <!-- ═══ Left Column: Sunscreen Coverage Metrics ═══════════ -->
    <div class="col-lg-5">

        <!-- Coverage Card -->
        <div class="sc-card mb-4 text-center">
            <div class="sc-card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa-solid fa-sun me-2" style="color:var(--secondary-color);"></i>Sunscreen Coverage</h5>
                <span class="sc-badge-dark small"><?php echo date('d M Y, H:i', strtotime($scan->created_at)); ?></span>
            </div>
            <div class="sc-card-body p-4">

                <!-- Donut Score Display -->
                <div class="position-relative mx-auto mb-3" style="width:140px; height:140px;">
                    <svg viewBox="0 0 36 36" style="transform:rotate(-90deg);width:100%;height:100%;">
                        <circle cx="18" cy="18" r="15.9155" fill="none" stroke="#eef0ee" stroke-width="3"/>
                        <circle cx="18" cy="18" r="15.9155" fill="none"
                            stroke="<?php echo in_array($status, ['Sangat Merata', 'Protected'], true) ? 'var(--color-score-good)' : (in_array($status, ['Cukup Merata', 'Partially Protected'], true) ? 'var(--color-score-fair)' : 'var(--color-score-poor)'); ?>"
                            stroke-width="3"
                            stroke-dasharray="<?php echo $score; ?> <?php echo 100 - $score; ?>"
                            stroke-linecap="round"/>
                    </svg>
                    <div class="position-absolute top-50 start-50 translate-middle text-center">
                        <div class="fw-bold" style="font-size:2rem; font-family:'Outfit',sans-serif; color:var(--primary-color); line-height:1;">
                            <?php echo $score; ?>%
                        </div>
                        <small class="text-muted" style="font-size:0.7rem; text-transform:uppercase;">Coverage</small>
                    </div>
                </div>

                <!-- Status Badge -->
                <div class="mb-3">
                    <span class="sc-sun-badge <?php echo sunBadgeClass($status); ?> px-3 py-2 fs-6">
                        <?php echo sunIcon($status); ?> <?php echo htmlspecialchars($status); ?>
                    </span>
                </div>

                <div class="sc-badge-confidence py-2 px-3 mb-3">
                    <i class="fa-solid fa-microchip me-1" style="color: #60a5fa;"></i>Keyakinan Estimasi: <strong><?php echo $confidencePct; ?>%</strong>
                </div>

                <hr class="my-3">

                <!-- Method and Hardware Badges -->
                <div class="d-flex justify-content-between small text-muted mb-2">
                    <span>Metode:</span>
                    <strong class="text-dark"><?php echo htmlspecialchars($scan->sunscreen_analysis_method ?? 'RGB Visual Estimation'); ?></strong>
                </div>
                <div class="d-flex justify-content-between small text-muted">
                    <span>Perangkat:</span>
                    <strong class="text-dark"><?php echo htmlspecialchars($scan->sunscreen_hardware_type ?? 'Standard RGB Camera'); ?></strong>
                </div>
            </div>
        </div>

        <!-- Attention Areas Card -->
        <div class="sc-card mb-4">
            <div class="sc-card-header">
                <h5><i class="fa-solid fa-triangle-exclamation me-2" style="color:var(--secondary-color);"></i>Area Perhatian</h5>
            </div>
            <div class="sc-card-body">
                <?php if (!empty($lowAreas)) : ?>
                    <p class="small text-muted mb-2">
                        Area berikut terindikasi memiliki cakupan sunscreen yang kurang merata. Disarankan untuk menambahkan/meratakan aplikasi sunscreen di area ini:
                    </p>
                    <ul class="list-group list-group-flush mb-0">
                        <?php foreach ($lowAreas as $area) : ?>
                            <li class="list-group-item px-0 py-2 d-flex align-items-center gap-2 border-0">
                                <span class="badge rounded-circle bg-danger-subtle text-danger p-1">
                                    <i class="fa-solid fa-exclamation" style="font-size:0.7rem; width:12px; height:12px;"></i>
                                </span>
                                <span class="fw-semibold text-dark small"><?php echo htmlspecialchars($area); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <div class="text-center py-3 text-success">
                        <i class="fa-solid fa-circle-check fs-2 mb-2 d-block"></i>
                        <p class="mb-0 fw-semibold small">Seluruh zona wajah utama terindikasi terlindungi dengan merata.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ═══ Mandatory Non-UV Information Notice ═════════════════ -->
        <div class="sc-advisory-banner">
            <div class="d-flex gap-3 align-items-start">
                <i class="fa-solid fa-circle-info fs-4 mt-1" style="color: #dfc68b; flex-shrink: 0;"></i>
                <div>
                    <h6 class="fw-bold mb-1 sc-advisory-title">Informasi Penting & Batasan Teknis</h6>
                    <div class="sc-advisory-quote mb-1">
                        "Hasil ini merupakan estimasi visual berbasis AI dan bukan pengukuran UV langsung."
                    </div>
                    <div class="sc-advisory-sub">
                        Kamera RGB standar mengestimasi pemerataan berdasarkan pantulan optik dan kilau permukaan kulit, bukan sensor radiasi ultraviolet.
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /.col-lg-5 -->

    <!-- ═══ Right Column: Heatmap Visualizer & Side-by-Side ══════ -->
    <div class="col-lg-7">

        <div class="sc-card mb-4">
            <div class="sc-card-header d-flex justify-content-between align-items-center">
                <h5><i class="fa-solid fa-map me-2" style="color:var(--secondary-color);"></i>Visualisasi Pemerataan (Heatmap)</h5>
                <span class="badge bg-secondary-subtle text-secondary small">Estimasi Zonasi AI</span>
            </div>
            <div class="sc-card-body p-3">

                <!-- Side-by-side or Tab view -->
                <ul class="nav nav-pills mb-3 dashboard-tabs justify-content-center" id="heatmapTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active px-3 py-2 small" id="heatmap-view-tab" data-bs-toggle="pill" data-bs-target="#heatmap-view" type="button" role="tab">
                            <i class="fa-solid fa-map me-1"></i>Peta Heatmap
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-3 py-2 small" id="photo-view-tab" data-bs-toggle="pill" data-bs-target="#photo-view" type="button" role="tab">
                            <i class="fa-solid fa-camera me-1"></i>Foto Asli
                        </button>
                    </li>
                </ul>

                <div class="tab-content text-center" id="heatmapTabContent">
                    <!-- Heatmap Tab -->
                    <div class="tab-pane fade show active" id="heatmap-view" role="tabpanel">
                        <?php if (!empty($scan->sunscreen_heatmap_path)) : ?>
                            <div class="rounded overflow-hidden shadow-sm bg-dark mx-auto" style="max-width: 480px; max-height: 420px;">
                                <img src="<?php echo URLROOT; ?>/sunscreen/serveHeatmap/<?php echo $scan->id; ?>"
                                     class="w-100 h-100 object-fit-cover"
                                     style="max-height: 420px;"
                                     alt="Peta Heatmap Sunscreen">
                            </div>
                        <?php else : ?>
                            <div class="sc-empty-state py-4">
                                <i class="fa-solid fa-map-slash"></i>
                                <p class="small">Visualisasi heatmap tidak tersedia untuk scan ini.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Photo Tab -->
                    <div class="tab-pane fade" id="photo-view" role="tabpanel">
                        <?php if (!empty($scan->image_path)) : ?>
                            <div class="rounded overflow-hidden shadow-sm bg-dark mx-auto" style="max-width: 480px; max-height: 420px;">
                                <img src="<?php echo URLROOT; ?>/scan/serveImage/<?php echo $scan->id; ?>"
                                     class="w-100 h-100 object-fit-cover"
                                     style="max-height: 420px;"
                                     alt="Foto Asli Scan Wajah">
                            </div>
                        <?php else : ?>
                            <div class="sc-empty-state py-4">
                                <i class="fa-solid fa-image-slash"></i>
                                <p class="small">Foto asli tidak tersedia.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Heatmap Legend -->
                <div class="mt-3 p-2 sc-heatmap-legend rounded d-flex justify-content-center gap-3 flex-wrap small" style="font-size:0.75rem;">
                    <span class="d-flex align-items-center gap-1">
                        <span style="width:12px; height:12px; background:rgba(40,200,80,0.85); border-radius:2px; display:inline-block;"></span>
                        <span>Cakupan Sangat Baik (≥85%)</span>
                    </span>
                    <span class="d-flex align-items-center gap-1">
                        <span style="width:12px; height:12px; background:rgba(220,190,40,0.85); border-radius:2px; display:inline-block;"></span>
                        <span>Cukup Baik (70-84%)</span>
                    </span>
                    <span class="d-flex align-items-center gap-1">
                        <span style="width:12px; height:12px; background:rgba(240,120,30,0.85); border-radius:2px; display:inline-block;"></span>
                        <span>Perlu Diratakan (50-69%)</span>
                    </span>
                    <span class="d-flex align-items-center gap-1">
                        <span style="width:12px; height:12px; background:rgba(230,40,40,0.85); border-radius:2px; display:inline-block;"></span>
                        <span>Kurang / Missed (&lt;50%)</span>
                    </span>
                </div>

            </div>
        </div>

        <!-- SPF Tips & Action Card -->
        <div class="sc-card">
            <div class="sc-card-header">
                <h5><i class="fa-solid fa-lightbulb me-2" style="color:var(--secondary-color);"></i>Rekomendasi Pemakaian Sunscreen</h5>
            </div>
            <div class="sc-card-body">
                <ul class="list-unstyled mb-0 small text-muted">
                    <li class="d-flex gap-2 mb-2">
                        <i class="fa-solid fa-circle-check text-success mt-1" style="font-size:0.75rem; flex-shrink:0;"></i>
                        <span>Gunakan takaran <strong>2 ruas jari penuh</strong> untuk menutupi seluruh wajah dan leher secara merata.</span>
                    </li>
                    <li class="d-flex gap-2 mb-2">
                        <i class="fa-solid fa-circle-check text-success mt-1" style="font-size:0.75rem; flex-shrink:0;"></i>
                        <span>Aplikasikan kembali (reapply) setiap <strong>2-3 jam</strong> saat beraktivitas di luar ruangan.</span>
                    </li>
                    <li class="d-flex gap-2">
                        <i class="fa-solid fa-circle-check text-success mt-1" style="font-size:0.75rem; flex-shrink:0;"></i>
                        <span>Perhatikan area yang sering terlewat: lipatan hidung, bawah mata, dahi bagian atas, dan leher.</span>
                    </li>
                </ul>
            </div>
        </div>

    </div><!-- /.col-lg-7 -->

</div><!-- /.row -->

<?php require APPROOT . '/views/templates/customer_footer.php'; ?>
