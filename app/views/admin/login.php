<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Admin Portal Login - SkinCheck'; ?></title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Custom Style Sheet -->
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/style.css">
</head>
<body class="sc-auth-section min-vh-100 d-flex align-items-center position-relative overflow-hidden">
    <div class="container position-relative z-1 py-4">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                
                <div class="sc-auth-box p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="sc-auth-icon-badge">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                        <h2 class="fw-bold mb-1 sc-auth-title">Admin Console</h2>
                        <p class="sc-auth-subtitle small mb-0">SkinCheck Internal Management Portal</p>
                    </div>
                    
                    <form action="<?php echo URLROOT; ?>/admin/login" method="POST">
                        
                        <!-- Email / Username Input -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Admin Username / Email</label>
                            <input type="text" name="email" id="email" 
                                   class="form-control <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" 
                                   value="<?php echo htmlspecialchars($data['email']); ?>" 
                                   placeholder="Masukkan username atau email admin" required>
                            <span class="invalid-feedback"><?php echo $data['email_err']; ?></span>
                        </div>
                        
                        <!-- Password Input -->
                        <div class="mb-4">
                            <label for="password" class="form-label">Security Password</label>
                            <input type="password" name="password" id="password" 
                                   class="form-control <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" 
                                   placeholder="Masukkan password admin" required>
                            <span class="invalid-feedback"><?php echo $data['password_err']; ?></span>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn sc-btn-gold py-2">
                                <i class="fa-solid fa-key me-2"></i>Authenticate Panel
                            </button>
                        </div>
                    </form>
                    
                    <hr class="my-4" style="border-color: var(--border-subtle);">
                    
                    <div class="text-center small">
                        <a href="<?php echo URLROOT; ?>/" class="sc-link-gold text-decoration-none"><i class="fa-solid fa-arrow-left me-1"></i>Return to Landing Page</a>
                    </div>
                </div>
                
            </div>
        </div>
    </div>

    <!-- Bootstrap 5.3 Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <!-- SkinCheck AI Chatbot Widget -->
    <?php require APPROOT . '/views/templates/chatbot_widget.php'; ?>
</body>
</html>
