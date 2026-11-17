<?php require APPROOT . '/views/templates/admin_header.php'; ?>

<!-- Admin Dashboard Workspace -->
<div class="row g-3 mb-4">

    <!-- Total Customers Card -->
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 border-0 shadow-sm bg-white rounded-4 d-flex flex-row align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:46px;height:46px;">
                <i class="fa-solid fa-users fs-5"></i>
            </div>
            <div>
                <span class="text-muted small d-block">Customers</span>
                <h3 class="fw-bold mb-0 text-primary"><?php echo $metrics->total_customers; ?></h3>
            </div>
        </div>
    </div>

    <!-- Total Scans Card -->
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 border-0 shadow-sm bg-white rounded-4 d-flex flex-row align-items-center gap-3">
            <div class="bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:46px;height:46px;">
                <i class="fa-solid fa-camera-retro fs-5"></i>
            </div>
            <div>
                <span class="text-muted small d-block">Analysis Scans</span>
                <h3 class="fw-bold mb-0 text-success"><?php echo $metrics->total_scans; ?></h3>
            </div>
        </div>
    </div>

    <!-- Total Cosmetics Card -->
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 border-0 shadow-sm bg-white rounded-4 d-flex flex-row align-items-center gap-3">
            <div class="bg-warning bg-opacity-10 text-warning rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:46px;height:46px;">
                <i class="fa-solid fa-prescription-bottle-medical fs-5"></i>
            </div>
            <div>
                <span class="text-muted small d-block">Skincare Catalogue</span>
                <h3 class="fw-bold mb-0 text-warning"><?php echo $metrics->total_products; ?></h3>
            </div>
        </div>
    </div>

    <!-- Average Health Index Card -->
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 border-0 shadow-sm bg-white rounded-4 d-flex flex-row align-items-center gap-3">
            <div class="bg-info bg-opacity-10 text-info rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:46px;height:46px;">
                <i class="fa-solid fa-square-poll-vertical fs-5"></i>
            </div>
            <div>
                <span class="text-muted small d-block">Average Health Score</span>
                <h3 class="fw-bold mb-0 text-info"><?php echo $metrics->avg_score; ?>/100</h3>
            </div>
        </div>
    </div>
</div>

<!-- Gemini / NVIDIA AI Token & Engine Monitor Widget -->
<?php 
$tm = $data['tokenMetrics'] ?? (object)['total_tokens' => 0, 'prompt_tokens' => 0, 'completion_tokens' => 0, 'total_requests' => 0, 'tokens_today' => 0, 'latest_model' => 'gemini-flash-lite-latest', 'tier_status' => 'Free Tier (Normal)']; 
$activeAiProvider = Setting::getVal('ai_provider', 'gemini');
$isNvidia = ($activeAiProvider === 'nvidia');

$currentNvidiaModel = Setting::getVal('nvidia_model', 'nvidia/nemotron-3.5-bolt-e4b');
$currentGeminiModel = Setting::getVal('gemini_primary_model', 'gemini-flash-lite-latest');
$activePrimaryModel = $isNvidia ? $currentNvidiaModel : $currentGeminiModel;

$dashboardEngineTitle = $isNvidia ? 'NVIDIA NIM Token Monitor' : 'Google Gemini Token Monitor';
$dashboardEngineDesc = $isNvidia 
    ? 'Pemantauan konsumsi kuota token inferensi NVIDIA NIM Cloud dan kuota aktivitas real-time sistem SkinCheck.'
    : 'Pemantauan konsumsi kuota token Google Gemini API dan kuota inferensi real-time sistem SkinCheck.';
?>
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(150deg, rgba(255, 255, 255, 0.84) 0%, rgba(236, 249, 246, 0.72) 100%) !important; backdrop-filter: blur(22px) saturate(185%); -webkit-backdrop-filter: blur(22px) saturate(185%); border: 1px solid rgba(255, 255, 255, 0.92) !important;">
    <div class="card-body p-4">
        <div class="row align-items-center g-4">
            <!-- Left: Engine & Model Status -->
            <div class="col-lg-4 border-end-lg" style="border-color: #dce8e4 !important;">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: #e6f4f1; border: 1px solid #cce6df; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fa-solid fa-microchip text-primary fs-5"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h5 class="mb-0 fw-bold text-dark"><?php echo $dashboardEngineTitle; ?></h5>
                            <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle" style="font-size: 0.68rem;">Live Active</span>
                        </div>
                        <small class="text-muted">Mesin Aktif: <code style="color: var(--primary-color); background: #e6f4f1; padding: 2px 7px; border-radius: 4px;"><?php echo htmlspecialchars($activePrimaryModel); ?></code></small>
                    </div>
                </div>
                <p class="mb-0 small text-muted" style="font-size: 0.82rem; line-height: 1.45;">
                    <?php echo $dashboardEngineDesc; ?>
                </p>
            </div>

            <!-- Middle: Main Token Counter -->
            <div class="col-lg-5">
                <div class="row g-3 text-center text-sm-start">
                    <div class="col-sm-6">
                        <span class="small d-block mb-1 text-muted" style="font-size: 0.8rem;">
                            <i class="fa-solid fa-coins me-1 text-primary"></i>Total Token Terpakai
                        </span>
                        <h2 class="fw-bold mb-0 text-primary" style="letter-spacing: -0.5px;">
                            <?php echo number_format($tm->total_tokens); ?>
                            <span class="text-muted" style="font-size: 0.85rem; font-weight: 500;">Tokens</span>
                        </h2>
                        <small class="text-muted" style="font-size: 0.75rem;">Hari ini: <strong class="text-dark">+<?php echo number_format($tm->tokens_today); ?></strong></small>
                    </div>
                    <div class="col-sm-6">
                        <span class="small d-block mb-1 text-muted" style="font-size: 0.8rem;">
                            <i class="fa-solid fa-arrow-right-arrow-left me-1 text-primary"></i>Rincian Token (In / Out)
                        </span>
                        <div class="d-flex align-items-center gap-3">
                            <div>
                                <div class="fw-bold text-dark small"><?php echo number_format($tm->prompt_tokens); ?></div>
                                <span class="text-muted" style="font-size: 0.72rem;">Input (Prompt)</span>
                            </div>
                            <div style="width: 1px; height: 26px; background: #dce8e4;"></div>
                            <div>
                                <div class="fw-bold text-dark small"><?php echo number_format($tm->completion_tokens); ?></div>
                                <span class="text-muted" style="font-size: 0.72rem;">Output (Response)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Actions & Tier -->
            <div class="col-lg-3 text-lg-end">
                <div class="mb-2">
                    <span class="badge rounded-pill px-3 py-1 sc-badge-gold" style="font-size: 0.75rem;">
                        <i class="fa-solid fa-shield-halved me-1"></i>Status: <?php echo htmlspecialchars($tm->tier_status ?? 'Free Tier (Normal)'); ?>
                    </span>
                </div>
                <div class="small mb-2 text-muted" style="font-size: 0.75rem;">
                    Total Panggilan: <strong class="text-dark"><?php echo number_format($tm->total_requests); ?> kali</strong>
                </div>
                <a href="<?php echo URLROOT; ?>/admin/aiAssistant" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.82rem;">
                    <i class="fa-solid fa-robot me-2"></i>Buka AI Console <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Tabs for Admin Tasks -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header border-0 pt-4 px-4 pb-2 text-center">
        <div class="d-flex justify-content-center">
            <ul class="nav nav-pills sc-admin-nav-capsule border-0 mb-0" id="adminTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-semibold" id="customers-tab"
                        data-bs-toggle="tab" data-bs-target="#customers-pane" type="button" role="tab"
                        aria-controls="customers-pane" aria-selected="true">
                        <i class="fa-solid fa-users"></i>
                        <span>Customer Registry</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" id="products-tab" data-bs-toggle="tab"
                        data-bs-target="#products-pane" type="button" role="tab" aria-controls="products-pane"
                        aria-selected="false">
                        <i class="fa-solid fa-prescription-bottle-medical"></i>
                        <span>Cosmetics Catalog</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" id="scans-tab" data-bs-toggle="tab"
                        data-bs-target="#scans-pane" type="button" role="tab" aria-controls="scans-pane"
                        aria-selected="false">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span>Scan Audits</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <div class="card-body p-4">
        <div class="tab-content" id="adminTabContent">

            <!-- 1. CUSTOMERS MANAGEMENT WORKSPACE -->
            <div class="tab-pane fade show active" id="customers-pane" role="tabpanel" aria-labelledby="customers-tab">
                <div
                    class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
                    <div>
                        <h4 class="fw-bold mb-1">Customer User Accounts</h4>
                        <p class="text-muted small mb-0">Total active registered customers in system.</p>
                    </div>
                    <button class="btn btn-primary d-flex align-items-center gap-2"
                        onclick="showToast('Fitur Tambah Pelanggan akan segera tersedia.', 'info');">
                        <i class="fa-solid fa-plus"></i>Create Customer
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Registered Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($customers as $customer): ?>
                                <tr>
                                    <td>#<?php echo $customer->id; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center fw-bold text-secondary"
                                                style="width: 32px; height: 32px; font-size: 0.85rem;">
                                                <?php echo strtoupper(substr($customer->name, 0, 1)); ?>
                                            </div>
                                            <span
                                                class="fw-semibold text-dark"><?php echo htmlspecialchars($customer->name); ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($customer->email); ?></td>
                                    <td><small
                                            class="text-muted"><?php echo date('d M Y, H:i', strtotime($customer->created_at)); ?></small>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-secondary"
                                                onclick="showToast('Edit data pelanggan #<?php echo $customer->id; ?> dalam proses pengembangan.', 'info');">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <button class="btn btn-outline-danger"
                                                onclick="confirm('Are you sure you want to delete customer ID: <?php echo $customer->id; ?>?')">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. COSMETICS PRODUCTS CATALOGUE -->
            <div class="tab-pane fade" id="products-pane" role="tabpanel" aria-labelledby="products-tab">
                <div
                    class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
                    <div>
                        <h4 class="fw-bold mb-1">Cosmetic Products Catalog</h4>
                        <p class="text-muted small mb-0">Manage recommendations mapping for sunscreen and cleanser
                            ingredients.</p>
                    </div>
                    <button class="btn btn-primary d-flex align-items-center gap-2"
                        onclick="showToast('Fitur Tambah Produk Kosmetik akan segera hadir.', 'info');">
                        <i class="fa-solid fa-plus"></i>Add Cosmetic Product
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Product Details</th>
                                <th>Category</th>
                                <th>Target Skin</th>
                                <th>Pricing</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>#<?php echo $product->id; ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($product->name); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($product->brand); ?></small>
                                    </td>
                                    <td><span
                                            class="badge bg-secondary-subtle text-secondary py-1 px-2 rounded"><?php echo htmlspecialchars($product->category); ?></span>
                                    </td>
                                    <td><small
                                            class="fw-semibold text-dark"><?php echo htmlspecialchars($product->skin_type); ?></small>
                                    </td>
                                    <td><span class="fw-bold text-success">IDR
                                            <?php echo number_format($product->price, 0, ',', '.'); ?></span></td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-secondary"
                                                onclick="showToast('Edit produk #<?php echo $product->id; ?> dalam proses pengembangan.', 'info');">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <button class="btn btn-outline-danger"
                                                onclick="confirm('Are you sure you want to delete product ID: <?php echo $product->id; ?>?')">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. SCAN DIAGNOSTICS AUDIT LOGS -->
            <div class="tab-pane fade" id="scans-pane" role="tabpanel" aria-labelledby="scans-tab">
                <div class="mb-4">
                    <h4 class="fw-bold mb-1">Dermatological Diagnostics Audits</h4>
                    <p class="text-muted small mb-0">System audit log of facial scans conducted by platform users.</p>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Scan ID</th>
                                <th>Customer User</th>
                                <th>Skin Score</th>
                                <th>Sunscreen Status</th>
                                <th>Analyzed At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($scans as $scan): ?>
                                <tr>
                                    <td>#<?php echo $scan->id; ?></td>
                                    <td><span
                                            class="fw-semibold text-dark"><?php echo htmlspecialchars($scan->user_name); ?></span>
                                    </td>
                                    <td><span
                                            class="badge bg-success-subtle text-success py-1 px-2 rounded fw-bold border border-success border-opacity-10"><?php echo $scan->score; ?>
                                            / 100</span></td>
                                    <td>
                                        <?php
                                        $badgeClass = 'bg-danger-subtle text-danger border-danger';
                                        if ($scan->sunscreen_status === 'Protected')
                                            $badgeClass = 'bg-success-subtle text-success border-success';
                                        if ($scan->sunscreen_status === 'Partially Protected')
                                            $badgeClass = 'bg-warning-subtle text-warning border-warning';
                                        ?>
                                        <span
                                            class="badge <?php echo $badgeClass; ?> border border-opacity-10 py-1 px-2 rounded-pill small">
                                            <?php echo htmlspecialchars($scan->sunscreen_status); ?>
                                        </span>
                                    </td>
                                    <td><small
                                            class="text-muted"><?php echo date('d M Y, H:i:s', strtotime($scan->created_at)); ?></small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require APPROOT . '/views/templates/admin_footer.php'; ?>