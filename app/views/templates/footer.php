    </main>

    <!-- Global Footer (Compact Clean Clinical) -->
    <footer class="sc-global-footer">
        <div class="container">
            <div class="row g-2 justify-content-between align-items-start">
                <div class="col-lg-4 col-md-5">
                    <div class="d-flex align-items-center mb-1">
                        <i class="fa-solid fa-microscope me-2 fs-5" style="color:var(--primary-color);"></i>
                        <h6 class="mb-0 fw-bold sc-footer-heading" style="font-family:'Outfit',sans-serif; font-size: 0.95rem;">Skin<span style="color:var(--primary-color);">Check</span></h6>
                    </div>
                    <p class="text-muted mb-0" style="font-size: 0.78rem; line-height: 1.45; max-width: 320px;">
                        Platform web untuk mengecek kondisi kulit wajah dan pemerataan sunscreen secara cepat lewat kamera browser.
                    </p>
                </div>
                
                <div class="col-lg-2 col-md-3">
                    <span class="text-uppercase fw-bold d-block mb-1" style="color: var(--primary-color); font-size: 0.74rem; letter-spacing: 0.05em;">Navigasi</span>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-1" style="font-size: 0.8rem;">
                        <li><a href="<?php echo URLROOT; ?>/" class="text-decoration-none">Home</a></li>
                        <li><a href="<?php echo URLROOT; ?>/#features" class="text-decoration-none">Features</a></li>
                        <li><a href="<?php echo URLROOT; ?>/auth/login" class="text-decoration-none">Masuk</a></li>
                        <li><a href="<?php echo URLROOT; ?>/auth/register" class="text-decoration-none">Daftar Akun</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-4">
                    <span class="text-uppercase fw-bold d-block mb-1 sc-footer-heading" style="font-size: 0.74rem; letter-spacing: 0.05em;">Contact & Support</span>
                    <ul class="list-unstyled mb-0 text-muted d-flex flex-column gap-1" style="font-size: 0.8rem;">
                        <li><i class="fa-regular fa-envelope me-1.5 text-primary" style="font-size: 0.75rem;"></i>support@skincheck.com</li>
                        <li><i class="fa-solid fa-phone me-1.5 text-primary" style="font-size: 0.75rem;"></i>+62 812-3456-7890</li>
                        <li><i class="fa-solid fa-location-dot me-1.5 text-primary" style="font-size: 0.75rem;"></i>Jakarta, Indonesia</li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-2" style="border-color: rgba(15, 118, 110, 0.12);">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-1">
                <p class="text-muted mb-0" style="font-size: 0.74rem;">&copy; <?php echo date('Y'); ?> SkinCheck. All Rights Reserved.</p>
                <div class="d-flex gap-2.5">
                    <a href="#" class="text-muted" style="font-size: 0.85rem;"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="text-muted" style="font-size: 0.85rem;"><i class="fa-brands fa-linkedin"></i></a>
                    <a href="#" class="text-muted" style="font-size: 0.85rem;"><i class="fa-brands fa-twitter"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3 Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    
    <!-- Main Application Scripts -->
    <script src="<?php echo URLROOT; ?>/public/js/main.js?v=<?php echo time(); ?>"></script>
    
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
