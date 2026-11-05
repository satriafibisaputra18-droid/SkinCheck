<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Admin Dashboard - SkinCheck'; ?></title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Custom Style Sheet -->
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/style.css">
    
    <!-- Chatbot Stylesheet -->
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/chatbot.css?v=<?php echo time(); ?>">
</head>
<body class="bg-light">

<?php
// Determine active nav link
$currentUrl = $_GET['url'] ?? '';
$urlParts   = explode('/', trim($currentUrl, '/'));
$urlAction  = $urlParts[1] ?? 'dashboard';
?>

    <!-- Responsive Admin Layout Container -->
    <div class="container-fluid">
        <div class="row">
            
            <!-- Sidebar -->
            <aside class="col-md-3 col-lg-2 px-0 admin-sidebar d-none d-md-block shadow-sm">
                <div class="p-4 border-bottom border-secondary border-opacity-25">
                    <a href="<?php echo URLROOT; ?>/" class="d-flex align-items-center text-white text-decoration-none">
                        <i class="fa-solid fa-face-smile-wink me-2 text-success fs-4"></i>
                        <span class="fs-4 fw-bold tracking-tight text-white">SkinCheck</span>
                    </a>
                    <small class="text-secondary d-block mt-1">Admin Panel v<?php echo SITE_VERSION; ?></small>
                </div>
                
                <div class="p-3">
                    <div class="text-secondary small text-uppercase fw-bold mb-3 px-3">Main Console</div>
                    <ul class="nav flex-column mb-4">
                        <li class="nav-item">
                            <a href="<?php echo URLROOT; ?>/admin/dashboard" class="nav-link <?php echo $urlAction === 'dashboard' ? 'active' : ''; ?>">
                                <i class="fa-solid fa-gauge me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo URLROOT; ?>/admin/aiAssistant" class="nav-link <?php echo $urlAction === 'aiAssistant' ? 'active' : ''; ?>">
                                <i class="fa-solid fa-robot me-2 text-warning"></i>AI Assistant
                                <span class="badge bg-warning bg-opacity-25 text-warning rounded-pill ms-auto small" style="font-size:0.65rem;">Gemini</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo URLROOT; ?>/admin/settings" class="nav-link <?php echo $urlAction === 'settings' ? 'active' : ''; ?>">
                                <i class="fa-solid fa-sliders me-2 text-info"></i>API & Settings
                                <span class="badge bg-info bg-opacity-25 text-info rounded-pill ms-auto small" style="font-size:0.65rem;">Config</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo URLROOT; ?>/" class="nav-link">
                                <i class="fa-solid fa-house me-2"></i>View Site
                            </a>
                        </li>
                    </ul>

                    <div class="text-secondary small text-uppercase fw-bold mb-3 px-3">Session</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="<?php echo URLROOT; ?>/auth/logout" class="nav-link text-danger">
                                <i class="fa-solid fa-arrow-right-from-bracket me-2 text-danger"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="col-md-9 col-lg-10 ms-auto px-md-4 py-4 min-vh-100 d-flex flex-column">
                
                <!-- Admin Header Navbar -->
                <header class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
                    <div>
                        <h1 class="h3 mb-0"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Internal Team'); ?></h1>
                        <span class="text-muted small">Systems Operational</span>
                    </div>
                    
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-success-subtle text-success py-2 px-3 border border-success border-opacity-10 rounded-pill">
                            <i class="fa-solid fa-shield-halved me-1"></i>Administrator
                        </span>
                        
                        <!-- Mobile Sidebar Toggle -->
                        <button class="btn btn-outline-primary d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar" aria-expanded="false" aria-label="Toggle navigation">
                            <i class="fa-solid fa-bars"></i>
                        </button>
                    </div>
                </header>

                <!-- Mobile Collapse Navigation Menu -->
                <div class="collapse d-md-none bg-dark text-white p-3 rounded mb-4" id="mobileSidebar">
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2">
                            <a href="<?php echo URLROOT; ?>/admin/dashboard" class="nav-link text-white <?php echo $urlAction === 'dashboard' ? 'active' : ''; ?>">
                                <i class="fa-solid fa-gauge me-2 text-success"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a href="<?php echo URLROOT; ?>/admin/aiAssistant" class="nav-link text-white <?php echo $urlAction === 'aiAssistant' ? 'active text-warning' : ''; ?>">
                                <i class="fa-solid fa-robot me-2 text-warning"></i>AI Assistant Console
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a href="<?php echo URLROOT; ?>/admin/settings" class="nav-link text-white <?php echo $urlAction === 'settings' ? 'active text-info' : ''; ?>">
                                <i class="fa-solid fa-sliders me-2 text-info"></i>API & Settings
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a href="<?php echo URLROOT; ?>/" class="nav-link text-white">
                                <i class="fa-solid fa-house me-2"></i>Landing Page
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo URLROOT; ?>/auth/logout" class="nav-link text-danger">
                                <i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Sign Out
                            </a>
                        </li>
                    </ul>
                </div>
