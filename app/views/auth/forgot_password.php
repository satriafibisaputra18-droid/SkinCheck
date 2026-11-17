<?php require APPROOT . '/views/templates/header.php'; ?>

<section class="py-5 sc-auth-section d-flex align-items-center position-relative overflow-hidden">
    <div class="container position-relative z-1">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5">
                
                <div class="card sc-auth-box border-0 p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="sc-auth-icon-wrap">
                            <i class="fa-solid fa-key"></i>
                        </div>
                        <h2 class="h3 fw-bold mb-1 sc-auth-heading">Lupa Kata Sandi</h2>
                        <p class="sc-auth-subtext mb-0">Masukkan email akun Anda. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi.</p>
                    </div>
                    
                    <form action="<?php echo URLROOT; ?>/auth/forgotPassword" method="POST">
                        
                        <!-- Email Input -->
                        <div class="mb-4">
                            <label for="email" class="form-label">Alamat Email Terdaftar</label>
                            <input type="email" name="email" id="email" 
                                   class="form-control <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" 
                                   value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>" 
                                   placeholder="nama@email.com" required autofocus>
                            <span class="invalid-feedback"><?php echo $data['email_err'] ?? ''; ?></span>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="d-grid mb-3">
                            <button type="submit" class="sc-btn-gold">
                                <i class="fa-solid fa-paper-plane me-2"></i> Kirim Tautan Reset
                            </button>
                        </div>
                    </form>
                    
                    <hr>
                    
                    <div class="text-center small">
                        <a href="<?php echo URLROOT; ?>/auth/login" class="sc-link-gold">
                            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Halaman Masuk
                        </a>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</section>

<?php require APPROOT . '/views/templates/footer.php'; ?>
