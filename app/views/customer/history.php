<?php
function scoreClass($s) { if($s===null)return''; return $s>=75?'good':($s>=50?'fair':'poor'); }
function sunBadgeClass($st) { $m=['Protected'=>'protected','Partially Protected'=>'partial','Unprotected'=>'unprotected']; return $m[$st]??'unprotected'; }
function sunIcon($st) { if($st==='Protected')return'<i class="fa-solid fa-shield-check"></i>'; if($st==='Partially Protected')return'<i class="fa-solid fa-shield-halved"></i>'; return'<i class="fa-solid fa-shield-slash"></i>'; }

require APPROOT . '/views/templates/customer_header.php';
?>

<!-- Toolbar -->
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div class="d-flex align-items-center gap-2">
        <a href="<?php echo URLROOT; ?>/customer/dashboard" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            <i class="fa-solid fa-arrow-left me-1"></i>Dashboard
        </a>
        <span class="small" style="color: #c9ded5;">/ Riwayat Pemindaian Kulit</span>
    </div>
    <a href="<?php echo URLROOT; ?>/scan/faceScan" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
        <i class="fa-solid fa-camera me-1"></i>Pemindaian Wajah Baru
    </a>
</div>

<!-- Stats Strip -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="sc-stat-card">
            <div class="sc-stat-icon score"><i class="fa-solid fa-clipboard-check"></i></div>
            <div>
                <div class="sc-stat-value"><?php echo count($scans); ?></div>
                <div class="sc-stat-label">Total Pemeriksaan</div>
            </div>
        </div>
    </div>
    <?php if (!empty($scans)) :
        $avgScore = array_sum(array_column(array_map(fn($s) => ['v' => $s->overall_score ?? 0], $scans), 'v')) / count($scans);
        $protected = count(array_filter($scans, fn($s) => ($s->sunscreen_status ?? '') === 'Protected'));
    ?>
    <div class="col-sm-4">
        <div class="sc-stat-card">
            <div class="sc-stat-icon score"><i class="fa-solid fa-chart-line"></i></div>
            <div>
                <div class="sc-stat-value"><?php echo round($avgScore); ?></div>
                <div class="sc-stat-label">Rata-Rata Skor</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="sc-stat-card">
            <div class="sc-stat-icon sun"><i class="fa-solid fa-shield-halved"></i></div>
            <div>
                <div class="sc-stat-value"><?php echo $protected; ?></div>
                <div class="sc-stat-label">Proteksi Optimal</div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- History Table -->
<div class="sc-card">
    <div class="sc-card-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="fa-solid fa-clock-rotate-left" style="color: #dfc68b;"></i>
            <h5 class="mb-0 fw-bold">Seluruh Rekaman Diagnostik Wajah</h5>
        </div>
        <span class="small" style="color: #94a3b8;">Diurutkan dari pemeriksaan terbaru</span>
    </div>
    <?php if (!empty($scans)) : ?>
        <div class="table-responsive">
            <table class="table sc-history-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Waktu & Tanggal</th>
                        <th>Jerawat</th>
                        <th>Kemerahan</th>
                        <th>Tekstur</th>
                        <th>Hiperpigmentasi</th>
                        <th>Proteksi UV</th>
                        <th>Skor</th>
                        <th class="text-end">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($scans as $s) :
                        $sunSt = $s->sunscreen_status ?? 'N/A';
                        $sc    = scoreClass($s->overall_score);
                        $isScanFailed = ($s->status ?? '') === 'failed';
                        $isScanProc = ($s->status ?? '') === 'processing' || ($s->status ?? '') === 'pending';
                    ?>
                        <tr>
                            <td>
                                <div class="fw-semibold text-dark"><?php echo date('d M Y', strtotime($s->created_at)); ?></div>
                                <small class="text-muted"><?php echo date('H:i', strtotime($s->created_at)); ?> WIB</small>
                                <?php if ($isScanFailed) : ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle ms-1" style="font-size:0.68rem;">Gagal</span>
                                <?php elseif ($isScanProc) : ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle ms-1" style="font-size:0.68rem;">Memproses</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="sc-badge-score"><?php echo $s->acne_score ?? '--'; ?></span></td>
                            <td><span class="sc-badge-score"><?php echo $s->redness_score ?? '--'; ?></span></td>
                            <td><span class="sc-badge-score"><?php echo $s->texture_score ?? '--'; ?></span></td>
                            <td><span class="sc-badge-score"><?php echo $s->hyperpigmentation_score ?? '--'; ?></span></td>
                            <td>
                                <span class="sc-sun-badge <?php echo sunBadgeClass($sunSt); ?>">
                                    <?php echo sunIcon($sunSt); ?> <?php echo htmlspecialchars($sunSt); ?>
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold fs-6"
                                      style="color:var(--<?php echo $sc==='good'?'color-score-good':($sc==='fair'?'color-score-fair':'color-score-poor'); ?>);">
                                    <?php echo $s->overall_score ?? '--'; ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="<?php echo URLROOT; ?>/customer/scanDetail/<?php echo $s->id; ?>"
                                   class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                    Detail Diagnostik
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else : ?>
        <div class="sc-empty-state py-5 text-center">
            <i class="fa-regular fa-folder-open mb-2 fs-3 text-muted"></i>
            <h6 class="fw-bold text-dark mb-1">Belum Ada Rekaman Riwayat</h6>
            <p class="text-muted small mb-3">Lakukan pemindaian wajah pertama Anda untuk mulai memantau tren perkembangan kesehatan kulit.</p>
            <a href="<?php echo URLROOT; ?>/scan/faceScan" class="btn btn-primary btn-sm rounded-pill px-4">
                Mulai Pemindaian Wajah
            </a>
        </div>
    <?php endif; ?>
</div>

<?php require APPROOT . '/views/templates/customer_footer.php'; ?>
