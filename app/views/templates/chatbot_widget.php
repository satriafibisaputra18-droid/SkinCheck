<?php
// Dynamic Active AI Engine Resolution from DB settings
$currentAiProvider = Setting::getVal('ai_provider', 'gemini');
if ($currentAiProvider === 'nvidia') {
    $currentNvidiaModel = Setting::getVal('nvidia_model', 'nvidia/nemotron-3.5-bolt-e4b');
    $activeEngineLabel = (strpos(strtolower($currentNvidiaModel), 'nemotron') !== false) ? 'NVIDIA Nemotron' : 'NVIDIA NIM';
} else {
    $currentGeminiModel = Setting::getVal('gemini_primary_model', 'gemini-flash-lite-latest');
    $activeEngineLabel = 'Gemini AI Studio';
}
?>
<!-- ── SkinCheck AI Chatbot Widget (Multi-Engine AI: Gemini & NVIDIA NIM) ── -->
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/chatbot.css?v=<?php echo time(); ?>">

<!-- Floating Trigger Button (macOS Liquid Glass Orb with Icon Morph) -->
<button class="sc-chatbot-fab" id="scChatbotFab" type="button" title="Konsultasi SkinCheck AI" aria-label="Buka Chatbot SkinCheck AI" aria-expanded="false">
    <i class="fa-solid fa-comment-dots sc-fab-icon-chat"></i>
    <i class="fa-solid fa-chevron-down sc-fab-icon-close"></i>
    <span class="sc-chatbot-fab-badge-status" id="scChatFabBadge" title="<?php echo htmlspecialchars($activeEngineLabel); ?> Online"></span>
</button>

<!-- Chat Window Widget -->
<div class="sc-chat-widget" id="scChatWidget" role="dialog" aria-label="Konsultasi SkinCheck AI">
    <!-- Header -->
    <div class="sc-chat-header">
        <div class="sc-chat-header-left">
            <div class="sc-chat-avatar">
                <i class="fa-solid fa-robot"></i>
                <span class="sc-chat-avatar-status"></span>
            </div>
            <div class="sc-chat-header-info">
                <h4>SkinCheck AI</h4>
                <small id="scChatHeaderStatus">
                    <span class="online-badge"></span> Online • <span id="scChatEngineLabel"><?php echo htmlspecialchars($activeEngineLabel); ?></span>
                </small>
            </div>
        </div>
        <div class="sc-chat-header-actions">
            <button type="button" class="sc-chat-btn-action" id="scChatReset" title="Mulai Obrolan Baru" aria-label="Reset percakapan">
                <i class="fa-solid fa-rotate-right"></i>
            </button>
            <button type="button" class="sc-chat-btn-action" id="scChatClose" title="Tutup Chat" aria-label="Tutup jendela chat">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>

    <!-- Message Body -->
    <div class="sc-chat-body" id="scChatBody">
        <!-- Welcome Card -->
        <div class="sc-chat-welcome">
            <!-- Role-Aware Welcome Title & Description -->
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <div class="sc-chat-welcome-icon" style="background: #e6f4f1; color: #0f766e;">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <h5>Halo Admin <?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?>!</h5>
                <p>
                    Tanyakan ringkasan metrik platform, data scan, atau katalog produk.
                </p>
            <?php elseif (isset($_SESSION['user_id'])): ?>
                <div class="sc-chat-welcome-icon">
                    <i class="fa-solid fa-spa"></i>
                </div>
                <h5>Halo <?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?>!</h5>
                <p>
                    Tanyakan seputar hasil scan, pilihan sunscreen, atau bahan aktif skincare.
                </p>
            <?php else: ?>
                <div class="sc-chat-welcome-icon">
                    <i class="fa-solid fa-spa"></i>
                </div>
                <h5>Halo! Saya SkinCheck AI</h5>
                <p>
                    Tanyakan seputar perawatan kulit, sunscreen, atau cara kerja SkinCheck.
                </p>
            <?php endif; ?>

            <!-- Quick Action Chips -->
            <div class="sc-chat-chips">
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <button type="button" class="sc-chat-chip" data-prompt="Siapa saja daftar nama customer yang terdaftar di SkinCheck saat ini?">
                        Daftar Nama Customer
                    </button>
                    <button type="button" class="sc-chat-chip" data-prompt="Tampilkan ringkasan riwayat log Scan Audits pemindaian wajah customer terbaru!">
                        Scan Audits
                    </button>
                    <button type="button" class="sc-chat-chip" data-prompt="Bagaimana ringkasan statistik dan metrik platform SkinCheck saat ini?">
                        Ringkasan Metrik
                    </button>
                    <button type="button" class="sc-chat-chip" data-prompt="Berapa total customer, admin, dan scan yang sudah selesai?">
                        Jumlah User &amp; Scan
                    </button>
                    <button type="button" class="sc-chat-chip" data-prompt="Produk skincare apa saja yang aktif di katalog database saat ini?">
                        Katalog Produk
                    </button>
                    <button type="button" class="sc-chat-chip" data-prompt="Apa saja faktor utama yang paling mempengaruhi penurunan skor kesehatan kulit customer?">
                        Tren Skor Kulit
                    </button>
                <?php elseif (isset($_SESSION['user_id'])): ?>
                    <button type="button" class="sc-chat-chip" data-prompt="Bagaimana kondisi kulit saya berdasarkan hasil scan terakhir saya?">
                        Hasil Scan Terakhir
                    </button>
                    <button type="button" class="sc-chat-chip" data-prompt="Rekomendasi sunscreen untuk kulit berminyak dan berjerawat?">
                        Sunscreen Kulit Berminyak
                    </button>
                    <button type="button" class="sc-chat-chip" data-prompt="Bagaimana cara mengatasi skin barrier yang rusak dan kulit dehidrasi?">
                        Perbaiki Skin Barrier
                    </button>
                    <button type="button" class="sc-chat-chip" data-prompt="Kandungan skincare apa yang efektif memudarkan bekas jerawat kehitaman?">
                        Bekas Jerawat
                    </button>
                <?php else: ?>
                    <button type="button" class="sc-chat-chip" data-prompt="Rekomendasi sunscreen untuk kulit berminyak dan berjerawat?">
                        Sunscreen Kulit Berminyak
                    </button>
                    <button type="button" class="sc-chat-chip" data-prompt="Bagaimana cara kerja fitur Face Scan dan Sunscreen Checker di SkinCheck?">
                        Cara Pakai SkinCheck
                    </button>
                    <button type="button" class="sc-chat-chip" data-prompt="Berapa takaran pemakaian sunscreen yang benar dan seberapa sering harus reapply?">
                        Aturan Sunscreen
                    </button>
                    <button type="button" class="sc-chat-chip" data-prompt="Bagaimana cara mengatasi skin barrier yang rusak dan kulit dehidrasi?">
                        Perbaiki Skin Barrier
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Typing Indicator Element -->
        <div class="sc-typing-indicator" id="scTypingIndicator">
            <span class="sc-typing-dot"></span>
            <span class="sc-typing-dot"></span>
            <span class="sc-typing-dot"></span>
        </div>
    </div>

    <!-- Input Footer -->
    <div class="sc-chat-footer">
        <div class="sc-chat-input-row">
            <textarea class="sc-chat-input" id="scChatInput" rows="1" placeholder="Tanyakan seputar kulit, jerawat, sunscreen..."></textarea>
            <button type="button" class="sc-chat-btn-send" id="scChatSend" disabled title="Kirim Pesan" aria-label="Kirim">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </div>
        <div class="sc-chat-disclaimer">
            <i class="fa-solid fa-database me-1 text-success"></i> Terhubung Real-Time ke Database &amp; <span id="scChatEngineDisclaimer"><?php echo htmlspecialchars($activeEngineLabel); ?></span>
        </div>
    </div>
</div>

<script>
    window.SKINCHECK_URLROOT = "<?php echo URLROOT; ?>";
    window.SKINCHECK_ACTIVE_PROVIDER = "<?php echo htmlspecialchars($currentAiProvider); ?>";
    window.SKINCHECK_ACTIVE_ENGINE = "<?php echo htmlspecialchars($activeEngineLabel); ?>";
    window.SKINCHECK_USER = <?php echo isset($_SESSION['user_id']) ? json_encode([
        'id' => (int)$_SESSION['user_id'],
        'name' => $_SESSION['user_name'] ?? 'User',
        'email' => $_SESSION['user_email'] ?? '',
        'role' => $_SESSION['user_role'] ?? 'customer'
    ]) : 'null'; ?>;
</script>
<script src="<?php echo URLROOT; ?>/public/js/chatbot.js?v=<?php echo time(); ?>"></script>

