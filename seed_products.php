<?php
/**
 * SkinCheck - Skincare Products Database Seeder
 * Populates comprehensive skincare catalog for all skin concerns into MySQL.
 */

require_once __DIR__ . '/app/config/config.php';

echo "SkinCheck Product Database Seeder\n";
echo "=================================\n";

$host = DB_HOST;
$user = DB_USER;
$pass = DB_PASS;
$dbname = DB_NAME;

try {
    $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Connected to database '{$dbname}'.\n";

    // Curated Skincare Catalog mapped to visual indicators and concerns
    $products = [
        // ── 1. ACNE & BLEMISHES (JERAWAT & KOMEDO) ──────────────────────
        [
            'brand' => 'Somethinc',
            'name' => 'Low pH Gentle Jelly Cleanser',
            'category' => 'Cleanser',
            'description' => 'Pembersih wajah bertekstur jelly dengan pH seimbang yang membersihkan debu dan minyak berlebih tanpa membuat kulit kering atau merusak skin barrier.',
            'ingredients' => 'Japanese Mugwort, Tea Tree Oil, Centella Asiatica, Peppermint',
            'skin_concern' => 'Acne',
            'usage_guidance' => 'Pagi & Malam: Basahi wajah, busakan produk di telapak tangan, pijat lembut dengan gerakan melingkar, lalu bilas air bersih.',
            'status' => 'active',
            'price' => 99000,
            'skin_type' => 'Acne-Prone, Sensitive, All Skin Types'
        ],
        [
            'brand' => 'COSRX',
            'name' => 'AHA/BHA Clarifying Treatment Toner',
            'category' => 'Toner',
            'description' => 'Toner eksfoliasi harian yang lembut membantu mencegah timbulnya komedo, membersihkan pori-pori tersumbat, dan menyeimbangkan kadar minyak wajah.',
            'ingredients' => 'Salix Alba (Willow) Bark Water, Pyrus Malus (Apple) Fruit Water, Glycolic Acid (AHA), Betaine Salicylate (BHA)',
            'skin_concern' => 'Acne',
            'usage_guidance' => 'Malam Hari: Semprotkan pada kapas secukupnya lalu usapkan lembut ke seluruh wajah setelah membersihkan muka.',
            'status' => 'active',
            'price' => 175000,
            'skin_type' => 'Oily, Acne-Prone, Normal'
        ],
        [
            'brand' => 'SKINTIFIC',
            'name' => '2% Salicylic Acid Anti Acne Serum',
            'category' => 'Serum',
            'description' => 'Serum dengan teknologi ACSD Salicylic Acid time-released yang efektif menenangkan jerawat meradang, memudarkan kemerahan jerawat, dan meratakan tekstur kulit.',
            'ingredients' => '2% Salicylic Acid, Pionin, Centella Asiatica, Niacinamide, Ceramide',
            'skin_concern' => 'Acne',
            'usage_guidance' => 'Pagi & Malam: Teteskan 2-3 tetes pada wajah bersih, tepuk-tepuk hingga meresap sebelum menggunakan moisturizer.',
            'status' => 'active',
            'price' => 139000,
            'skin_type' => 'Acne-Prone, Oily'
        ],
        [
            'brand' => 'SKINTIFIC',
            'name' => 'Salicylic Acid Blemish Clearing Spot Treatment',
            'category' => 'Spot Treatment',
            'description' => 'Obat totol jerawat aktif transparan yang cepat meredakan inflamasi jerawat dan mempercepat penyembuhan tanpa meninggalkan bekas gelap.',
            'ingredients' => 'Salicylic Acid, Pionin, Centella Asiatica, Niacinamide',
            'skin_concern' => 'Acne',
            'usage_guidance' => 'Pagi & Malam: Totolkan langsung pada jerawat yang aktif atau meradang sebagai langkah terakhir skincare.',
            'status' => 'active',
            'price' => 89000,
            'skin_type' => 'Acne-Prone'
        ],
        [
            'brand' => 'Somethinc',
            'name' => 'Calm Down! Skinpair R-Cover Cream',
            'category' => 'Moisturizer',
            'description' => 'Pelembap ringan penenang jerawat dan kemerahan yang melindungi pertahanan skin barrier tanpa menyumbat pori-pori.',
            'ingredients' => 'Madagascar Centella Asiatica, Encapsulated Ceramide, Panthenol, Murumuru Butter',
            'skin_concern' => 'Acne',
            'usage_guidance' => 'Pagi & Malam: Aplikasikan merata ke seluruh wajah dan leher setelah pemakaian serum.',
            'status' => 'active',
            'price' => 129000,
            'skin_type' => 'Acne-Prone, Sensitive, Combination'
        ],

        // ── 2. REDNESS & SENSITIVE SKIN (KEMERAHAN & SKIN BARRIER) ──────
        [
            'brand' => 'Anua',
            'name' => 'Heartleaf 77% Soothing Toner',
            'category' => 'Toner',
            'description' => 'Toner penenang nomor satu dengan kandungan 77% ekstrak Daun Heartleaf yang sangat ampuh meredakan iritasi, kemerahan, dan inflamasi kulit reaktif.',
            'ingredients' => '77% Houttuynia Cordata (Heartleaf) Extract, Panthenol, Centella Asiatica, Chamomile',
            'skin_concern' => 'Redness',
            'usage_guidance' => 'Pagi & Malam: Tuangkan ke telapak tangan atau kapas, tepuk lembut ke kulit wajah setelah cuci muka.',
            'status' => 'active',
            'price' => 210000,
            'skin_type' => 'Sensitive, Irritated, All Skin Types'
        ],
        [
            'brand' => 'SKIN1004',
            'name' => 'Madagascar Centella Ampoule',
            'category' => 'Serum',
            'description' => 'Ampoule murni 100% ekstrak Centella Asiatica dari Madagaskar yang memberikan efek dingin instan, meredakan kemerahan, dan memperbaiki skin barrier rapuh.',
            'ingredients' => '100% Centella Asiatica Extract',
            'skin_concern' => 'Redness',
            'usage_guidance' => 'Pagi & Malam: Teteskan 3-4 tetes dan ratakan ke seluruh wajah, terutama di area yang memerah atau iritasi.',
            'status' => 'active',
            'price' => 195000,
            'skin_type' => 'Sensitive, Redness-Prone, Normal'
        ],
        [
            'brand' => 'SKINTIFIC',
            'name' => '5X Ceramide Barrier Repair Moisture Gel',
            'category' => 'Moisturizer',
            'description' => 'Pelembap gel populer yang menggabungkan 5 jenis ceramide untuk memperbaiki skin barrier yang rusak, mengunci kelembapan, dan meredakan rasa perih kemerahan.',
            'ingredients' => '5X Ceramide (NP, NS, AS, EOP, AP), Hyaluronic Acid, Centella Asiatica, Marine Collagen',
            'skin_concern' => 'Redness',
            'usage_guidance' => 'Pagi & Malam: Usapkan secukupnya ke wajah dan leher secara merata untuk hidrasi intensif.',
            'status' => 'active',
            'price' => 139000,
            'skin_type' => 'Sensitive, Dry, Combination, Damaged Barrier'
        ],
        [
            'brand' => 'La Roche-Posay',
            'name' => 'Cicaplast Baume B5+ Ultra-Repairing Soothing Balm',
            'category' => 'Moisturizer',
            'description' => 'Balsem multifungsi penolong kulit iritasi, kemerahan parah, luka bekas jerawat, dan kulit yang terkelupas akibat over-exfoliating.',
            'ingredients' => 'Madecassoside, 5% Panthenol (Vitamin B5), Tribioma Prebiotic Complex, Shea Butter, Zinc Gluconate',
            'skin_concern' => 'Redness',
            'usage_guidance' => 'Malam Hari atau saat iritasi: Oleskan tebal di area kemerahan atau kulit yang teriritasi setelah pelembap biasa.',
            'status' => 'active',
            'price' => 250000,
            'skin_type' => 'Dry, Sensitive, Severely Irritated'
        ],
        [
            'brand' => 'SKIN1004',
            'name' => 'Centella Ampoule Foam Cleanser',
            'category' => 'Cleanser',
            'description' => 'Pembersih wajah busa lembut ber-pH 5 yang diperkaya konsentrat Centella Asiatica untuk membersihkan kotoran tanpa memicu kemerahan kulit sensitif.',
            'ingredients' => 'Centella Asiatica Extract, Sodium Cocoyl Isethionate, Glycerin, Cacao Extract',
            'skin_concern' => 'Redness',
            'usage_guidance' => 'Pagi & Malam: Buat busa melimpah dengan sedikit air di tangan, pijat di wajah perlahan, lalu bilas.',
            'status' => 'active',
            'price' => 179000,
            'skin_type' => 'Sensitive, Dry, All Skin Types'
        ],

        // ── 3. HYPERPIGMENTATION & DARK SPOTS (FLEK & BEKAS JERAWAT) ─────
        [
            'brand' => 'The Ordinary',
            'name' => 'Niacinamide 10% + Zinc 1%',
            'category' => 'Serum',
            'description' => 'Serum konsentrasi tinggi Niacinamide dan mineral Zinc yang terbukti klinis menyamarkan noda hitam bekas jerawat (PIH/PIE) serta mengontrol sebum pori-pori.',
            'ingredients' => '10% Niacinamide (Vitamin B3), 1% Zinc PCA, Tamarindus Indica Seed Gum',
            'skin_concern' => 'Hyperpigmentation',
            'usage_guidance' => 'Pagi & Malam: Oleskan beberapa tetes ke seluruh wajah sebelum krim pelembap.',
            'status' => 'active',
            'price' => 165000,
            'skin_type' => 'Oily, Combination, Hyperpigmentation-Prone'
        ],
        [
            'brand' => 'Somethinc',
            'name' => 'Revive Potion 3% Arbutin + Bakuchiol',
            'category' => 'Serum',
            'description' => 'Serum pencerah ganda yang mengombinasikan Alpha Arbutin generasi baru dan Bakuchiol untuk memudarkan flek hitam, meratakan warna kulit, dan mencerahkan kulit kusam.',
            'ingredients' => '3% Alpha Arbutin, Bakuchiol, SabiWhite, Rice Bran Water',
            'skin_concern' => 'Hyperpigmentation',
            'usage_guidance' => 'Pagi & Malam: Teteskan pada kulit yang sudah dibersihkan, ratakan sebelum moisturizer.',
            'status' => 'active',
            'price' => 119000,
            'skin_type' => 'Dull Skin, Dark Spots, All Skin Types'
        ],
        [
            'brand' => 'Avoskin',
            'name' => 'Your Skin Bae Vitamin C 3% + Niacinamide 2%',
            'category' => 'Serum',
            'description' => 'Serum antioksidan tinggi yang menghambat produksi melanin penyebab bintik hitam, menangkal polusi radikal bebas, dan membuat kulit tampak glowing alami.',
            'ingredients' => '3% Ethyl Ascorbic Acid (Vitamin C), 2% Niacinamide, Mandarin Orange Extract',
            'skin_concern' => 'Hyperpigmentation',
            'usage_guidance' => 'Pagi Hari: Gunakan beberapa tetes sebelum pelembap, dan selalu akhiri dengan tabir surya (sunscreen).',
            'status' => 'active',
            'price' => 149000,
            'skin_type' => 'All Skin Types, Dull Skin'
        ],
        [
            'brand' => 'SKINTIFIC',
            'name' => 'SymWhite 377 Dark Spot Moisture Gel',
            'category' => 'Moisturizer',
            'description' => 'Pelembap pencerah dengan molekul SymWhite 377 yang bekerja 100x lebih efektif dari kojic acid dalam menarget bintik hitam membandel dan bekas jerawat.',
            'ingredients' => 'SymWhite 377 (Phenylethyl Resorcinol), Niacinamide, Tranexamic Acid, Ceramide',
            'skin_concern' => 'Hyperpigmentation',
            'usage_guidance' => 'Pagi & Malam: Ratakan pada seluruh wajah secara rutin untuk hasil warna kulit yang merata.',
            'status' => 'active',
            'price' => 149000,
            'skin_type' => 'Hyperpigmentation, Dull, Normal, Combination'
        ],
        [
            'brand' => 'Hada Labo',
            'name' => 'Shirojyun Premium Ultimate Whitening Lotion',
            'category' => 'Toner',
            'description' => 'Hydrating toner pencerah asal Jepang dengan kandungan Tranexamic Acid murni dan Vitamin C untuk menyamarkan melasma serta melembapkan lapisan epidermis terdalam.',
            'ingredients' => 'Tranexamic Acid, Vitamin C Phosphate, Vitamin E, Nano Hyaluronic Acid',
            'skin_concern' => 'Hyperpigmentation',
            'usage_guidance' => 'Pagi & Malam: Tuang ke telapak tangan lalu tepuk-tepuk lembut ke wajah hingga meresap sempurna.',
            'status' => 'active',
            'price' => 98000,
            'skin_type' => 'All Skin Types, Dry, Pigmented'
        ],

        // ── 4. TEXTURE & PORES (TEKSTUR KASAR & PORI-PORI) ───────────────
        [
            'brand' => 'Dermalogica',
            'name' => 'Daily Microfoliant Exfoliator',
            'category' => 'Exfoliator',
            'description' => 'Bubuk eksfoliasi beras legendaris yang aktif saat dicampur air, mengangkat sel kulit mati seketika, dan memperhalus tekstur kulit kasar tanpa abrasi mikro.',
            'ingredients' => 'Rice Bran, Salicylic Acid, Papain Enzyme, Colloidal Oatmeal, Ginkgo Biloba',
            'skin_concern' => 'Texture',
            'usage_guidance' => 'Malam Hari (2-3x seminggu): Tuang 1/2 sendok teh ke tangan basah, gosok jadi busa lembut, pijat ke wajah 1 menit lalu bilas.',
            'status' => 'active',
            'price' => 950000,
            'skin_type' => 'Uneven Texture, Rough Skin, All Types'
        ],
        [
            'brand' => 'Avoskin',
            'name' => 'Miraculous Refining Serum',
            'category' => 'Exfoliator',
            'description' => 'Serum chemical peeling rumahan dengan perpaduan AHA BHA PHA dan Niacinamide yang meremajakan sel kulit, mengecilkan penampakan pori, dan menghaluskan bruntusan.',
            'ingredients' => '10% AHA (Glycolic Acid), 3% BHA (Salicylic Acid), 2% Niacinamide, Ceramide, Aloe Vera',
            'skin_concern' => 'Texture',
            'usage_guidance' => 'Malam Hari (2-3 kali seminggu): Aplikasikan tipis pada wajah bersih, tunggu 10 menit lalu lanjutkan dengan moisturizer pelembap.',
            'status' => 'active',
            'price' => 189000,
            'skin_type' => 'Rough Texture, Oily, Normal (Bukan untuk kulit hiper-sensitif)'
        ],
        [
            'brand' => 'Somethinc',
            'name' => 'Level 1% Encapsulated Retinol',
            'category' => 'Serum',
            'description' => 'Serum retinol dengan enkapsulasi teknologi tinggi yang menembus ke dalam pori-pori untuk merangsang produksi kolagen, menyamarkan kerutan halus, dan menghaluskan tekstur kulit.',
            'ingredients' => '1% Encapsulated Retinol, Ceramide-3, Allantoin, Hyaluronic Acid',
            'skin_concern' => 'Texture',
            'usage_guidance' => 'Malam Hari: Mulai 1-2x seminggu, gunakan seukuran kacang polong setelah moisturizer (sandwich method). Wajib pakai sunscreen keesokan harinya.',
            'status' => 'active',
            'price' => 155000,
            'skin_type' => 'Aging, Uneven Texture, Large Pores'
        ],
        [
            'brand' => 'Paula\'s Choice',
            'name' => 'Skin Perfecting 2% BHA Liquid Exfoliant',
            'category' => 'Exfoliator',
            'description' => 'Cairan eksfoliasi global terkemuka yang menembus lapisan sebum pori-pori terdalam untuk melarutkan sel kulit mati dan menghaluskan permukaan kulit bertekstur.',
            'ingredients' => '2% Salicylic Acid (BHA), Camellia Oleifera (Green Tea) Leaf Extract, Methylpropanediol',
            'skin_concern' => 'Texture',
            'usage_guidance' => 'Malam Hari: Usapkan lembut memakai kapas pada wajah bersih, jangan dibilas, lanjutkan ke langkah serum dan pelembap.',
            'status' => 'active',
            'price' => 480000,
            'skin_type' => 'Large Pores, Rough Texture, Oily, Combination'
        ],

        // ── 5. DRYNESS & HYDRATION (KULIT KERING & DEHIDRASI) ────────────
        [
            'brand' => 'CeraVe',
            'name' => 'Hydrating Facial Cleanser',
            'category' => 'Cleanser',
            'description' => 'Pembersih wajah berformula lotion tanpa busa yang membersihkan secara efektif tanpa mengikis minyak alami dan kelembapan esensial kulit kering.',
            'ingredients' => '3 Essential Ceramides (1, 3, 6-II), Hyaluronic Acid, Glycerin, Phytosphingosine',
            'skin_concern' => 'Dryness',
            'usage_guidance' => 'Pagi & Malam: Basahi wajah, usapkan lotion pembersih dengan lembut, bilas hingga bersih.',
            'status' => 'active',
            'price' => 215000,
            'skin_type' => 'Dry, Very Dry, Sensitive, Normal'
        ],
        [
            'brand' => 'Hada Labo',
            'name' => 'Gokujyun Premium Ultimate Hydrating Lotion',
            'category' => 'Toner',
            'description' => 'Lotion hidrasi intensif berkonsistensi kaya dengan 7 jenis asam hialuronat yang mengunci air hingga ke lapisan kulit terdalam untuk mengatasi dehidrasi parah.',
            'ingredients' => '7 Types of Hyaluronic Acid, Hydroxyethyl Urea, Allantoin',
            'skin_concern' => 'Dryness',
            'usage_guidance' => 'Pagi & Malam: Tepuk-tepuk pada kulit wajah yang lembap tepat setelah mencuci muka.',
            'status' => 'active',
            'price' => 110000,
            'skin_type' => 'Dry, Extremely Dehydrated'
        ],
        [
            'brand' => 'Isntree',
            'name' => 'Hyaluronic Acid Aqua Gel Cream',
            'category' => 'Moisturizer',
            'description' => 'Krim pelembap bertekstur gel sejuk yang kaya akan 5 molekul Hyaluronic Acid untuk hidrasi mendalam tanpa rasa lengket atau greasy.',
            'ingredients' => '5 Types of Hyaluronic Acid, Centella Asiatica, Betaine, Sodium PCA',
            'skin_concern' => 'Dryness',
            'usage_guidance' => 'Pagi & Malam: Oleskan merata pada wajah setelah memakai toner atau serum hidrasi.',
            'status' => 'active',
            'price' => 240000,
            'skin_type' => 'Dehydrated, Combination, Oily-Dehydrated'
        ],
        [
            'brand' => 'CeraVe',
            'name' => 'Moisturizing Cream (Ceramides + MVE)',
            'category' => 'Moisturizer',
            'description' => 'Krim pelembap tebal dengan teknologi MVE Delivery yang melepaskan kelembapan dan 3 ceramide esensial secara bertahap selama 24 jam penuh.',
            'ingredients' => 'Ceramides 1, 3, 6-II, Hyaluronic Acid, Petrolatum, Dimethicone',
            'skin_concern' => 'Dryness',
            'usage_guidance' => 'Pagi & Malam: Aplikasikan pada kulit kering atau bersisik pada wajah dan leher sesuai kebutuhan.',
            'status' => 'active',
            'price' => 269000,
            'skin_type' => 'Dry, Very Dry, Compromised Skin'
        ],

        // ── 6. SUNSCREEN & UV DEFENSE (TABIR SURYA) ───────────────────────
        [
            'brand' => 'EltaMD',
            'name' => 'UV Clear Broad-Spectrum SPF 46',
            'category' => 'Sunscreen',
            'description' => 'Sunscreen mineral hibrida bebas minyak berformulasi medis dengan Niacinamide dan Zinc Oxide murni yang menenangkan kulit sensitif dan rawan jerawat.',
            'ingredients' => '9.0% Transparent Zinc Oxide, 7.5% Octinoxate, 5% Niacinamide, Hyaluronic Acid, Vitamin E',
            'skin_concern' => 'Sun Protection',
            'usage_guidance' => 'Pagi Hari: Aplikasikan sebanyak 2 jari ke seluruh wajah dan leher 15 menit sebelum terkena sinar matahari.',
            'status' => 'active',
            'price' => 399000,
            'skin_type' => 'Acne-Prone, Sensitive, Hyperpigmentation'
        ],
        [
            'brand' => 'La Roche-Posay',
            'name' => 'Anthelios UVMune 400 Invisible Fluid SPF 50+',
            'category' => 'Sunscreen',
            'description' => 'Sunscreen teknologi filter Mexoryl 400 mutakhir yang memberikan proteksi superior terhadap sinar UVA ultra-panjang. Tahan air, keringat, dan tidak perih di mata.',
            'ingredients' => 'Mexoryl 400, Netlock Technology, Thermal Spring Water, Glycerin',
            'skin_concern' => 'Sun Protection',
            'usage_guidance' => 'Pagi Hari: Kocok botol sebelum dipakai, aplikasikan 2 jari merata ke wajah dan leher. Reapply tiap 2 jam bila berkeringat/berenang.',
            'status' => 'active',
            'price' => 360000,
            'skin_type' => 'All Skin Types, Sensitive, Outdoor'
        ],
        [
            'brand' => 'CeraVe',
            'name' => 'Hydrating Mineral Sunscreen SPF 30',
            'category' => 'Sunscreen',
            'description' => 'Tabir surya 100% mineral fisik dengan Titanium Dioxide dan Zinc Oxide yang membentuk lapisan perisai pelindung tanpa memicu iritasi di kulit sensitif.',
            'ingredients' => '100% Mineral Zinc Oxide & Titanium Dioxide, Ceramides 1, 3, 6-II, Niacinamide',
            'skin_concern' => 'Sun Protection',
            'usage_guidance' => 'Pagi Hari: Oleskan sedikit demi sedikit ke seluruh permukaan wajah hingga merata.',
            'status' => 'active',
            'price' => 289000,
            'skin_type' => 'Sensitive, Dry, Reactive Skin'
        ],
        [
            'brand' => 'Azarine',
            'name' => 'Hydrasoothe Sunscreen Gel SPF 45 PA++++',
            'category' => 'Sunscreen',
            'description' => 'Sunscreen gel berbahan dasar air yang ringan, dingin, tanpa white cast, dan bebas alkohol serta silikon. Sangat nyaman untuk iklim tropis Indonesia.',
            'ingredients' => 'Propolis, Aloe Vera, Green Tea, Royal Jelly, Pomegranate Extract',
            'skin_concern' => 'Sun Protection',
            'usage_guidance' => 'Pagi Hari: Oleskan merata 2 ruas jari pada wajah dan leher setelah pelembap.',
            'status' => 'active',
            'price' => 68000,
            'skin_type' => 'Oily, Combination, Acne-Prone'
        ],
        [
            'brand' => 'Skin Aqua',
            'name' => 'UV Moisture Gel SPF 30 PA++',
            'category' => 'Sunscreen',
            'description' => 'Pelembap tabir surya harian berbahan dasar air yang menyegarkan dengan Hyaluronic Acid dan Kolagen untuk menjaga kelembutan kulit sehari-hari.',
            'ingredients' => 'Improved Hyaluronic Acid (AcHA), Collagen, Vitamin B5 & E',
            'skin_concern' => 'Sun Protection',
            'usage_guidance' => 'Pagi Hari: Ratakan ke seluruh wajah dan leher sebelum beraktivitas di luar ruangan.',
            'status' => 'active',
            'price' => 65000,
            'skin_type' => 'Normal to Oily, Daily Indoor'
        ],
        [
            'brand' => 'La Roche-Posay',
            'name' => 'Anthelios Melt-In Milk Sunscreen SPF 60',
            'category' => 'Sunscreen',
            'description' => 'Tabir surya bertekstur susu velvet yang melembapkan secara mendalam dengan proteksi spektrum luas Cell-Ox Shield antioksidan.',
            'ingredients' => 'Avobenzone, Homosalate, Octisalate, Octocrylene, Senna Alata Extract',
            'skin_concern' => 'Sun Protection',
            'usage_guidance' => 'Pagi Hari: Usapkan merata ke seluruh wajah dan bagian tubuh yang terpapar matahari.',
            'status' => 'active',
            'price' => 450000,
            'skin_type' => 'Dry, Normal'
        ]
    ];

    // Truncate or Upsert products
    echo "Upserting " . count($products) . " curated skincare products...\n";

    // Clean existing products to avoid duplicates and ensure standard data
    $pdo->exec("TRUNCATE TABLE products");

    $insertSql = "INSERT INTO products 
        (brand, name, category, description, ingredients, skin_concern, usage_guidance, status, price, skin_type) 
        VALUES (:brand, :name, :category, :description, :ingredients, :skin_concern, :usage_guidance, :status, :price, :skin_type)";
    
    $stmt = $pdo->prepare($insertSql);

    $inserted = 0;
    foreach ($products as $p) {
        $stmt->execute([
            ':brand' => $p['brand'],
            ':name' => $p['name'],
            ':category' => $p['category'],
            ':description' => $p['description'],
            ':ingredients' => $p['ingredients'],
            ':skin_concern' => $p['skin_concern'],
            ':usage_guidance' => $p['usage_guidance'],
            ':status' => $p['status'],
            ':price' => $p['price'],
            ':skin_type' => $p['skin_type']
        ]);
        $inserted++;
        echo "  [+] Seeded: [{$p['skin_concern']}] {$p['brand']} - {$p['name']} (Rp " . number_format($p['price'], 0, ',', '.') . ")\n";
    }

    echo "=================================\n";
    echo "SUCCESS: {$inserted} skincare products successfully seeded into MySQL!\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
