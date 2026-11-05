<?php require APPROOT . '/views/templates/header.php'; ?>

<!-- Hero Section with Centered Large Typography -->
<section id="hero" class="hero-gradient py-5 position-relative">
    <!-- Ambient Corner Accents (Placed strictly at outer edges to ensure crystal-clear typography contrast) -->
    <div class="position-absolute top-0 start-0 translate-middle rounded-circle" style="width: 520px; height: 520px; background: radial-gradient(circle, rgba(191, 161, 95, 0.12) 0%, rgba(191, 161, 95, 0) 70%); filter: blur(60px); pointer-events: none;"></div>
    <div class="position-absolute bottom-0 end-0 translate-middle-y rounded-circle" style="width: 560px; height: 560px; background: radial-gradient(circle, rgba(26, 74, 64, 0.08) 0%, rgba(26, 74, 64, 0) 70%); filter: blur(70px); pointer-events: none;"></div>

    <div class="container py-lg-5 position-relative z-1 text-center">
        <!-- Badge -->
        <div class="mb-3">
            <span class="sc-hero-badge">
                <i class="fa-solid fa-shield-halved text-primary"></i> Platform Evaluasi & Edukasi Kesehatan Kulit
            </span>
        </div>

        <!-- Big Typography: SkinCheck in the center -->
        <h1 class="sc-hero-title">SkinCheck</h1>

        <!-- Subtitle -->
        <h2 class="sc-hero-subtitle">
            Evaluasi Mandiri 4 Indikator Sawar Wajah & Verifikasi Proteksi Tabir Surya
        </h2>

        <!-- Contextual Description -->
        <p class="sc-hero-desc">
            Pendekatan evaluasi kesehatan sawar kulit berbasis parameter klinis terstruktur: analisis kecenderungan komedonal & pori, pemetaan distribusi eritema (kemerahan), verifikasi kestabilan mikrotekstur stratum korneum, serta pemantauan kerataan filter tabir surya secara menyeluruh.
        </p>

        <!-- Centered Action Buttons -->
        <div class="d-flex align-items-center justify-content-center flex-wrap gap-3 mb-5">
            <a href="<?php echo URLROOT; ?>/auth/register" class="sc-hero-btn-primary">
                <i class="fa-solid fa-camera me-2"></i>Mulai Pemeriksaan Wajah
            </a>
            <a href="#features" class="sc-hero-btn-secondary">
                <i class="fa-solid fa-compass me-2"></i>Eksplorasi Fitur
            </a>
        </div>

        <!-- Centered Clinical 4-Pillars Indicator Strip -->
        <div class="sc-hero-strip">
            <div class="row g-3 g-lg-0 align-items-center">
                <div class="col-6 col-lg-3 sc-strip-item">
                    <div class="sc-strip-icon bg-danger-subtle text-danger">
                        <i class="fa-solid fa-circle-dot"></i>
                    </div>
                    <div class="sc-strip-title">Jerawat & Pori</div>
                    <p class="sc-strip-desc">Deteksi kepadatan komedo, pori terbuka, dan sebum berlebih.</p>
                </div>
                <div class="col-6 col-lg-3 sc-strip-item">
                    <div class="sc-strip-icon bg-warning-subtle text-warning">
                        <i class="fa-solid fa-heart-pulse"></i>
                    </div>
                    <div class="sc-strip-title">Kemerahan (Eritema)</div>
                    <p class="sc-strip-desc">Pemetaan reaktivitas vaskular & iritasi sawar kulit.</p>
                </div>
                <div class="col-6 col-lg-3 sc-strip-item">
                    <div class="sc-strip-icon bg-primary-subtle text-primary">
                        <i class="fa-solid fa-wave-square"></i>
                    </div>
                    <div class="sc-strip-title">Tekstur Permukaan</div>
                    <p class="sc-strip-desc">Evaluasi kehalusan mikro-relief stratum korneum.</p>
                </div>
                <div class="col-6 col-lg-3 sc-strip-item">
                    <div class="sc-strip-icon bg-success-subtle text-success">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div class="sc-strip-title">Proteksi Tabir Surya</div>
                    <p class="sc-strip-desc">Verifikasi kerataan aplikasi dan area wajah yang terlewat.</p>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Features Grid (Dark Luxury) -->
<section id="features" class="py-5" style="background: #09110e; border-top: 1px solid rgba(255, 255, 255, 0.05);">
    <div class="container py-lg-5">
        <div class="text-center max-w-2xl mx-auto mb-5">
            <span class="small fw-bold text-uppercase tracking-wider" style="color: var(--secondary-color);">Fitur & Ekosistem</span>
            <h2 class="display-6 fw-bold mt-2 text-white">Personalized Skincare Ecosystem</h2>
            <p class="text-white-50" style="max-width: 600px; margin: 0 auto;">Layanan evaluasi diagnostik dan pendukung kesehatan sawar kulit terintegrasi langsung di peramban Anda.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 p-4 border-0 shadow-sm glass-card text-center text-md-start">
                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-3 d-inline-block mb-4" style="width: fit-content;">
                        <i class="fa-solid fa-camera fs-4"></i>
                    </div>
                    <h4 class="h5 fw-bold mb-2 text-white">Browser Face Scan</h4>
                    <p class="text-white-50 small mb-0">Evaluasi parameter klinis wajah: jerawat, eritema, mikrotekstur, dan hidrasi stratum korneum melalui kamera standar.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 p-4 border-0 shadow-sm glass-card text-center text-md-start">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3 d-inline-block mb-4" style="width: fit-content;">
                        <i class="fa-solid fa-sun fs-4"></i>
                    </div>
                    <h4 class="h5 fw-bold mb-2 text-white">Sunscreen Checker</h4>
                    <p class="text-white-50 small mb-0">Verifikasi kerataan aplikasi filter tabir surya dan identifikasi area kulit wajah yang masih belum terproteksi optimal.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 p-4 border-0 shadow-sm glass-card text-center text-md-start">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3 d-inline-block mb-4" style="width: fit-content;">
                        <i class="fa-solid fa-comments-dollar fs-4 text-success"></i>
                    </div>
                    <h4 class="h5 fw-bold mb-2 text-white">AI Assistant Chat</h4>
                    <p class="text-white-50 small mb-0">Konsultasikan kebutuhan perawatan kulit Anda dengan asisten AI yang dilatih berdasarkan basis data dermatologi.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 p-4 border-0 shadow-sm glass-card text-center text-md-start">
                    <div class="bg-info bg-opacity-10 text-info rounded-3 p-3 d-inline-block mb-4" style="width: fit-content;">
                        <i class="fa-solid fa-prescription-bottle-medical fs-4"></i>
                    </div>
                    <h4 class="h5 fw-bold mb-2 text-white">Kurasi Skincare</h4>
                    <p class="text-white-50 small mb-0">Rekomendasi formula produk dan tabir surya terkurasi sesuai profil sawar kulit dan indikator klinis spesifik Anda.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Technology Framework (Dark Luxury) -->
<section id="technology" class="py-5" style="background: #060b09; border-top: 1px solid rgba(255, 255, 255, 0.05);">
    <div class="container py-lg-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?q=80&w=600&auto=format&fit=crop" class="img-fluid rounded-4 shadow-lg border border-white border-opacity-10" alt="AI Skincare Technology">
            </div>
            
            <div class="col-lg-6">
                <span class="small fw-bold text-uppercase tracking-wider" style="color: var(--secondary-color);">Metodologi Evaluasi</span>
                <h2 class="display-6 fw-bold mt-2 mb-4 text-white">Bagaimana SkinCheck Melakukan Analisis Wajah</h2>
                
                <div class="d-flex gap-3 mb-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 40px; height: 40px; flex-shrink: 0; background: #1a4a40; border: 1px solid rgba(255,255,255,0.15);">
                        1
                    </div>
                    <div>
                        <h4 class="h5 fw-bold text-white">Pengambilan Citra Berkualitas</h4>
                        <p class="text-white-50 mb-0">Umpan kamera standar menangkap bingkai resolusi tinggi tanpa kompresi pada kondisi pencahayaan terstandarisasi.</p>
                    </div>
                </div>
                
                <div class="d-flex gap-3 mb-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 40px; height: 40px; flex-shrink: 0; background: #1a4a40; border: 1px solid rgba(255,255,255,0.15);">
                        2
                    </div>
                    <div>
                        <h4 class="h5 fw-bold text-white">Ekstraksi Landmark Face Mesh</h4>
                        <p class="text-white-50 mb-0">Pemetaan zona wajah (dahi, hidung, pipi, dagu) untuk analisis komedonal, eritema, dan keseragaman tekstur stratum korneum.</p>
                    </div>
                </div>

                <div class="d-flex gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 40px; height: 40px; flex-shrink: 0; background: #1a4a40; border: 1px solid rgba(255,255,255,0.15);">
                        3
                    </div>
                    <div>
                        <h4 class="h5 fw-bold text-white">Verifikasi Kerataan Lapisan UV</h4>
                        <p class="text-white-50 mb-0">Evaluasi spektral citra untuk memperkirakan kerataan lapisan tabir surya dan menandai area yang terlewatkan.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section (High Contrast, No Blurry Washed Out Blob) -->
<section class="sc-cta-section py-5 text-center position-relative overflow-hidden">
    <div class="container py-lg-4 position-relative z-1">
        <div class="sc-cta-card mx-auto p-4 p-md-5">
            <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-20 rounded-pill px-3 py-1.5 mb-3 small fw-medium">
                <i class="fa-solid fa-shield-halved me-1.5" style="color: var(--secondary-color);"></i> Platform Evaluasi Sawar Kulit Mandiri
            </span>
            <h2 class="display-6 fw-bold mb-3 text-white">Mulai Pantau Kesehatan Kulit Wajah Anda</h2>
            <p class="lead mb-4 mx-auto" style="max-width: 620px; font-size: 1.05rem; line-height: 1.7; color: rgba(255, 255, 255, 0.9) !important;">
                Evaluasi 4 indikator sawar kulit wajah secara objektif dan verifikasi kerataan aplikasi tabir surya secara real-time langsung melalui peramban web Anda tanpa perlu mengunduh aplikasi tambahan.
            </p>
            <div class="d-flex justify-content-center align-items-center flex-wrap gap-3">
                <a href="<?php echo URLROOT; ?>/auth/register" class="btn sc-cta-btn-primary px-4 py-2.5 rounded-pill fw-bold">
                    <i class="fa-solid fa-camera me-2"></i>Mulai Pemeriksaan Sekarang
                </a>
                <a href="<?php echo URLROOT; ?>/auth/login" class="btn sc-cta-btn-secondary px-4 py-2.5 rounded-pill fw-semibold">
                    Masuk ke Akun
                </a>
            </div>
        </div>
    </div>
</section>

<?php require APPROOT . '/views/templates/footer.php'; ?>
