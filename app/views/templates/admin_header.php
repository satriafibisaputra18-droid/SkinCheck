<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Admin Dashboard - SkinCheck'; ?></title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Custom Style Sheet -->
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/style.css?v=<?php echo time(); ?>">
    
    <!-- Chatbot Stylesheet -->
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/chatbot.css?v=<?php echo time(); ?>">

    <!-- Clean Admin Layout Styles -->
    <style>
    html, body {
        height: 100%;
    }

    .sc-admin-layout {
        display: flex;
        width: 100%;
        height: 100vh;
        background-color: #f8fafc;
        position: relative;
        overflow: hidden;
    }

    .sc-admin-sidebar {
        width: 250px;
        min-width: 250px;
        background: #ffffff;
        border-right: 1px solid #e2e8f0;
        height: 100vh;
        max-height: 100vh;
        display: flex;
        flex-direction: column;
        z-index: 1040;
        position: relative;
        overflow-y: auto;
        overflow-x: hidden;
        flex-shrink: 0;
    }

    .sc-admin-main {
        flex: 1 1 0%;
        min-width: 0;
        width: 100%;
        height: 100vh;
        max-height: 100vh;
        overflow-y: auto;
        overflow-x: hidden;
        background-color: #f8fafc;
        scroll-behavior: smooth;
    }

    /* Elegant subtle scrollbars for independent panels */
    .sc-admin-sidebar::-webkit-scrollbar {
        width: 4px;
    }
    .sc-admin-sidebar::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 4px;
    }
    .sc-admin-sidebar::-webkit-scrollbar-thumb:hover {
        background: #cbd5e1;
    }

    .sc-admin-main::-webkit-scrollbar {
        width: 6px;
    }
    .sc-admin-main::-webkit-scrollbar-track {
        background: transparent;
    }
    .sc-admin-main::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .sc-admin-main::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .sc-admin-sidebar .nav-link {
        color: #475569 !important;
        border-radius: 8px;
        padding: 0.65rem 0.95rem;
        font-weight: 500;
        font-size: 0.88rem;
        transition: all 0.15s ease;
        display: flex;
        align-items: center;
    }

    .sc-admin-sidebar .nav-link:hover {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
    }

    .sc-admin-sidebar .nav-link.active {
        background: #0f766e !important;
        color: #ffffff !important;
        font-weight: 600;
        box-shadow: 0 2px 6px rgba(15, 118, 110, 0.2);
    }

    .sc-admin-sidebar .nav-link.text-danger:hover {
        background-color: #fef2f2 !important;
        color: #dc2626 !important;
    }

    .sc-sidebar-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(15, 23, 42, 0.4);
        backdrop-filter: blur(2px);
        z-index: 1035;
    }

    @media (max-width: 991.98px) {
        .sc-admin-layout {
            height: auto;
            min-height: 100vh;
            overflow: visible;
        }
        .sc-admin-main {
            height: auto;
            max-height: none;
            overflow: visible;
        }
        .sc-admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            height: 100vh;
            margin-left: -250px;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.1);
        }
        .sc-admin-sidebar.show-mobile {
            margin-left: 0;
        }
        .sc-sidebar-backdrop.show {
            display: block;
        }
    }
    </style>
</head>
<body class="bg-light">

<!-- Global SVG Filter for Liquid Glass Refraction Effect -->
<svg class="d-none" aria-hidden="true" style="position: absolute; width: 0; height: 0; overflow: hidden;" version="1.1" xmlns="http://www.w3.org/2000/svg">
    <defs>
        <filter id="sc-liquid-glass" x="-20%" y="-20%" width="140%" height="140%" color-interpolation-filters="sRGB">
            <feTurbulence type="fractalNoise" baseFrequency="0.04 0.04" numOctaves="1" seed="2" result="turbulence"/>
            <feGaussianBlur in="turbulence" stdDeviation="2.5" result="blurredNoise"/>
            <feDisplacementMap in="SourceGraphic" in2="blurredNoise" scale="22" xChannelSelector="R" yChannelSelector="B" result="displaced"/>
            <feGaussianBlur in="displaced" stdDeviation="1.5" result="finalBlur"/>
            <feComposite in="finalBlur" in2="finalBlur" operator="over"/>
        </filter>
    </defs>
</svg>

<?php
// Determine active nav link
$currentUrl = $_GET['url'] ?? '';
$urlParts   = explode('/', trim($currentUrl, '/'));
$urlAction  = $urlParts[1] ?? 'dashboard';
?>

<!-- Responsive Admin Layout Container -->
<div class="sc-admin-layout">
    
    <!-- Left Sidebar Panel -->
    <aside id="adminSidebar" class="sc-admin-sidebar">
        <!-- Sidebar Brand -->
        <div class="d-flex align-items-center p-3 px-4 border-bottom">
            <a href="<?php echo URLROOT; ?>/admin/dashboard" class="d-flex align-items-center text-dark text-decoration-none">
                <i class="fa-solid fa-microscope me-2 fs-5 text-primary"></i>
                <span class="fs-5 fw-bold tracking-tight text-dark" style="font-family:'Outfit',sans-serif;">Skin<span class="text-primary">Check</span></span>
            </a>
        </div>
        
        <!-- Navigation Menu -->
        <div class="p-3 d-flex flex-column justify-content-between flex-grow-1">
            <div>
                <div class="text-muted small text-uppercase fw-semibold mb-2 px-3" style="font-size:0.68rem; letter-spacing:0.06em;">Menu Utama</div>
                <ul class="nav flex-column mb-4 gap-1">
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/admin/dashboard" class="nav-link <?php echo $urlAction === 'dashboard' ? 'active' : ''; ?>">
                            <i class="fa-solid fa-gauge me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/admin/aiAssistant" class="nav-link <?php echo $urlAction === 'aiAssistant' ? 'active' : ''; ?>">
                            <i class="fa-solid fa-robot me-2"></i>Asisten AI
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/admin/settings" class="nav-link <?php echo $urlAction === 'settings' ? 'active' : ''; ?>">
                            <i class="fa-solid fa-sliders me-2"></i>Pengaturan Sistem
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/" class="nav-link" target="_blank">
                            <i class="fa-solid fa-arrow-up-right-from-square me-2"></i>Lihat Website
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <div class="text-muted small text-uppercase fw-semibold mb-2 px-3" style="font-size:0.68rem; letter-spacing:0.06em;">Akun & Sesi</div>
                <ul class="nav flex-column gap-1">
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/auth/logout" class="nav-link text-danger">
                            <i class="fa-solid fa-arrow-right-from-bracket me-2 text-danger"></i>Keluar
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </aside>

    <!-- Mobile Backdrop -->
    <div id="adminSidebarBackdrop" class="sc-sidebar-backdrop" onclick="toggleAdminSidebar(false)"></div>

    <script>
    function toggleAdminSidebar(show) {
        const sb = document.getElementById('adminSidebar');
        const bd = document.getElementById('adminSidebarBackdrop');
        if (!sb) return;
        if (show === undefined) {
            sb.classList.toggle('show-mobile');
            if (bd) bd.classList.toggle('show');
        } else if (show) {
            sb.classList.add('show-mobile');
            if (bd) bd.classList.add('show');
        } else {
            sb.classList.remove('show-mobile');
            if (bd) bd.classList.remove('show');
        }
    }
    </script>

    <!-- Main Content Area -->
    <main id="adminMainArea" class="sc-admin-main">
        <div class="px-3 px-md-4 py-4 min-vh-100 d-flex flex-column">
            
            <!-- Admin Top Header Navbar -->
            <header class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-light border d-lg-none" onclick="toggleAdminSidebar(true)" title="Buka Menu" aria-label="Buka Menu">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div>
                        <h1 class="h5 fw-bold mb-0 text-dark"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Administrator'); ?></h1>
                        <span class="text-muted small">Panel Administrasi Sistem</span>
                    </div>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-success-subtle text-success py-2 px-3 border border-success border-opacity-10 rounded-pill small">
                        <i class="fa-solid fa-shield-halved me-1"></i>Administrator
                    </span>
                </div>
            </header>
