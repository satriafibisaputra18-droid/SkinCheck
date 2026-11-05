<?php require APPROOT . '/views/templates/customer_header.php'; ?>

<!-- Toolbar -->
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div class="d-flex align-items-center gap-2">
        <a href="<?php echo URLROOT; ?>/customer/dashboard" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            <i class="fa-solid fa-arrow-left me-1"></i>Dashboard
        </a>
        <span class="small" style="color: #c9ded5;">/ Konsultasi & Edukasi Kulit</span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge sc-badge-secure px-3 py-2 rounded-pill small">
            <i class="fa-solid fa-circle-check me-1" style="font-size:0.65rem;"></i>Layanan Konsultasi Aktif
        </span>
        <button id="scPageChatReset" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            <i class="fa-solid fa-rotate-right me-1"></i>Reset Sesi
        </button>
    </div>
</div>

<div class="row g-4">

    <!-- Chat Panel -->
    <div class="col-lg-8">
        <div class="sc-card d-flex flex-column" style="height: 680px; max-height: calc(100vh - 200px);">
            <div class="sc-card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:40px;height:40px;border-radius:12px;background:rgba(191,161,95,0.15);color:#dfc68b;border:1px solid rgba(191,161,95,0.3);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fa-solid fa-comment-medical fs-5"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 text-white" style="font-size:0.98rem; font-weight:700;">Konsultan Kesehatan Kulit SkinCheck</h5>
                        <small style="color:#94a3b8; font-size:0.75rem;">Panduan Dermatologi Klinis &amp; Kurasi Komposisi Skincare</small>
                    </div>
                </div>
                <div class="small d-none d-sm-flex align-items-center gap-1" style="color:#dfc68b;">
                    <i class="fa-regular fa-clock me-1"></i><span>Respon Terstruktur</span>
                </div>
            </div>

            <!-- Chat message container -->
            <div class="sc-card-body flex-grow-1 overflow-auto p-3 p-md-4 d-flex flex-column gap-3"
                id="scPageChatMessages" style="background: rgba(0, 0, 0, 0.28);">
                <!-- Welcome greeting -->
                <div class="d-flex gap-3 mb-2">
                    <div style="width:36px;height:36px;border-radius:50%;background:rgba(56,217,169,0.15);color:#38d9a9;border:1px solid rgba(56,217,169,0.3);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fa-solid fa-stethoscope" style="font-size:0.85rem;"></i>
                    </div>
                    <div style="max-width:85%;">
                        <div class="sc-chat-bubble-bot">
                            <p class="fw-bold mb-1" style="color: #dfc68b;">
                                Selamat datang, <?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?>.
                            </p>
                            <p class="mb-2" style="color: #c9ded5; line-height: 1.6;">
                                Selamat datang di ruang konsultasi kesehatan kulit SkinCheck. Anda dapat mendiskusikan interpretasi skor hasil pemindaian wajah, panduan pemilihan tabir surya yang sesuai dengan aktivitas harian, analisis kompatibilitas bahan aktif, serta urutan pemakaian skincare yang aman untuk memperkuat sawar kulit (skin barrier) Anda.
                            </p>
                            <div class="d-flex flex-wrap gap-2 mt-3">
                                <button type="button"
                                    class="btn btn-sm rounded-pill sc-page-prompt-btn"
                                    data-text="Bagaimana cara memilih sunscreen terbaik untuk jenis kulit saya?">
                                    Rekomendasi Sunscreen Sesuai Kulit
                                </button>
                                <button type="button"
                                    class="btn btn-sm rounded-pill sc-page-prompt-btn"
                                    data-text="Apa perbedaan Physical/Mineral Sunscreen dan Chemical Sunscreen?">
                                    Physical vs Chemical Sunscreen
                                </button>
                                <button type="button"
                                    class="btn btn-sm rounded-pill sc-page-prompt-btn"
                                    data-text="Bagaimana urutan skincare routine pagi dan malam yang benar untuk kulit berjerawat?">
                                    Rutinitas untuk Kulit Berjerawat
                                </button>
                                <button type="button"
                                    class="btn btn-sm rounded-pill sc-page-prompt-btn"
                                    data-text="Bagaimana cara membaca skor hidrasi dan kemerahan di SkinCheck?">
                                    Interpretasi Indikator Scan Wajah
                                </button>
                            </div>
                        </div>
                        <span class="small mt-1 d-block" style="color: #64748b; font-size:0.7rem;">SkinCheck Clinical Guidance System</span>
                    </div>
                </div>

                <!-- Typing indicator -->
                <div id="scPageTyping"
                    class="d-none align-items-center gap-2 p-2 px-3 sc-chat-typing-box rounded-pill shadow-sm"
                    style="width:fit-content;">
                    <div class="spinner-grow spinner-grow-sm text-warning" role="status"></div>
                    <span class="small" style="color: #dfc68b;">Sedang menyusun rekomendasi klinis...</span>
                </div>
            </div>

            <!-- Chat Input Footer -->
            <div class="p-3 border-top" style="background: rgba(255, 255, 255, 0.02); border-color: rgba(255, 255, 255, 0.08) !important;">
                <form id="scPageChatForm" class="d-flex gap-2 align-items-center">
                    <input type="text" id="scPageChatInput" class="form-control sc-dark-input rounded-pill px-4 py-2"
                        placeholder="Tanyakan seputar pemilihan produk, barier kulit, atau bahan aktif..." autocomplete="off">
                    <button type="submit" id="scPageChatSubmit"
                        class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center"
                        style="width:42px;height:42px;flex-shrink:0;">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </form>
                <div class="d-flex justify-content-between align-items-center mt-2 px-2"
                    style="font-size:0.75rem; color:#94a3b8;">
                    <span><i class="fa-solid fa-shield-halved me-1" style="color:#38d9a9;"></i> Komunikasi terenkripsi &amp; data pribadi aman</span>
                    <span>Tekan <strong>Enter</strong> untuk mengirim</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Sidebar -->
    <div class="col-lg-4">
        <div class="sc-card mb-4">
            <div class="sc-card-header">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-list-check me-2" style="color: #dfc68b;"></i>Cakupan Konsultasi</h5>
            </div>
            <div class="sc-card-body">
                <ul class="list-unstyled mb-0">
                    <?php
                    $features = [
                        ['fa-chart-line', 'Interpretasi 4 indikator kesehatan kulit wajah & status barier.'],
                        ['fa-sun', 'Panduan proteksi radiasi UV, cakupan tabir surya & re-aplikasi.'],
                        ['fa-flask', 'Analisis bahan aktif (Ceramide, Niacinamide, Salicylic, Retinol).'],
                        ['fa-box-open', 'Rekomendasi produk kurasi resmi dari katalog SkinCheck.'],
                        ['fa-book-medical', 'Edukasi patofisiologi kulit secara saintifik dan mudah dimengerti.'],
                    ];
                    foreach ($features as $f): ?>
                        <li class="d-flex gap-3 mb-3">
                            <div class="flex-shrink-0 text-center" style="width:24px;">
                                <i class="fa-solid <?php echo $f[0]; ?>" style="color: #dfc68b; font-size:0.9rem;margin-top:2px;"></i>
                            </div>
                            <span class="small" style="color: #c9ded5; line-height:1.55;"><?php echo $f[1]; ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <!-- Quick Tips Card -->
        <div class="sc-card mb-4" style="background: rgba(255, 255, 255, 0.025); border: 1px solid rgba(191, 161, 95, 0.2);">
            <div class="sc-card-header bg-transparent border-0 pb-1">
                <h6 class="fw-bold mb-0" style="color: #dfc68b;">
                    <i class="fa-solid fa-circle-question me-2" style="color: #dfc68b;"></i>Tips Konsultasi Efektif
                </h6>
            </div>
            <div class="sc-card-body pt-2" style="font-size: 0.82rem; color: #c9ded5; line-height:1.65;">
                <p class="mb-2">
                    Sebutkan <strong class="text-white">tipe kulit Anda</strong> (kering, berminyak, kombinasi, atau sensitif) serta <strong class="text-white">keluhan utama</strong> (seperti bruntusan pada dahi, kemerahan di cuping hidung, atau bekas jerawat menggelap) agar evaluasi berjalan lebih terarah.
                </p>
                <p class="mb-0">
                    Anda juga dapat menanyakan kecocokan produk tertentu yang ada di katalog SkinCheck dengan riwayat skor pemindaian Anda.
                </p>
            </div>
        </div>

        <!-- Disclaimer -->
        <div class="sc-advisory-banner" style="font-size:0.78rem; line-height:1.55;">
            <div class="d-flex gap-2 align-items-start">
                <i class="fa-solid fa-triangle-exclamation mt-1" style="color: #dfc68b; flex-shrink: 0;"></i>
                <div>
                    <strong class="sc-advisory-title">Catatan Medis:</strong> Layanan konsultasi ini menyediakan informasi dan edukasi kesehatan kulit mandiri. Untuk kondisi peradangan berat (kistik), luka terbuka, atau gejala infeksi dermatologis, disarankan berkonsultasi langsung dengan Dokter Spesialis Kulit &amp; Kelamin (Sp.D.V.E).
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Page-specific Chat Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chatForm = document.getElementById('scPageChatForm');
        const chatInput = document.getElementById('scPageChatInput');
        const chatSubmit = document.getElementById('scPageChatSubmit');
        const msgContainer = document.getElementById('scPageChatMessages');
        const typingIndicator = document.getElementById('scPageTyping');
        const resetBtn = document.getElementById('scPageChatReset');

        const NODE_URL = 'http://localhost:5000/api/chat';
        const PHP_URL = '<?php echo URLROOT; ?>/chatbot/chat';

        let history = [];

        // Prompt buttons click
        document.querySelectorAll('.sc-page-prompt-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const promptText = this.getAttribute('data-text');
                if (promptText) {
                    sendPageMessage(promptText);
                }
            });
        });

        // Form submit
        chatForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const text = chatInput.value.trim();
            if (!text) return;
            chatInput.value = '';
            sendPageMessage(text);
        });

        // Reset button
        resetBtn.addEventListener('click', function () {
            if (confirm('Bersihkan percakapan ini dan mulai sesi konsultasi baru?')) {
                history = [];
                // Remove user & AI messages except initial welcome
                const cards = msgContainer.querySelectorAll('.sc-page-msg');
                cards.forEach(c => c.remove());
            }
        });

        async function sendPageMessage(text) {
            // Render user message
            appendUserMsg(text);
            history.push({ role: 'user', text: text });

            // Show typing
            showTyping(true);

            try {
                let resData = null;
                const userPayload = {
                    id: <?php echo (int)($_SESSION['user_id'] ?? 0); ?>,
                    name: <?php echo json_encode($_SESSION['user_name'] ?? 'User'); ?>,
                    email: <?php echo json_encode($_SESSION['user_email'] ?? ''); ?>,
                    role: <?php echo json_encode($_SESSION['user_role'] ?? 'customer'); ?>
                };

                // Attempt Node.js server
                try {
                    const res = await fetch(NODE_URL, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            message: text,
                            history: history.slice(-8),
                            user: userPayload
                        })
                    });
                    if (res.ok) {
                        resData = await res.json();
                    } else {
                        throw new Error('Node server returned ' + res.status);
                    }
                } catch (nodeErr) {
                    console.warn('[PageChat] Direct Node call failed, using PHP bridge:', nodeErr.message);
                    const phpRes = await fetch(PHP_URL, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            message: text,
                            history: history.slice(-8),
                            user: userPayload
                        })
                    });
                    resData = await phpRes.json();
                }

                if (resData && resData.success && resData.reply) {
                    appendBotMsg(resData.reply);
                    history.push({ role: 'model', text: resData.reply });
                } else {
                    appendBotMsg('⚠️ Maaf, terjadi kendala: ' + (resData?.error || 'Silakan coba lagi.'));
                }

            } catch (err) {
                console.error('[PageChat] Error:', err);
                appendBotMsg('⚠️ Tidak dapat terhubung ke server chatbot. Pastikan server Node.js aktif.');
            } finally {
                showTyping(false);
                chatInput.focus();
            }
        }

        function appendUserMsg(text) {
            const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            const div = document.createElement('div');
            div.className = 'd-flex gap-3 justify-content-end mb-2 sc-page-msg';
            div.innerHTML = `
            <div style="max-width:85%;" class="text-end">
                <div class="sc-chat-bubble-user text-start">
                    ${escapeHtml(text)}
                </div>
                <span class="small mt-1 d-block" style="color: #94a3b8; font-size:0.7rem;">${time} • Anda</span>
            </div>
            <div style="width:36px;height:36px;border-radius:50%;background:rgba(191,161,95,0.2);color:#dfc68b;border:1px solid rgba(191,161,95,0.4);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fa-solid fa-user" style="font-size:0.85rem;"></i>
            </div>
        `;
            msgContainer.appendChild(div);
            scrollDown();
        }

        function appendBotMsg(markdownText) {
            const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            const div = document.createElement('div');
            div.className = 'd-flex gap-3 mb-2 sc-page-msg';
            div.innerHTML = `
            <div style="width:36px;height:36px;border-radius:50%;background:rgba(56,217,169,0.15);color:#38d9a9;border:1px solid rgba(56,217,169,0.3);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fa-solid fa-wand-magic-sparkles" style="font-size:0.85rem;"></i>
            </div>
            <div style="max-width:85%;">
                <div class="sc-chat-bubble-bot">
                    ${formatMd(markdownText)}
                </div>
                <span class="small mt-1 d-block" style="color: #94a3b8; font-size:0.7rem;">${time} • SkinCheck AI</span>
            </div>
        `;
            msgContainer.appendChild(div);
            scrollDown();
        }

        function showTyping(show) {
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
                return '<pre class="p-2 rounded small overflow-auto my-2" style="background:rgba(0,0,0,0.45);border:1px solid rgba(255,255,255,0.1);color:#38d9a9;"><code>' + code.trim() + '</code></pre>';
            });

            // Inline code
            escaped = escaped.replace(/`([^`]+)`/g, '<code class="px-1 py-0.5 rounded small" style="background:rgba(255,255,255,0.08);color:#dfc68b;">$1</code>');

            // Bold (**text** or __text__)
            escaped = escaped.replace(/(\*\*|__)(.*?)\1/g, '<strong style="color:#ffffff;">$2</strong>');

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
                    out.push('<hr class="my-2" style="border-color:rgba(255,255,255,0.1);">');
                    continue;
                }

                // Headers: # up to ######
                const headerMatch = line.match(/^(\s*)(#{1,6})\s+(.*)$/);
                if (headerMatch) {
                    if (inUl) { out.push('</ul>'); inUl = false; }
                    if (inOl) { out.push('</ol>'); inOl = false; }
                    if (inTable) { out.push('</tbody></table></div>'); inTable = false; }
                    const level = headerMatch[2].length;
                    const content = headerMatch[3].trim();
                    if (level === 1) {
                        out.push('<h5 class="fw-bold mt-3 mb-2" style="color:#dfc68b;">' + content + '</h5>');
                    } else if (level === 2) {
                        out.push('<h6 class="fw-bold mt-2 mb-1" style="color:#dfc68b;font-size:1.05rem;">' + content + '</h6>');
                    } else if (level === 3) {
                        out.push('<h6 class="fw-bold mt-2 mb-1" style="color:#dfc68b;font-size:0.98rem;">' + content + '</h6>');
                    } else if (level === 4) {
                        out.push('<h6 class="fw-bold mt-2 mb-1 text-white" style="font-size:0.92rem;">' + content + '</h6>');
                    } else if (level === 5) {
                        out.push('<div class="fw-bold text-white mt-2 mb-1 small">' + content + '</div>');
                    } else {
                        out.push('<div class="fw-semibold mt-1 mb-1 small" style="color:#c9ded5;">' + content + '</div>');
                    }
                    continue;
                }

                // Blockquotes: > quote
                const quoteMatch = line.match(/^\s*&gt;\s*(.*)$/);
                if (quoteMatch) {
                    if (inUl) { out.push('</ul>'); inUl = false; }
                    if (inOl) { out.push('</ol>'); inOl = false; }
                    if (inTable) { out.push('</tbody></table></div>'); inTable = false; }
                    out.push('<blockquote class="border-start border-3 ps-2 my-2 fst-italic small" style="border-color:#dfc68b !important; color:#c9ded5;">' + quoteMatch[1] + '</blockquote>');
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
                        out.push('<div class="table-responsive my-2"><table class="table table-sm table-bordered small mb-0" style="border-color:rgba(255,255,255,0.1);color:#e2ede8;">');
                        out.push('<thead><tr>' + cells.map(function (c) { return '<th style="background:rgba(255,255,255,0.06);color:#dfc68b;font-weight:700;">' + c + '</th>'; }).join('') + '</tr></thead><tbody>');
                        inTable = true;
                    } else {
                        out.push('<tr>' + cells.map(function (c) { return '<td style="border-color:rgba(255,255,255,0.06);">' + c + '</td>'; }).join('') + '</tr>');
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

<?php require APPROOT . '/views/templates/customer_footer.php'; ?>