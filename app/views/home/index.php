<?php require APPROOT . '/views/templates/header.php'; ?>

<!-- Hero Section with Centered Large Typography -->
<section id="hero" class="hero-gradient py-5 position-relative">
    <div class="container py-lg-5 position-relative z-1 text-center">
        <div class="sc-hero-parallax">
        <!-- Badge -->
        <div class="mb-3">
            <span class="sc-hero-badge">
                <i class="fa-solid fa-shield-halved text-primary"></i> Cek Kulit &amp; Proteksi UV
            </span>
        </div>

        <!-- Big Typography: SkinCheck in the center -->
        <h1 class="sc-hero-title">SkinCheck</h1>

        <!-- Subtitle -->
        <h2 class="sc-hero-subtitle">
            Cek Kondisi Kulit &amp; Pemerataan Sunscreen dari Browser
        </h2>

        <!-- Contextual Description -->
        <p class="sc-hero-desc">
            Pantau jerawat, kemerahan, tekstur, hiperpigmentasi, dan cakupan sunscreen dalam hitungan detik tanpa unduh aplikasi.
        </p>

        <!-- Centered Action Buttons -->
        <?php
        $isLoggedIn = isset($_SESSION['user_id']);
        $isAdmin    = ($isLoggedIn && ($_SESSION['user_role'] ?? '') === 'admin');
        $primaryCtaUrl = $isLoggedIn ? ($isAdmin ? URLROOT . '/admin/dashboard' : URLROOT . '/scan/faceScan') : URLROOT . '/auth/register';
        $dashCtaUrl    = $isAdmin ? URLROOT . '/admin/dashboard' : URLROOT . '/customer/dashboard';
        ?>
        <div class="d-flex align-items-center justify-content-center flex-wrap gap-3 mb-5">
            <a href="<?php echo $primaryCtaUrl; ?>" class="sc-hero-btn-primary">
                <i class="fa-solid <?php echo $isAdmin ? 'fa-gauge-high' : 'fa-camera'; ?> me-2"></i><span><?php echo $isAdmin ? 'Buka Admin Panel' : 'Mulai Scan Wajah'; ?></span>
            </a>
            <a href="#features" class="sc-hero-btn-secondary">
                <i class="fa-solid fa-compass me-2"></i><span>Lihat Fitur</span>
            </a>
        </div>
        </div>

        <!-- Centered Clinical 4-Pillars Indicator Strip -->
        <div class="sc-hero-strip">
            <div class="row g-3 g-lg-0 align-items-center">
                <div class="col-6 col-lg-3 sc-strip-item">
                    <div class="sc-strip-icon bg-danger-subtle text-danger">
                        <i class="fa-solid fa-circle-dot"></i>
                    </div>
                    <div class="sc-strip-title">Jerawat &amp; Pori</div>
                    <p class="sc-strip-desc">Deteksi bintik aktif dan sebum di area wajah.</p>
                </div>
                <div class="col-6 col-lg-3 sc-strip-item">
                    <div class="sc-strip-icon bg-warning-subtle text-warning">
                        <i class="fa-solid fa-heart-pulse"></i>
                    </div>
                    <div class="sc-strip-title">Kemerahan</div>
                    <p class="sc-strip-desc">Cek area kulit yang sensitif atau kemerahan.</p>
                </div>
                <div class="col-6 col-lg-3 sc-strip-item">
                    <div class="sc-strip-icon bg-primary-subtle text-primary">
                        <i class="fa-solid fa-wave-square"></i>
                    </div>
                    <div class="sc-strip-title">Tekstur Kulit</div>
                    <p class="sc-strip-desc">Ukur kehalusan dan kerataan permukaan kulit.</p>
                </div>
                <div class="col-6 col-lg-3 sc-strip-item">
                    <div class="sc-strip-icon bg-success-subtle text-success">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div class="sc-strip-title">Proteksi UV</div>
                    <p class="sc-strip-desc">Lihat area wajah yang belum tertutup sunscreen.</p>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Features Grid (Clean Clinical) -->
<section id="features" class="py-5 sc-section-white">
    <div class="container py-lg-5">
        <div class="text-center max-w-2xl mx-auto mb-5" data-sc-reveal="blur-up">
            <span class="small fw-bold text-uppercase tracking-wider text-primary">Fitur Utama</span>
            <h2 class="display-6 fw-bold mt-2 text-dark">Semua Kebutuhan Cek Kulit di Satu Tempat</h2>
            <p class="text-muted" style="max-width: 560px; margin: 0 auto;">Cepat, privat, dan langsung bisa digunakan lewat kamera peramban Anda.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3" data-sc-reveal="tilt-up" data-sc-delay="0">
                <div class="card h-100 p-4 border-0 glass-card text-center text-md-start">
                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-3 d-inline-block mb-4" style="width: fit-content;">
                        <i class="fa-solid fa-camera fs-4"></i>
                    </div>
                    <h4 class="h5 fw-bold mb-2">Face Scan</h4>
                    <p class="text-muted small mb-0">Cek jerawat, kemerahan, tekstur, dan noda gelap dari kamera depan.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3" data-sc-reveal="tilt-up" data-sc-delay="100">
                <div class="card h-100 p-4 border-0 glass-card text-center text-md-start">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3 d-inline-block mb-4" style="width: fit-content;">
                        <i class="fa-solid fa-sun fs-4"></i>
                    </div>
                    <h4 class="h5 fw-bold mb-2">Sunscreen Check</h4>
                    <p class="text-muted small mb-0">Cek seberapa merata sunscreen Anda dan temukan area yang terlewat.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3" data-sc-reveal="tilt-up" data-sc-delay="200">
                <div class="card h-100 p-4 border-0 glass-card text-center text-md-start">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3 d-inline-block mb-4" style="width: fit-content;">
                        <i class="fa-solid fa-comments fs-4"></i>
                    </div>
                    <h4 class="h5 fw-bold mb-2">Konsultasi AI</h4>
                    <p class="text-muted small mb-0">Tanyakan hasil scan, urutan skincare, atau kandungan aktif ke asisten AI.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3" data-sc-reveal="tilt-up" data-sc-delay="300">
                <div class="card h-100 p-4 border-0 glass-card text-center text-md-start">
                    <div class="bg-info bg-opacity-10 text-info rounded-3 p-3 d-inline-block mb-4" style="width: fit-content;">
                        <i class="fa-solid fa-prescription-bottle-medical fs-4"></i>
                    </div>
                    <h4 class="h5 fw-bold mb-2">Riwayat &amp; Progres</h4>
                    <p class="text-muted small mb-0">Simpan hasil scan untuk memantau perubahan kondisi kulit dari waktu ke waktu.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Technology Framework (Clean Clinical) -->
<section id="technology" class="py-5 sc-section-sage">
    <div class="container py-lg-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="sc-tech-parallax" data-sc-reveal="clip-left">
                    <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?q=80&w=600&auto=format&fit=crop" class="img-fluid rounded-4 shadow-md border" alt="AI Skincare Technology">
                </div>
            </div>
            
            <div class="col-lg-6">
                <div data-sc-reveal="slide-right">
                    <span class="small fw-bold text-uppercase tracking-wider text-primary">Cara Kerja</span>
                    <h2 class="display-6 fw-bold mt-2 mb-4 text-dark">3 Langkah Mudah Cek Kulit</h2>
                </div>
                
                <div class="d-flex align-items-start gap-2 mb-3" data-sc-reveal="step-cascade" data-sc-delay="80">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 38px; height: 38px; flex-shrink: 0; background: var(--primary-color);">
                        1
                    </div>
                    <div>
                        <h4 class="h5 fw-bold text-dark">Ambil Foto dengan Cahaya Rata</h4>
                        <p class="text-muted mb-0">Gunakan cahaya depan yang terang agar kamera menangkap detail wajah dengan jelas.</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-start gap-2 mb-3" data-sc-reveal="step-cascade" data-sc-delay="180">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 38px; height: 38px; flex-shrink: 0; background: var(--primary-color);">
                        2
                    </div>
                    <div>
                        <h4 class="h5 fw-bold text-dark">Analisis Otomatis 4 Indikator</h4>
                        <p class="text-muted mb-0">Sistem memetakan skor jerawat, kemerahan, tekstur, dan pigmentasi secara cepat.</p>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-2" data-sc-reveal="step-cascade" data-sc-delay="280">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 38px; height: 38px; flex-shrink: 0; background: var(--primary-color);">
                        3
                    </div>
                    <div>
                        <h4 class="h5 fw-bold text-dark">Cek Heatmap Sunscreen</h4>
                        <p class="text-muted mb-0">Lihat persentase cakupan dan area wajah yang perlu ditambah tabir surya.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="sc-cta-section py-5 text-center position-relative overflow-hidden">
    <div class="container py-lg-4 position-relative z-1">
        <div class="sc-cta-card mx-auto p-4 p-md-5" data-sc-reveal="scale-pop">
            <span class="badge bg-white bg-opacity-25 text-white border border-white border-opacity-50 rounded-pill px-3 py-1 mb-3 small fw-medium">
                <i class="fa-solid fa-shield-halved me-2"></i>SkinCheck Web App
            </span>
            <h2 class="display-6 fw-bold mb-3 text-white">Siap Cek Kondisi Kulit Anda?</h2>
            <p class="lead mb-4 mx-auto" style="max-width: 540px; font-size: 1.02rem; line-height: 1.65; color: rgba(255, 255, 255, 0.94) !important;">
                Mulai analisis wajah dan cek pemerataan sunscreen langsung dari peramban Anda secara gratis.
            </p>
            <div class="d-flex justify-content-center align-items-center flex-wrap gap-3">
                <?php if ($isLoggedIn) : ?>
                    <a href="<?php echo $primaryCtaUrl; ?>" class="btn sc-cta-btn-primary px-4 py-2 rounded-pill fw-bold">
                        <i class="fa-solid <?php echo $isAdmin ? 'fa-gauge-high' : 'fa-camera'; ?> me-2"></i><span><?php echo $isAdmin ? 'Admin Console' : 'Mulai Scan Sekarang'; ?></span>
                    </a>
                    <a href="<?php echo $dashCtaUrl; ?>" class="btn sc-cta-btn-secondary px-4 py-2 rounded-pill fw-semibold">
                        <i class="fa-solid fa-chart-pie me-2"></i><span>Buka Dashboard</span>
                    </a>
                <?php else : ?>
                    <a href="<?php echo URLROOT; ?>/auth/register" class="btn sc-cta-btn-primary px-4 py-2 rounded-pill fw-bold">
                        <i class="fa-solid fa-camera me-2"></i><span>Mulai Sekarang</span>
                    </a>
                    <a href="<?php echo URLROOT; ?>/auth/login" class="btn sc-cta-btn-secondary px-4 py-2 rounded-pill fw-semibold">
                        <span>Masuk ke Akun</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require APPROOT . '/views/templates/footer.php'; ?>
