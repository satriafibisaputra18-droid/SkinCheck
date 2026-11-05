<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Portal Kesehatan Kulit - SkinCheck'); ?></title>

    <!-- Google Fonts: Outfit & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Custom SkinCheck CSS with cache buster -->
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/style.css?v=<?php echo time(); ?>">
</head>
<body class="sc-dashboard-body">

<?php
// Route helper for active link state
$currentUrl = $_GET['url'] ?? '';
$urlParts   = explode('/', trim($currentUrl, '/'));
$urlAction  = $urlParts[1] ?? 'dashboard';
$isScan     = ($urlParts[0] ?? '') === 'scan' || $urlAction === 'scanFace';
$isSun      = ($urlParts[0] ?? '') === 'sunscreen' || $urlAction === 'sunscreenCheck';
$isHist     = $urlAction === 'history' || $urlAction === 'scanDetail';
$isConsult  = $urlAction === 'aiAssistant';
$isProfile  = $urlAction === 'profile';
$isDash     = $urlAction === 'dashboard' || empty($urlAction);
?>

<!-- ── Modern Consumer Navigation Header ──────────────────── -->
<header class="sc-consumer-nav">
    <div class="container-xl">
        
        <!-- Left: Mobile Toggle & Brand Logo -->
        <div class="d-flex align-items-center gap-2 gap-sm-3 sc-nav-left">
            <button class="sc-consumer-mobile-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#scMobileMenu" aria-controls="scMobileMenu" aria-label="Buka menu navigasi">
                <i class="fa-solid fa-bars"></i>
            </button>
            <a href="<?php echo URLROOT; ?>/customer/dashboard" class="sc-consumer-brand">
                <span>Skin<span style="color:#dfc68b;">Check</span></span>
            </a>
        </div>

        <!-- Center: Desktop Navigation Links (Clean Glass Capsule) -->
        <nav class="sc-consumer-links">
            <a href="<?php echo URLROOT; ?>/customer/dashboard" class="sc-consumer-link <?php echo $isDash ? 'active' : ''; ?>">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?php echo URLROOT; ?>/scan/faceScan" class="sc-consumer-link <?php echo $isScan ? 'active' : ''; ?>">
                <i class="fa-solid fa-camera"></i>
                <span>Face Scan</span>
            </a>
            <a href="<?php echo URLROOT; ?>/sunscreen/check" class="sc-consumer-link <?php echo $isSun ? 'active' : ''; ?>">
                <i class="fa-solid fa-sun"></i>
                <span>Sunscreen</span>
            </a>
            <a href="<?php echo URLROOT; ?>/customer/history" class="sc-consumer-link <?php echo $isHist ? 'active' : ''; ?>">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Riwayat</span>
            </a>
            <a href="<?php echo URLROOT; ?>/customer/aiAssistant" class="sc-consumer-link <?php echo $isConsult ? 'active' : ''; ?>">
                <i class="fa-solid fa-comment-medical"></i>
                <span>Konsultasi</span>
            </a>
        </nav>

        <!-- Right: Profile Dropdown (Spacious & Clean) -->
        <div class="sc-consumer-actions">
            <div class="dropdown">
                <button class="btn btn-link p-0 d-flex align-items-center gap-2 text-decoration-none sc-user-profile-btn" id="topbarUserMenu" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="sc-avatar overflow-hidden position-relative">
                        <?php if (!empty($_SESSION['user_avatar']) && file_exists(APPROOT . '/../public/' . $_SESSION['user_avatar'])) : ?>
                            <img src="<?php echo URLROOT; ?>/public/<?php echo htmlspecialchars($_SESSION['user_avatar']); ?>?v=<?php echo time(); ?>" alt="Avatar" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                        <?php else : ?>
                            <?php echo strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)); ?>
                        <?php endif; ?>
                    </div>
                    <div class="d-none d-lg-block text-start pe-1" style="line-height:1.2;">
                        <div class="fw-semibold text-white small"><?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'] ?? 'User')[0]); ?></div>
                        <span class="sc-role-badge">Customer</span>
                    </div>
                    <i class="fa-solid fa-chevron-down small text-white-50 ms-1 d-none d-sm-inline"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-2 rounded-3" aria-labelledby="topbarUserMenu" style="min-width: 210px;">
                    <li class="px-3 py-2 border-bottom" style="border-color: rgba(255,255,255,0.08) !important;">
                        <div class="fw-semibold small text-white"><?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></div>
                        <div class="text-white-50 text-truncate" style="font-size:0.75rem;"><?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?></div>
                    </li>
                    <li class="pt-1">
                        <a class="dropdown-item py-2 small" href="<?php echo URLROOT; ?>/customer/profile">
                            <i class="fa-regular fa-user me-2 opacity-75"></i>Profil Saya
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 small" href="<?php echo URLROOT; ?>/customer/history">
                            <i class="fa-solid fa-clock-rotate-left me-2 opacity-75"></i>Riwayat Pemeriksaan
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <a class="dropdown-item py-2 small text-danger" href="<?php echo URLROOT; ?>/auth/logout">
                            <i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Keluar Akun
                        </a>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</header>

<!-- ── Responsive Offcanvas Mobile Drawer ────────────────── -->
<div class="offcanvas offcanvas-start border-0 sc-mobile-offcanvas" tabindex="-1" id="scMobileMenu" aria-labelledby="scMobileMenuLabel" style="max-width: 290px;">
    <div class="offcanvas-header border-bottom" style="border-color: rgba(255,255,255,0.08) !important;">
        <div class="sc-consumer-brand" id="scMobileMenuLabel">
            <span>Skin<span style="color:#dfc68b;">Check</span></span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body p-3 d-flex flex-column justify-content-between">
        <nav class="d-flex flex-column gap-1">
            <div class="text-uppercase text-white-50 fw-bold px-3 py-1" style="font-size:0.68rem; letter-spacing:0.06em;">Navigasi Layanan</div>
            <a href="<?php echo URLROOT; ?>/customer/dashboard" class="sc-consumer-link <?php echo $isDash ? 'active' : ''; ?>">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?php echo URLROOT; ?>/scan/faceScan" class="sc-consumer-link <?php echo $isScan ? 'active' : ''; ?>">
                <i class="fa-solid fa-camera"></i>
                <span>Face Scan</span>
            </a>
            <a href="<?php echo URLROOT; ?>/sunscreen/check" class="sc-consumer-link <?php echo $isSun ? 'active' : ''; ?>">
                <i class="fa-solid fa-sun"></i>
                <span>Sunscreen</span>
            </a>
            <a href="<?php echo URLROOT; ?>/customer/history" class="sc-consumer-link <?php echo $isHist ? 'active' : ''; ?>">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Riwayat</span>
            </a>
            <a href="<?php echo URLROOT; ?>/customer/aiAssistant" class="sc-consumer-link <?php echo $isConsult ? 'active' : ''; ?>">
                <i class="fa-solid fa-comment-medical"></i>
                <span>Konsultasi</span>
            </a>
            
            <div class="text-uppercase text-white-50 fw-bold px-3 py-1 mt-3" style="font-size:0.68rem; letter-spacing:0.06em;">Pengaturan Akun</div>
            <a href="<?php echo URLROOT; ?>/customer/profile" class="sc-consumer-link <?php echo $isProfile ? 'active' : ''; ?>">
                <i class="fa-regular fa-user"></i>
                <span>Profil Saya</span>
            </a>
        </nav>
        
        <div class="pt-3 border-top" style="border-color: rgba(255,255,255,0.08) !important;">
            <a href="<?php echo URLROOT; ?>/auth/logout" class="sc-consumer-link text-danger w-100">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Keluar Akun</span>
            </a>
        </div>
    </div>
</div>

<!-- ── Consumer Main Content Wrapper ─────────────────────── -->
<main class="sc-consumer-main container-xl">
