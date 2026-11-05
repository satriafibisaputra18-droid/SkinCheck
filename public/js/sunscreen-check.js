/**
 * SkinCheck – Sunscreen Evenness Check JavaScript Module
 *
 * Handles camera lifecycle, face framing guide, lighting checklist,
 * capture, preview, retake, and AI visual sunscreen coverage estimation upload.
 *
 * IMPORTANT: Standard RGB camera is used for AI-based visual sunscreen coverage estimation.
 * Standard RGB cameras do not measure UV.
 */

'use strict';

class SunscreenCheckApp {

    constructor(config = {}) {
        this.uploadUrl  = config.uploadUrl  || '';
        this.csrfToken  = config.csrfToken  || '';
        this.redirectTo = config.redirectTo || '/customer/dashboard';

        this.stream       = null;
        this.capturedBlob = null;
        this.state        = 'idle'; // idle | live | captured | uploading | done

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
    }

    _bindElements() {
        this.videoEl        = document.getElementById('sc-sun-video');
        this.canvasEl       = document.getElementById('sc-sun-canvas');
        this.previewEl      = document.getElementById('sc-sun-preview');
        this.placeholderEl  = document.getElementById('sc-sun-placeholder');
        this.hudEl          = document.getElementById('sc-sun-hud');
        this.statusEl       = document.getElementById('sc-sun-status-msg');
        this.uploadProgEl   = document.getElementById('sc-sun-upload-progress');
        this.progressBarEl  = document.getElementById('sc-sun-progress-bar');
        this.validationEl   = document.getElementById('sc-sun-validation-result');

        this.btnStart    = document.getElementById('sc-sun-btn-start');
        this.btnCapture  = document.getElementById('sc-sun-btn-capture');
        this.btnRetake   = document.getElementById('sc-sun-btn-retake');
        this.btnSubmit   = document.getElementById('sc-sun-btn-submit');
        this.btnStop     = document.getElementById('sc-sun-btn-stop');
        this.permErrorEl = document.getElementById('sc-sun-perm-error');
    }

    _bindEvents() {
        this.btnStart?.addEventListener('click',   () => this.startCamera());
        this.btnCapture?.addEventListener('click', () => this.captureImage());
        this.btnRetake?.addEventListener('click',  () => this.retake());
        this.btnSubmit?.addEventListener('click',  () => this.submitCheck());
        this.btnStop?.addEventListener('click',    () => this.stopCamera());
    }

    // ── Camera Lifecycle ──────────────────────────────────────────────────

    async startCamera() {
        this._hidePermError();
        this._setStatus('<i class="fa-solid fa-spinner fa-spin me-1"></i>Meminta izin akses kamera…');

        if (!navigator.mediaDevices?.getUserMedia) {
            this._showPermError(
                'WebRTC Tidak Didukung',
                'Browser Anda tidak mendukung akses kamera langsung. Silakan gunakan Chrome, Firefox, atau Safari versi terbaru.'
            );
            return;
        }

        try {
            this.stream = await navigator.mediaDevices.getUserMedia(this.videoConstraints);

            this.videoEl.srcObject = this.stream;
            await this.videoEl.play();

            this.state = 'live';
            this._updateUI();
            this._setStatus('<i class="fa-solid fa-circle text-success me-1" style="font-size:.55rem;"></i>Kamera aktif — Posisikan wajah di dalam frame oval.', 'success');

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
        this._setStatus('Kamera dimatikan.', 'muted');
    }

    // ── Capture & Client-Side Pre-Validation ──────────────────────────────

    captureImage() {
        if (this.state !== 'live' || !this.stream) return;

        const vw = this.videoEl.videoWidth  || 640;
        const vh = this.videoEl.videoHeight || 480;

        this.canvasEl.width  = vw;
        this.canvasEl.height = vh;

        const ctx = this.canvasEl.getContext('2d');
        // Mirror horizontally to match preview
        ctx.translate(vw, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(this.videoEl, 0, 0, vw, vh);
        ctx.setTransform(1, 0, 0, 1, 0, 0);

        // Pre-validate lighting & resolution
        const validation = this._clientValidate(ctx, vw, vh);

        if (!validation.ok) {
            this._showValidationWarning(validation.message, 'warning');
        }

        this.canvasEl.toBlob((blob) => {
            if (!blob) {
                this._showValidationWarning('Gagal mengambil gambar dari kamera.', 'danger');
                return;
            }

            this.capturedBlob = blob;

            const url = URL.createObjectURL(blob);
            this.previewEl.src = url;
            this.previewEl.onload = () => URL.revokeObjectURL(url);

            this.state = 'captured';
            this._updateUI();
            this._setStatus('Foto diambil. Tinjau hasil dan klik "Periksa Sunscreen" atau "Ulangi".', 'success');

        }, 'image/jpeg', 0.92);
    }

    _clientValidate(ctx, w, h) {
        if (w < 320 || h < 240) {
            return { ok: false, message: `Resolusi terlalu rendah (${w}×${h}). Minimal 320×240.` };
        }

        const sampleW = Math.floor(w * 0.6);
        const sampleH = Math.floor(h * 0.6);
        const offsetX = Math.floor(w * 0.2);
        const offsetY = Math.floor(h * 0.2);

        let totalLuma = 0;
        const step    = 10;
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
            return { ok: false, message: 'Pencahayaan terlalu gelap. Dekatkan diri ke sumber cahaya merata.' };
        }
        if (avgBrightness > 240) {
            return { ok: false, message: 'Pencahayaan terlalu terang / silau (overexposed).' };
        }

        return { ok: true, message: 'Pencahayaan optimal untuk estimasi visual.' };
    }

    retake() {
        this.capturedBlob = null;
        this.previewEl.src = '';
        this.state = 'live';
        this._clearValidation();
        this._updateUI();
        this._setStatus('<i class="fa-solid fa-circle text-success me-1" style="font-size:.55rem;"></i>Kamera aktif — Posisikan wajah di dalam frame oval.', 'success');
    }

    // ── Upload & Sunscreen Estimation Submission ───────────────────────────

    async submitCheck() {
        if (!this.capturedBlob || this.state !== 'captured') return;

        this.state = 'uploading';
        this._updateUI();
        this._setStatus('<i class="fa-solid fa-spinner fa-spin me-1"></i>Menganalisis pemerataan visual sunscreen…');
        this._showProgress(0);

        const formData = new FormData();
        formData.append('scan_image', this.capturedBlob, 'sunscreen_scan.jpg');
        formData.append('csrf_token', this.csrfToken);

        try {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', this.uploadUrl, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const pct = Math.round((e.loaded / e.total) * 100);
                    this._showProgress(pct);
                }
            });

            xhr.addEventListener('load', () => {
                let resp;
                try { resp = JSON.parse(xhr.responseText); }
                catch { resp = { success: false, message: 'Server mengembalikan respons tidak valid.' }; }

                if (xhr.status >= 200 && xhr.status < 300 && resp.success) {
                    this._onUploadSuccess(resp);
                } else {
                    this._onUploadError(resp.message || 'Pemeriksaan gagal. Silakan coba lagi.', resp.validation_errors || []);
                }
            });

            xhr.addEventListener('error', () => {
                this._onUploadError('Terjadi kesalahan jaringan. Periksa koneksi internet Anda.');
            });

            xhr.timeout = 30000;
            xhr.send(formData);

        } catch (err) {
            this._onUploadError('Kesalahan tak terduga: ' + err.message);
        }
    }

    _onUploadSuccess(resp) {
        this._showProgress(100);

        if (resp.is_low_confidence) {
            this._setStatus('<i class="fa-solid fa-triangle-exclamation text-warning me-1"></i>Peringatan Keyakinan Rendah', 'warning');
            this._showValidationWarning(
                '<strong>Hasil kurang meyakinkan.</strong> Silakan ambil gambar ulang dengan pencahayaan yang lebih baik untuk hasil optimal. ' +
                `<br><a href="${resp.redirect_url}" class="btn btn-sm btn-outline-warning mt-2">Lihat Hasil Estimasi (${resp.coverage_score}%)</a>`,
                'warning'
            );
            this.state = 'captured';
            this._updateUI();
            return;
        }

        this._setStatus('<i class="fa-solid fa-circle-check text-success me-1"></i>Estimasi visual selesai! Mengalihkan…', 'success');
        this._showValidationWarning(`Sunscreen Coverage: <strong>${resp.coverage_score}% (${resp.coverage_status})</strong>`, 'success');
        this.state = 'done';
        this._updateUI();

        if (this.stream) this.stream.getTracks().forEach(t => t.stop());

        setTimeout(() => {
            window.location.href = resp.redirect_url || this.redirectTo;
        }, 1500);
    }

    _onUploadError(message, validationErrors = []) {
        this.state = 'captured';
        this._updateUI();
        this._hideProgress();

        let fullMessage = message;
        if (validationErrors.length > 0) {
            fullMessage = validationErrors.join('<br>');
        }
        this._showValidationWarning(fullMessage, 'danger');
        this._setStatus('<i class="fa-solid fa-triangle-exclamation text-danger me-1"></i>Pemeriksaan gagal.', 'danger');
    }

    _handleCameraError(err) {
        console.error('Camera error in SunscreenCheckApp:', err);
        let title   = 'Kendala Akses Kamera';
        let message = err.message || 'Tidak dapat mengakses kamera.';

        if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
            title   = 'Izin Kamera Ditolak';
            message = 'Browser tidak memiliki izin akses kamera. Klik ikon gembok/kamera di bilah alamat browser, pilih "Izinkan", lalu muat ulang halaman.';
        } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
            title   = 'Kamera Tidak Ditemukan';
            message = 'Perangkat kamera tidak terdeteksi pada perangkat ini. Pastikan kamera terpasang dengan baik.';
        } else if (err.name === 'NotReadableError' || err.name === 'TrackStartError') {
            title   = 'Kamera Sedang Digunakan';
            message = 'Kamera sedang dipakai oleh tab atau aplikasi lain. Tutup aplikasi tersebut dan coba lagi.';
        }

        this._showPermError(title, message);
        this.state = 'idle';
        this._updateUI();
    }

    _updateUI() {
        const s = this.state;
        this._toggle(this.videoEl,       s === 'live');
        this._toggle(this.hudEl,         s === 'live');
        this._toggle(this.previewEl,     s === 'captured' || s === 'done');
        this._toggle(this.placeholderEl, s === 'idle');

        this._toggle(this.btnStart,   s === 'idle');
        this._toggle(this.btnCapture, s === 'live');
        this._toggle(this.btnRetake,  s === 'captured');
        this._toggle(this.btnSubmit,  s === 'captured');
        this._toggle(this.btnStop,    s === 'live');

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
        const typeMap = { warning: 'alert-warning', danger: 'alert-danger', success: 'alert-success' };
        this.validationEl.className = 'alert border-0 py-2 px-3 mb-3 ' + (typeMap[type] || 'alert-warning');
        this.validationEl.innerHTML = `<i class="fa-solid fa-${type === 'success' ? 'circle-check' : (type === 'danger' ? 'circle-xmark' : 'triangle-exclamation')} me-2"></i>${message}`;
        this.validationEl.classList.remove('d-none');
    }

    _clearValidation() {
        if (this.validationEl) {
            this.validationEl.classList.add('d-none');
            this.validationEl.innerHTML = '';
        }
    }

    _showProgress(pct) {
        if (this.uploadProgEl) this.uploadProgEl.classList.remove('d-none');
        if (this.progressBarEl) {
            this.progressBarEl.style.width  = pct + '%';
            this.progressBarEl.textContent  = pct < 100 ? pct + '%' : 'Menganalisis…';
        }
    }

    _hideProgress() {
        this.uploadProgEl?.classList.add('d-none');
    }
}

window.addEventListener('beforeunload', () => {
    window.__sunscreenCheckApp?.stopCamera?.();
});
