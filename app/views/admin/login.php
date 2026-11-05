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
<body class="hero-gradient min-vh-100 d-flex align-items-center position-relative overflow-hidden">
    <!-- Ambient Luxury Glow -->
    <div class="position-absolute top-50 start-50 translate-middle rounded-circle" style="width: 580px; height: 580px; background: radial-gradient(circle, rgba(31, 107, 87, 0.22) 0%, rgba(191, 161, 95, 0.1) 45%, transparent 70%); filter: blur(80px); pointer-events: none;"></div>

    <div class="container position-relative z-1">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                
                <div class="card glass-card border-0 p-4 p-md-5">
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
                            <button type="submit" class="btn btn-primary py-2.5">
                                <i class="fa-solid fa-key me-2"></i>Authenticate Panel
                            </button>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="text-center small">
                        <a href="<?php echo URLROOT; ?>/" class="sc-auth-subtitle text-decoration-none" style="transition: color 0.2s ease;" onmouseover="this.style.color='#dfc68b'" onmouseout="this.style.color='#9eb5ab'"><i class="fa-solid fa-arrow-left me-1"></i>Return to Landing Page</a>
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
