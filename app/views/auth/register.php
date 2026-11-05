<?php require APPROOT . '/views/templates/header.php'; ?>

<!-- Dedicated In-Page Luxury Dark Styles (Guarantees zero-cache rendering) -->
<style>
/* Scoped Dark Auth Architecture */
.sc-auth-section {
    background: #080f0c !important;
    background-image: 
        radial-gradient(at 0% 0%, rgba(191, 161, 95, 0.1) 0px, transparent 50%),
        radial-gradient(at 100% 100%, rgba(26, 74, 64, 0.18) 0px, transparent 50%),
        radial-gradient(at 50% 50%, rgba(31, 107, 87, 0.12) 0px, transparent 65%) !important;
    min-height: 85vh;
}

.sc-auth-box {
    background: #0d1b16 !important;
    border: 1px solid rgba(223, 198, 139, 0.22) !important;
    border-radius: 20px !important;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.08) !important;
    position: relative;
    overflow: hidden;
    color: #e2ede8 !important;
}

.sc-auth-box::before {
    content: '';
    position: absolute;
    top: 0;
    left: 10%;
    right: 10%;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(223, 198, 139, 0.7), transparent);
    pointer-events: none;
}

/* Icon & Header */
.sc-auth-icon-wrap {
    width: 64px;
    height: 64px;
    margin: 0 auto 1.15rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(31, 107, 87, 0.5) 0%, rgba(13, 38, 30, 0.9) 100%) !important;
    border: 1px solid rgba(223, 198, 139, 0.4) !important;
    color: #dfc68b !important;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.45);
}

.sc-auth-icon-wrap i {
    font-size: 1.55rem;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.4));
}

.sc-auth-heading {
    color: #ffffff !important;
    font-family: 'Outfit', sans-serif !important;
    font-weight: 700 !important;
    letter-spacing: -0.02em;
}

.sc-auth-subtext {
    color: #94a3b8 !important;
    font-size: 0.88rem;
}

.sc-auth-gold-badge {
    background: rgba(223, 198, 139, 0.12) !important;
    color: #dfc68b !important;
    border: 1px solid rgba(223, 198, 139, 0.35) !important;
    padding: 0.4rem 1.15rem !important;
    border-radius: 999px !important;
    font-size: 0.78rem !important;
    font-weight: 600 !important;
    letter-spacing: 0.02em !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.45rem !important;
}

/* Form Inputs - Strict Dark Theme Matching Customer Portal */
.sc-auth-box .form-label {
    color: #dfc68b !important;
    font-size: 0.82rem !important;
    font-weight: 600 !important;
    margin-bottom: 0.45rem !important;
    letter-spacing: 0.015em;
    display: block;
    text-align: left;
}

.sc-auth-box .form-control {
    background: rgba(255, 255, 255, 0.04) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    color: #ffffff !important;
    border-radius: 12px !important;
    padding: 0.72rem 1rem !important;
    font-size: 0.92rem !important;
    transition: all 0.25s ease !important;
}

.sc-auth-box .form-control::placeholder {
    color: rgba(255, 255, 255, 0.3) !important;
}

.sc-auth-box .form-control:focus {
    background: rgba(255, 255, 255, 0.08) !important;
    border-color: #dfc68b !important;
    color: #ffffff !important;
    box-shadow: 0 0 0 3px rgba(223, 198, 139, 0.2) !important;
    outline: none !important;
}

/* Chrome/Edge Autofill Override: Completely Prevents White/Blue Pale Box */
.sc-auth-box input:-webkit-autofill,
.sc-auth-box input:-webkit-autofill:hover, 
.sc-auth-box input:-webkit-autofill:focus {
    -webkit-text-fill-color: #ffffff !important;
    -webkit-box-shadow: 0 0 0 1000px #11211b inset !important;
    border: 1px solid rgba(223, 198, 139, 0.45) !important;
    caret-color: #dfc68b !important;
    transition: background-color 5000s ease-in-out 0s !important;
}

/* Terms Checkbox */
.sc-auth-box .form-check-input {
    background-color: rgba(255, 255, 255, 0.08) !important;
    border: 1px solid rgba(255, 255, 255, 0.25) !important;
    border-radius: 6px !important;
    width: 1.15rem !important;
    height: 1.15rem !important;
    cursor: pointer;
}

.sc-auth-box .form-check-input:checked {
    background-color: #1f6b57 !important;
    border-color: #dfc68b !important;
    box-shadow: 0 0 8px rgba(223, 198, 139, 0.35) !important;
}

.sc-auth-box .form-check-label {
    color: #94a3b8 !important;
    font-size: 0.82rem !important;
}

.sc-auth-box .form-check-label a {
    color: #dfc68b !important;
    font-weight: 600;
    text-decoration: none;
}

.sc-auth-box .form-check-label a:hover {
    color: #f7e7be !important;
    text-decoration: underline;
}

/* Primary Action Button - Customer Gold Pill Button */
.sc-btn-gold {
    background: linear-gradient(135deg, #dfc68b 0%, #c4a968 100%) !important;
    border: 1px solid rgba(223, 198, 139, 0.5) !important;
    color: #05120f !important;
    font-weight: 700 !important;
    font-size: 0.95rem !important;
    border-radius: 999px !important;
    padding: 0.8rem 1.6rem !important;
    box-shadow: 0 4px 18px rgba(191, 161, 95, 0.35) !important;
    transition: all 0.25s ease !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    cursor: pointer;
}

.sc-btn-gold:hover {
    background: #eed8a1 !important;
    color: #05120f !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 26px rgba(191, 161, 95, 0.5) !important;
}

/* Divider & Footer link */
.sc-auth-box hr {
    border-color: rgba(255, 255, 255, 0.08) !important;
    margin: 1.5rem 0 !important;
}

.sc-link-gold {
    color: #dfc68b !important;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.2s ease;
}

.sc-link-gold:hover {
    color: #f7e7be !important;
    text-decoration: underline;
}
</style>

<section class="py-5 sc-auth-section d-flex align-items-center position-relative overflow-hidden">
    <!-- Ambient Backdrop Glow -->
    <div class="position-absolute top-50 start-50 translate-middle rounded-circle" style="width: 580px; height: 580px; background: radial-gradient(circle, rgba(31, 107, 87, 0.22) 0%, rgba(191, 161, 95, 0.1) 45%, transparent 70%); filter: blur(85px); pointer-events: none;"></div>

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
                                <i class="fa-brands fa-telegram text-info"></i> Verifikasi Instan 1-Klik via Telegram
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
                                <span class="invalid-feedback" style="color: #fca5a5; font-size: 0.78rem;"><?php echo $data['name_err']; ?></span>
                            </div>
                            
                            <!-- Email Input -->
                            <div class="col-12">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" name="email" id="email" 
                                       class="form-control <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" 
                                       value="<?php echo htmlspecialchars($data['email']); ?>" 
                                       placeholder="name@example.com" required>
                                <span class="invalid-feedback" style="color: #fca5a5; font-size: 0.78rem;"><?php echo $data['email_err']; ?></span>
                            </div>
                            
                            <!-- Password Input -->
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" 
                                       class="form-control <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" 
                                       placeholder="Min 6 characters" required>
                                <span class="invalid-feedback" style="color: #fca5a5; font-size: 0.78rem;"><?php echo $data['password_err']; ?></span>
                            </div>
                            
                            <!-- Confirm Password Input -->
                            <div class="col-md-6">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" name="confirm_password" id="confirm_password" 
                                       class="form-control <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>" 
                                       placeholder="Re-enter password" required>
                                <span class="invalid-feedback" style="color: #fca5a5; font-size: 0.78rem;"><?php echo $data['confirm_password_err']; ?></span>
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
