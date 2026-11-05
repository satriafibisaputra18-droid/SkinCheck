<?php
/**
 * SkinCheck - Database Migration Script
 * Connects to MySQL, creates the database, schema tables,
 * and seeds initial demo and catalog records.
 */

// Load Configuration
if (file_exists(__DIR__ . '/app/config/config.php')) {
    require_once __DIR__ . '/app/config/config.php';
} else {
    die("Configuration file not found at app/config/config.php.\n");
}

echo "SkinCheck Database Setup & Migration\n";
echo "====================================\n";

$host = DB_HOST;
$user = DB_USER;
$pass = DB_PASS;
$dbname = DB_NAME;

try {
    // 1. Establish initial connection to MySQL server
    $dsn = "mysql:host={$host};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ];
    
    echo "[1/4] Connecting to MySQL host '{$host}'... ";
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "CONNECTED\n";
    
    // 2. Create the target schema database
    echo "[2/4] Initializing database '{$dbname}'... ";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$dbname}`");
    echo "OK\n";
    
    // 3. Create tables in proper sequence
    echo "[3/4] Structuring database tables:\n";
    
    // users table
    echo "      - Creating table 'users'... ";
    $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `email` VARCHAR(255) NOT NULL UNIQUE,
        `password_hash` VARCHAR(255) NOT NULL,
        `role` ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
        `status` VARCHAR(50) NOT NULL DEFAULT 'active',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    echo "OK\n";

    // skin_scans table
    echo "      - Creating table 'skin_scans'... ";
    $pdo->exec("CREATE TABLE IF NOT EXISTS `skin_scans` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `image_path` VARCHAR(255) NOT NULL,
        `status` VARCHAR(50) NOT NULL DEFAULT 'completed',
        `image_quality_score` DECIMAL(5,2) NOT NULL DEFAULT 100.00,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB");
    echo "OK\n";

    // analysis_results table
    echo "      - Creating table 'analysis_results'... ";
    $pdo->exec("CREATE TABLE IF NOT EXISTS `analysis_results` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `skin_scan_id` INT NOT NULL,
        `acne_score` INT NOT NULL,
        `redness_score` INT NOT NULL,
        `texture_score` INT NOT NULL,
        `hyperpigmentation_score` INT NOT NULL,
        `overall_score` INT NOT NULL,
        `ai_response` TEXT,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`skin_scan_id`) REFERENCES `skin_scans` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB");
    echo "OK\n";

    // sunscreen_results table
    echo "      - Creating table 'sunscreen_results'... ";
    $pdo->exec("CREATE TABLE IF NOT EXISTS `sunscreen_results` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `skin_scan_id` INT NOT NULL,
        `coverage_score` INT NOT NULL,
        `coverage_status` VARCHAR(50) NOT NULL,
        `heatmap_path` VARCHAR(255) NOT NULL,
        `low_coverage_areas` TEXT,
        `analysis_method` VARCHAR(100) NOT NULL,
        `hardware_type` VARCHAR(100) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`skin_scan_id`) REFERENCES `skin_scans` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB");
    echo "OK\n";

    // products table
    echo "      - Creating table 'products'... ";
    $pdo->exec("CREATE TABLE IF NOT EXISTS `products` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `brand` VARCHAR(100) NOT NULL,
        `name` VARCHAR(255) NOT NULL,
        `category` VARCHAR(100) NOT NULL,
        `description` TEXT NOT NULL,
        `ingredients` TEXT NOT NULL,
        `skin_concern` VARCHAR(255) NOT NULL,
        `usage_guidance` TEXT NOT NULL,
        `status` VARCHAR(50) NOT NULL DEFAULT 'active',
        `price` INT NOT NULL DEFAULT 0,
        `skin_type` VARCHAR(100) NOT NULL DEFAULT 'All Skin Types',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    echo "OK\n";

    // activity_logs table
    echo "      - Creating table 'activity_logs'... ";
    $pdo->exec("CREATE TABLE IF NOT EXISTS `activity_logs` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NULL,
        `activity` VARCHAR(255) NOT NULL,
        `ip_address` VARCHAR(45) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB");
    echo "OK\n";

    // settings table (Centralized API Keys & Config)
    echo "      - Creating table 'settings'... ";
    $pdo->exec("CREATE TABLE IF NOT EXISTS `settings` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `setting_key` VARCHAR(100) NOT NULL UNIQUE,
        `setting_value` TEXT NULL,
        `setting_group` VARCHAR(50) NOT NULL DEFAULT 'general',
        `setting_label` VARCHAR(255) NOT NULL,
        `setting_type` VARCHAR(50) NOT NULL DEFAULT 'text',
        `is_secret` TINYINT(1) NOT NULL DEFAULT 0,
        `description` VARCHAR(255) NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "OK\n";

    // 4. Seeding Seed Data
    echo "[4/4] Injecting baseline seeds:\n";
    
    // Hash Passwords securely
    $adminHash = password_hash('admin123', PASSWORD_DEFAULT);
    $customerHash = password_hash('password123', PASSWORD_DEFAULT);
    
    // Admin Seed
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute(['admin@skincheck.com']);
    if (!$stmt->fetch()) {
        $insert = $pdo->prepare("INSERT INTO users (name, email, password_hash, role, status) VALUES (?, ?, ?, 'admin', 'active')");
        $insert->execute(['Internal Administrator', 'admin@skincheck.com', $adminHash]);
        echo "      - Seeded Administrator (admin@skincheck.com)... OK\n";
    } else {
        echo "      - Administrator already exists... Skipping\n";
    }

    // Customer Seed
    $stmt->execute(['customer@example.com']);
    $customerRow = $stmt->fetch();
    if (!$customerRow) {
        $insert = $pdo->prepare("INSERT INTO users (name, email, password_hash, role, status) VALUES (?, ?, ?, 'customer', 'active')");
        $insert->execute(['Jane Doe', 'customer@example.com', $customerHash]);
        
        // Retrieve newly inserted ID
        $stmt->execute(['customer@example.com']);
        $customerRow = $stmt->fetch();
        echo "      - Seeded Customer Jane Doe (customer@example.com)... OK\n";
    } else {
        echo "      - Customer Jane Doe already exists... Skipping\n";
    }

    // Products Seeding
    $countProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    if ($countProducts == 0) {
        $productsSeed = [
            // Acne
            ['Somethinc', 'Low pH Gentle Jelly Cleanser', 'Cleanser', 'Pembersih wajah bertekstur jelly dengan pH seimbang yang membersihkan debu dan minyak berlebih tanpa membuat kulit kering atau merusak skin barrier.', 'Japanese Mugwort, Tea Tree Oil, Centella Asiatica, Peppermint', 'Acne', 'Pagi & Malam: Basahi wajah, busakan produk di telapak tangan, pijat lembut dengan gerakan melingkar, lalu bilas air bersih.', 'active', 99000, 'Acne-Prone, Sensitive, All Skin Types'],
            ['COSRX', 'AHA/BHA Clarifying Treatment Toner', 'Toner', 'Toner eksfoliasi harian yang lembut membantu mencegah timbulnya komedo, membersihkan pori-pori tersumbat, dan menyeimbangkan kadar minyak wajah.', 'Salix Alba (Willow) Bark Water, Pyrus Malus (Apple) Fruit Water, Glycolic Acid (AHA), Betaine Salicylate (BHA)', 'Acne', 'Malam Hari: Semprotkan pada kapas secukupnya lalu usapkan lembut ke seluruh wajah setelah membersihkan muka.', 'active', 175000, 'Oily, Acne-Prone, Normal'],
            ['SKINTIFIC', '2% Salicylic Acid Anti Acne Serum', 'Serum', 'Serum dengan teknologi ACSD Salicylic Acid time-released yang efektif menenangkan jerawat meradang, memudarkan kemerahan jerawat, dan meratakan tekstur kulit.', '2% Salicylic Acid, Pionin, Centella Asiatica, Niacinamide, Ceramide', 'Acne', 'Pagi & Malam: Teteskan 2-3 tetes pada wajah bersih, tepuk-tepuk hingga meresap sebelum menggunakan moisturizer.', 'active', 139000, 'Acne-Prone, Oily'],
            ['SKINTIFIC', 'Salicylic Acid Blemish Clearing Spot Treatment', 'Spot Treatment', 'Obat totol jerawat aktif transparan yang cepat meredakan inflamasi jerawat dan mempercepat penyembuhan tanpa meninggalkan bekas gelap.', 'Salicylic Acid, Pionin, Centella Asiatica, Niacinamide', 'Acne', 'Pagi & Malam: Totolkan langsung pada jerawat yang aktif atau meradang sebagai langkah terakhir skincare.', 'active', 89000, 'Acne-Prone'],
            ['Somethinc', 'Calm Down! Skinpair R-Cover Cream', 'Moisturizer', 'Pelembap ringan penenang jerawat dan kemerahan yang melindungi pertahanan skin barrier tanpa menyumbat pori-pori.', 'Madagascar Centella Asiatica, Encapsulated Ceramide, Panthenol, Murumuru Butter', 'Acne', 'Pagi & Malam: Aplikasikan merata ke seluruh wajah dan leher setelah pemakaian serum.', 'active', 129000, 'Acne-Prone, Sensitive, Combination'],
            // Redness
            ['Anua', 'Heartleaf 77% Soothing Toner', 'Toner', 'Toner penenang nomor satu dengan kandungan 77% ekstrak Daun Heartleaf yang sangat ampuh meredakan iritasi, kemerahan, dan inflamasi kulit reaktif.', '77% Houttuynia Cordata (Heartleaf) Extract, Panthenol, Centella Asiatica, Chamomile', 'Redness', 'Pagi & Malam: Tuangkan ke telapak tangan atau kapas, tepuk lembut ke kulit wajah setelah cuci muka.', 'active', 210000, 'Sensitive, Irritated, All Skin Types'],
            ['SKIN1004', 'Madagascar Centella Ampoule', 'Serum', 'Ampoule murni 100% ekstrak Centella Asiatica dari Madagaskar yang memberikan efek dingin instan, meredakan kemerahan, dan memperbaiki skin barrier rapuh.', '100% Centella Asiatica Extract', 'Redness', 'Pagi & Malam: Teteskan 3-4 tetes dan ratakan ke seluruh wajah, terutama di area yang memerah atau iritasi.', 'active', 195000, 'Sensitive, Redness-Prone, Normal'],
            ['SKINTIFIC', '5X Ceramide Barrier Repair Moisture Gel', 'Moisturizer', 'Pelembap gel populer yang menggabungkan 5 jenis ceramide untuk memperbaiki skin barrier yang rusak, mengunci kelembapan, dan meredakan rasa perih kemerahan.', '5X Ceramide (NP, NS, AS, EOP, AP), Hyaluronic Acid, Centella Asiatica, Marine Collagen', 'Redness', 'Pagi & Malam: Usapkan secukupnya ke wajah dan leher secara merata untuk hidrasi intensif.', 'active', 139000, 'Sensitive, Dry, Combination, Damaged Barrier'],
            ['La Roche-Posay', 'Cicaplast Baume B5+ Ultra-Repairing Soothing Balm', 'Moisturizer', 'Balsem multifungsi penolong kulit iritasi, kemerahan parah, luka bekas jerawat, dan kulit yang terkelupas akibat over-exfoliating.', 'Madecassoside, 5% Panthenol (Vitamin B5), Tribioma Prebiotic Complex, Shea Butter, Zinc Gluconate', 'Redness', 'Malam Hari atau saat iritasi: Oleskan tebal di area kemerahan atau kulit yang teriritasi setelah pelembap biasa.', 'active', 250000, 'Dry, Sensitive, Severely Irritated'],
            ['SKIN1004', 'Centella Ampoule Foam Cleanser', 'Cleanser', 'Pembersih wajah busa lembut ber-pH 5 yang diperkaya konsentrat Centella Asiatica untuk membersihkan kotoran tanpa memicu kemerahan kulit sensitif.', 'Centella Asiatica Extract, Sodium Cocoyl Isethionate, Glycerin, Cacao Extract', 'Redness', 'Pagi & Malam: Buat busa melimpah dengan sedikit air di tangan, pijat di wajah perlahan, lalu bilas.', 'active', 179000, 'Sensitive, Dry, All Skin Types'],
            // Hyperpigmentation
            ['The Ordinary', 'Niacinamide 10% + Zinc 1%', 'Serum', 'Serum konsentrasi tinggi Niacinamide dan mineral Zinc yang terbukti klinis menyamarkan noda hitam bekas jerawat (PIH/PIE) serta mengontrol sebum pori-pori.', '10% Niacinamide (Vitamin B3), 1% Zinc PCA, Tamarindus Indica Seed Gum', 'Hyperpigmentation', 'Pagi & Malam: Oleskan beberapa tetes ke seluruh wajah sebelum krim pelembap.', 'active', 165000, 'Oily, Combination, Hyperpigmentation-Prone'],
            ['Somethinc', 'Revive Potion 3% Arbutin + Bakuchiol', 'Serum', 'Serum pencerah ganda yang mengombinasikan Alpha Arbutin generasi baru dan Bakuchiol untuk memudarkan flek hitam, meratakan warna kulit, dan mencerahkan kulit kusam.', '3% Alpha Arbutin, Bakuchiol, SabiWhite, Rice Bran Water', 'Hyperpigmentation', 'Pagi & Malam: Teteskan pada kulit yang sudah dibersihkan, ratakan sebelum moisturizer.', 'active', 119000, 'Dull Skin, Dark Spots, All Skin Types'],
            ['Avoskin', 'Your Skin Bae Vitamin C 3% + Niacinamide 2%', 'Serum', 'Serum antioksidan tinggi yang menghambat produksi melanin penyebab bintik hitam, menangkal polusi radikal bebas, dan membuat kulit tampak glowing alami.', '3% Ethyl Ascorbic Acid (Vitamin C), 2% Niacinamide, Mandarin Orange Extract', 'Hyperpigmentation', 'Pagi Hari: Gunakan beberapa tetes sebelum pelembap, dan selalu akhiri dengan tabir surya (sunscreen).', 'active', 149000, 'All Skin Types, Dull Skin'],
            ['SKINTIFIC', 'SymWhite 377 Dark Spot Moisture Gel', 'Moisturizer', 'Pelembap pencerah dengan molekul SymWhite 377 yang bekerja 100x lebih efektif dari kojic acid dalam menarget bintik hitam membandel dan bekas jerawat.', 'SymWhite 377 (Phenylethyl Resorcinol), Niacinamide, Tranexamic Acid, Ceramide', 'Hyperpigmentation', 'Pagi & Malam: Ratakan pada seluruh wajah secara rutin untuk hasil warna kulit yang merata.', 'active', 149000, 'Hyperpigmentation, Dull, Normal, Combination'],
            ['Hada Labo', 'Shirojyun Premium Ultimate Whitening Lotion', 'Toner', 'Hydrating toner pencerah asal Jepang dengan kandungan Tranexamic Acid murni dan Vitamin C untuk menyamarkan melasma serta melembapkan lapisan epidermis terdalam.', 'Tranexamic Acid, Vitamin C Phosphate, Vitamin E, Nano Hyaluronic Acid', 'Hyperpigmentation', 'Pagi & Malam: Tuang ke telapak tangan lalu tepuk-tepuk lembut ke wajah hingga meresap sempurna.', 'active', 98000, 'All Skin Types, Dry, Pigmented'],
            // Texture
            ['Dermalogica', 'Daily Microfoliant Exfoliator', 'Exfoliator', 'Bubuk eksfoliasi beras legendaris yang aktif saat dicampur air, mengangkat sel kulit mati seketika, dan memperhalus tekstur kulit kasar tanpa abrasi mikro.', 'Rice Bran, Salicylic Acid, Papain Enzyme, Colloidal Oatmeal, Ginkgo Biloba', 'Texture', 'Malam Hari (2-3x seminggu): Tuang 1/2 sendok teh ke tangan basah, gosok jadi busa lembut, pijat ke wajah 1 menit lalu bilas.', 'active', 950000, 'Uneven Texture, Rough Skin, All Types'],
            ['Avoskin', 'Miraculous Refining Serum', 'Exfoliator', 'Serum chemical peeling rumahan dengan perpaduan AHA BHA PHA dan Niacinamide yang meremajakan sel kulit, mengecilkan penampakan pori, dan menghaluskan bruntusan.', '10% AHA (Glycolic Acid), 3% BHA (Salicylic Acid), 2% Niacinamide, Ceramide, Aloe Vera', 'Texture', 'Malam Hari (2-3 kali seminggu): Aplikasikan tipis pada wajah bersih, tunggu 10 menit lalu lanjutkan dengan moisturizer pelembap.', 'active', 189000, 'Rough Texture, Oily, Normal (Bukan untuk kulit hiper-sensitif)'],
            ['Somethinc', 'Level 1% Encapsulated Retinol', 'Serum', 'Serum retinol dengan enkapsulasi teknologi tinggi yang menembus ke dalam pori-pori untuk merangsang produksi kolagen, menyamarkan kerutan halus, dan menghaluskan tekstur kulit.', '1% Encapsulated Retinol, Ceramide-3, Allantoin, Hyaluronic Acid', 'Texture', 'Malam Hari: Mulai 1-2x seminggu, gunakan seukuran kacang polong setelah moisturizer (sandwich method). Wajib pakai sunscreen keesokan harinya.', 'active', 155000, 'Aging, Uneven Texture, Large Pores'],
            ['Paula\'s Choice', 'Skin Perfecting 2% BHA Liquid Exfoliant', 'Exfoliator', 'Cairan eksfoliasi global terkemuka yang menembus lapisan sebum pori-pori terdalam untuk melarutkan sel kulit mati dan menghaluskan permukaan kulit bertekstur.', '2% Salicylic Acid (BHA), Camellia Oleifera (Green Tea) Leaf Extract, Methylpropanediol', 'Texture', 'Malam Hari: Usapkan lembut memakai kapas pada wajah bersih, jangan dibilas, lanjutkan ke langkah serum dan pelembap.', 'active', 480000, 'Large Pores, Rough Texture, Oily, Combination'],
            // Dryness
            ['CeraVe', 'Hydrating Facial Cleanser', 'Cleanser', 'Pembersih wajah berformula lotion tanpa busa yang membersihkan secara efektif tanpa mengikis minyak alami dan kelembapan esensial kulit kering.', '3 Essential Ceramides (1, 3, 6-II), Hyaluronic Acid, Glycerin, Phytosphingosine', 'Dryness', 'Pagi & Malam: Basahi wajah, usapkan lotion pembersih dengan lembut, bilas hingga bersih.', 'active', 215000, 'Dry, Very Dry, Sensitive, Normal'],
            ['Hada Labo', 'Gokujyun Premium Ultimate Hydrating Lotion', 'Toner', 'Lotion hidrasi intensif berkonsistensi kaya dengan 7 jenis asam hialuronat yang mengunci air hingga ke lapisan kulit terdalam untuk mengatasi dehidrasi parah.', '7 Types of Hyaluronic Acid, Hydroxyethyl Urea, Allantoin', 'Dryness', 'Pagi & Malam: Tepuk-tepuk pada kulit wajah yang lembap tepat setelah mencuci muka.', 'active', 110000, 'Dry, Extremely Dehydrated'],
            ['Isntree', 'Hyaluronic Acid Aqua Gel Cream', 'Moisturizer', 'Krim pelembap bertekstur gel sejuk yang kaya akan 5 molekul Hyaluronic Acid untuk hidrasi mendalam tanpa rasa lengket atau greasy.', '5 Types of Hyaluronic Acid, Centella Asiatica, Betaine, Sodium PCA', 'Dryness', 'Pagi & Malam: Oleskan merata pada wajah setelah memakai toner atau serum hidrasi.', 'active', 240000, 'Dehydrated, Combination, Oily-Dehydrated'],
            ['CeraVe', 'Moisturizing Cream (Ceramides + MVE)', 'Moisturizer', 'Krim pelembap tebal dengan teknologi MVE Delivery yang melepaskan kelembapan dan 3 ceramide esensial secara bertahap selama 24 jam penuh.', 'Ceramides 1, 3, 6-II, Hyaluronic Acid, Petrolatum, Dimethicone', 'Dryness', 'Pagi & Malam: Aplikasikan pada kulit kering atau bersisik pada wajah dan leher sesuai kebutuhan.', 'active', 269000, 'Dry, Very Dry, Compromised Skin'],
            // Sun Protection
            ['EltaMD', 'UV Clear Broad-Spectrum SPF 46', 'Sunscreen', 'Sunscreen mineral hibrida bebas minyak berformulasi medis dengan Niacinamide dan Zinc Oxide murni yang menenangkan kulit sensitif dan rawan jerawat.', '9.0% Transparent Zinc Oxide, 7.5% Octinoxate, 5% Niacinamide, Hyaluronic Acid, Vitamin E', 'Sun Protection', 'Pagi Hari: Aplikasikan sebanyak 2 jari ke seluruh wajah dan leher 15 menit sebelum terkena sinar matahari.', 'active', 399000, 'Acne-Prone, Sensitive, Hyperpigmentation'],
            ['La Roche-Posay', 'Anthelios UVMune 400 Invisible Fluid SPF 50+', 'Sunscreen', 'Sunscreen teknologi filter Mexoryl 400 mutakhir yang memberikan proteksi superior terhadap sinar UVA ultra-panjang. Tahan air, keringat, dan tidak perih di mata.', 'Mexoryl 400, Netlock Technology, Thermal Spring Water, Glycerin', 'Sun Protection', 'Pagi Hari: Kocok botol sebelum dipakai, aplikasikan 2 jari merata ke wajah dan leher. Reapply tiap 2 jam bila berkeringat/berenang.', 'active', 360000, 'All Skin Types, Sensitive, Outdoor'],
            ['CeraVe', 'Hydrating Mineral Sunscreen SPF 30', 'Sunscreen', 'Tabir surya 100% mineral fisik dengan Titanium Dioxide dan Zinc Oxide yang membentuk lapisan perisai pelindung tanpa memicu iritasi di kulit sensitif.', '100% Mineral Zinc Oxide & Titanium Dioxide, Ceramides 1, 3, 6-II, Niacinamide', 'Sun Protection', 'Pagi Hari: Oleskan sedikit demi sedikit ke seluruh permukaan wajah hingga merata.', 'active', 289000, 'Sensitive, Dry, Reactive Skin'],
            ['Azarine', 'Hydrasoothe Sunscreen Gel SPF 45 PA++++', 'Sunscreen', 'Sunscreen gel berbahan dasar air yang ringan, dingin, tanpa white cast, dan bebas alkohol serta silikon. Sangat nyaman untuk iklim tropis Indonesia.', 'Propolis, Aloe Vera, Green Tea, Royal Jelly, Pomegranate Extract', 'Sun Protection', 'Pagi Hari: Oleskan merata 2 ruas jari pada wajah dan leher setelah pelembap.', 'active', 68000, 'Oily, Combination, Acne-Prone'],
            ['Skin Aqua', 'UV Moisture Gel SPF 30 PA++', 'Sunscreen', 'Pelembap tabir surya harian berbahan dasar air yang menyegarkan dengan Hyaluronic Acid dan Kolagen untuk menjaga kelembutan kulit sehari-hari.', 'Improved Hyaluronic Acid (AcHA), Collagen, Vitamin B5 & E', 'Sun Protection', 'Pagi Hari: Ratakan ke seluruh wajah dan leher sebelum beraktivitas di luar ruangan.', 'active', 65000, 'Normal to Oily, Daily Indoor'],
            ['La Roche-Posay', 'Anthelios Melt-In Milk Sunscreen SPF 60', 'Sunscreen', 'Tabir surya bertekstur susu velvet yang melembapkan secara mendalam dengan proteksi spektrum luas Cell-Ox Shield antioksidan.', 'Avobenzone, Homosalate, Octisalate, Octocrylene, Senna Alata Extract', 'Sun Protection', 'Pagi Hari: Usapkan merata ke seluruh wajah dan bagian tubuh yang terpapar matahari.', 'active', 450000, 'Dry, Normal']
        ];
        
        $insertProduct = $pdo->prepare("INSERT INTO products (brand, name, category, description, ingredients, skin_concern, usage_guidance, status, price, skin_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($productsSeed as $p) {
            $insertProduct->execute($p);
        }
        echo "      - Seeded default products list (" . count($productsSeed) . " items)... OK\n";
    } else {
        echo "      - Skincare products list already seeded... Skipping\n";
    }

    // Seed mock scans history for Jane Doe
    if ($customerRow) {
        $customerId = $customerRow->id;
        $countScans = $pdo->query("SELECT COUNT(*) FROM skin_scans WHERE user_id = {$customerId}")->fetchColumn();
        if ($countScans == 0) {
            // Scan 1: Protected
            $pdo->exec("INSERT INTO skin_scans (id, user_id, image_path, status, image_quality_score, created_at) VALUES (101, {$customerId}, 'public/img/scans/scan_101.jpg', 'completed', 98.50, '2026-08-28 10:15:22')");
            $pdo->exec("INSERT INTO analysis_results (skin_scan_id, acne_score, redness_score, texture_score, hyperpigmentation_score, overall_score, ai_response, created_at) VALUES (101, 12, 15, 78, 15, 84, 'Slight dry patch detected around the forehead area. Sunscreen coverage is highly even.', '2026-08-28 10:15:22')");
            $pdo->exec("INSERT INTO sunscreen_results (skin_scan_id, coverage_score, coverage_status, heatmap_path, low_coverage_areas, analysis_method, hardware_type, created_at) VALUES (101, 94, 'Protected', 'public/img/heatmaps/heatmap_101.png', 'None', 'UV Reflectance Imaging', 'Standard RGB Mobile Camera', '2026-08-28 10:15:22')");

            // Scan 2: Partially Protected
            $pdo->exec("INSERT INTO skin_scans (id, user_id, image_path, status, image_quality_score, created_at) VALUES (98, {$customerId}, 'public/img/scans/scan_98.jpg', 'completed', 92.10, '2026-08-20 16:44:10')");
            $pdo->exec("INSERT INTO analysis_results (skin_scan_id, acne_score, redness_score, texture_score, hyperpigmentation_score, overall_score, ai_response, created_at) VALUES (98, 25, 30, 60, 18, 71, 'Elevated redness detected. Reapplication of sunscreen recommended, specifically on the cheeks.', '2026-08-20 16:44:10')");
            $pdo->exec("INSERT INTO sunscreen_results (skin_scan_id, coverage_score, coverage_status, heatmap_path, low_coverage_areas, analysis_method, hardware_type, created_at) VALUES (98, 40, 'Partially Protected', 'public/img/heatmaps/heatmap_98.png', 'Cheeks, Nose', 'UV Reflectance Imaging', 'Standard RGB Mobile Camera', '2026-08-20 16:44:10')");

            // Scan 3: Unprotected
            $pdo->exec("INSERT INTO skin_scans (id, user_id, image_path, status, image_quality_score, created_at) VALUES (85, {$customerId}, 'public/img/scans/scan_85.jpg', 'completed', 88.00, '2026-08-10 09:30:15')");
            $pdo->exec("INSERT INTO analysis_results (skin_scan_id, acne_score, redness_score, texture_score, hyperpigmentation_score, overall_score, ai_response, created_at) VALUES (85, 30, 45, 52, 20, 65, 'Significant UV exposure risk detected. Skin barrier dry. Hydrate skin and apply sunscreen.', '2026-08-10 09:30:15')");
            $pdo->exec("INSERT INTO sunscreen_results (skin_scan_id, coverage_score, coverage_status, heatmap_path, low_coverage_areas, analysis_method, hardware_type, created_at) VALUES (85, 0, 'Unprotected', 'public/img/heatmaps/heatmap_85.png', 'Entire Face', 'UV Reflectance Imaging', 'Standard RGB Mobile Camera', '2026-08-10 09:30:15')");
            
            echo "      - Seeded scan diagnostic logs for Jane Doe... OK\n";
        } else {
            echo "      - Scan diagnostics logs already seeded... Skipping\n";
        }
    }
    
    echo "====================================\n";
    echo "Database setup successfully finished!\n";

} catch (PDOException $e) {
    fwrite(STDERR, "Database Setup Error: " . $e->getMessage() . "\n");
    exit(1);
}
