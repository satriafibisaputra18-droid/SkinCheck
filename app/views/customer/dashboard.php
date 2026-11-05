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
    if ($status === 'Protected') return '<i class="fa-solid fa-shield-check"></i>';
    if ($status === 'Partially Protected') return '<i class="fa-solid fa-shield-halved"></i>';
    if ($status === 'Unprotected') return '<i class="fa-solid fa-shield-slash"></i>';
    return '<i class="fa-regular fa-clock"></i>';
}

$ls = $latest_scan; // shorthand for latest scan

require APPROOT . '/views/templates/customer_header.php';
?>



<!-- ── Executive Welcome Banner (Hero Card) ─────────────────── -->
<div class="sc-dashboard-hero">
    <div class="row align-items-center g-3">
        <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                <span class="badge sc-badge-verified px-2.5 py-1 rounded-pill small">
                    <i class="fa-solid fa-circle-check me-1"></i>Status Terverifikasi • Profil Aktif
                </span>
                <span class="badge sc-badge-date px-2.5 py-1 rounded-pill small">
                    <i class="fa-regular fa-calendar-check me-1 text-warning"></i><?php echo date('d F Y'); ?>
                </span>
            </div>
            <h1 class="h3 fw-bold mb-1 text-white" style="letter-spacing:-0.02em;">
                Selamat Datang Kembali, <span class="sc-highlight-gold"><?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?></span>
            </h1>
            <p class="text-white-50 mb-0 small" style="max-width: 650px; line-height: 1.55;">
                Pantau perkembangan kesehatan sawar kulit (<em>skin barrier</em>), efektivitas proteksi tabir surya UV, serta rekomendasi rutinitas harian terarah Anda.
            </p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <div class="sc-hero-status-pill d-inline-flex flex-column align-items-lg-end">
                <div class="d-flex align-items-center gap-2 mb-1 justify-content-lg-end">
                    <span class="sc-status-indicator-dot"></span>
                    <span class="text-white fw-semibold small">Sistem Pemantauan Aktif</span>
                </div>
                <div class="text-white-50 small" style="font-size:0.78rem;">
                    <?php if ($ls): ?>
                        Pemeriksaan Terakhir: <strong class="text-white"><?php echo date('d M Y, H:i', strtotime($ls->created_at)); ?> WIB</strong>
                    <?php else: ?>
                        Belum ada rekaman pemindaian
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── Sleek Horizontal Action Cards (Compact Bento Grid) ── -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3 d-flex">
        <a href="<?php echo URLROOT; ?>/scan/faceScan" class="sc-action-card text-decoration-none w-100">
            <div class="sc-action-icon" style="background:rgba(45, 180, 140, 0.12); color:#38d9a9;">
                <i class="fa-solid fa-camera"></i>
            </div>
            <div class="flex-grow-1 overflow-hidden">
                <h6 class="text-truncate">Pemeriksaan Wajah</h6>
                <p class="text-truncate">Analisis 4 indikator kulit</p>
            </div>
            <i class="fa-solid fa-chevron-right text-white-50 small sc-action-arrow"></i>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-xl-3 d-flex">
        <a href="<?php echo URLROOT; ?>/sunscreen/check" class="sc-action-card text-decoration-none w-100">
            <div class="sc-action-icon" style="background:rgba(223, 198, 139, 0.12); color:#dfc68b;">
                <i class="fa-solid fa-sun"></i>
            </div>
            <div class="flex-grow-1 overflow-hidden">
                <h6 class="text-truncate">Proteksi Sunscreen</h6>
                <p class="text-truncate">Cek kerataan lapisan UV</p>
            </div>
            <i class="fa-solid fa-chevron-right text-white-50 small sc-action-arrow"></i>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-xl-3 d-flex">
        <a href="<?php echo URLROOT; ?>/customer/aiAssistant" class="sc-action-card text-decoration-none w-100">
            <div class="sc-action-icon" style="background:rgba(91, 142, 224, 0.12); color:#7aa8f2;">
                <i class="fa-solid fa-comment-medical"></i>
            </div>
            <div class="flex-grow-1 overflow-hidden">
                <h6 class="text-truncate">Konsultasi Kulit</h6>
                <p class="text-truncate">Edukasi & kurasi produk</p>
            </div>
            <i class="fa-solid fa-chevron-right text-white-50 small sc-action-arrow"></i>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-xl-3 d-flex">
        <a href="<?php echo URLROOT; ?>/customer/history" class="sc-action-card text-decoration-none w-100">
            <div class="sc-action-icon" style="background:rgba(180, 115, 230, 0.12); color:#c488f7;">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div class="flex-grow-1 overflow-hidden">
                <h6 class="text-truncate">Catatan Riwayat</h6>
                <p class="text-truncate"><?php echo $scan_count; ?> rekaman tersimpan</p>
            </div>
            <i class="fa-solid fa-chevron-right text-white-50 small sc-action-arrow"></i>
        </a>
    </div>
</div>

<!-- ── Primary Analytics & Sunscreen Protection Grid ─────── -->
<div class="row g-4 mb-4">

    <!-- Primary Evaluation Card -->
    <div class="col-lg-8">
        <div class="sc-card h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="sc-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-microscope text-warning"></i>
                        <h5 class="mb-0 fw-bold text-white">Evaluasi Kondisi Kulit Terakhir</h5>
                    </div>
                    <?php if ($ls): ?>
                        <span class="badge sc-badge-date small">
                            Pemeriksaan: <?php echo date('d M Y, H:i', strtotime($ls->created_at)); ?> WIB
                        </span>
                    <?php endif; ?>
                </div>
                
                <div class="sc-card-body">
                    <?php if ($ls): ?>
                        <div class="row align-items-center g-4 pb-3 border-bottom" style="border-color: rgba(255,255,255,0.08) !important;">
                            <div class="col-md-auto text-center px-md-3">
                                <?php $sc = scoreClass($ls->overall_score); ?>
                                <div class="sc-score-circle <?php echo $sc; ?> mx-auto mb-2">
                                    <?php echo $ls->overall_score ?? '--'; ?>
                                </div>
                                <div class="fw-bold text-white" style="font-size:0.88rem;">
                                    Skor Keseluruhan
                                </div>
                                <div class="text-white-50" style="font-size:0.75rem;">
                                    <?php echo scoreStatusLabel($ls->overall_score); ?>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="sc-metric-bar">
                                    <div class="sc-metric-bar-header">
                                        <span class="sc-metric-bar-label fw-semibold">
                                            <i class="fa-solid fa-circle me-1.5" style="color:var(--color-acne);font-size:0.5rem;"></i>Tingkat Jerawat & Pori (Acne Indicator)
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
                                            <i class="fa-solid fa-circle me-1.5" style="color:var(--color-redness);font-size:0.5rem;"></i>Kemerahan & Reaktivitas (Erythema)
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
                                            <i class="fa-solid fa-circle me-1.5" style="color:var(--color-texture);font-size:0.5rem;"></i>Kehalusan Permukaan Kulit (Texture)
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
                                            <i class="fa-solid fa-circle me-1.5" style="color:var(--color-hyperpig);font-size:0.5rem;"></i>Hiperpigmentasi & Noda (Melanin Uniformity)
                                        </span>
                                        <span class="sc-metric-bar-value" style="color:var(--color-hyperpig);"><?php echo $ls->hyperpigmentation_score ?? 0; ?> / 100</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" style="width:<?php echo $ls->hyperpigmentation_score ?? 0; ?>%;background:var(--color-hyperpig);"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Structured Clinical Diagnostic Box -->
                        <div class="sc-diagnostic-box">
                            <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                                <h6 class="fw-bold text-white mb-0" style="font-size:0.88rem;">
                                    <i class="fa-solid fa-book-open-reader me-1.5 text-warning"></i>Catatan Kontekstual Integritas Kulit
                                </h6>
                                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                    <span class="sc-diagnostic-tag-pill">
                                        <i class="fa-solid fa-shield me-1 text-success"></i>Sawar Stabil
                                    </span>
                                    <span class="sc-diagnostic-tag-pill">
                                        <i class="fa-solid fa-droplet me-1 text-info"></i>Hidrasi Terjaga
                                    </span>
                                </div>
                            </div>
                            <p class="text-white-50 small mb-3" style="line-height:1.6; text-align:justify;">
                                Berdasarkan pemindaian visual komputasional terbaru, integritas sawar kulit (<em>skin barrier</em>) Anda menunjukkan skor komposit 
                                <strong class="text-white"><?php echo $ls->overall_score ?? 0; ?>/100</strong>. Indikator jerawat berada pada nilai 
                                <strong class="text-white"><?php echo $ls->acne_score ?? 0; ?>/100</strong>, yang menandakan aktivitas folikel sebaceous berada pada kategori 
                                <?php echo ($ls->acne_score < 25) ? '<span class="text-white">rendah dan stabil</span>' : (($ls->acne_score < 55) ? '<span class="text-white">moderat dengan potensi komedo</span>' : '<span class="text-white">membutuhkan eksfoliasi berkala</span>'); ?>. 
                                Reaktivitas kapiler perifer (eritema) tercatat <strong class="text-white"><?php echo $ls->redness_score ?? 0; ?>/100</strong>, membuktikan sawar kulit dalam keadaan tenang dan terlindungi.
                            </p>
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-1 border-top" style="border-color:rgba(255,255,255,0.06) !important;">
                                <span class="text-white-50 small" style="font-size:0.75rem;">
                                    <i class="fa-solid fa-circle-info me-1 text-warning"></i>Ingin membaca rincian citra dan peta area wajah?
                                </span>
                                <a href="<?php echo URLROOT; ?>/customer/scanDetail/<?php echo $ls->id; ?>" class="btn sc-btn-outline-gold btn-sm rounded-pill px-3">
                                    Buka Lembar Diagnostik Lengkap <i class="fa-solid fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="sc-empty-state py-5 text-center">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-3 text-warning" style="width:60px;height:60px;background:rgba(191,161,95,0.12);border:1px solid rgba(191,161,95,0.25);">
                                <i class="fa-solid fa-camera fs-4"></i>
                            </div>
                            <h6 class="fw-bold text-white mb-1">Belum Ada Rekaman Pemindaian</h6>
                            <p class="text-white-50 small mb-3 mx-auto" style="max-width:440px; line-height:1.6;">
                                Lakukan pemindaian wajah pertama Anda menggunakan kamera depan untuk mendapatkan analisis komprehensif 4 indikator kesehatan kulit.
                            </p>
                            <a href="<?php echo URLROOT; ?>/scan/faceScan" class="btn sc-btn-gold btn-sm rounded-pill px-4">
                                Mulai Pemindaian Wajah Sekarang
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Sunscreen Protection Evaluation Card (Perfect Visual Balance) -->
    <div class="col-lg-4">
        <div class="sc-card h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="sc-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-sun text-warning"></i>
                        <h5 class="mb-0 fw-bold text-white">Status Perlindungan UV</h5>
                    </div>
                </div>
                
                <div class="sc-card-body pb-0">
                    <?php if ($ls && $ls->sunscreen_coverage_score !== null): ?>
                        <?php $sunStatus = $ls->sunscreen_status ?? 'Unprotected'; ?>
                        <div class="text-center py-1">
                            <!-- Coverage Donut Gauge -->
                            <div class="position-relative mx-auto mb-2" style="width:104px;height:104px;">
                                <svg viewBox="0 0 36 36" style="transform:rotate(-90deg);width:100%;height:100%;">
                                    <circle cx="18" cy="18" r="15.9155" fill="none" stroke="rgba(255, 255, 255, 0.08)" stroke-width="3" />
                                    <circle cx="18" cy="18" r="15.9155" fill="none"
                                        stroke="<?php echo $sunStatus === 'Protected' ? 'var(--color-score-good)' : ($sunStatus === 'Partially Protected' ? 'var(--color-score-fair)' : 'var(--color-score-poor)'); ?>"
                                        stroke-width="3.2"
                                        stroke-dasharray="<?php echo $ls->sunscreen_coverage_score; ?> <?php echo 100 - $ls->sunscreen_coverage_score; ?>"
                                        stroke-linecap="round" />
                                </svg>
                                <div class="position-absolute top-50 start-50 translate-middle fw-bold"
                                    style="font-size:1.35rem;font-family:'Outfit',sans-serif;color:#dfc68b;">
                                    <?php echo $ls->sunscreen_coverage_score; ?>%
                                </div>
                            </div>

                            <div class="mb-3">
                                <span class="sc-sun-badge <?php echo sunBadgeClass($sunStatus); ?>">
                                    <?php echo sunIcon($sunStatus); ?> <?php echo htmlspecialchars($sunStatus); ?>
                                </span>
                            </div>

                            <!-- Balanced Mini-Metrics Grid -->
                            <div class="sc-uv-stats-grid">
                                <div class="sc-uv-stat-pill">
                                    <div class="val"><?php echo $ls->sunscreen_coverage_score; ?>%</div>
                                    <div class="lbl">Cakupan Lapisan</div>
                                </div>
                                <div class="sc-uv-stat-pill">
                                    <div class="val">~2.5 Jam</div>
                                    <div class="lbl">Daya Fotoproteksi</div>
                                </div>
                                <div class="sc-uv-stat-pill">
                                    <div class="val">16:15 WIB</div>
                                    <div class="lbl">Re-aplikasi Ideal</div>
                                </div>
                            </div>

                            <!-- Contextual Sunscreen Narrative -->
                            <div class="sc-dark-subcard p-2.5 rounded-3 mb-3 text-start">
                                <div class="fw-bold text-white small mb-1" style="font-size:0.8rem;">
                                    <i class="fa-solid fa-shield-halved me-1 text-warning"></i>Pentingnya Cakupan Merata
                                </div>
                                <p class="text-white-50 small mb-0" style="font-size:0.75rem; line-height:1.5;">
                                    Lapisan tabir surya yang rata membentuk perisai fotoprotektif terhadap radiasi UV-A (penuaan dini dan degradasi kolagen) serta UV-B (sunburn).
                                </p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-1">
                            <!-- Visual Ring Icon -->
                            <div class="position-relative mx-auto mb-2 d-flex align-items-center justify-content-center" style="width:104px;height:104px;border-radius:50%;background:radial-gradient(circle, rgba(191,161,95,0.14) 0%, rgba(20,48,38,0.3) 70%);border:2px dashed rgba(191,161,95,0.35);">
                                <i class="fa-solid fa-shield-halved" style="font-size:2.4rem;color:#dfc68b;"></i>
                            </div>

                            <div class="mb-3">
                                <span class="badge sc-badge-date px-3 py-1 rounded-pill small">
                                    <i class="fa-regular fa-clock text-warning me-1.5"></i>Belum Diverifikasi Hari Ini
                                </span>
                            </div>

                            <!-- Balanced Mini-Metrics Grid (Recommended Protocol) -->
                            <div class="sc-uv-stats-grid">
                                <div class="sc-uv-stat-pill">
                                    <div class="val">SPF 30+</div>
                                    <div class="lbl">Filter UV-B</div>
                                </div>
                                <div class="sc-uv-stat-pill">
                                    <div class="val">PA++++</div>
                                    <div class="lbl">Filter UV-A</div>
                                </div>
                                <div class="sc-uv-stat-pill">
                                    <div class="val">2 Jari</div>
                                    <div class="lbl">Dosis Takaran</div>
                                </div>
                            </div>

                            <!-- Contextual Clinical Guidance Subcard -->
                            <div class="sc-dark-subcard p-2.5 rounded-3 mb-3 text-start">
                                <div class="fw-bold text-white small mb-1" style="font-size:0.8rem;">
                                    <i class="fa-solid fa-shield-halved me-1 text-warning"></i>Pentingnya Verifikasi Kerataan
                                </div>
                                <p class="text-white-50 small mb-0" style="font-size:0.75rem; line-height:1.5;">
                                    Lapisan tabir surya yang tidak merata meninggalkan celah mikro tempat radiasi UV memicu hiperpigmentasi dan merusak elastisitas kulit.
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="p-3 pt-0">
                <a href="<?php echo URLROOT; ?>/sunscreen/check" class="btn sc-btn-outline-gold btn-sm w-100 rounded-pill py-2">
                    <i class="fa-solid fa-sun me-1.5"></i>Periksa Lapisan Sunscreen
                </a>
            </div>
        </div>
    </div>

</div>

<!-- ── In-Depth Educational Guide & Clinical Routine ──────── -->
<div class="row g-4 mb-4">

    <!-- Clinical Routine Protocol Guidance -->
    <div class="col-lg-8">
        <div class="sc-card h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="sc-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-notes-medical text-warning"></i>
                        <h5 class="mb-0 fw-bold text-white">Panduan Protokol Perawatan Berdasarkan Indikator Kulit</h5>
                    </div>
                </div>
                <div class="sc-card-body">
                    <p class="text-white-50 small mb-3" style="line-height:1.6;">
                        Menjaga integritas sawar kulit membutuhkan sinergi antara rutinitas proteksi fotoprotektif di siang hari dan fase regenerasi lipid di malam hari. Berikut adalah kurasi langkah perawatan Anda:
                    </p>

                    <div class="row g-3">
                        <!-- Morning Protocol -->
                        <div class="col-md-6">
                            <div class="sc-protocol-card">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="sc-badge-morning">
                                        <i class="fa-solid fa-sun me-1"></i>Pagi Hari (Proteksi)
                                    </span>
                                </div>
                                <div>
                                    <div class="sc-protocol-step">
                                        <span class="sc-step-num">1</span>
                                        <div><strong class="text-white">Pembersih Lembut:</strong> Bersihkan sebum tanpa mengikis stratum korneum.</div>
                                    </div>
                                    <div class="sc-protocol-step">
                                        <span class="sc-step-num">2</span>
                                        <div><strong class="text-white">Toner Hidratif:</strong> Kembalikan kadar air dan hidrasi selular.</div>
                                    </div>
                                    <div class="sc-protocol-step">
                                        <span class="sc-step-num">3</span>
                                        <div><strong class="text-white">Serum Antioksidan:</strong> Netralkan radikal bebas polusi dan UV.</div>
                                    </div>
                                    <div class="sc-protocol-step">
                                        <span class="sc-step-num">4</span>
                                        <div><strong class="text-white">Pelembap Ringan:</strong> Kunci kelembapan dengan tekstur gel-cream.</div>
                                    </div>
                                    <div class="sc-protocol-step">
                                        <span class="sc-step-num">5</span>
                                        <div><strong class="text-white">Tabir Surya SPF 30+:</strong> Aplikasikan dua jari penuh secara merata.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Night Protocol -->
                        <div class="col-md-6">
                            <div class="sc-protocol-card">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="sc-badge-night">
                                        <i class="fa-solid fa-moon me-1"></i>Malam Hari (Pemulihan)
                                    </span>
                                </div>
                                <div>
                                    <div class="sc-protocol-step">
                                        <span class="sc-step-num">1</span>
                                        <div><strong class="text-white">Double Cleansing:</strong> Larutkan residu sunscreen dan debu mikro.</div>
                                    </div>
                                    <div class="sc-protocol-step">
                                        <span class="sc-step-num">2</span>
                                        <div><strong class="text-white">Gentle Facial Wash:</strong> Bersihkan sisa kotoran dengan pH netral (5.5).</div>
                                    </div>
                                    <div class="sc-protocol-step">
                                        <span class="sc-step-num">3</span>
                                        <div><strong class="text-white">Targeted Treatment:</strong> Gunakan Centella Asiatica / Salicylic Acid.</div>
                                    </div>
                                    <div class="sc-protocol-step">
                                        <span class="sc-step-num">4</span>
                                        <div><strong class="text-white">Ceramide Barrier Cream:</strong> Suplai lipid esensial untuk regenerasi.</div>
                                    </div>
                                    <div class="sc-protocol-step">
                                        <span class="sc-step-num">5</span>
                                        <div><strong class="text-white">Sleeping Recovery:</strong> Istirahat optimal 7-8 jam untuk pemulihan seluler.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Clinical Tips Banner -->
                    <div class="mt-4 p-3 rounded-3 sc-dark-subcard d-flex align-items-center gap-2.5" style="font-size: 0.8rem; line-height: 1.55; color: #c9ded5;">
                        <i class="fa-solid fa-lightbulb" style="color: #dfc68b; font-size: 0.95rem; flex-shrink: 0;"></i>
                        <div>
                            <strong style="color: #dfc68b;">Tips Klinis:</strong>
                            <span> Selalu lakukan uji tempel (<em>patch test</em>) pada area rahang selama 24 jam sebelum memperkenalkan bahan aktif berkonsentrasi tinggi ke seluruh wajah.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dermatological Consultation Hub -->
    <div class="col-lg-4">
        <div class="sc-consult-card h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="sc-consult-icon">
                    <i class="fa-solid fa-comment-medical"></i>
                </div>
                <h4 class="fw-bold text-white">Pusat Konsultasi & Kurasi Produk</h4>
                <p class="text-white-50" style="font-size:0.84rem; line-height:1.55;">
                    Bingung memilih bahan aktif yang tepat untuk jerawat atau mencari tabir surya yang tidak meninggalkan <em>whitecast</em>? Layanan konsultasi cerdas kami siap membedah komposisi skincare sesuai kebutuhan kulit Anda.
                </p>
            </div>
            
            <div class="pt-2">
                <div class="p-3 rounded-3 sc-dark-subcard mb-3 small text-white-50">
                    <div class="fw-semibold text-white mb-2" style="font-size:0.8rem;">Topik Konsultasi Populer:</div>
                    <div class="d-flex flex-column gap-1.5" style="font-size:0.78rem;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-check text-warning" style="font-size:0.7rem;"></i>
                            <span>Kesesuaian bahan aktif dengan jenis kulit</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-check text-warning" style="font-size:0.7rem;"></i>
                            <span>Rekomendasi kurasi produk resmi terverifikasi</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-check text-warning" style="font-size:0.7rem;"></i>
                            <span>Panduan eksfoliasi aman untuk kulit sensitif</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 mb-3 text-white-50 small px-1" style="font-size:0.72rem;">
                    <span class="rounded-circle bg-success d-inline-block" style="width:7px;height:7px;"></span>
                    <span>SkinCheck AI & Tim Konsultasi Aktif</span>
                </div>

                <a href="<?php echo URLROOT; ?>/customer/aiAssistant" class="btn sc-btn-gold btn-sm w-100 rounded-pill py-2.5 fw-semibold d-inline-flex align-items-center justify-content-center gap-2">
                    <i class="fa-solid fa-comments"></i>
                    <span>Buka Ruang Konsultasi</span>
                </a>
            </div>
        </div>
    </div>

</div>

<!-- ── Recent History Table ──────────────────────────────── -->
<div class="sc-card">
    <div class="sc-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="fa-solid fa-clock-rotate-left text-warning"></i>
            <h5 class="mb-0 fw-bold text-white">Riwayat Pemindaian Terkini</h5>
            <span class="badge sc-badge-date px-2 py-0.5 rounded-pill small ms-1">
                <?php echo $scan_count; ?> Catatan
            </span>
        </div>
        <a href="<?php echo URLROOT; ?>/customer/history" class="btn sc-btn-outline-gold btn-sm rounded-pill px-3">
            Lihat Seluruh Catatan <i class="fa-solid fa-arrow-right ms-1"></i>
        </a>
    </div>

    <?php if (!empty($recent_scans)): ?>
        <div class="table-responsive">
            <table class="table sc-history-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Waktu & Tanggal</th>
                        <th>Indikator Jerawat</th>
                        <th>Kemerahan</th>
                        <th>Proteksi Sunscreen</th>
                        <th>Skor Komposit</th>
                        <th class="text-end">Rincian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_scans as $s):
                        $sunSt = !empty($s->sunscreen_status) ? $s->sunscreen_status : 'Belum Dicek';
                        $sc = scoreClass($s->overall_score);
                    ?>
                        <tr>
                            <td>
                                <div class="fw-semibold text-white"><?php echo date('d M Y', strtotime($s->created_at)); ?></div>
                                <small class="text-white-50"><?php echo date('H:i', strtotime($s->created_at)); ?> WIB</small>
                            </td>
                            <td>
                                <span class="badge sc-badge-metric">
                                    <?php echo $s->acne_score ?? '--'; ?> / 100
                                </span>
                            </td>
                            <td>
                                <span class="badge sc-badge-metric">
                                    <?php echo $s->redness_score ?? '--'; ?> / 100
                                </span>
                            </td>
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
                                    Detail Diagnostik
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="sc-empty-state py-4 text-center">
            <i class="fa-regular fa-folder-open mb-2 fs-3 text-white-50"></i>
            <p class="text-white-50 small mb-0">Belum ada riwayat pemindaian yang tersimpan dalam sistem.</p>
        </div>
    <?php endif; ?>
</div>

<?php require APPROOT . '/views/templates/customer_footer.php'; ?>