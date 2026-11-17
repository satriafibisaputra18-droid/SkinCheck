<?php require APPROOT . '/views/templates/header.php'; ?>

<section class="py-5 sc-auth-section d-flex align-items-center position-relative overflow-hidden">
    <div class="container position-relative z-1">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5">
                
                <?php if (!empty($data['general_err'])) : ?>
                    <script>document.addEventListener('DOMContentLoaded', () => { if (window.showToast) window.showToast(<?php echo json_encode($data['general_err']); ?>, 'error'); });</script>
                <?php endif; ?>
                
                <div class="card sc-auth-box border-0 p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="sc-auth-icon-wrap">
                            <i class="fa-solid fa-lock-open"></i>
                        </div>
                        <h2 class="h3 fw-bold mb-1 sc-auth-heading">Atur Kata Sandi Baru</h2>
                        <p class="sc-auth-subtext mb-0">Silakan buat kata sandi baru yang aman untuk akun Anda (<strong><?php echo htmlspecialchars($data['email']); ?></strong>).</p>
                    </div>
                    
                    <form action="<?php echo URLROOT; ?>/auth/resetPassword" method="POST">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($data['token']); ?>">
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($data['email']); ?>">

                        <!-- New Password Input -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Kata Sandi Baru</label>
                            <input type="password" name="password" id="password" 
                                   class="form-control <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" 
                                   placeholder="Minimal 6 karakter" required autofocus>
                            <span class="invalid-feedback"><?php echo $data['password_err'] ?? ''; ?></span>
                        </div>
                        
                        <!-- Confirm Password Input -->
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" name="confirm_password" id="confirm_password" 
                                   class="form-control <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>" 
                                   placeholder="Ketik ulang kata sandi baru" required>
                            <span class="invalid-feedback"><?php echo $data['confirm_password_err'] ?? ''; ?></span>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid mb-3">
                            <button type="submit" class="sc-btn-gold">
                                <i class="fa-solid fa-circle-check me-2"></i> Perbarui Kata Sandi
                            </button>
                        </div>
                    </form>
                    
                    <hr>
                    
                    <div class="text-center small">
                        <a href="<?php echo URLROOT; ?>/auth/login" class="sc-link-gold">
                            <i class="fa-solid fa-arrow-left me-1"></i> Batal & Kembali ke Login
                        </a>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</section>

<?php require APPROOT . '/views/templates/footer.php'; ?>
