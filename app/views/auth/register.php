<?php require APPROOT . '/views/templates/header.php'; ?>

<section class="py-5 sc-auth-section d-flex align-items-center position-relative overflow-hidden">
    <div class="container position-relative z-1">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                
                <div class="card sc-auth-box border-0 p-4 p-md-5">
                    
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <div class="sc-auth-icon-wrap">
                            <i class="fa-solid fa-user-plus"></i>
                        </div>
                        <h2 class="h3 fw-bold mb-1 sc-auth-heading">Create Account</h2>
                        <p class="sc-auth-subtext mb-3">Analyze skin condition and UV protection instantly</p>
                        <div class="d-flex justify-content-center">
                            <span class="sc-auth-gold-badge">
                                <i class="fa-brands fa-whatsapp text-success fs-6 me-1"></i> Verifikasi Instan Kode OTP via WhatsApp
                            </span>
                        </div>
                    </div>

                    
                    <form action="<?php echo URLROOT; ?>/auth/register" method="POST">
                        <div class="row g-3">
                            
                            <!-- Name Input -->
                            <div class="col-12">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" name="name" id="name" 
                                       class="form-control <?php echo (!empty($data['name_err'])) ? 'is-invalid' : ''; ?>" 
                                       value="<?php echo htmlspecialchars($data['name']); ?>" 
                                       placeholder="e.g. Jane Doe" required>
                                <span class="invalid-feedback"><?php echo $data['name_err']; ?></span>
                            </div>
                            
                            <!-- Email Input -->
                            <div class="col-12">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" name="email" id="email" 
                                       class="form-control <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" 
                                       value="<?php echo htmlspecialchars($data['email']); ?>" 
                                       placeholder="name@example.com" required>
                                <span class="invalid-feedback"><?php echo $data['email_err']; ?></span>
                            </div>

                            <!-- WhatsApp Phone Input -->
                            <div class="col-12">
                                <label for="phone" class="form-label">Nomor WhatsApp Aktif</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-success border-end-0" style="border-color: #dce8e4;">
                                        <i class="fa-brands fa-whatsapp fs-5"></i>
                                    </span>
                                    <input type="tel" name="phone" id="phone" 
                                           class="form-control border-start-0 <?php echo (!empty($data['phone_err'])) ? 'is-invalid' : ''; ?>" 
                                           value="<?php echo htmlspecialchars($data['phone'] ?? ''); ?>" 
                                           placeholder="Contoh: 081234567890" required>
                                </div>
                                <?php if (!empty($data['phone_err'])): ?>
                                    <span class="invalid-feedback d-block"><?php echo $data['phone_err']; ?></span>
                                <?php else: ?>
                                    <small class="text-muted" style="font-size: 0.75rem;">Kode OTP 6-digit akan dikirimkan otomatis ke WhatsApp nomor ini.</small>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Password Input -->
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" 
                                       class="form-control <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" 
                                       placeholder="Min 6 characters" required>
                                <span class="invalid-feedback"><?php echo $data['password_err']; ?></span>
                            </div>
                            
                            <!-- Confirm Password Input -->
                            <div class="col-md-6">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" name="confirm_password" id="confirm_password" 
                                       class="form-control <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>" 
                                       placeholder="Re-enter password" required>
                                <span class="invalid-feedback"><?php echo $data['confirm_password_err']; ?></span>
                            </div>
                            
                        </div>

                        <!-- Checkbox Agreement -->
                        <div class="form-check my-4 text-start">
                            <input class="form-check-input" type="checkbox" id="termsAgree" required>
                            <label class="form-check-label" for="termsAgree">
                                I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                            </label>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="d-grid mb-3">
                            <button type="submit" class="sc-btn-gold">
                                Lanjut ke Verifikasi OTP <i class="fa-solid fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </form>
                    
                    <hr>
                    
                    <div class="text-center small">
                        <span class="sc-auth-subtext">Already have an account?</span> 
                        <a href="<?php echo URLROOT; ?>/auth/login" class="sc-link-gold ms-1">Sign In</a>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</section>

<?php require APPROOT . '/views/templates/footer.php'; ?>
