<?php require APPROOT . '/views/templates/header.php'; ?>

<section class="py-5 sc-auth-section d-flex align-items-center position-relative overflow-hidden">
    <div class="container position-relative z-1">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">
                
                <div class="card sc-auth-box border-0 p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="sc-auth-icon-wrap">
                            <i class="fa-solid fa-user-lock"></i>
                        </div>
                        <h2 class="h3 fw-bold mb-1 sc-auth-heading">Welcome Back</h2>
                        <p class="sc-auth-subtext mb-0">Sign in to scan and monitor skin conditions</p>
                    </div>
                    
                    <form action="<?php echo URLROOT; ?>/auth/login" method="POST">
                        
                        <!-- Email Input -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" name="email" id="email" 
                                   class="form-control <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" 
                                   value="<?php echo htmlspecialchars($data['email']); ?>" 
                                   placeholder="name@example.com" required>
                            <span class="invalid-feedback"><?php echo $data['email_err']; ?></span>
                        </div>
                        
                        <!-- Password Input -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="password" class="form-label mb-0">Password</label>
                                <a href="<?php echo URLROOT; ?>/auth/forgotPassword" class="sc-link-gold small" style="font-size: 0.8rem;">
                                    Lupa kata sandi?
                                </a>
                            </div>
                            <input type="password" name="password" id="password" 
                                   class="form-control <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" 
                                   placeholder="Enter your password" required>
                            <span class="invalid-feedback"><?php echo $data['password_err']; ?></span>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="d-grid mb-3">
                            <button type="submit" class="sc-btn-gold">
                                <i class="fa-solid fa-right-to-bracket me-2"></i> Sign In
                            </button>
                        </div>
                    </form>
                    
                    <hr>
                    
                    <div class="text-center small">
                        <span class="sc-auth-subtext">Don't have an account?</span> 
                        <a href="<?php echo URLROOT; ?>/auth/register" class="sc-link-gold ms-1">Register Now</a>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</section>

<?php require APPROOT . '/views/templates/footer.php'; ?>
