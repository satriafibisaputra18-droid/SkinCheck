/**
 * SkinCheck AI Chatbot Client Controller
 * Powered by Node.js Server & Google Gemini AI Studio
 */

(function () {
    'use strict';

    // Endpoints
    const NODE_SERVER_URL = 'http://localhost:5000/api/chat';
    const PHP_FALLBACK_URL = (window.SKINCHECK_URLROOT || '') + '/chatbot/chat';

    // DOM Elements
    let fabBtn, widget, closeBtn, resetBtn, msgBody, chatInput, sendBtn, typingIndicator;

    // State
    let isOpen = false;
    let isWaiting = false;
    let chatHistory = [];

    // Storage Key - user-scoped to prevent guest / other account history contamination
    function getStorageKey() {
        const uid = window.SKINCHECK_USER?.id || 'guest';
        return 'skincheck_chat_history_u_' + uid;
    }

    /**
     * Initialize on DOM Ready
     */
    function init() {
        fabBtn = document.getElementById('scChatbotFab');
        widget = document.getElementById('scChatWidget');
        closeBtn = document.getElementById('scChatClose');
        resetBtn = document.getElementById('scChatReset');
        msgBody = document.getElementById('scChatBody');
        chatInput = document.getElementById('scChatInput');
        sendBtn = document.getElementById('scChatSend');
        typingIndicator = document.getElementById('scTypingIndicator');

        if (!fabBtn || !widget) return;

        // Restore history
        loadSavedHistory();

        // Event Listeners
        fabBtn.addEventListener('click', toggleChat);
        if (closeBtn) closeBtn.addEventListener('click', closeChat);
        if (resetBtn) resetBtn.addEventListener('click', resetChat);

        if (sendBtn) {
            sendBtn.addEventListener('click', handleSend);
        }

        if (chatInput) {
            chatInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    handleSend();
                }
            });

            chatInput.addEventListener('input', function () {
                sendBtn.disabled = !chatInput.value.trim() || isWaiting;
            });
        }

        // Delegate chip clicks
        document.addEventListener('click', function (e) {
            const chip = e.target.closest('.sc-chat-chip');
            if (chip && chip.dataset.prompt) {
                if (!isOpen) openChat();
                sendMessage(chip.dataset.prompt);
            }
        });
    }

    /**
     * Toggle Chat Open/Close
     */
    function toggleChat() {
        if (isOpen) {
            closeChat();
        } else {
            openChat();
        }
    }

    function openChat() {
        isOpen = true;
        widget.classList.add('open');
        fabBtn.setAttribute('aria-expanded', 'true');
        setTimeout(() => {
            if (chatInput) chatInput.focus();
            scrollToBottom();
        }, 150);
    }

    function closeChat() {
        isOpen = false;
        widget.classList.remove('open');
        fabBtn.setAttribute('aria-expanded', 'false');
    }

    /**
     * Reset Chat Conversation
     */
    function resetChat() {
        if (confirm('Mulai percakapan baru dengan SkinCheck AI? Riwayat chat ini akan dibersihkan.')) {
            chatHistory = [];
            sessionStorage.removeItem(getStorageKey());
            
            // Remove user and bot messages except welcome card
            const msgs = msgBody.querySelectorAll('.sc-chat-msg');
            msgs.forEach(m => m.remove());

            appendBotMessage(
                'Sesi konsultasi baru telah dimulai. Ada yang ingin Anda tanyakan seputar kondisi kulit, jerawat, atau rekomendasi produk sunscreen hari ini? 🌿',
                false
            );
        }
    }

    /**
     * Send message from input
     */
    function handleSend() {
        if (isWaiting || !chatInput) return;
        const text = chatInput.value.trim();
        if (!text) return;

        chatInput.value = '';
        sendBtn.disabled = true;
        sendMessage(text);
    }

    /**
     * Core message sending logic
     */
    async function sendMessage(text) {
        if (isWaiting) return;

        // 1. Render user message
        appendUserMessage(text);
        chatHistory.push({ role: 'user', text: text });
        saveHistory();

        // 2. Set waiting state & show typing
        setWaiting(true);

        try {
            // Build recent history for context (last 8 turns)
            const recentHistory = chatHistory.slice(-8);

            let data = null;
            let usedServer = 'Node.js';

            // Attempt 1: Node.js server directly
            try {
                const response = await fetch(NODE_SERVER_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        message: text,
                        history: recentHistory,
                        user: window.SKINCHECK_USER || null
                    })
                });

                if (response.ok) {
                    data = await response.json();
                } else {
                    throw new Error('Node server returned status ' + response.status);
                }
            } catch (nodeErr) {
                console.warn('[Chatbot] Node.js server direct call failed, trying PHP bridge:', nodeErr.message);

                // Attempt 2: PHP MVC Bridge Fallback
                const phpResponse = await fetch(PHP_FALLBACK_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        message: text,
                        history: recentHistory,
                        user: window.SKINCHECK_USER || null
                    })
                });

                data = await phpResponse.json();
                usedServer = 'PHP Bridge';
            }

            if (data && data.success && data.reply) {
                appendBotMessage(data.reply, true);
                chatHistory.push({ role: 'model', text: data.reply });
                saveHistory();
            } else {
                const errMsg = data?.error || 'Maaf, terjadi kendala saat memproses jawaban. Silakan coba kembali.';
                appendBotMessage('⚠️ ' + errMsg, false);
            }

        } catch (err) {
            console.error('[Chatbot] Request failed:', err);
            appendBotMessage(
                '⚠️ Tidak dapat terhubung ke server chatbot. Pastikan server Node.js aktif (`node chatbot-server/server.js`) atau periksa koneksi internet Anda.',
                false
            );
        } finally {
            setWaiting(false);
            if (chatInput) {
                chatInput.focus();
                sendBtn.disabled = !chatInput.value.trim();
            }
        }
    }

    /**
     * Append User Message Bubble
     */
    function appendUserMessage(text) {
        const timeStr = getCurrentTime();
        const msgDiv = document.createElement('div');
        msgDiv.className = 'sc-chat-msg user';
        msgDiv.innerHTML = `
            <div class="sc-chat-msg-avatar">
                <i class="fa-solid fa-user"></i>
            </div>
            <div>
                <div class="sc-chat-bubble">${escapeHtml(text)}</div>
                <span class="sc-chat-msg-time">${timeStr}</span>
            </div>
        `;
        msgBody.appendChild(msgDiv);
        scrollToBottom();
    }

    /**
     * Append Bot Message Bubble
     */
    function appendBotMessage(markdownText, save = true) {
        const timeStr = getCurrentTime();
        const msgDiv = document.createElement('div');
        msgDiv.className = 'sc-chat-msg bot';
        
        const parsedHtml = formatMarkdown(markdownText);

        msgDiv.innerHTML = `
            <div class="sc-chat-msg-avatar">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
            </div>
            <div>
                <div class="sc-chat-bubble">${parsedHtml}</div>
                <span class="sc-chat-msg-time">${timeStr} • SkinCheck AI</span>
            </div>
        `;
        msgBody.appendChild(msgDiv);
        scrollToBottom();
    }

    /**
     * Set loading & typing state
     */
    function setWaiting(waiting) {
        isWaiting = waiting;
        if (typingIndicator) {
            if (waiting) {
                typingIndicator.classList.add('active');
                msgBody.appendChild(typingIndicator); // ensure it's at bottom
            } else {
                typingIndicator.classList.remove('active');
            }
        }
        if (sendBtn) sendBtn.disabled = waiting;
        scrollToBottom();
    }

    /**
     * Auto scroll to bottom
     */
    function scrollToBottom() {
        if (!msgBody) return;
        requestAnimationFrame(() => {
            msgBody.scrollTop = msgBody.scrollHeight;
        });
    }

    /**
     * Helper: Format Markdown to HTML safely (Supports # to ######, bold, italic, code, tables, blockquotes, lists)
     */
    function formatMarkdown(text) {
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
                    continue; // Skip markdown separator row
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

    function escapeHtml(str) {
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function getCurrentTime() {
        const now = new Date();
        return now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    /**
     * Session Storage Persistence
     */
    function saveHistory() {
        try {
            sessionStorage.setItem(getStorageKey(), JSON.stringify(chatHistory.slice(-20)));
        } catch (e) {}
    }

    function loadSavedHistory() {
        try {
            const saved = sessionStorage.getItem(getStorageKey());
            if (!saved) return;
            const parsed = JSON.parse(saved);
            if (Array.isArray(parsed) && parsed.length > 0) {
                chatHistory = parsed;
                parsed.forEach(item => {
                    if (item.role === 'user') {
                        appendUserMessage(item.text);
                    } else if (item.role === 'model' || item.role === 'assistant') {
                        appendBotMessage(item.text, false);
                    }
                });
            }
        } catch (e) {
            sessionStorage.removeItem(getStorageKey());
        }
    }

    // Expose global helper if needed
    window.SkinCheckChatbot = {
        open: openChat,
        close: closeChat,
        send: sendMessage
    };

    // Auto boot
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
