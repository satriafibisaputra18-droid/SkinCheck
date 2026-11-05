<?php
/**
 * SkinCheck - Chatbot Controller
 * Acts as an application bridge between client and the Node.js Gemini Chatbot service.
 */

class ChatbotController extends Controller {

    private const NODE_ENDPOINT = 'http://127.0.0.1:5000/api/chat';
    private const GEMINI_API_KEY = '';

    public function __construct() {
        // Allow public and authenticated access
    }

    /**
     * Chat API Endpoint: POST /chatbot/chat
     */
    public function chat() {
        // Set JSON headers
        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Metode request tidak diizinkan. Gunakan POST.']);
            exit;
        }

        $inputRaw = file_get_contents('php://input');
        $data = json_decode($inputRaw, true);

        if (!$data || empty($data['message'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Pesan (message) wajib diisi.']);
            exit;
        }

        $message = trim($data['message']);
        $history = $data['history'] ?? [];
        $user = $data['user'] ?? null;
        if (!$user && isset($_SESSION['user_id'])) {
            $user = [
                'id' => (int)$_SESSION['user_id'],
                'name' => $_SESSION['user_name'] ?? 'User',
                'email' => $_SESSION['user_email'] ?? '',
                'role' => $_SESSION['user_role'] ?? 'customer'
            ];
        } else if ($user && !empty($user['id'])) {
            if (empty($user['email']) && isset($_SESSION['user_email'])) {
                $user['email'] = $_SESSION['user_email'];
            }
            if (empty($user['role']) && isset($_SESSION['user_role'])) {
                $user['role'] = $_SESSION['user_role'];
            }
        }

        // 1. First attempt: Forward to Node.js Chatbot Server
        $nodeResponse = $this->callNodeServer([
            'message' => $message,
            'history' => $history,
            'user' => $user
        ]);

        if ($nodeResponse !== null) {
            echo json_encode($nodeResponse);
            exit;
        }

        // 2. Secondary fallback: Direct Gemini API if Node.js server is momentarily restarting
        $fallbackResponse = $this->callDirectGemini($message, $history, $user);
        echo json_encode($fallbackResponse);
        exit;
    }

    /**
     * Send request to local Node.js server
     */
    private function callNodeServer(array $payload): ?array {
        $ch = curl_init(self::NODE_ENDPOINT);
        $jsonData = json_encode($payload);

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $jsonData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonData)
            ],
            CURLOPT_TIMEOUT => 25,
            CURLOPT_CONNECTTIMEOUT => 3
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($response !== false && $httpCode >= 200 && $httpCode < 300) {
            $parsed = json_decode($response, true);
            if ($parsed && isset($parsed['reply'])) {
                $parsed['serverType'] = 'Node.js Chatbot Server (Active)';
                return $parsed;
            }
        }

        return null;
    }

    /**
     * Retrieve live database context to inject into Gemini prompt
     */
    private function getLiveDatabaseContext(?array $user): array {
        $db = new Database();
        $isLoggedIn = !empty($user['id']);
        $userRole = $user['role'] ?? ($isLoggedIn ? 'customer' : 'guest');
        $userName = $user['name'] ?? ($userRole === 'admin' ? 'Admin' : 'Pengguna');
        $userEmail = $user['email'] ?? '';

        if ($isLoggedIn && $db->isConnected()) {
            $db->query("SELECT name, email, role FROM users WHERE id = :id LIMIT 1");
            $db->bind(':id', (int)$user['id']);
            $uRow = $db->single();
            if ($uRow) {
                $userName = $uRow->name ?: $userName;
                $userEmail = $uRow->email ?: $userEmail;
                $userRole = $uRow->role ?: $userRole;
            }
        }
        $isAdmin = ($isLoggedIn && $userRole === 'admin');

        $ctx = [
            'isLoggedIn' => $isLoggedIn,
            'isAdmin' => $isAdmin,
            'userName' => $userName,
            'userEmail' => $userEmail,
            'userRole' => $userRole,
            'totalUsers' => 0,
            'totalCustomers' => 0,
            'totalAdmins' => 0,
            'totalScans' => 0,
            'avgScore' => 0,
            'productsCount' => 0,
            'productSummary' => '',
            'userSpecificScan' => ''
        ];

        if (!$db->isConnected()) {
            return $ctx;
        }

        // 1. Products catalog
        $db->query("SELECT id, brand, name, category, skin_concern, skin_type, price, ingredients, description, usage_guidance 
                    FROM products 
                    WHERE status = 'active' 
                    ORDER BY FIELD(skin_concern, 'Acne', 'Redness', 'Hyperpigmentation', 'Texture', 'Dryness', 'Sun Protection'), category ASC, id ASC");
        $prods = $db->resultSet();
        $ctx['productsCount'] = count($prods);

        $grouped = [];
        foreach ($prods as $p) {
            $c = $p->skin_concern ?: 'Lainnya';
            $grouped[$c][] = "- [{$p->category}] {$p->brand} - {$p->name} (Rp " . number_format($p->price, 0, ',', '.') . ") | Tipe Kulit: {$p->skin_type} | Kandungan Utama: {$p->ingredients} | Manfaat: {$p->description} | Panduan Pakai: {$p->usage_guidance}";
        }

        $prodLines = [];
        foreach ($grouped as $concern => $items) {
            $prodLines[] = "\n[Kategori Masalah Kulit: " . strtoupper($concern) . "]\n" . implode("\n", $items);
        }
        $ctx['productSummary'] = implode("\n", $prodLines);

        if ($isAdmin) {
            // Count users by role
            $db->query("SELECT 
                COUNT(*) as totalUsers,
                SUM(CASE WHEN role = 'customer' THEN 1 ELSE 0 END) as totalCustomers,
                SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as totalAdmins
                FROM users");
            $uStat = $db->single();
            $ctx['totalUsers'] = $uStat ? (int)$uStat->totalUsers : 0;
            $ctx['totalCustomers'] = $uStat ? (int)$uStat->totalCustomers : 0;
            $ctx['totalAdmins'] = $uStat ? (int)$uStat->totalAdmins : 0;

            // Count scans
            $db->query("SELECT 
                COUNT(s.id) as totalScans,
                ROUND(AVG(a.overall_score), 1) as avgScore
                FROM skin_scans s
                LEFT JOIN analysis_results a ON a.skin_scan_id = s.id");
            $sStat = $db->single();
            $ctx['totalScans'] = $sStat ? (int)$sStat->totalScans : 0;
            $ctx['avgScore'] = $sStat ? (float)$sStat->avgScore : 0;
        } elseif ($isLoggedIn) {
            $db->query("SELECT s.id, s.created_at, a.overall_score, a.acne_score, a.redness_score, sr.coverage_score, sr.coverage_status
                FROM skin_scans s
                LEFT JOIN analysis_results a ON a.skin_scan_id = s.id
                LEFT JOIN sunscreen_results sr ON sr.skin_scan_id = s.id
                WHERE s.user_id = :uid
                ORDER BY s.created_at DESC LIMIT 1");
            $db->bind(':uid', (int)$user['id']);
            $scan = $db->single();
            if ($scan) {
                $ctx['userSpecificScan'] = "HASIL SCAN TERAKHIR (Tanggal: {$scan->created_at}): Skor Kulit: {$scan->overall_score}/100, Jerawat: {$scan->acne_score}, Kemerahan: {$scan->redness_score}, Status Sunscreen: {$scan->coverage_status} ({$scan->coverage_score}%).";
            }
        }

        return $ctx;
    }

    /**
     * Fallback directly to Gemini if Node.js server is not reachable
     */
    private function callDirectGemini(string $message, array $history, ?array $user = null): array {
        $primaryModel = Setting::getVal('gemini_primary_model', 'gemini-flash-lite-latest');
        $fallbackStr = Setting::getVal('gemini_fallback_models', 'gemini-3.5-flash-lite,gemini-3.8-flash,gemini-3.7-flash,gemini-3.5-flash');
        $candidates = array_unique(array_filter(array_merge([$primaryModel], array_map('trim', explode(',', $fallbackStr)))));
        $apiKey = Setting::getVal('gemini_api_key', self::GEMINI_API_KEY);
        $temperature = (float)Setting::getVal('gemini_temperature', 0.7);
        
        $contents = [];
        foreach ($history as $h) {
            if (!empty($h['text'])) {
                $role = ($h['role'] === 'model' || $h['role'] === 'assistant') ? 'model' : 'user';
                $contents[] = [
                    'role' => $role,
                    'parts' => [['text' => $h['text']]]
                ];
            }
        }
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $message]]
        ];

        // Fetch real database context
        $ctx = $this->getLiveDatabaseContext($user);

        $sysPrompt = "Anda adalah SkinCheck AI Assistant resmi pada sistem SkinCheck. RUANG LINGKUP EKSKLUSIF: Anda HANYA diperbolehkan menjawab seputar identitas akun pengguna yang sedang aktif (nama akun, email, role, status scan), kesehatan kulit, jerawat, flek, tekstur, skin barrier, skincare routine, perlindungan tabir surya/sunscreen, dan fitur platform SkinCheck.\n\n";

        $sysPrompt .= "ATURAN GAYA KOMUNIKASI (DILARANG NYEPAM SALAM/HALO):\n";
        $sysPrompt .= "1. JANGAN PERNAH mengawali jawaban dengan sapaan berulang seperti 'Halo', 'Halo Admin', 'Hai', 'Halo [Nama]' di setiap kali pengguna bertanya!\n";
        $sysPrompt .= "2. LANGSUNG JAWAB to-the-point ke inti pertanyaan secara mengalir, luwes, dan alami tanpa basa-basi salam yang diulang-ulang.\n";
        $sysPrompt .= "3. HANYA ucapkan salam jika pengguna secara khusus mengirim pesan yang HANYA berisi salam pembuka murni (seperti 'Halo', 'Hai', 'Selamat pagi'). Jika pengguna sedang bertanya informasi atau konsultasi, LANGSUNG jawab penjelasannya tanpa awalan 'Halo'!\n";
        $sysPrompt .= "4. PENTING: Larangan salam berulang ini BUKAN berarti dilarang menyebut nama akun pengguna saat pengguna secara sengaja menanyakan identitasnya (seperti 'siapa saya?'). Ketika ditanya identitas, sebutkan nama akunnya secara lugas tanpa harus menambahkan sapaan basa-basi di depannya.\n\n";

        if ($ctx['isAdmin']) {
            $sysPrompt .= "STATUS OTORISASI: ADMINISTRATOR SISTEM SKINCHECK (Nama: {$ctx['userName']}, Email: {$ctx['userEmail']}, Role: Admin Portal).\n";
            $sysPrompt .= "ATURAN PERTANYAAN IDENTITAS UNTUK ADMINISTRATOR:\n";
            $sysPrompt .= "- Jika Administrator bertanya identitas dirinya (seperti 'siapa saya?', 'nama saya siapa?', 'akun siapa ini?'): JAWAB LANGSUNG bahwa mereka terautentikasi sebagai Administrator Sistem SkinCheck dengan nama akun {$ctx['userName']} (Email: {$ctx['userEmail']}).\n\n";
            $sysPrompt .= "FAKTA RESMI DATABASE (DATA REAL-TIME UNTUK ADMIN - HARUS DIGUNAKAN SECARA PRESISI):\n";
            $sysPrompt .= "- Total Pelanggan / Customer Terdaftar: {$ctx['totalCustomers']} orang\n";
            $sysPrompt .= "- Total Administrator Sistem: {$ctx['totalAdmins']} orang\n";
            $sysPrompt .= "- Total Keseluruhan Pengguna: {$ctx['totalUsers']} akun\n";
            $sysPrompt .= "- Total Pemindaian Kulit: {$ctx['totalScans']} kali pemindaian\n";
            $sysPrompt .= "- Rata-rata Skor Kesehatan Kulit: {$ctx['avgScore']}/100\n";
            $sysPrompt .= "- Jumlah Produk Skincare Aktif: {$ctx['productsCount']} produk\n\n";
            $sysPrompt .= "ATURAN MENJAWAB UNTUK ADMINISTRATOR:\n";
            $sysPrompt .= "1. Jika Administrator bertanya berapa banyak customer yang daftar: JAWAB LANGSUNG to-the-point bahwa total customer terdaftar di database SkinCheck saat ini adalah {$ctx['totalCustomers']} orang.\n";
            $sysPrompt .= "2. DILARANG KERAS MENGARANG ATAU MENYEBUT ANGKA LAIN (seperti ribuan, 10k, atau 15k). Selalu gunakan angka riil {$ctx['totalCustomers']} orang customer!\n";
        } elseif ($ctx['isLoggedIn']) {
            $sysPrompt .= "STATUS OTORISASI: PENGGUNA TERDAFTAR / SUDAH LOGIN (Nama: {$ctx['userName']}, Email: {$ctx['userEmail']}, Role: Customer).\n\n";
            $sysPrompt .= "ATURAN PERTANYAAN IDENTITAS DIRI & AKUN CUSTOMER (SANGAT PENTING & PRIORITAS TINGGI):\n";
            $sysPrompt .= "- Ketika customer bertanya tentang identitasnya, namanya, atau akunnya (seperti: 'siapa saya?', 'nama saya siapa?', 'siapa nama saya?', 'akun siapa ini?', 'siapa saya di aplikasi ini?', 'saya siapa?', 'kamu kenal saya?', 'siapa yang login?', 'nama akun saya apa?', 'akun saya apa?'):\n";
            $sysPrompt .= "  * ANDA WAJIB MENJAWAB SECARA LANGSUNG, SPESIFIK, DAN JELAS dengan menyebutkan nama akun customer yang sedang login: \"{$ctx['userName']}\".\n";
            $sysPrompt .= "  * Sebutkan juga alamat email terdaftar ({$ctx['userEmail']}) dan konfirmasikan status perannya sebagai Pelanggan / Customer aktif SkinCheck.\n";
            $sysPrompt .= "  * DILARANG KERAS menjawab secara ambigu, mengabaikan nama pengguna, atau hanya berkata 'Anda adalah customer' tanpa menyebutkan nama spesifik \"{$ctx['userName']}\"!\n\n";
            $sysPrompt .= "ATURAN AKSES DATABASE UNTUK CUSTOMER:\n";
            $sysPrompt .= "1. Jika customer bertanya berapa banyak customer yang daftar, berapa banyak pengguna, atau data platform lainnya: Tolak langsung secara sopan dan jelaskan bahwa data statistik pendaftaran dan platform bersifat rahasia internal administrator, dan customer hanya berhak mengakses data hasil scan miliknya sendiri. DILARANG KERAS MENYEBUTKAN ANGKA APAPUN ATAU MENGARANG 15K!\n";
            $sysPrompt .= "2. Jika ditanya apakah bisa lihat database: 'Bisa, namun demi privasi dan keamanan data, saya hanya dapat mengakses dan melihat riwayat pemindaian kulit Anda sendiri di SkinCheck.'\n";
            if (!empty($ctx['userSpecificScan'])) {
                $sysPrompt .= "DATA SCAN PRIBADI {$ctx['userName']}: {$ctx['userSpecificScan']}\n";
            }
        } else {
            $sysPrompt .= "STATUS OTORISASI: PENGGUNA TAMU / BELUM LOGIN.\n";
            $sysPrompt .= "ATURAN PERTANYAAN IDENTITAS UNTUK PENGUNJUNG TAMU:\n";
            $sysPrompt .= "- Jika pengunjung bertanya 'siapa saya?': Jelaskan dengan ramah bahwa saat ini mereka mengakses sebagai Pengunjung Tamu (Guest) dan belum login, sehingga sistem belum mengenali nama akunnya. Silakan login atau mendaftar terlebih dahulu. 😊\n\n";
            $sysPrompt .= "ATURAN AKSES DATABASE UNTUK PENGUNJUNG TAMU:\n";
            $sysPrompt .= "1. DILARANG KERAS menampilkan database, jumlah pengguna, atau data internal apapun.\n";
            $sysPrompt .= "2. Jika ditanya ada berapa customer yang daftar atau apakah bisa lihat database, tolak langsung dengan ramah: 'Mohon maaf, demi menjaga privasi dan keamanan data pengguna, akses ke data sistem dan statistik pendaftaran dibatasi untuk publik. Anda saat ini belum login. Silakan login terlebih dahulu untuk mengakses riwayat pemindaian kulit Anda sendiri. 😊'\n";
            $sysPrompt .= "3. DILARANG KERAS MENYEBUTKAN ANGKA PERKIRAAN ATAU MENGARANG ANGKA SEPERTI 15k!\n";
        }

        $sysPrompt .= "\nKATALOG PRODUK SKINCARE RESMI SKINCHECK (DATA REAL-TIME DATABASE):\n";
        $sysPrompt .= !empty($ctx['productSummary']) ? $ctx['productSummary'] : "Katalog produk aktif tersedia di sistem.\n";

        $sysPrompt .= "\n\nPANDUAN & ATURAN REKOMENDASI PRODUK SKINCARE:\n";
        $sysPrompt .= "1. Ketika pengguna bertanya tentang rekomendasi produk skincare, solusi permasalahan kulit (jerawat, bruntusan, kemerahan, iritasi, skin barrier rusak, noda hitam/flek, tekstur kasar, pori-pori besar, kulit kering, dehidrasi), atau perlindungan sunscreen:\n";
        $sysPrompt .= "   - WAJIB PRIORITASKAN merekomendasikan produk-produk yang ADA DI DALAM KATALOG RESMI SKINCHECK di atas!\n";
        $sysPrompt .= "   - Jelaskan dengan rapi dan terstruktur: Nama Produk & Brand resmi, Kisaran Harga resmi (Rp), Bahan Aktif Utama & Alasan kenapa produk tersebut cocok untuk kondisi kulitnya (misal Salicylic Acid untuk jerawat, Centella/Ceramide untuk kemerahan & skin barrier, Niacinamide/Arbutin untuk flek hitam), serta Panduan Waktu & Cara Pakai (pagi/malam, urutan langkah).\n";
        $sysPrompt .= "   - Susun urutan rekomendasi skincare secara logis dan teratur: Pembersih (Cleanser) -> Toner -> Serum -> Pelembap (Moisturizer) -> Tabir Surya (Sunscreen untuk pagi hari).\n";
        $sysPrompt .= "   - DILARANG MENGARANG produk fiktif atau menyarankan merek luar jika di katalog SkinCheck sudah tersedia produk yang sesuai!\n";
        $sysPrompt .= "2. Jika pengguna memiliki riwayat hasil scan (untuk pengguna customer yang sudah login dengan data scan):\n";
        $sysPrompt .= "   - Kaitkan rekomendasi produk secara personal dengan skor indikator hasil scan mereka.\n";
        $sysPrompt .= "3. Berikan edukasi pendukung seperti anjuran patch test bagi pemilik kulit sensitif dan disclaimer medis bila terdapat peradangan hebat.\n\n";

        foreach ($candidates as $model) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $apiKey;
            $body = json_encode([
                'contents' => $contents,
                'systemInstruction' => ['parts' => [['text' => $sysPrompt]]],
                'generationConfig' => ['temperature' => $temperature, 'maxOutputTokens' => 2048]
            ]);

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_TIMEOUT => 20
            ]);

            $res = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($res !== false && $code === 200) {
                $data = json_decode($res, true);
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                if ($reply) {
                    $usage = $data['usageMetadata'] ?? [];
                    $promptTok = (int)($usage['promptTokenCount'] ?? ceil(strlen($body) / 4));
                    $compTok = (int)($usage['candidatesTokenCount'] ?? ceil(strlen($reply) / 4));
                    $totTok = (int)($usage['totalTokenCount'] ?? ($promptTok + $compTok));

                    try {
                        $tokenModel = $this->model('AiTokenUsage');
                        $tokenModel->recordUsage([
                            'user_id' => $user['id'] ?? null,
                            'user_role' => $user['role'] ?? 'guest',
                            'feature' => 'chat',
                            'model' => $model,
                            'prompt_tokens' => $promptTok,
                            'completion_tokens' => $compTok,
                            'total_tokens' => $totTok,
                            'duration_ms' => 0,
                            'status' => 'success'
                        ]);
                    } catch (Exception $e) {}

                    return [
                        'success' => true,
                        'reply' => trim($reply),
                        'modelUsed' => $model,
                        'serverType' => 'Gemini Direct Fallback (Real MySQL Data Connected)',
                        'tokens' => [
                            'prompt' => $promptTok,
                            'completion' => $compTok,
                            'total' => $totTok
                        ]
                    ];
                }
            }
        }

        return [
            'success' => false,
            'error' => 'Gagal menghubungi AI Gemini. Pastikan server Node.js aktif atau periksa koneksi internet.'
        ];
    }
}
