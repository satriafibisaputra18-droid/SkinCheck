/**
 * SkinCheck – Face Scan Module
 * Handles the full camera → capture → validate → upload flow.
 *
 * Depends on: Bootstrap 5, FontAwesome 6
 * Entry point: new FaceScanApp(config) called from the view.
 */

'use strict';

class FaceScanApp {

    // ── Config & State ────────────────────────────────────────────────────

    constructor(config = {}) {
        this.uploadUrl  = config.uploadUrl  || '';
        this.csrfToken  = config.csrfToken  || '';
        this.redirectTo = config.redirectTo || '/customer/dashboard';
        this.audioUrl   = config.audioUrl   || '';

        this.stream       = null;
        this.capturedBlob = null;
        this.state        = 'idle'; // idle | live | captured | uploading | done
        this.audioCtx     = null;
        this.htmlAudio    = null;
        this.reportUrl    = null;
        this.countdownTimer = null;

        // Constraints: prefer front-facing camera, reasonable resolution
        this.videoConstraints = {
            video: {
                facingMode: 'user',
                width:  { ideal: 1280, min: 320 },
                height: { ideal: 960,  min: 240 },
            },
            audio: false,
        };

        this._bindElements();
        this._bindEvents();
        this._updateUI();

        // Relocate modal to document.body to prevent parent z-index stacking traps
        try {
            const modalEl = document.getElementById('scScanCompleteModal');
            if (modalEl && modalEl.parentElement !== document.body) {
                document.body.appendChild(modalEl);
            }
        } catch (e) {}

        // Prime audio on page interaction
        const primeHandler = () => {
            this._initAudio();
            document.removeEventListener('click', primeHandler);
        };
        document.addEventListener('click', primeHandler, { once: true });
    }

    _initAudio() {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!this.audioCtx && AudioCtx) {
                this.audioCtx = new AudioCtx();
            }
            if (this.audioCtx && this.audioCtx.state === 'suspended') {
                this.audioCtx.resume();
            }

            // Pre-unlock HTML5 audio element
            const domAudio = document.getElementById('sc-audio-player');
            if (domAudio) {
                domAudio.volume = 0.001;
                const p = domAudio.play();
                if (p && typeof p.then === 'function') {
                    p.then(() => {
                        domAudio.pause();
                        domAudio.currentTime = 0;
                        domAudio.volume = 1.0;
                    }).catch(() => {});
                }
            }

            if (this.audioUrl && !this.htmlAudio) {
                this.htmlAudio = new Audio(this.audioUrl);
                this.htmlAudio.preload = 'auto';
            }
        } catch (e) {
            console.warn('Audio init note:', e);
        }
    }

    // ── DOM Bindings ──────────────────────────────────────────────────────

    _bindElements() {
        this.videoEl        = document.getElementById('sc-video');
        this.canvasEl       = document.getElementById('sc-canvas');
        this.previewEl      = document.getElementById('sc-preview');
        this.placeholderEl  = document.getElementById('sc-placeholder');
        this.hudEl          = document.getElementById('sc-hud');
        this.laserEl        = document.getElementById('sc-laser');
        this.statusEl       = document.getElementById('sc-status-msg');
        this.countdownEl    = document.getElementById('sc-countdown');
        this.uploadProgEl   = document.getElementById('sc-upload-progress');
        this.progressBarEl  = document.getElementById('sc-progress-bar');
        this.validationEl   = document.getElementById('sc-validation-result');
        this.qualityBadgeEl = document.getElementById('sc-quality-badge');

        this.btnStart    = document.getElementById('sc-btn-start');
        this.btnCapture  = document.getElementById('sc-btn-capture');
        this.btnRetake   = document.getElementById('sc-btn-retake');
        this.btnSubmit   = document.getElementById('sc-btn-submit');
        this.btnStop     = document.getElementById('sc-btn-stop');
        this.permErrorEl = document.getElementById('sc-perm-error');
    }

    _bindEvents() {
        this.btnStart?.addEventListener('click',   () => this.startCamera());
        this.btnCapture?.addEventListener('click', () => this.captureImage());
        this.btnRetake?.addEventListener('click',  () => this.retake());
        this.btnSubmit?.addEventListener('click',  () => this.submitScan());
        this.btnStop?.addEventListener('click',    () => this.stopCamera());
    }

    // ── Camera Lifecycle ──────────────────────────────────────────────────

    async startCamera() {
        this._hidePermError();
        this._setStatus('<i class="fa-solid fa-spinner fa-spin me-1"></i>Requesting camera access…');

        if (!navigator.mediaDevices?.getUserMedia) {
            this._showPermError(
                'WebRTC not supported',
                'Your browser does not support camera access. Please use Chrome, Firefox, or Safari on a modern device.'
            );
            return;
        }

        try {
            this.stream = await navigator.mediaDevices.getUserMedia(this.videoConstraints);

            this.videoEl.srcObject = this.stream;
            await this.videoEl.play();

            this.state = 'live';
            this._updateUI();
            this._setStatus('<i class="fa-solid fa-circle text-success me-1" style="font-size:.55rem;"></i>Camera active — posisikan wajah di tengah frame.', 'success');

        } catch (err) {
            this._handleCameraError(err);
        }
    }

    stopCamera() {
        if (this.stream) {
            this.stream.getTracks().forEach(t => t.stop());
            this.stream = null;
        }
        this.videoEl.srcObject = null;
        this.capturedBlob = null;
        this.state = 'idle';
        this._clearValidation();
        this._updateUI();
        this._setStatus('Camera stopped.', 'muted');
    }

    // ── Capture & Client-Side Validation ─────────────────────────────────

    captureImage() {
        if (this.state !== 'live' || !this.stream) return;

        const vw = this.videoEl.videoWidth  || 640;
        const vh = this.videoEl.videoHeight || 480;

        this.canvasEl.width  = vw;
        this.canvasEl.height = vh;

        const ctx = this.canvasEl.getContext('2d');
        // Mirror to match preview (front camera)
        ctx.translate(vw, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(this.videoEl, 0, 0, vw, vh);
        ctx.setTransform(1, 0, 0, 1, 0, 0); // reset

        // Client-side pre-validation
        const validation = this._clientValidate(ctx, vw, vh);

        if (!validation.ok) {
            this._showValidationWarning(validation.message, 'warning');
            // Allow retake but don't block — server re-validates anyway
        }

        // Convert canvas to Blob (JPEG, quality 0.92)
        this.canvasEl.toBlob((blob) => {
            if (!blob) {
                this._showValidationWarning('Could not capture image from camera.', 'danger');
                return;
            }

            this.capturedBlob = blob;

            // Show preview
            const url = URL.createObjectURL(blob);
            this.previewEl.src = url;
            this.previewEl.onload = () => URL.revokeObjectURL(url);

            this._showQualityBadge(validation);

            this.state = 'captured';
            this._updateUI();
            this._setStatus('Image captured. Review and submit, or retake.', 'success');

        }, 'image/jpeg', 0.92);
    }

    /**
     * Client-side pre-validation (fast UX feedback).
     * Server will re-validate authoritatively after upload.
     */
    _clientValidate(ctx, w, h) {
        // 1. Resolution check
        if (w < 320 || h < 240) {
            return { ok: false, message: `Resolusi terlalu rendah (${w}×${h}). Minimal 320×240.`, score: 0 };
        }

        // 2. Brightness check — sample centre region pixels
        const sampleW = Math.floor(w * 0.6);
        const sampleH = Math.floor(h * 0.6);
        const offsetX = Math.floor(w * 0.2);
        const offsetY = Math.floor(h * 0.2);

        let totalLuma = 0;
        const step    = 10; // px step
        let samples   = 0;

        const imageData = ctx.getImageData(offsetX, offsetY, sampleW, sampleH);
        const data      = imageData.data;

        for (let i = 0; i < data.length; i += 4 * step) {
            const r = data[i], g = data[i + 1], b = data[i + 2];
            totalLuma += 0.299 * r + 0.587 * g + 0.114 * b;
            samples++;
        }

        const avgBrightness = samples > 0 ? totalLuma / samples : 128;

        if (avgBrightness < 30) {
            return { ok: false, message: 'Gambar terlalu gelap. Perbaiki pencahayaan dan ambil ulang.', score: Math.round(avgBrightness), brightness: avgBrightness };
        }

        // 3. Compute quality score (0–100)
        const resScore    = Math.min(100, (w * h) / (1280 * 960) * 100);
        const brightScore = Math.min(100, (avgBrightness / 200) * 100);
        const qualScore   = Math.round(0.6 * resScore + 0.4 * brightScore);

        return { ok: true, message: 'Image looks good!', score: qualScore, brightness: avgBrightness };
    }

    retake() {
        this.capturedBlob = null;
        this.previewEl.src = '';
        this.state = 'live';
        this._clearValidation();
        this._updateUI();
        this._setStatus('<i class="fa-solid fa-circle text-success me-1" style="font-size:.55rem;"></i>Kamera aktif — posisikan wajah di tengah frame.', 'success');
    }

    // ── Upload ────────────────────────────────────────────────────────────

    async submitScan() {
        if (!this.capturedBlob || this.state !== 'captured') return;

        // Unlock audio context and keep it alive during server wait
        this._initAudio();
        try {
            if (this.audioCtx) {
                if (this.audioCtx.state === 'suspended') {
                    this.audioCtx.resume();
                }
                // Keep-alive silent oscillator so browser audio engine never suspends
                if (!this._keepAliveNode) {
                    const silentGain = this.audioCtx.createGain();
                    silentGain.gain.value = 0.00001; // virtually silent
                    const osc = this.audioCtx.createOscillator();
                    osc.connect(silentGain);
                    silentGain.connect(this.audioCtx.destination);
                    osc.start();
                    this._keepAliveNode = { osc, gain: silentGain };
                }
            }
        } catch (e) {}

        // Request browser notification permission if not yet decided
        if ('Notification' in window && Notification.permission === 'default') {
            try { Notification.requestPermission(); } catch (e) {}
        }

        this.state = 'uploading';
        this._updateUI();
        this._setStatus('<i class="fa-solid fa-spinner fa-spin me-1"></i>Tahap 1/3: Mengunggah gambar...');
        this._showProgress(20);

        const formData = new FormData();
        formData.append('scan_image', this.capturedBlob, 'scan.jpg');
        formData.append('csrf_token', this.csrfToken);

        // Progress stages during AI verification
        let stageTimer = setInterval(() => {
            if (this.state !== 'uploading') {
                clearInterval(stageTimer);
                return;
            }
            const currentPct = parseInt(this.progressBarEl?.style.width || '20', 10);
            if (currentPct < 60) {
                this._showProgress(currentPct + 15);
                this._setStatus('<i class="fa-solid fa-brain me-1 text-primary"></i>Tahap 2/3: Memverifikasi & menganalisis kondisi kulit...');
            } else if (currentPct < 90) {
                this._showProgress(currentPct + 10);
                this._setStatus('<i class="fa-solid fa-calculator me-1 text-info"></i>Tahap 3/3: Menghitung skor kesehatan kulit...');
            }
        }, 1600);

        try {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', this.uploadUrl, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            // Upload progress
            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const pct = Math.min(60, Math.round((e.loaded / e.total) * 60));
                    this._showProgress(pct);
                }
            });

            xhr.addEventListener('load', () => {
                clearInterval(stageTimer);
                let resp;
                try { resp = JSON.parse(xhr.responseText); }
                catch { resp = { success: false, message: 'Server returned an invalid response.' }; }

                if (xhr.status >= 200 && xhr.status < 300 && resp.success) {
                    this._onUploadSuccess(resp);
                } else {
                    this._onUploadError(resp.message || 'Upload failed. Please try again.', resp.validation_errors || []);
                }
            });

            xhr.addEventListener('error', () => {
                clearInterval(stageTimer);
                this._onUploadError('Network error. Check your connection and try again.');
            });

            xhr.addEventListener('timeout', () => {
                clearInterval(stageTimer);
                this._onUploadError('Request timed out. Please try again.');
            });

            xhr.timeout = 45000;
            xhr.send(formData);

        } catch (err) {
            clearInterval(stageTimer);
            this._onUploadError('Unexpected error: ' + err.message);
        }
    }

    playNotificationSound() {
        console.log('[FaceScanApp] Triggering bell chime notification sound...');

        // 1. Synthesize bell chime via Web Audio API (Loud & resonant 3-tone clinical chime)
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (AudioCtx) {
                if (!this.audioCtx || this.audioCtx.state === 'closed') {
                    this.audioCtx = new AudioCtx();
                }
                const ctx = this.audioCtx;
                if (ctx.state === 'suspended') {
                    ctx.resume();
                }

                const now = ctx.currentTime;
                // High-fidelity clinical chime: Tone 1 (880Hz A5) -> Tone 2 (1318.5Hz E6) -> Tone 3 (1760Hz A6)
                const bellNotes = [
                    { f: 880,   offset: 0,    dur: 0.60, gain: 0.85 },
                    { f: 1760,  offset: 0,    dur: 0.40, gain: 0.35 },
                    { f: 1318.5,offset: 0.15, dur: 0.75, gain: 0.90 },
                    { f: 2637,  offset: 0.15, dur: 0.45, gain: 0.40 },
                    { f: 1760,  offset: 0.30, dur: 0.90, gain: 0.75 }
                ];

                bellNotes.forEach(n => {
                    const osc = ctx.createOscillator();
                    const gainNode = ctx.createGain();
                    osc.type = 'triangle'; // rich acoustic bell timbre
                    osc.frequency.setValueAtTime(n.f, now + n.offset);

                    gainNode.gain.setValueAtTime(0.0001, now + n.offset);
                    gainNode.gain.linearRampToValueAtTime(n.gain, now + n.offset + 0.015);
                    gainNode.gain.exponentialRampToValueAtTime(0.0001, now + n.offset + n.dur);

                    osc.connect(gainNode);
                    gainNode.connect(ctx.destination);
                    osc.start(now + n.offset);
                    osc.stop(now + n.offset + n.dur + 0.05);
                });
            }
        } catch (e) {
            console.warn('[FaceScanApp] WebAudio chime warning:', e);
        }

        // 2. Play Base64 in-memory WAV chime
        try {
            if (window.__SC_CHIME_B64) {
                const b64Audio = new Audio(window.__SC_CHIME_B64);
                b64Audio.volume = 1.0;
                const p2 = b64Audio.play();
                if (p2 && typeof p2.catch === 'function') p2.catch(() => {});
            }
        } catch (e) {}

        // 3. Play HTML5 audio tag
        try {
            const domAudio = document.getElementById('sc-audio-player');
            if (domAudio) {
                domAudio.currentTime = 0;
                domAudio.volume = 1.0;
                const p = domAudio.play();
                if (p && typeof p.catch === 'function') p.catch(() => {});
            }
        } catch (e) {}

        // 4. Haptic vibration for mobile phones
        if ('vibrate' in navigator) {
            try { navigator.vibrate([200, 100, 250]); } catch (e) {}
        }
    }

    testCompleteFlow() {
        this.playNotificationSound();
        this.showCompleteModal({
            overall_score: 94,
            acne_score: 96,
            redness_score: 92,
            texture_score: 95,
            hyperpigmentation_score: 93,
            redirect_url: this.redirectTo
        });
    }

    showCompleteModal(resp) {
        const modalEl = document.getElementById('scScanCompleteModal');
        if (!modalEl) return;

        // Ensure modal is directly under document.body to avoid stacking context traps
        if (modalEl.parentElement !== document.body) {
            document.body.appendChild(modalEl);
        }

        const score = resp.overall_score ?? 85;
        this.reportUrl = resp.redirect_url || this.redirectTo;

        const scoreValEl = document.getElementById('sc-modal-score-val');
        const badgeEl = document.getElementById('sc-modal-score-badge');
        const btnView = document.getElementById('sc-modal-btn-view');
        const acneEl = document.getElementById('sc-modal-acne');
        const redEl = document.getElementById('sc-modal-redness');
        const textEl = document.getElementById('sc-modal-texture');
        const pigEl = document.getElementById('sc-modal-pigment');
        const countdownEl = document.getElementById('sc-modal-countdown');

        if (scoreValEl) scoreValEl.textContent = score;

        if (badgeEl) {
            if (score >= 80) {
                badgeEl.textContent = 'Kondisi Prima & Terverifikasi';
                badgeEl.className = 'badge bg-success-subtle text-success px-3 py-1 rounded-pill fw-semibold';
            } else if (score >= 60) {
                badgeEl.textContent = 'Kondisi Cukup Baik';
                badgeEl.className = 'badge bg-warning-subtle text-warning px-3 py-1 rounded-pill fw-semibold';
            } else {
                badgeEl.textContent = 'Perlu Perawatan Khusus';
                badgeEl.className = 'badge bg-danger-subtle text-danger px-3 py-1 rounded-pill fw-semibold';
            }
        }

        const formatMetric = (val) => (val !== undefined && val !== null) ? `${val}/100` : '-';
        if (acneEl) acneEl.textContent = formatMetric(resp.acne_score);
        if (redEl) redEl.textContent = formatMetric(resp.redness_score);
        if (textEl) textEl.textContent = formatMetric(resp.texture_score);
        if (pigEl) pigEl.textContent = formatMetric(resp.hyperpigmentation_score);

        if (btnView) btnView.href = this.reportUrl;

        // Guaranteed Full-Screen Overlay Display
        modalEl.style.display = 'flex';
        modalEl.classList.add('show');
        modalEl.removeAttribute('aria-hidden');
        modalEl.setAttribute('aria-modal', 'true');
        document.body.classList.add('modal-open');

        // Auto-redirect countdown (15 seconds, relaxed, user can cancel or click immediately)
        let seconds = 15;
        if (this.countdownTimer) clearInterval(this.countdownTimer);
        if (countdownEl) countdownEl.textContent = seconds;
        this.countdownTimer = setInterval(() => {
            seconds--;
            if (countdownEl) countdownEl.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(this.countdownTimer);
                this.goToReport();
            }
        }, 1000);
    }

    goToReport() {
        if (this.countdownTimer) clearInterval(this.countdownTimer);
        window.location.href = this.reportUrl || this.redirectTo;
    }

    closeModal() {
        if (this.countdownTimer) clearInterval(this.countdownTimer);
        const modalEl = document.getElementById('scScanCompleteModal');
        if (modalEl) {
            modalEl.classList.remove('show');
            modalEl.style.display = 'none';
        }
        document.body.classList.remove('modal-open');
        const toastEl = document.getElementById('sc-floating-toast');
        if (toastEl) toastEl.classList.remove('show');
    }

    _flashTabTitle(score) {
        const originalTitle = document.title;
        const alertTitle = `🔔 (1) Analisis Selesai (${score}/100) - SkinCheck`;
        let count = 0;
        const maxFlash = 8;
        const interval = setInterval(() => {
            document.title = (count % 2 === 0) ? alertTitle : originalTitle;
            count++;
            if (count >= maxFlash) {
                clearInterval(interval);
                document.title = originalTitle;
            }
        }, 850);
    }

    _onUploadSuccess(resp) {
        this._showProgress(100);
        this.state = 'done';
        this._updateUI();

        const score = resp.overall_score ?? 85;
        this.reportUrl = resp.redirect_url || this.redirectTo;

        // 1. Play crystal-clear notification chime
        this.playNotificationSound();

        // 2. Flash browser tab title so user notices even from other tabs
        this._flashTabTitle(score);

        // 3. Browser desktop notification
        if ('Notification' in window && Notification.permission === 'granted') {
            try {
                const notif = new Notification('Analisis Wajah Selesai 🔔', {
                    body: `Skor kesehatan kulit: ${score}/100. Ketuk untuk membuka laporan & rekomendasi.`,
                    icon: '/public/favicon.ico'
                });
                notif.onclick = () => {
                    window.focus();
                    this.goToReport();
                };
            } catch (e) {}
        }

        // 4. Show Floating Dynamic Toast Banner at top
        const toastEl = document.getElementById('sc-floating-toast');
        const toastScoreEl = document.getElementById('sc-toast-score');
        if (toastScoreEl) toastScoreEl.textContent = score;
        if (toastEl) {
            toastEl.classList.add('show');
        }

        // 5. Clear any inline validation banner
        if (this.validationEl) {
            this.validationEl.className = 'd-none';
            this.validationEl.innerHTML = '';
        }
        this._setStatus('<i class="fa-solid fa-circle-check text-success me-1"></i>Analisis selesai.', 'success');

        // Stop camera stream
        if (this.stream) this.stream.getTracks().forEach(t => t.stop());

        // 6. Open the true popup modal with full animation & styling
        this.showCompleteModal(resp);
    }

    _onUploadError(message, validationErrors = []) {
        this.state = 'captured'; // allow retry
        this._updateUI();
        this._hideProgress();

        let fullMessage = message;
        if (validationErrors.length > 0) {
            fullMessage = validationErrors.join('<br>');
        }
        this._showValidationWarning(fullMessage, 'danger');
        this._setStatus('<i class="fa-solid fa-triangle-exclamation text-danger me-1"></i>Scan gagal. Periksa pesan error.', 'danger');
    }

    // ── Error Handling ────────────────────────────────────────────────────

    _handleCameraError(err) {
        console.error('Camera error:', err);
        let title   = 'Camera Error';
        let message = err.message || 'Could not access camera.';

        if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
            title   = 'Camera Permission Denied';
            message = 'Browser tidak mendapatkan izin kamera. Klik ikon kunci/kamera di address bar, izinkan akses kamera, lalu muat ulang halaman.';
        } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
            title   = 'No Camera Found';
            message = 'Tidak ada kamera yang terdeteksi di perangkat ini. Pastikan kamera terhubung dan tidak digunakan aplikasi lain.';
        } else if (err.name === 'NotReadableError' || err.name === 'TrackStartError') {
            title   = 'Camera In Use';
            message = 'Kamera sedang digunakan oleh aplikasi lain. Tutup aplikasi tersebut dan coba lagi.';
        } else if (err.name === 'OverconstrainedError') {
            title   = 'Camera Constraints Error';
            message = 'Resolusi yang diminta tidak didukung kamera ini. Mencoba resolusi yang lebih rendah…';
            this._retryWithLowerResolution();
            return;
        }

        this._showPermError(title, message);
        this.state = 'idle';
        this._updateUI();
    }

    async _retryWithLowerResolution() {
        try {
            this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
            this.videoEl.srcObject = this.stream;
            await this.videoEl.play();
            this.state = 'live';
            this._updateUI();
            this._setStatus('Kamera aktif (resolusi standar).', 'success');
        } catch (e) {
            this._showPermError('Camera Error', 'Could not start camera at any resolution: ' + e.message);
            this.state = 'idle';
            this._updateUI();
        }
    }

    // ── UI State Machine ──────────────────────────────────────────────────

    _updateUI() {
        const s = this.state;

        // Video / preview / placeholder visibility
        this._toggle(this.videoEl,       s === 'live');
        this._toggle(this.hudEl,         s === 'live');
        this._toggle(this.previewEl,     s === 'captured' || s === 'done');
        this._toggle(this.placeholderEl, s === 'idle');

        // Buttons
        this._toggle(this.btnStart,   s === 'idle');
        this._toggle(this.btnCapture, s === 'live');
        this._toggle(this.btnRetake,  s === 'captured');
        this._toggle(this.btnSubmit,  s === 'captured');
        this._toggle(this.btnStop,    s === 'live');

        // Disable submit while uploading
        if (this.btnSubmit) this.btnSubmit.disabled = (s === 'uploading' || s === 'done');
        if (this.btnRetake) this.btnRetake.disabled = (s === 'uploading' || s === 'done');
    }

    _toggle(el, visible) {
        if (!el) return;
        el.classList.toggle('d-none', !visible);
    }

    _setStatus(html, type = 'muted') {
        if (!this.statusEl) return;
        const colorMap = { success: 'text-success', danger: 'text-danger', warning: 'text-warning', muted: 'text-muted' };
        this.statusEl.className = 'sc-status-msg small mt-2 mb-0 ' + (colorMap[type] || 'text-muted');
        this.statusEl.innerHTML = html;
    }

    _showPermError(title, message) {
        if (!this.permErrorEl) return;
        this.permErrorEl.classList.remove('d-none');
        const titleEl = this.permErrorEl.querySelector('.sc-perm-title');
        const msgEl   = this.permErrorEl.querySelector('.sc-perm-msg');
        if (titleEl) titleEl.textContent = title;
        if (msgEl)   msgEl.textContent   = message;
    }

    _hidePermError() {
        this.permErrorEl?.classList.add('d-none');
    }

    _showValidationWarning(message, type = 'warning') {
        if (!this.validationEl) return;
        const typeMap = {
            warning: 'alert-warning',
            danger:  'alert-danger',
            success: 'alert-success',
        };
        this.validationEl.className = 'alert border-0 py-2 px-3 mb-3 ' + (typeMap[type] || 'alert-warning');
        this.validationEl.innerHTML = `<i class="fa-solid fa-${type === 'success' ? 'circle-check' : (type === 'danger' ? 'circle-xmark' : 'triangle-exclamation')} me-2"></i>${message}`;
        this.validationEl.classList.remove('d-none');
    }

    _clearValidation() {
        if (this.validationEl) {
            this.validationEl.classList.add('d-none');
            this.validationEl.innerHTML = '';
        }
        if (this.qualityBadgeEl) this.qualityBadgeEl.classList.add('d-none');
    }

    _showQualityBadge(validation) {
        if (!this.qualityBadgeEl) return;
        const score = validation.score || 0;
        const cls   = score >= 70 ? 'bg-success' : (score >= 40 ? 'bg-warning text-dark' : 'bg-danger');
        this.qualityBadgeEl.className = `badge ${cls} ms-2`;
        this.qualityBadgeEl.textContent = `Quality: ${score}`;
        this.qualityBadgeEl.classList.remove('d-none');
    }

    _showProgress(pct) {
        if (this.uploadProgEl)  this.uploadProgEl.classList.remove('d-none');
        if (this.progressBarEl) {
            this.progressBarEl.style.width     = pct + '%';
            this.progressBarEl.ariaValuenow    = pct;
            this.progressBarEl.textContent     = pct < 100 ? pct + '%' : 'Memproses…';
        }
    }

    _hideProgress() {
        this.uploadProgEl?.classList.add('d-none');
    }
}

// Release camera on page navigation
window.addEventListener('beforeunload', () => {
    window.__faceScanApp?.stopCamera?.();
});
