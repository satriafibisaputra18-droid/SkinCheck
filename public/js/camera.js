/**
 * SkinCheck – Camera & Scanner Interaction Handler
 * Manages webcam access, the scanning laser animation, and UI state transitions.
 * No AI analysis is performed here; this module handles camera I/O only.
 */

class SkinScanner {
    constructor() {
        this.video     = document.getElementById('webcam-feed');
        this.btnStart  = document.getElementById('btn-start-camera');
        this.btnScan   = document.getElementById('btn-trigger-scan');
        this.btnStop   = document.getElementById('btn-stop-camera');
        this.laser     = document.getElementById('scan-laser-line');
        this.loader    = document.getElementById('scan-loader');
        this.placeholder = document.getElementById('camera-feed-placeholder');

        this.stream    = null;
        this.scanning  = false;

        if (this.btnStart) this.btnStart.addEventListener('click', () => this.startCamera());
        if (this.btnScan)  this.btnScan.addEventListener('click',  () => this.triggerScanAnim());
        if (this.btnStop)  this.btnStop.addEventListener('click',  () => this.stopCamera());
    }

    async startCamera() {
        try {
            if (this.stream) this.stopCamera();

            this.stream = await navigator.mediaDevices.getUserMedia({
                video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' },
                audio: false
            });

            if (this.video) {
                this.video.srcObject = this.stream;
                this.video.play();
            }

            this._show(this.video);
            this._hide(this.placeholder);
            this._hide(this.btnStart);
            this._show(this.btnScan);
            this._show(this.btnStop);

        } catch (err) {
            console.error('Camera error:', err);
            const errMsg = 'Gagal mengakses kamera. Pastikan izin kamera aktif pada peramban Anda, lalu coba lagi.';
            if (window.showToast) {
                window.showToast(errMsg, 'error');
            } else {
                alert(errMsg);
            }
        }
    }

    stopCamera() {
        if (this.stream) {
            this.stream.getTracks().forEach(t => t.stop());
            this.stream = null;
        }
        if (this.video) { this.video.srcObject = null; }

        this._hide(this.video);
        this._show(this.placeholder);
        this._show(this.btnStart);
        this._hide(this.btnScan);
        this._hide(this.btnStop);
        if (this.laser)  this.laser.style.display = 'none';
        if (this.loader) this.loader.classList.add('d-none');
        this.scanning = false;
        if (this.btnScan) this.btnScan.removeAttribute('disabled');
    }

    /**
     * Plays a 3-second scanning animation.
     * NOTE: No AI call is made. The animation indicates a future analysis trigger.
     */
    triggerScanAnim() {
        if (!this.stream || this.scanning) return;

        this.scanning = true;
        if (this.btnScan) this.btnScan.setAttribute('disabled', 'true');
        if (this.laser)   this.laser.style.display = 'block';
        if (this.loader)  this.loader.classList.remove('d-none');

        setTimeout(() => {
            if (this.laser)  this.laser.style.display = 'none';
            if (this.loader) this.loader.classList.add('d-none');
            this.scanning = false;
            if (this.btnScan) this.btnScan.removeAttribute('disabled');
        }, 3000);
    }

    _show(el) { if (el) el.classList.remove('d-none'); }
    _hide(el) { if (el) el.classList.add('d-none'); }
}

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('webcam-feed')) {
        window.skinScannerInstance = new SkinScanner();
    }
});

// Release camera track when user navigates away
window.addEventListener('beforeunload', () => {
    if (window.skinScannerInstance) {
        window.skinScannerInstance.stopCamera();
    }
});
