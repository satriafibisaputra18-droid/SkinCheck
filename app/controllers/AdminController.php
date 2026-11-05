<?php
/**
 * SkinCheck - Admin Controller
 * Manages administrative capabilities: user lists, product catalogs, scan logs, and dashboard metrics.
 */

class AdminController extends Controller {
    private $userModel;
    private $productModel;
    private $scanModel;
    private $aiTokenModel;
    private $settingModel;

    public function __construct() {
        // We load models here
        $this->userModel = $this->model('User');
        $this->productModel = $this->model('Product');
        $this->scanModel = $this->model('Scan');
        $this->aiTokenModel = $this->model('AiTokenUsage');
        $this->settingModel = $this->model('Setting');
    }

    /**
     * Admin Portal Login Page and action.
     */
    public function login() {
        // If already logged in, redirect to admin dashboard
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
            header('Location: ' . URLROOT . '/admin/dashboard');
            exit;
        }

        $data = [
            'title' => 'Admin Portal Login - SkinCheck',
            'email' => '',
            'password' => '',
            'email_err' => '',
            'password_err' => ''
        ];

        // Process admin form submit
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data['email'] = trim($_POST['email'] ?? '');
            $data['password'] = trim($_POST['password'] ?? '');

            // Field validations
            if (empty($data['email'])) {
                $data['email_err'] = 'Please enter administrator email.';
            }
            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter security password.';
            }

            if (empty($data['email_err']) && empty($data['password_err'])) {
                // Check and login administrator
                $loggedInAdmin = $this->userModel->login($data['email'], $data['password']);

                if ($loggedInAdmin && $loggedInAdmin->role === 'admin') {
                    // Set admin session state
                    $_SESSION['user_id'] = $loggedInAdmin->id;
                    $_SESSION['user_name'] = $loggedInAdmin->name;
                    $_SESSION['user_email'] = $loggedInAdmin->email;
                    $_SESSION['user_role'] = $loggedInAdmin->role;

                    header('Location: ' . URLROOT . '/admin/dashboard');
                    exit;
                } else {
                    $data['password_err'] = 'Invalid administrator credentials.';
                }
            }
        }

        $this->view('admin/login', $data);
    }

    /**
     * Admin Dashboard Overview
     */
    public function dashboard() {
        // Enforce administrative access control
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . URLROOT . '/admin/login');
            exit;
        }

        // Fetch administration dataset
        $customersList = $this->userModel->getUsers();
        $productsCatalog = $this->productModel->getProducts();
        $recentScans = $this->scanModel->getRecentScans();
        $tokenMetrics = $this->aiTokenModel->getTokenMetrics();

        // Calculate real metric values from database
        $totalCustomers = count($customersList);
        $totalProducts = count($productsCatalog);
        $scanMetrics = $this->scanModel->getAdminMetrics();
        $totalScans = $scanMetrics ? (int)$scanMetrics->total_scans : 0;
        $averageHealthScore = $scanMetrics ? (float)$scanMetrics->avg_score : 0;

        $data = [
            'title' => 'Admin Dashboard - SkinCheck',
            'customers' => $customersList,
            'products' => $productsCatalog,
            'scans' => $recentScans,
            'tokenMetrics' => $tokenMetrics,
            'metrics' => (object)[
                'total_customers' => $totalCustomers,
                'total_products' => $totalProducts,
                'total_scans' => $totalScans,
                'avg_score' => $averageHealthScore
            ]
        ];

        $this->view('admin/dashboard', $data);
    }

    /**
     * Dedicated Executive AI Assistant & Analytics Console
     */
    public function aiAssistant() {
        // Enforce administrative access control
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . URLROOT . '/admin/login');
            exit;
        }

        // Fetch administration dataset
        $customersList = $this->userModel->getUsers();
        $productsCatalog = $this->productModel->getProducts();
        $recentScans = $this->scanModel->getRecentScans();
        $tokenMetrics = $this->aiTokenModel->getTokenMetrics();

        $totalCustomers = count($customersList);
        $totalProducts = count($productsCatalog);
        $scanMetrics = $this->scanModel->getAdminMetrics();
        $totalScans = $scanMetrics ? (int)$scanMetrics->total_scans : 0;
        $averageHealthScore = $scanMetrics ? (float)$scanMetrics->avg_score : 0;

        $data = [
            'title' => 'AI Executive Assistant - SkinCheck Admin',
            'customers' => $customersList,
            'products' => $productsCatalog,
            'scans' => $recentScans,
            'tokenMetrics' => $tokenMetrics,
            'metrics' => (object)[
                'total_customers' => $totalCustomers,
                'total_products' => $totalProducts,
                'total_scans' => $totalScans,
                'avg_score' => $averageHealthScore
            ]
        ];

        $this->view('admin/ai_assistant', $data);
    }

    /**
     * Application & External API Keys Configuration Management Console
     * Route: GET /admin/settings and POST /admin/settings
     */
    public function settings() {
        // Enforce administrative access control
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . URLROOT . '/admin/login');
            exit;
        }

        // Process Settings Update Form
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_DEFAULT);
            $submitted = $_POST['settings'] ?? [];

            if (!empty($submitted) && is_array($submitted)) {
                $saved = $this->settingModel->updateMultiple($submitted);
                if ($saved) {
                    $_SESSION['admin_flash_success'] = 'Konfigurasi & API Key berhasil diperbarui dan diterapkan ke seluruh sistem!';
                } else {
                    $_SESSION['admin_flash_error'] = 'Gagal menyimpan pengaturan ke database. Silakan coba kembali.';
                }
            } else {
                $_SESSION['admin_flash_error'] = 'Tidak ada data pengaturan yang dikirimkan.';
            }

            header('Location: ' . URLROOT . '/admin/settings');
            exit;
        }

        // Fetch Grouped Settings
        $groupedSettings = $this->settingModel->getAllGrouped();

        $data = [
            'title' => 'Pengaturan & API Key - SkinCheck Admin',
            'groupedSettings' => $groupedSettings
        ];

        $this->view('admin/settings', $data);
    }

    /**
     * Live AJAX Test for API Keys (Telegram, Resend, Gemini)
     * Route: POST /admin/testApiKey
     */
    public function testApiKey() {
        header('Content-Type: application/json; charset=UTF-8');

        // Enforce administrative access control
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Akses ditolak. Sesi administrator diperlukan.']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Metode request tidak diizinkan.']);
            exit;
        }

        $service = trim($_POST['service'] ?? '');
        $key = trim($_POST['key'] ?? '');
        $extra = trim($_POST['extra'] ?? '');

        switch ($service) {
            case 'telegram':
                $res = $this->settingModel->testTelegram($key);
                break;
            case 'resend':
                $res = $this->settingModel->testResend($key);
                break;
            case 'gemini':
                $res = $this->settingModel->testGemini($key, $extra ?: 'gemini-2.5-flash');
                break;
            default:
                $res = ['success' => false, 'message' => 'Layanan API tidak dikenali.'];
        }

        echo json_encode($res);
        exit;
    }
}

