    </main>

    <!-- Global Footer -->
    <footer class="py-5 sc-global-footer text-white">
        <div class="container">
            <div class="row g-4 justify-content-between">
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fa-solid fa-shield-halved me-2 fs-3" style="color:var(--secondary-color);"></i>
                        <h4 class="text-white mb-0 fw-bold" style="font-family:'Outfit',sans-serif;">Skin<span style="color:var(--secondary-color);">Check</span></h4>
                    </div>
                    <p class="small mb-0" style="color: rgba(255, 255, 255, 0.78); line-height: 1.7;">
                        SkinCheck adalah platform evaluasi kesehatan sawar kulit wajah berbasis web terstruktur, membantu pengguna memantau 4 indikator klinis dan verifikasi kerataan aplikasi tabir surya secara real-time.
                    </p>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <h5 class="text-uppercase mb-3 fs-6" style="color: var(--secondary-color); font-weight: 700; letter-spacing: 0.05em;">Navigasi</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><a href="<?php echo URLROOT; ?>/" class="text-decoration-none" style="color: rgba(255, 255, 255, 0.75);">Home</a></li>
                        <li class="mb-2"><a href="<?php echo URLROOT; ?>/#features" class="text-decoration-none" style="color: rgba(255, 255, 255, 0.75);">Features</a></li>
                        <li class="mb-2"><a href="<?php echo URLROOT; ?>/auth/login" class="text-decoration-none" style="color: rgba(255, 255, 255, 0.75);">Masuk</a></li>
                        <li class="mb-2"><a href="<?php echo URLROOT; ?>/auth/register" class="text-decoration-none" style="color: rgba(255, 255, 255, 0.75);">Daftar Akun</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-uppercase mb-3 fs-6">Contact & Support</h5>
                    <ul class="list-unstyled mb-0 text-white-50 small">
                        <li class="mb-2"><i class="fa-regular fa-envelope me-2"></i>support@skincheck.com</li>
                        <li class="mb-2"><i class="fa-solid fa-phone me-2"></i>+62 812-3456-7890</li>
                        <li class="mb-2"><i class="fa-solid fa-location-dot me-2"></i>Jakarta, Indonesia</li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4 border-secondary">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <p class="small text-white-50 mb-0">&copy; <?php echo date('Y'); ?> SkinCheck. All Rights Reserved.</p>
                <div class="d-flex gap-3">
                    <a href="#" class="text-white-50"><i class="fa-brands fa-instagram fs-5"></i></a>
                    <a href="#" class="text-white-50"><i class="fa-brands fa-linkedin fs-5"></i></a>
                    <a href="#" class="text-white-50"><i class="fa-brands fa-twitter fs-5"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3 Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    
    <!-- Main Application Scripts -->
    <script src="<?php echo URLROOT; ?>/public/js/main.js"></script>
    
    <!-- Camera Script (Loads conditionally when camera container exists on page) -->
    <?php if (strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false) : ?>
        <script src="<?php echo URLROOT; ?>/public/js/camera.js"></script>
    <?php endif; ?>

    <!-- Global Floating Toast Notification System -->
    <?php require APPROOT . '/views/templates/toast.php'; ?>

    <!-- SkinCheck AI Chatbot Widget (Node.js & Gemini AI Studio) -->
    <?php require APPROOT . '/views/templates/chatbot_widget.php'; ?>
</body>
</html>
