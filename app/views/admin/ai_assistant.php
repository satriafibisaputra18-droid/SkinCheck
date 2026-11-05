<?php require APPROOT . '/views/templates/admin_header.php'; ?>

<!-- Executive Toolbar -->
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div class="d-flex align-items-center gap-2">
        <a href="<?php echo URLROOT; ?>/admin/dashboard" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i>Dashboard
        </a>
        <span class="text-muted small">/ Executive AI Assistant & Analytics Console</span>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <span class="badge border px-3 py-2 rounded-pill small" style="background: rgba(191, 161, 95, 0.15); color: #8f7435; border-color: rgba(191, 161, 95, 0.4) !important;">
            <i class="fa-solid fa-coins me-1.5" style="color: #bfa15f;"></i><strong id="scHeaderTotalTokens"><?php echo number_format($data['tokenMetrics']->total_tokens ?? 0); ?></strong> Tokens Digunakan
        </span>
        <span class="badge bg-success-subtle text-success border border-success border-opacity-25 px-3 py-2 rounded-pill small">
            <i class="fa-solid fa-circle text-success me-1" style="font-size:0.55rem;"></i>Gemini Flash & Node.js Live
        </span>
        <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill small">
            <i class="fa-solid fa-database text-primary me-1"></i>Database RBAC: Admin Full
        </span>
        <button id="scAdminChatReset" class="btn btn-sm btn-outline-danger rounded-pill px-3">
            <i class="fa-solid fa-rotate-right me-1"></i>Reset Sesi
        </button>
    </div>
</div>

<!-- Executive Metric Quick-Bar -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 rounded-4 bg-white d-flex flex-row align-items-center justify-content-between sc-admin-stat-card" data-prompt="Berapa total customer terdaftar di database dan bagaimana profil aktivitas mereka?">
            <div>
                <span class="text-muted small d-block">Total Customers</span>
                <h4 class="fw-bold mb-0 text-primary"><?php echo $metrics->total_customers; ?></h4>
            </div>
            <div class="bg-primary bg-opacity-10 text-primary p-2.5 rounded-3">
                <i class="fa-solid fa-users fs-5"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 rounded-4 bg-white d-flex flex-row align-items-center justify-content-between sc-admin-stat-card" data-prompt="Bagaimana statistik keseluruhan scan wajah dan status perlindungan sunscreen customer?">
            <div>
                <span class="text-muted small d-block">Analysis Scans</span>
                <h4 class="fw-bold mb-0 text-success"><?php echo $metrics->total_scans; ?></h4>
            </div>
            <div class="bg-success bg-opacity-10 text-success p-2.5 rounded-3">
                <i class="fa-solid fa-camera-retro fs-5"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 rounded-4 bg-white d-flex flex-row align-items-center justify-content-between sc-admin-stat-card" data-prompt="Produk apa saja yang ada di katalog skincare SkinCheck saat ini?">
            <div>
                <span class="text-muted small d-block">Skincare Catalog</span>
                <h4 class="fw-bold mb-0 text-warning"><?php echo $metrics->total_products; ?></h4>
            </div>
            <div class="bg-warning bg-opacity-10 text-warning p-2.5 rounded-3">
                <i class="fa-solid fa-prescription-bottle-medical fs-5"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 rounded-4 bg-white d-flex flex-row align-items-center justify-content-between sc-admin-stat-card" data-prompt="Bagaimana evaluasi rata-rata skor kesehatan kulit customer (78.5/100) dan rekomendasi perbaikannya?">
            <div>
                <span class="text-muted small d-block">Average Health Score</span>
                <h4 class="fw-bold mb-0 text-info"><?php echo $metrics->avg_score; ?>/100</h4>
            </div>
            <div class="bg-info bg-opacity-10 text-info p-2.5 rounded-3">
                <i class="fa-solid fa-square-poll-vertical fs-5"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">

    <!-- Main Chat Workspace (8 cols) -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 d-flex flex-column" style="height: 700px; max-height: calc(100vh - 220px); background: #ffffff;">
            
            <!-- Card Header -->
            <div class="p-3 px-4 border-bottom d-flex justify-content-between align-items-center bg-white rounded-top-4">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:42px;height:42px;border-radius:12px;background:linear-gradient(135deg, #1a4a40 0%, #2f7a6b 100%);display:flex;align-items:center;justify-content:center;box-shadow: 0 4px 12px rgba(26,74,64,0.25);">
                        <i class="fa-solid fa-robot text-white fs-5"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="mb-0 fw-bold text-dark" style="font-size:1.05rem;">SkinCheck AI Executive Console</h5>
                            <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 rounded-pill px-2 py-0.5" style="font-size:0.65rem;">ADMIN CLEARANCE</span>
                        </div>
                        <small class="text-muted">Kecerdasan Buatan Terintegrasi Database & Gemini AI Studio</small>
                    </div>
                </div>
                <div class="d-none d-sm-flex align-items-center gap-2 text-muted small">
                    <span class="sc-pulse-dot"></span>
                    <span>Database Sync Active</span>
                </div>
            </div>

            <!-- Chat Messages Log -->
            <div class="flex-grow-1 overflow-auto p-3 p-md-4 d-flex flex-column gap-3" id="scAdminChatMessages" style="background:#f8faf9;">
                
                <!-- Welcome Card -->
                <div class="d-flex gap-3 mb-2 sc-admin-welcome">
                    <div style="width:38px;height:38px;border-radius:10px;background:#1a4a40;color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fa-solid fa-chart-line text-warning" style="font-size:0.95rem;"></i>
                    </div>
                    <div style="max-width:88%;">
                        <div class="p-4 rounded-4 bg-white border shadow-sm" style="font-size:0.9rem;line-height:1.6;">
                            <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                                <h6 class="fw-bold text-primary mb-0">
                                    <i class="fa-solid fa-shield-halved me-1 text-success"></i> Halo Administrator <?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?>!
                                </h6>
                                <span class="badge bg-success-subtle text-success small">Hak Akses Penuh</span>
                            </div>
                            <p class="mb-2 text-dark">
                                Selamat datang di <strong>Executive AI Console</strong>. Sebagai Administrator, Anda memiliki otorisasi penuh untuk melakukan audit statistik platform, memeriksa ringkasan data pengguna, menganalisis pola hasil scan wajah, serta meminta rekomendasi formulasi klinis berdasarkan database SkinCheck.
                            </p>
                            <p class="mb-3 text-muted small">
                                💡 Klik salah satu topik cepat di bawah ini atau ketikkan pertanyaan audit spesifik Anda:
                            </p>

                            <!-- Executive Prompt Chips -->
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill sc-admin-prompt-pill" data-text="Bagaimana ringkasan statistik dan metrik platform SkinCheck saat ini?">
                                    📊 Ringkasan Metrik Platform
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill sc-admin-prompt-pill" data-text="Berapa total customer, admin, dan scan yang sudah selesai di database?">
                                    👥 Jumlah User & Scan
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill sc-admin-prompt-pill" data-text="Produk skincare apa saja yang aktif di katalog database saat ini?">
                                    🛍️ Katalog Produk Aktif
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill sc-admin-prompt-pill" data-text="Apa saja faktor utama yang paling mempengaruhi penurunan skor kesehatan kulit customer?">
                                    📈 Analisis Tren Kesehatan Kulit
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill sc-admin-prompt-pill" data-text="Berikan rekomendasi perbaikan formula dan pemilihan produk untuk meningkatkan efektivitas proteksi sunscreen customer.">
                                    🧴 Rekomendasi Klinis Formula
                                </button>
                            </div>
                        </div>
                        <span class="text-muted small mt-1 d-block ps-1" style="font-size:0.72rem;">SkinCheck AI Executive Assistant • Otoritas Penuh</span>
                    </div>
                </div>

                <!-- Typing indicator -->
                <div id="scAdminTyping" class="d-none align-items-center gap-2 p-2 px-3 bg-white border rounded-pill shadow-sm" style="width:fit-content;">
                    <div class="spinner-grow spinner-grow-sm text-primary" role="status"></div>
                    <span class="small text-muted">SkinCheck AI sedang menganalisis data platform...</span>
                </div>
            </div>

            <!-- Chat Input Footer -->
            <div class="p-3 px-4 border-top bg-white rounded-bottom-4">
                <form id="scAdminChatForm" class="d-flex gap-2 align-items-center">
                    <input type="text" id="scAdminChatInput" class="form-control rounded-pill px-3 py-2.5" placeholder="Tanyakan data pengguna, total scan, tren kulit, atau analisis klinis..." autocomplete="off">
                    <button type="submit" id="scAdminChatSubmit" class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width:44px;height:44px;flex-shrink:0;">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </form>
                <div class="d-flex justify-content-between align-items-center mt-2 px-2" style="font-size:0.75rem; color:#789088;">
                    <span><i class="fa-solid fa-lock me-1 text-success"></i> Sesi terenkripsi & diautentikasi dengan ID Admin: <strong>#<?php echo (int)$_SESSION['user_id']; ?></strong></span>
                    <span>Tekan <strong>Enter</strong> untuk mengirim</span>
                </div>
            </div>

        </div>
    </div>

    <!-- Executive Insights Sidebar (4 cols) -->
    <div class="col-lg-4">
        
        <!-- AI Token & Consumption Monitor Card -->
        <?php $tm = $data['tokenMetrics'] ?? (object)['total_tokens' => 0, 'prompt_tokens' => 0, 'completion_tokens' => 0, 'total_requests' => 0, 'tokens_today' => 0, 'avg_tokens_per_req' => 0, 'latest_model' => 'gemini-flash-lite-latest', 'model_breakdown' => []]; ?>
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
            <div class="card-header bg-white border-0 pt-3 pb-2 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-coins me-2" style="color: #bfa15f;"></i>Konsumsi Token AI
                </h6>
                <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(34, 197, 94, 0.12); color: #16a34a; font-size: 0.65rem; border: 1px solid rgba(34, 197, 94, 0.25);">
                    <i class="fa-solid fa-circle text-success me-1" style="font-size:0.45rem;"></i>Active Monitor
                </span>
            </div>
            <div class="card-body pt-1 pb-3">
                <!-- Big Total Counter -->
                <div class="p-3 rounded-3 mb-3" style="background: linear-gradient(135deg, rgba(20, 60, 48, 0.06) 0%, rgba(191, 161, 95, 0.1) 100%); border: 1px solid rgba(191, 161, 95, 0.25);">
                    <span class="text-muted small d-block mb-1" style="font-size: 0.78rem;">Total Token Digunakan</span>
                    <div class="d-flex align-items-baseline gap-2">
                        <h3 class="fw-bold mb-0 text-dark" id="scSidebarTotalTokens" style="letter-spacing: -0.5px;">
                            <?php echo number_format($tm->total_tokens); ?>
                        </h3>
                        <span class="text-muted small fw-semibold">Tokens</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top" style="border-color: rgba(0,0,0,0.06) !important; font-size: 0.75rem;">
                        <span class="text-muted">Hari ini:</span>
                        <span class="fw-bold text-success">+<?php echo number_format($tm->tokens_today); ?> tokens</span>
                    </div>
                </div>

                <!-- Input vs Output Breakdown -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between small text-muted mb-1" style="font-size: 0.78rem;">
                        <span>Prompt: <strong class="text-dark" id="scSidebarPromptTokens"><?php echo number_format($tm->prompt_tokens); ?></strong></span>
                        <span>Output: <strong class="text-dark" id="scSidebarCompTokens"><?php echo number_format($tm->completion_tokens); ?></strong></span>
                    </div>
                    <?php 
                        $pctPrompt = $tm->total_tokens > 0 ? round(($tm->prompt_tokens / $tm->total_tokens) * 100) : 70;
                        $pctComp = 100 - $pctPrompt;
                    ?>
                    <div class="progress" style="height: 7px; border-radius: 10px; background: #e2e8f0;">
                        <div class="progress-bar" role="progressbar" style="width: <?php echo $pctPrompt; ?>%; background: #1a4a40;" title="Input Prompt: <?php echo $pctPrompt; ?>%"></div>
                        <div class="progress-bar" role="progressbar" style="width: <?php echo $pctComp; ?>%; background: #dfc68b;" title="Response Output: <?php echo $pctComp; ?>%"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-1 text-muted" style="font-size: 0.68rem;">
                        <span><i class="fa-solid fa-circle me-1" style="color: #1a4a40; font-size: 0.5rem;"></i>Input Prompt (<?php echo $pctPrompt; ?>%)</span>
                        <span><i class="fa-solid fa-circle me-1" style="color: #dfc68b; font-size: 0.5rem;"></i>Output (<?php echo $pctComp; ?>%)</span>
                    </div>
                </div>

                <!-- Metrics List -->
                <ul class="list-unstyled mb-0" style="font-size: 0.8rem;">
                    <li class="d-flex justify-content-between py-1.5 border-bottom" style="border-color: #f1f5f9 !important;">
                        <span class="text-muted">Total Panggilan AI</span>
                        <strong class="text-dark" id="scSidebarReqCount"><?php echo number_format($tm->total_requests); ?> req</strong>
                    </li>
                    <li class="d-flex justify-content-between py-1.5 border-bottom" style="border-color: #f1f5f9 !important;">
                        <span class="text-muted">Rata-rata per Chat</span>
                        <strong class="text-dark"><?php echo number_format($tm->avg_tokens_per_req); ?> tok/req</strong>
                    </li>
                    <li class="d-flex justify-content-between py-1.5 border-bottom" style="border-color: #f1f5f9 !important;">
                        <span class="text-muted">Mesin Aktif</span>
                        <code class="text-success small fw-semibold"><?php echo htmlspecialchars($tm->latest_model); ?></code>
                    </li>
                    <li class="d-flex justify-content-between py-1.5">
                        <span class="text-muted">Status Kuota</span>
                        <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">Normal • Free Tier</span>
                    </li>
                </ul>

                <?php if (!empty($tm->model_breakdown)): ?>
                    <div class="mt-2.5 pt-2 border-top" style="border-color: #f1f5f9 !important;">
                        <span class="text-muted d-block mb-1.5" style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Sebaran Model:</span>
                        <?php foreach ($tm->model_breakdown as $mb): ?>
                            <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 0.75rem;">
                                <span class="text-truncate text-secondary" style="max-width: 140px;"><?php echo htmlspecialchars($mb->model); ?></span>
                                <span class="fw-semibold text-dark"><?php echo number_format($mb->tokens_count); ?> tok</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Privilege Badge Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
            <div class="card-header bg-white border-0 pt-3 pb-1">
                <h6 class="fw-bold mb-0 text-primary">
                    <i class="fa-solid fa-user-shield me-2 text-warning"></i>Hak Akses AI Administrator
                </h6>
            </div>
            <div class="card-body pt-2">
                <ul class="list-unstyled mb-0" style="font-size: 0.84rem;">
                    <li class="d-flex gap-2.5 mb-2.5 text-secondary">
                        <i class="fa-solid fa-check text-success mt-1"></i>
                        <span>Akses analitik metrik agregat database (Total User, Scan, Produk).</span>
                    </li>
                    <li class="d-flex gap-2.5 mb-2.5 text-secondary">
                        <i class="fa-solid fa-check text-success mt-1"></i>
                        <span>Audit kesehatan rata-rata kulit & tingkat proteksi sunscreen.</span>
                    </li>
                    <li class="d-flex gap-2.5 mb-2.5 text-secondary">
                        <i class="fa-solid fa-check text-success mt-1"></i>
                        <span>Rekomendasi pengembangan katalog produk berbasis dermatologi.</span>
                    </li>
                    <li class="d-flex gap-2.5 text-secondary">
                        <i class="fa-solid fa-shield-halved text-primary mt-1"></i>
                        <span><strong>RBAC Guardrail</strong>: User publik tidak dapat mengakses metrik internal ini.</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Quick Audit Actions -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
            <div class="card-header bg-white border-0 pt-3 pb-1">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-bolt me-2 text-warning"></i>Pintasan Audit Cepat
                </h6>
            </div>
            <div class="card-body pt-2 d-grid gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm text-start py-2 px-3 rounded-3 sc-admin-prompt-pill" data-text="Berapa perbandingan customer yang terlindungi penuh vs belum terlindungi sunscreen?">
                    <i class="fa-solid fa-sun me-2 text-warning"></i>Audit Proteksi Sunscreen
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm text-start py-2 px-3 rounded-3 sc-admin-prompt-pill" data-text="Bagaimana sebaran produk menurut kategori (Sunscreen, Serum, Moisturizer, Cleanser)?">
                    <i class="fa-solid fa-layer-group me-2 text-info"></i>Audit Kategori Produk
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm text-start py-2 px-3 rounded-3 sc-admin-prompt-pill" data-text="Kriteria apa saja yang menentukan seorang customer mendapat skor kulit di atas 85?">
                    <i class="fa-solid fa-star me-2 text-success"></i>Kriteria Skor Kulit Optimal
                </button>
            </div>
        </div>

        <!-- System Architecture & Security Notice -->
        <div class="alert alert-success border-0 rounded-4 shadow-sm" style="font-size:0.8rem; background: linear-gradient(135deg, #eef9f5 0%, #e2f4ed 100%);">
            <div class="d-flex gap-2 mb-1 fw-bold text-success">
                <i class="fa-solid fa-circle-check mt-0.5"></i>
                <span>Status Sistem: Terverifikasi</span>
            </div>
            <p class="mb-0 text-muted" style="line-height:1.5;">
                Chatbot ditenagai arsitektur hybrid Express.js Node.js (Port 5000) dan fallback PHP MVC Bridge terhubung langsung ke Google Gemini AI Studio API.
            </p>
        </div>

    </div>

</div>

<!-- Admin Chat Console Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatForm = document.getElementById('scAdminChatForm');
    const chatInput = document.getElementById('scAdminChatInput');
    const msgContainer = document.getElementById('scAdminChatMessages');
    const typingIndicator = document.getElementById('scAdminTyping');
    const resetBtn = document.getElementById('scAdminChatReset');

    const NODE_URL = 'http://localhost:5000/api/chat';
    const PHP_URL  = '<?php echo URLROOT; ?>/chatbot/chat';

    let history = [];

    // Prompt pills click
    document.querySelectorAll('.sc-admin-prompt-pill').forEach(btn => {
        btn.addEventListener('click', function() {
            const promptText = this.getAttribute('data-text');
            if (promptText) {
                sendAdminMessage(promptText);
            }
        });
    });

    // Stat cards click
    document.querySelectorAll('.sc-admin-stat-card').forEach(card => {
        card.style.cursor = 'pointer';
        card.addEventListener('click', function() {
            const promptText = this.getAttribute('data-prompt');
            if (promptText) {
                sendAdminMessage(promptText);
            }
        });
    });

    // Form submit
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const text = chatInput.value.trim();
        if (!text) return;
        chatInput.value = '';
        sendAdminMessage(text);
    });

    // Reset button
    resetBtn.addEventListener('click', function() {
        if (confirm('Bersihkan percakapan sesi ini dan mulai log baru?')) {
            history = [];
            const msgs = msgContainer.querySelectorAll('.sc-admin-msg');
            msgs.forEach(m => m.remove());
        }
    });

    async function sendAdminMessage(text) {
        appendAdminUserMsg(text);
        history.push({ role: 'user', text: text });

        showAdminTyping(true);

        const adminPayload = {
            id: <?php echo (int)($_SESSION['user_id'] ?? 0); ?>,
            name: <?php echo json_encode($_SESSION['user_name'] ?? 'Administrator'); ?>,
            email: <?php echo json_encode($_SESSION['user_email'] ?? ''); ?>,
            role: 'admin'
        };

        try {
            let resData = null;

            // Attempt 1: Node.js express backend (port 5000)
            try {
                const res = await fetch(NODE_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        message: text,
                        history: history.slice(-8),
                        user: adminPayload
                    })
                });
                if (res.ok) {
                    resData = await res.json();
                } else {
                    throw new Error('Node server returned ' + res.status);
                }
            } catch (nodeErr) {
                console.warn('[AdminChat] Direct Node call failed, using PHP bridge:', nodeErr.message);
                const phpRes = await fetch(PHP_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        message: text,
                        history: history.slice(-8),
                        user: adminPayload
                    })
                });
                resData = await phpRes.json();
            }

            if (resData && resData.success && resData.reply) {
                appendAdminBotMsg(resData.reply, {
                    model: resData.modelUsed,
                    tokens: resData.tokens,
                    durationMs: resData.durationMs
                });
                history.push({ role: 'model', text: resData.reply });

                if (resData.tokens && resData.tokens.total) {
                    updateLiveTokens(resData.tokens);
                }
            } else {
                appendAdminBotMsg('⚠️ Maaf Administrator, terjadi kendala: ' + (resData?.error || 'Silakan coba lagi.'));
            }

        } catch (err) {
            console.error('[AdminChat] Error:', err);
            appendAdminBotMsg('⚠️ Tidak dapat terhubung ke server chatbot. Pastikan server Node.js aktif di port 5000.');
        } finally {
            showAdminTyping(false);
            chatInput.focus();
        }
    }

    function appendAdminUserMsg(text) {
        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const div = document.createElement('div');
        div.className = 'd-flex gap-3 justify-content-end mb-2 sc-admin-msg';
        div.innerHTML = `
            <div style="max-width:85%;" class="text-end">
                <div class="p-3 rounded-4 text-white text-start shadow-sm" style="background: linear-gradient(135deg, #1a4a40 0%, #2f7a6b 100%); font-size:0.9rem; line-height:1.55;">
                    ${escapeHtml(text)}
                </div>
                <span class="text-muted small mt-1 d-block" style="font-size:0.7rem;">${time} • Administrator</span>
            </div>
            <div style="width:36px;height:36px;border-radius:10px;background:#bfa15f;color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fa-solid fa-user-shield" style="font-size:0.9rem;"></i>
            </div>
        `;
        msgContainer.appendChild(div);
        scrollDown();
    }

    function appendAdminBotMsg(markdownText, meta = null) {
        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const div = document.createElement('div');
        div.className = 'd-flex gap-3 mb-2 sc-admin-msg';

        let badgeInfo = `${time} • SkinCheck AI Executive`;
        if (meta && meta.tokens && meta.tokens.total) {
            badgeInfo += ` • <span class="badge bg-light text-dark border px-2 py-0.5"><i class="fa-solid fa-coins text-warning me-1"></i>${meta.tokens.total.toLocaleString()} tokens</span>`;
            if (meta.model) {
                badgeInfo += ` • <span class="text-secondary">${meta.model}</span>`;
            }
        }

        div.innerHTML = `
            <div style="width:36px;height:36px;border-radius:10px;background:#1a4a40;color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fa-solid fa-wand-magic-sparkles text-warning" style="font-size:0.9rem;"></i>
            </div>
            <div style="max-width:85%;">
                <div class="p-3 rounded-4 bg-white border shadow-sm" style="font-size:0.9rem; line-height:1.6;">
                    ${formatMd(markdownText)}
                </div>
                <span class="text-muted small mt-1 d-block" style="font-size:0.7rem;">${badgeInfo}</span>
            </div>
        `;
        msgContainer.appendChild(div);
        scrollDown();
    }

    function updateLiveTokens(tok) {
        const headerEl = document.getElementById('scHeaderTotalTokens');
        const sidebarTotalEl = document.getElementById('scSidebarTotalTokens');
        const sidebarPromptEl = document.getElementById('scSidebarPromptTokens');
        const sidebarCompEl = document.getElementById('scSidebarCompTokens');
        const sidebarReqEl = document.getElementById('scSidebarReqCount');

        if (sidebarTotalEl) {
            const curTot = parseInt(sidebarTotalEl.textContent.replace(/,/g, ''), 10) || 0;
            const newTot = curTot + (tok.total || 0);
            sidebarTotalEl.textContent = newTot.toLocaleString();
            if (headerEl) headerEl.textContent = newTot.toLocaleString();
        }
        if (sidebarPromptEl) {
            const curP = parseInt(sidebarPromptEl.textContent.replace(/,/g, ''), 10) || 0;
            sidebarPromptEl.textContent = (curP + (tok.prompt || 0)).toLocaleString();
        }
        if (sidebarCompEl) {
            const curC = parseInt(sidebarCompEl.textContent.replace(/,/g, ''), 10) || 0;
            sidebarCompEl.textContent = (curC + (tok.completion || 0)).toLocaleString();
        }
        if (sidebarReqEl) {
            const curR = parseInt(sidebarReqEl.textContent.replace(/[^0-9]/g, ''), 10) || 0;
            sidebarReqEl.textContent = (curR + 1).toLocaleString() + ' req';
        }
    }

    function showAdminTyping(show) {
        if (show) {
            typingIndicator.classList.remove('d-none');
            typingIndicator.classList.add('d-flex');
            msgContainer.appendChild(typingIndicator);
        } else {
            typingIndicator.classList.remove('d-flex');
            typingIndicator.classList.add('d-none');
        }
        scrollDown();
    }

    function scrollDown() {
        requestAnimationFrame(() => {
            msgContainer.scrollTop = msgContainer.scrollHeight;
        });
    }

    function escapeHtml(str) {
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function formatMd(text) {
        if (!text) return '';

        // Normalize line breaks
        text = text.replace(/\r\n/g, '\n').replace(/\r/g, '\n');

        // Escape HTML entities first to prevent XSS
        let escaped = escapeHtml(text);

        // Fenced code blocks
        escaped = escaped.replace(/```([a-z0-9_-]*)\n([\s\S]*?)```/gim, function (match, lang, code) {
            return '<pre class="bg-light p-2 rounded small border overflow-auto my-2"><code>' + code.trim() + '</code></pre>';
        });

        // Inline code
        escaped = escaped.replace(/`([^`]+)`/g, '<code class="bg-light px-1 py-0.5 rounded text-danger small">$1</code>');

        // Bold (**text** or __text__)
        escaped = escaped.replace(/(\*\*|__)(.*?)\1/g, '<strong>$2</strong>');

        // Italic (*text* or _text_)
        escaped = escaped.replace(/(\*|_)(.*?)\1/g, '<em>$2</em>');

        // Process line by line for block elements
        const lines = escaped.split('\n');
        let out = [];
        let inUl = false;
        let inOl = false;
        let inTable = false;

        for (let i = 0; i < lines.length; i++) {
            let line = lines[i];
            let trimmed = line.trim();

            // Horizontal rules: --- or *** or ___
            if (/^(\-{3,}|\*{3,}|_{3,})$/.test(trimmed)) {
                if (inUl) { out.push('</ul>'); inUl = false; }
                if (inOl) { out.push('</ol>'); inOl = false; }
                if (inTable) { out.push('</tbody></table></div>'); inTable = false; }
                out.push('<hr class="my-2 opacity-25">');
                continue;
            }

            // Headers: # up to ###### (handles #, ##, ###, ####, #####, ######)
            const headerMatch = line.match(/^(\s*)(#{1,6})\s+(.*)$/);
            if (headerMatch) {
                if (inUl) { out.push('</ul>'); inUl = false; }
                if (inOl) { out.push('</ol>'); inOl = false; }
                if (inTable) { out.push('</tbody></table></div>'); inTable = false; }
                const level = headerMatch[2].length;
                const content = headerMatch[3].trim();
                if (level === 1) {
                    out.push('<h5 class="fw-bold mt-3 mb-2 text-primary">' + content + '</h5>');
                } else if (level === 2) {
                    out.push('<h6 class="fw-bold mt-2 mb-1 text-primary" style="font-size:1.05rem;">' + content + '</h6>');
                } else if (level === 3) {
                    out.push('<h6 class="fw-bold mt-2 mb-1 text-primary" style="font-size:0.98rem;">' + content + '</h6>');
                } else if (level === 4) {
                    out.push('<h6 class="fw-bold mt-2 mb-1 text-dark" style="font-size:0.92rem;">' + content + '</h6>');
                } else if (level === 5) {
                    out.push('<div class="fw-bold text-dark mt-2 mb-1 small">' + content + '</div>');
                } else {
                    out.push('<div class="fw-semibold text-muted mt-1 mb-1 small">' + content + '</div>');
                }
                continue;
            }

            // Blockquotes: > quote
            const quoteMatch = line.match(/^\s*&gt;\s*(.*)$/);
            if (quoteMatch) {
                if (inUl) { out.push('</ul>'); inUl = false; }
                if (inOl) { out.push('</ol>'); inOl = false; }
                if (inTable) { out.push('</tbody></table></div>'); inTable = false; }
                out.push('<blockquote class="border-start border-3 ps-2 text-muted my-2 fst-italic small">' + quoteMatch[1] + '</blockquote>');
                continue;
            }

            // Tables: | col1 | col2 |
            if (trimmed.startsWith('|') && trimmed.endsWith('|')) {
                if (inUl) { out.push('</ul>'); inUl = false; }
                if (inOl) { out.push('</ol>'); inOl = false; }

                if (/^\|(\s*[-:]+[-| :]*)\|$/.test(trimmed)) {
                    continue;
                }

                const cells = trimmed.slice(1, -1).split('|').map(function (c) { return c.trim(); });
                if (!inTable) {
                    out.push('<div class="table-responsive my-2"><table class="table table-sm table-bordered table-striped small mb-0">');
                    out.push('<thead><tr>' + cells.map(function (c) { return '<th class="bg-light fw-bold">' + c + '</th>'; }).join('') + '</tr></thead><tbody>');
                    inTable = true;
                } else {
                    out.push('<tr>' + cells.map(function (c) { return '<td>' + c + '</td>'; }).join('') + '</tr>');
                }
                continue;
            } else if (inTable) {
                out.push('</tbody></table></div>');
                inTable = false;
            }

            // Unordered list items (- or *)
            const ulMatch = line.match(/^\s*[\*\-]\s+(.*)$/);
            if (ulMatch) {
                if (inOl) { out.push('</ol>'); inOl = false; }
                if (!inUl) { out.push('<ul class="mb-2 ps-3">'); inUl = true; }
                out.push('<li>' + ulMatch[1] + '</li>');
                continue;
            }

            // Ordered list items (1. or 2.)
            const olMatch = line.match(/^\s*(\d+)\.\s+(.*)$/);
            if (olMatch) {
                if (inUl) { out.push('</ul>'); inUl = false; }
                if (!inOl) { out.push('<ol class="mb-2 ps-3">'); inOl = true; }
                out.push('<li>' + olMatch[2] + '</li>');
                continue;
            }

            // Regular paragraph/text
            if (inUl) { out.push('</ul>'); inUl = false; }
            if (inOl) { out.push('</ol>'); inOl = false; }

            if (trimmed.length > 0) {
                if (trimmed.startsWith('<pre') || trimmed.endsWith('</pre>')) {
                    out.push(trimmed);
                } else {
                    out.push('<p class="mb-2">' + trimmed + '</p>');
                }
            }
        }

        if (inUl) out.push('</ul>');
        if (inOl) out.push('</ol>');
        if (inTable) out.push('</tbody></table></div>');

        return out.join('');
    }
});
</script>

<?php require APPROOT . '/views/templates/admin_footer.php'; ?>
