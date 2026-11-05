<!-- ── SkinCheck AI Chatbot Widget (Node.js & Gemini Studio) ── -->
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/chatbot.css?v=<?php echo time(); ?>">

<!-- Floating Trigger Button with Chat Icon -->
<button class="sc-chatbot-fab" id="scChatbotFab" type="button" aria-label="Buka Chatbot SkinCheck AI" aria-expanded="false">
    <div class="sc-chatbot-fab-icon-wrap">
        <i class="fa-solid fa-comment-dots"></i>
        <span class="sc-chatbot-fab-badge-status" title="Gemini AI Online"></span>
    </div>
    <div class="sc-chatbot-fab-text">
        <span class="sc-chatbot-fab-title">
            Chat AI <i class="fa-solid fa-wand-magic-sparkles sparkle"></i>
        </span>
        <span class="sc-chatbot-fab-sub">Konsultasi Kulit</span>
    </div>
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
                <small>
                    <span class="online-badge"></span> Online • Gemini AI Studio
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
                <div class="sc-chat-welcome-icon" style="background: rgba(191, 161, 95, 0.15); color: #bfa15f;">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <h5>Halo Admin <?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?>! 🛡️</h5>
                <p>
                    Saya adalah <strong>SkinCheck AI Executive & Dermatology Assistant</strong>. Anda dapat menanyakan ringkasan metrik platform, total customer, pemindaian kulit, katalog produk, atau konsultasi dermatologi klinis.
                </p>
            <?php elseif (isset($_SESSION['user_id'])): ?>
                <div class="sc-chat-welcome-icon">
                    <i class="fa-solid fa-spa"></i>
                </div>
                <h5>Halo <?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?>! ✨</h5>
                <p>
                    Asisten kecerdasan buatan untuk analisis kulit, rekomendasi tabir surya (sunscreen), dan panduan skincare berbasis klinis. Ada yang bisa saya bantu hari ini?
                </p>
            <?php else: ?>
                <div class="sc-chat-welcome-icon">
                    <i class="fa-solid fa-spa"></i>
                </div>
                <h5>Halo! Saya SkinCheck AI ✨</h5>
                <p>
                    Asisten kecerdasan buatan untuk analisis kulit, rekomendasi tabir surya (sunscreen), dan panduan skincare berbasis klinis. Ada yang bisa saya bantu hari ini?
                </p>
            <?php endif; ?>

            <!-- Quick Action Chips -->
            <div class="sc-chat-chips">
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <button type="button" class="sc-chat-chip" data-prompt="Bagaimana ringkasan statistik dan metrik platform SkinCheck saat ini?">
                        📊 Ringkasan Metrik Platform
                    </button>
                    <button type="button" class="sc-chat-chip" data-prompt="Berapa total customer, admin, dan scan yang sudah selesai?">
                        👥 Jumlah User & Scan
                    </button>
                    <button type="button" class="sc-chat-chip" data-prompt="Produk skincare apa saja yang aktif di katalog database saat ini?">
                        🛍️ Katalog Produk Aktif
                    </button>
                    <button type="button" class="sc-chat-chip" data-prompt="Apa saja faktor utama yang paling mempengaruhi penurunan skor kesehatan kulit customer?">
                        📈 Analisis Kesehatan Kulit
                    </button>
                <?php elseif (isset($_SESSION['user_id'])): ?>
                    <button type="button" class="sc-chat-chip" data-prompt="Bagaimana kondisi kulit saya berdasarkan hasil scan terakhir saya?">
                        🔍 Hasil Scan Terakhir Saya
                    </button>
                    <button type="button" class="sc-chat-chip" data-prompt="Rekomendasi sunscreen untuk kulit berminyak dan berjerawat?">
                        🧴 Sunscreen Kulit Berminyak
                    </button>
                    <button type="button" class="sc-chat-chip" data-prompt="Bagaimana cara mengatasi skin barrier yang rusak dan kulit dehidrasi?">
                        💧 Perbaiki Skin Barrier
                    </button>
                    <button type="button" class="sc-chat-chip" data-prompt="Kandungan skincare apa yang efektif memudarkan bekas jerawat kehitaman?">
                        🌿 Hilangkan Bekas Jerawat
                    </button>
                    <button type="button" class="sc-chat-chip" data-prompt="Apakah Anda bisa melihat database SkinCheck?">
                        🔒 Akses Database
                    </button>
                <?php else: ?>
                    <button type="button" class="sc-chat-chip" data-prompt="Rekomendasi sunscreen untuk kulit berminyak dan berjerawat?">
                        🧴 Sunscreen Kulit Berminyak
                    </button>
                    <button type="button" class="sc-chat-chip" data-prompt="Bagaimana cara kerja fitur Face Scan dan Sunscreen Checker di SkinCheck?">
                        🔍 Cara Pakai SkinCheck
                    </button>
                    <button type="button" class="sc-chat-chip" data-prompt="Berapa takaran pemakaian sunscreen yang benar dan seberapa sering harus reapply?">
                        ☀️ Aturan Pakai Sunscreen
                    </button>
                    <button type="button" class="sc-chat-chip" data-prompt="Kandungan skincare apa yang efektif memudarkan bekas jerawat kehitaman?">
                        🌿 Hilangkan Bekas Jerawat
                    </button>
                    <button type="button" class="sc-chat-chip" data-prompt="Bagaimana cara mengatasi skin barrier yang rusak dan kulit dehidrasi?">
                        💧 Perbaiki Skin Barrier
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
            <i class="fa-solid fa-database me-1 text-success"></i> Terhubung Real-Time ke Database & Gemini Studio
        </div>
    </div>
</div>

<script>
    window.SKINCHECK_URLROOT = "<?php echo URLROOT; ?>";
    window.SKINCHECK_USER = {
        id: <?php echo isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 'null'; ?>,
        name: <?php echo isset($_SESSION['user_name']) ? json_encode($_SESSION['user_name']) : 'null'; ?>,
        email: <?php echo isset($_SESSION['user_email']) ? json_encode($_SESSION['user_email']) : 'null'; ?>,
        role: <?php echo isset($_SESSION['user_role']) ? json_encode($_SESSION['user_role']) : 'null'; ?>
    };
</script>
<script src="<?php echo URLROOT; ?>/public/js/chatbot.js?v=<?php echo time(); ?>"></script>

