<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $description ?? 'SkinCheck - Advanced AI Skin Analysis & Sunscreen Tracker'; ?>">
    <title><?php echo $title ?? 'SkinCheck'; ?></title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <!-- FontAwesome for Premium Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Custom Style Sheet with Cache Buster -->
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/style.css?v=<?php echo time(); ?>">
</head>
<body class="sc-dark-theme">

    <!-- Responsive Public Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top sc-public-navbar py-3">
        <div class="container d-flex align-items-center justify-content-between">
            <!-- Left Balance Spacer on desktop to ensure glass capsule stays dead-center -->
            <div class="d-none d-lg-block" style="width: 140px;"></div>
            
            <button class="navbar-toggler border-0 shadow-none p-1" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa-solid fa-bars text-white fs-5"></i>
            </button>
            
            <div class="collapse navbar-collapse justify-content-center" id="mainNavbar">
                <!-- Centered Glass Panel Nav Links -->
                <ul class="navbar-nav sc-glass-nav-container mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link sc-glass-nav-link <?php echo ($_GET['url'] ?? '') === '' || ($_GET['url'] ?? '') === 'home' ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/#hero">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link sc-glass-nav-link" href="<?php echo URLROOT; ?>/#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link sc-glass-nav-link" href="<?php echo URLROOT; ?>/#technology">Technology</a>
                    </li>
                    <?php
                    $dashUrl = URLROOT . '/customer/dashboard';
                    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
                        $dashUrl = URLROOT . '/admin/dashboard';
                    }
                    ?>
                    <li class="nav-item">
                        <a class="nav-link sc-glass-nav-link <?php echo (strpos(($_GET['url'] ?? ''), 'customer') !== false || strpos(($_GET['url'] ?? ''), 'admin') !== false) ? 'active' : ''; ?>" href="<?php echo $dashUrl; ?>">Dashboard</a>
                    </li>
                </ul>
            </div>
            
            <div class="d-flex align-items-center justify-content-end gap-2" style="min-width: 140px;">
                    <?php if (isset($_SESSION['user_id'])) : ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-primary rounded-pill dropdown-toggle d-flex align-items-center gap-2 py-1.5 px-3 small" type="button" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="sc-avatar overflow-hidden position-relative" style="width:26px;height:26px;font-size:0.75rem;">
                                    <?php if (!empty($_SESSION['user_avatar']) && file_exists(APPROOT . '/../public/' . $_SESSION['user_avatar'])) : ?>
                                        <img src="<?php echo URLROOT; ?>/public/<?php echo htmlspecialchars($_SESSION['user_avatar']); ?>?v=<?php echo time(); ?>" alt="Avatar" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                                    <?php else : ?>
                                        <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                                    <?php endif; ?>
                                </div>
                                <span class="fw-semibold"><?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm mt-2 rounded-3" aria-labelledby="userMenuDropdown" style="min-width: 190px;">
                                <li class="px-3 py-2 border-bottom">
                                    <div class="fw-semibold small text-dark"><?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
                                    <small class="text-muted text-truncate d-block" style="font-size:0.72rem;"><?php echo htmlspecialchars($_SESSION['user_email']); ?></small>
                                </li>
                                <?php if ($_SESSION['user_role'] === 'admin') : ?>
                                    <li class="pt-1">
                                        <a class="dropdown-item py-2 small" href="<?php echo URLROOT; ?>/admin/dashboard">
                                            <i class="fa-solid fa-chart-line me-2 text-primary"></i>Admin Panel
                                        </a>
                                    </li>
                                <?php else : ?>
                                    <li class="pt-1">
                                        <a class="dropdown-item py-2 small" href="<?php echo URLROOT; ?>/customer/dashboard">
                                            <i class="fa-solid fa-chart-pie me-2 text-primary"></i>My Dashboard
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2 small" href="<?php echo URLROOT; ?>/customer/profile">
                                            <i class="fa-regular fa-user me-2 text-muted"></i>Profil Saya
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a class="dropdown-item py-2 small text-danger" href="<?php echo URLROOT; ?>/auth/logout">
                                        <i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Sign Out
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else : ?>
                        <a href="<?php echo URLROOT; ?>/auth/login" class="btn btn-sm sc-glass-btn-login rounded-pill px-3 py-1.5 fw-semibold">Masuk</a>
                        <a href="<?php echo URLROOT; ?>/auth/register" class="btn btn-sm btn-primary rounded-pill px-3.5 py-1.5 fw-semibold shadow-xs">Daftar Akun</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <main class="flex-grow-1">
