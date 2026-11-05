require('dotenv').config();
const express = require('express');
const cors = require('cors');
const mysql = require('mysql2/promise');

const app = express();
const PORT = process.env.PORT || 5000;
const GEMINI_API_KEY = process.env.GEMINI_API_KEY || '';

// Model candidate list with automatic fallback
const PRIMARY_MODEL = process.env.PRIMARY_MODEL || 'gemini-3.5-flash';
const FALLBACK_MODELS = (process.env.FALLBACK_MODELS || 'gemini-3.5-flash-lite,gemini-3.8-flash,gemini-flash-latest')
  .split(',')
  .map(m => m.trim())
  .filter(Boolean);

const MODEL_CHAIN = [PRIMARY_MODEL, ...FALLBACK_MODELS.filter(m => m !== PRIMARY_MODEL)];

// MySQL Connection Pool
let dbPool = null;
try {
  dbPool = mysql.createPool({
    host: process.env.DB_HOST || '127.0.0.1',
    port: Number(process.env.DB_PORT) || 3306,
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASS || '',
    database: process.env.DB_NAME || 'skincheck',
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0
  });
  console.log('✅ MySQL Database Pool initialized for database: ' + (process.env.DB_NAME || 'skincheck'));
} catch (dbInitErr) {
  console.error('❌ Failed to initialize MySQL Pool:', dbInitErr.message);
}

// Middleware
app.use(cors({
  origin: '*',
  methods: ['GET', 'POST', 'OPTIONS'],
  allowedHeaders: ['Content-Type', 'Authorization']
}));
app.use(express.json({ limit: '2mb' }));

/**
 * Query and format active skincare products grouped by concern
 */
async function fetchProductCatalogSummary() {
  if (!dbPool) return '';
  try {
    const [rows] = await dbPool.query(`
      SELECT id, brand, name, category, skin_concern, skin_type, price, ingredients, description, usage_guidance
      FROM products
      WHERE status = 'active'
      ORDER BY FIELD(skin_concern, 'Acne', 'Redness', 'Hyperpigmentation', 'Texture', 'Dryness', 'Sun Protection'), category ASC, id ASC
    `);

    if (!rows || rows.length === 0) return '';

    const grouped = {};
    for (const p of rows) {
      const c = p.skin_concern || 'Lainnya';
      if (!grouped[c]) grouped[c] = [];
      grouped[c].push(p);
    }

    let summaryText = 'KATALOG PRODUK RESMI SKINCHECK (DATABASE REAL-TIME):\n';
    for (const [concern, items] of Object.entries(grouped)) {
      summaryText += `\n[Kategori Masalah Kulit: ${concern.toUpperCase()}]\n`;
      for (const item of items) {
        summaryText += `- [${item.category}] ${item.brand} - ${item.name} (Rp ${Number(item.price).toLocaleString('id-ID')}) | Tipe Kulit: ${item.skin_type} | Kandungan Utama: ${item.ingredients} | Manfaat: ${item.description} | Panduan Pakai: ${item.usage_guidance}\n`;
      }
    }

    return summaryText.trim();
  } catch (err) {
    console.error('[Database] Failed to fetch product catalog summary:', err.message);
    return '';
  }
}

/**
 * Fetch database context based on authentication status:
 * - Guest (not logged in): NO access to user count, stats, or raw database, BUT has access to product catalog
 * - Logged in: ONLY access to that user's own scan records and products catalog
 */
async function getLiveDatabaseContext(user = null) {
  const userId = user?.id ? Number(user.id) : null;
  let userName = user?.name || 'Pengguna';
  let userEmail = user?.email || '';
  let userRole = user?.role || (userId ? 'customer' : 'guest');
  const isLoggedIn = Boolean(userId && userId > 0);

  // Fetch product catalog for all users
  const productSummary = await fetchProductCatalogSummary();

  if (!isLoggedIn) {
    // ── GUEST / UNLOGGED USER ──
    // Strictly NO database statistics, NO user counts, NO raw data
    return {
      connected: Boolean(dbPool),
      isLoggedIn: false,
      isAdmin: false,
      userId: null,
      userName: null,
      userEmail: null,
      userRole: 'guest',
      userSpecificContext: '',
      adminMetricsContext: '',
      productSummary
    };
  }

  // ── LOGGED-IN USER ──
  if (!dbPool) {
    return {
      connected: false,
      isLoggedIn: true,
      isAdmin: userRole === 'admin',
      userId,
      userName,
      userEmail,
      userRole,
      userSpecificContext: 'Database MySQL sedang tidak terhubung.',
      adminMetricsContext: '',
      productSummary
    };
  }

  try {
    // Always fetch latest authoritative name, email, and role from users table
    const [uRows] = await dbPool.query('SELECT id, name, email, role FROM users WHERE id = ?', [userId]);
    if (uRows && uRows.length > 0) {
      userName = uRows[0].name || userName;
      userEmail = uRows[0].email || userEmail;
      userRole = uRows[0].role || userRole;
    }
    const isAdmin = Boolean(userRole === 'admin');

    // ── ADMIN ROLE CONTEXT ──
    if (isAdmin) {
      const [userRows] = await dbPool.query(`
        SELECT 
          COUNT(*) as totalUsers,
          SUM(CASE WHEN role = 'customer' THEN 1 ELSE 0 END) as totalCustomers,
          SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as totalAdmins,
          SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as activeUsers
        FROM users
      `);
      const userStats = userRows[0] || {};

      const [scanRows] = await dbPool.query(`
        SELECT 
          COUNT(s.id) as totalScans,
          ROUND(AVG(a.overall_score), 1) as avgScore,
          ROUND(AVG(a.acne_score), 1) as avgAcne,
          ROUND(AVG(a.redness_score), 1) as avgRedness,
          ROUND(AVG(sr.coverage_score), 1) as avgCoverage
        FROM skin_scans s
        LEFT JOIN analysis_results a ON a.skin_scan_id = s.id
        LEFT JOIN sunscreen_results sr ON sr.skin_scan_id = s.id
      `);
      const scanStats = scanRows[0] || {};

      const totalCust = Number(userStats.totalCustomers) || 0;
      const totalAdm = Number(userStats.totalAdmins) || 0;
      const totalAll = Number(userStats.totalUsers) || 0;
      const totalScn = Number(scanStats.totalScans) || 0;
      const avgSc = Number(scanStats.avgScore) || 0;

      const [prodRows] = await dbPool.query("SELECT COUNT(*) as totalProducts FROM products WHERE status = 'active'");
      const totalProducts = Number(prodRows[0]?.totalProducts) || 0;

      const adminMetricsContext = `
FAKTA RESMI DATABASE SKINCHECK (DATA REAL-TIME UNTUK ADMINISTRATOR - HARUS DIGUNAKAN SECARA PRESISI):
- Total Pelanggan / Customer Terdaftar: ${totalCust} orang
- Total Administrator Sistem: ${totalAdm} orang
- Total Keseluruhan Pengguna: ${totalAll} akun
  * Akun Aktif: ${Number(userStats.activeUsers) || 0} akun
- Total Pemindaian (Scan Wajah & Sunscreen) Selesai: ${totalScn} kali pemindaian
- Rata-rata Skor Kesehatan Kulit Pelanggan: ${avgSc}/100
- Rata-rata Skor Jerawat: ${Number(scanStats.avgAcne) || 0}
- Rata-rata Skor Kemerahan: ${Number(scanStats.avgRedness) || 0}
- Rata-rata Tingkat Cakupan Sunscreen: ${Number(scanStats.avgCoverage) || 0}%
- Jumlah Katalog Produk Skincare Aktif: ${totalProducts} produk

PERINGATAN SANGAT KETAT (ANTI-HALUSINASI ANGKA):
Ketika Administrator bertanya berapa jumlah customer yang daftar atau data platform lainnya:
- Anda HANYA BOLEH MENYEBUTKAN ANGKA FAKTUEL DI ATAS: Total customer yang terdaftar saat ini adalah ${totalCust} orang.
- DILARANG KERAS MENGARANG ATAU MENYEBUT ANGKA LAIN (misalnya ribuan, 15k, 10.000, dsb). Jika di database hanya ada ${totalCust} customer, maka sebutkan dengan jujur dan tepat bahwa ada ${totalCust} orang customer terdaftar!
`;

      return {
        connected: true,
        isLoggedIn: true,
        isAdmin: true,
        userId,
        userName,
        userEmail,
        userRole,
        adminMetricsContext,
        productSummary
      };
    }

    // ── CUSTOMER ROLE CONTEXT (Only personal scan) ──
    let userSpecificContext = '';
    userSpecificContext += `DATA PENGGUNA AKTIF: Nama: ${userName} (Email: ${userEmail}, Role: Customer)`;

    const [recentScan] = await dbPool.query(`
      SELECT 
        s.id as scanId,
        s.created_at as scanDate,
        a.overall_score,
        a.acne_score,
        a.redness_score,
        a.texture_score,
        a.hyperpigmentation_score,
        a.ai_response,
        sr.coverage_score,
        sr.coverage_status,
        sr.low_coverage_areas
      FROM skin_scans s
      LEFT JOIN analysis_results a ON a.skin_scan_id = s.id
      LEFT JOIN sunscreen_results sr ON sr.skin_scan_id = s.id
      WHERE s.user_id = ?
      ORDER BY s.created_at DESC
      LIMIT 1
    `, [userId]);

    if (recentScan && recentScan.length > 0) {
      const sc = recentScan[0];
      userSpecificContext += `\nHASIL PEMINDAIAN (SCAN) TERAKHIR ANDA (${new Date(sc.scanDate).toLocaleDateString('id-ID')}):
- Skor Keseluruhan Kulit: ${sc.overall_score ?? 'N/A'}/100
- Skor Jerawat (Acne): ${sc.acne_score ?? 'N/A'} (makin rendah makin baik)
- Skor Kemerahan (Redness): ${sc.redness_score ?? 'N/A'}
- Skor Tekstur: ${sc.texture_score ?? 'N/A'}
- Hiperpigmentasi: ${sc.hyperpigmentation_score ?? 'N/A'}
- Status Proteksi Sunscreen: ${sc.coverage_status ?? 'N/A'} (Skor Cakupan: ${sc.coverage_score ?? 0}%)
- Catatan Diagnostik Sistem: ${sc.ai_response ?? 'Kondisi kulit normal.'}`;
    } else {
      userSpecificContext += '\nPENGGUNA BELUM MEMILIKI RIWAYAT SCAN di akun ini.';
    }

    return {
      connected: true,
      isLoggedIn: true,
      isAdmin: false,
      userId,
      userName,
      userEmail,
      userRole,
      userSpecificContext,
      adminMetricsContext: '',
      productSummary
    };

  } catch (err) {
    console.error('[Database] Error querying user context:', err.message);
    return {
      connected: false,
      isLoggedIn: true,
      isAdmin: userRole === 'admin',
      userId,
      userName,
      userEmail,
      userRole,
      userSpecificContext: `Gagal membaca data scan: ${err.message}`,
      adminMetricsContext: '',
      productSummary: ''
    };
  }
}

/**
 * Build dynamic system prompt with strict privacy guardrails
 */
function buildSystemPrompt(liveDbContext) {
  const { isLoggedIn, isAdmin, userName, userEmail, userSpecificContext, adminMetricsContext, productSummary } = liveDbContext;

  let authContextInstruction = '';

  if (!isLoggedIn) {
    // ── GUEST / UNAUTHENTICATED RULE ──
    authContextInstruction = `
STATUS OTORISASI: PENGUNJUNG TAMU / BELUM LOGIN (HALAMAN BERANDA UTAMA PUBLIK).

ATURAN PERTANYAAN IDENTITAS UNTUK PENGUNJUNG TAMU:
- JIKA PENGUNJUNG BERTANYA TENTANG IDENTITASNYA (misalnya: "siapa saya?", "nama saya siapa?", "akun siapa ini?", "apakah kamu kenal saya?"):
  * JAWABLAH SECARA RAMAH DAN LUGAS:
    "Saat ini Anda belum masuk (login) ke sistem dan sedang mengakses platform SkinCheck sebagai Pengunjung Tamu (Guest). Oleh karena itu, saya belum mengenali nama akun Anda. Silakan login atau mendaftar akun terlebih dahulu jika Anda ingin mengakses profil dan riwayat kesehatan kulit Anda secara personal. Namun, saya tetap siap membantu Anda dengan konsultasi seputar kesehatan kulit, jerawat, tipe kulit, atau rekomendasi sunscreen! 😊"

ATURAN PRIVASI & AKSES DATABASE UNTUK PENGUNJUNG BELUM LOGIN (SANGAT KETAT):
1. Anda DILARANG KERAS menampilkan data database, statistik internal, jumlah pengguna terdaftar, jumlah scan, tabel, atau raw data database apapun kepada pengguna ini.
2. JIKA PENGGUNA BERTANYA TENTANG JUMLAH PENGGUNA/CUSTOMER ATAU DATABASE (misalnya: "ada berapa customer yang daftar?", "berapa penggunanya?", "apakah bisa melihat database?", "bisa lihat datanya?", "berapa banyak penggunanya?"):
   - ANDA WAJIB MENOLAK DENGAN RAMAH:
     "Mohon maaf, demi menjaga privasi dan keamanan pengguna, data statistik pendaftaran dan database sistem SkinCheck tidak dapat diakses secara terbuka untuk publik. Anda saat ini belum masuk (login) ke sistem. Silakan login terlebih dahulu jika Anda ingin mengakses konsultasi berbasis data kulit Anda sendiri. Namun, saya tetap siap membantu pertanyaan seputar kesehatan kulit, jerawat, tipe kulit, atau rekomendasi sunscreen! 😊"
3. DILARANG KERAS MENYEBUTKAN ANGKA PERKIRAAN ATAU MENGARANG ANGKA APAPUN (DILARANG MENYEBUT 15k, RIBUAN, ATAU ESTIMASI LAIN)!
4. Anda HANYA BOLEH menjawab seputar edukasi kesehatan kulit, tipe kulit, kandungan skincare, aturan sunscreen, dan panduan fitur SkinCheck secara umum.
`;
  } else if (isAdmin) {
    // ── ADMIN ROLE RULE ──
    authContextInstruction = `
STATUS OTORISASI: ADMINISTRATOR SISTEM SKINCHECK (Nama: ${userName}, Email: ${userEmail || 'admin@skincheck.com'}, Role: Admin Portal).
PERAN SPESIFIK: Anda adalah "SkinCheck AI Executive & Dermatology Assistant" resmi untuk Administrator / Pengelola Platform SkinCheck.

ATURAN PERTANYAAN IDENTITAS UNTUK ADMINISTRATOR:
- JIKA ADMINISTRATOR BERTANYA IDENTITAS DIRI / AKUN (misalnya: "siapa saya?", "nama saya siapa?", "akun siapa ini?", "siapa yang login?"):
  * JAWAB LANGSUNG to-the-point dan spesifik:
    "Anda terautentikasi sebagai Administrator Sistem SkinCheck dengan nama akun **${userName}**${userEmail ? ' (Email: ' + userEmail + ')' : ''}."

HAK AKSES METRIK SISTEM ADMINISTRATOR:
Sebagai Administrator resmi, pengguna ini MEMILIKI HAK AKSES RESMI untuk memantau ringkasan metrik, performa sistem, dan statistik pemindaian platform SkinCheck.
${adminMetricsContext}

DAFTAR PRODUK SKINCARE DI SISTEM:
${productSummary || 'Katalog produk aktif di sistem.'}

ATURAN MENJAWAB UNTUK ADMINISTRATOR:
1. Ketika Administrator bertanya tentang data platform, statistik user/customer, jumlah scan, rata-rata skor kulit, atau status katalog produk:
   - Jawablah secara lengkap, terstruktur, lugas, dan akurat menggunakan data metrik di atas.
   - Jika ditanya berapa customer yang daftar, sebutkan secara tegas dan jujur angka sebenarnya yang tercantum di atas tanpa dilebih-lebihkan.
2. DILARANG KERAS MENGARANG ANGKA (seperti 15k atau ribuan) jika di database tercatat angka berbeda.
3. Konsultasi Klinis & Formulasi Produk:
   - Bantu Administrator menganalisis kecocokan produk skincare di katalog dengan permasalahan kulit umum pelanggan.
4. Keamanan:
   - Tetap jangan membocorkan password_hash akun pengguna manapun.
`;
  } else {
    // ── CUSTOMER ROLE RULE ──
    const hasScanData = userSpecificContext && userSpecificContext.includes('HASIL PEMINDAIAN');
    authContextInstruction = `
STATUS OTORISASI: PENGGUNA TERAUTENTIKASI / SUDAH LOGIN (Nama: ${userName}, Email: ${userEmail || 'Terdaftar'}, Role: Customer).

ATURAN PERTANYAAN IDENTITAS DIRI & AKUN CUSTOMER (SANGAT PENTING & PRIORITAS TINGGI):
- KETIKA CUSTOMER BERTANYA TENTANG IDENTITASNYA, NAMANYA, ATAU AKUNNYA (misalnya: "siapa saya?", "nama saya siapa?", "siapa nama saya?", "akun siapa ini?", "siapa saya di aplikasi ini?", "saya siapa?", "kamu kenal saya?", "siapa yang login?", "nama akun saya apa?", "akun saya apa?"):
  * ANDA WAJIB MENJAWAB SECARA LANGSUNG, SPESIFIK, DAN JELAS dengan menyebutkan nama akun customer yang sedang login: "${userName}".
  * Sebutkan juga alamat email terdaftar (${userEmail || 'yang tertera pada akun Anda'}) dan status perannya sebagai Pelanggan / Customer aktif SkinCheck.
  * Sebutkan pula secara singkat status pemindaian kulitnya:
    ${hasScanData ? '- Sebutkan bahwa riwayat hasil pemindaian kulit terakhir miliknya tercatat pada sistem.' : '- Sebutkan bahwa akun ini saat ini belum memiliki riwayat pemindaian (scan) wajah.'}
  * Contoh pola jawaban yang lugas dan tepat:
    "Anda login sebagai **${userName}** dengan email terdaftar **${userEmail || ''}**, berstatus sebagai pelanggan (customer) resmi SkinCheck. ${hasScanData ? 'Riwayat pemindaian kulit Anda tercatat di sistem kami.' : 'Saat ini Anda belum memiliki riwayat pemindaian wajah di akun ini.'} Ada yang ingin Anda konsultasikan seputar kondisi kulit atau rekomendasi produk perawatan untuk Anda hari ini?"
  * DILARANG KERAS menjawab secara ambigu, mengabaikan nama pengguna, atau hanya menjawab secara umum tanpa menyebutkan nama spesifik "${userName}"!

ATURAN PRIVASI & AKSES DATABASE UNTUK CUSTOMER:
1. KETIKA CUSTOMER BERTANYA TENTANG JUMLAH CUSTOMER / STATISTIK SISTEM (misalnya: "ada berapa customer yang daftar?", "berapa pengguna SkinCheck?", "berapa orang yang pakai aplikasi ini?"):
   - JAWABLAH DENGAN RAMAH & TEGAS:
     "Mohon maaf, demi menjaga privasi dan keamanan seluruh pengguna, statistik pendaftaran dan data sistem secara keseluruhan hanya dapat diakses secara internal oleh Administrator platform. Sebagai customer, saya hanya dapat mengakses dan menampilkan data profil serta riwayat pemindaian (scan) kulit milik Anda sendiri. Ada yang ingin Anda tanyakan seputar kondisi kulit atau rekomendasi skincare untuk Anda? 😊"
   - DILARANG KERAS MENGARANG ATAU MENYEBUT ANGKA LAIN (SEPERTI 15k, RIBUAN, ATAU ESTIMASI LAIN)!
2. KETIKA CUSTOMER BERTANYA APAKAH BISA MELIHAT DATABASE ("apakah bisa melihat database?", "bisa lihat data saya?"):
   - JAWABLAH DENGAN RAMAH & TEGAS:
     "Bisa, namun demi privasi dan keamanan data, saya hanya dapat mengakses dan melihat data yang berkaitan dengan riwayat pemindaian (scan) dan profil kesehatan kulit Anda sendiri di SkinCheck. Saya tidak dapat mengakses atau menampilkan data pengguna lain maupun database sistem secara keseluruhan."
3. DATA RIWAYAT SCAN MILIK ${userName} YANG DAPAT ANDA AKSES:
${userSpecificContext}
4. PRODUK TERSEDIA DI SISTEM (UNTUK REKOMENDASI):
${productSummary || 'Katalog produk tersedia di menu Cosmetic Finder.'}
5. Anda DAPAT menganalisis perkembangan skor jerawat, kemerahan, dan sunscreen coverage milik ${userName} berdasarkan hasil scan terakhir miliknya di atas.
6. Anda DILARANG KERAS menampilkan data pengguna lain atau raw data tabel database.
`;
  }

  return `
PERAN & IDENTITAS RESMI:
Anda adalah "Konsultan Dermatologi Klinis SkinCheck", spesialis edukasi kesehatan kulit dan kurasi perawatan dermatologis resmi pada platform SkinCheck.

${authContextInstruction}

KATALOG PRODUK SKINCARE RESMI SKINCHECK (DATA REAL-TIME DATABASE):
${productSummary || 'Katalog produk aktif tersedia di sistem.'}

PANDUAN & ATURAN REKOMENDASI PRODUK SKINCARE:
1. Ketika pengguna bertanya tentang rekomendasi produk skincare, solusi permasalahan kulit (jerawat, bruntusan, kemerahan, iritasi, skin barrier rusak, noda hitam/flek, tekstur kasar, pori-pori besar, kulit kering, dehidrasi), atau perlindungan sunscreen:
   - WAJIB PRIORITASKAN merekomendasikan produk-produk yang ADA DI DALAM KATALOG RESMI SKINCHECK di atas!
   - Jelaskan dengan rapi dan terstruktur:
     * Nama Produk & Brand resmi
     * Kisaran Harga resmi (Rp)
     * Bahan Aktif Utama & Alasan kenapa produk tersebut cocok untuk kondisi kulitnya (misal: Salicylic Acid untuk jerawat, Centella/Ceramide untuk kemerahan & skin barrier, Niacinamide/Arbutin untuk flek hitam)
     * Waktu & Urutan Pemakaian (pagi/malam, sebelum/sesudah apa)
   - Susun urutan rekomendasi skincare secara logis dan teratur: Pembersih (Cleanser) -> Toner -> Serum -> Pelembap (Moisturizer) -> Tabir Surya (Sunscreen untuk pagi hari).
   - DILARANG MENGARANG produk fiktif atau menyarankan merek luar jika di katalog SkinCheck sudah tersedia produk yang sesuai!
2. Jika pengguna memiliki riwayat hasil scan (untuk pengguna customer yang sudah login dengan data scan):
   - Kaitkan rekomendasi produk secara personal dengan skor indikator hasil scan mereka (misal: "Berdasarkan hasil scan Anda dengan indikasi jerawat skor 45 dan kemerahan 30, kombinasi produk SkinCheck yang sangat disarankan adalah...").
3. Berikan edukasi pendukung seperti anjuran patch test bagi pemilik kulit sensitif dan disclaimer medis bila terdapat peradangan hebat.

ATURAN BATASAN KETAT (STRICT GUARDRAIL):
1. RUANG LINGKUP EKSKLUSIF: Anda HANYA diperbolehkan menjawab pertanyaan yang secara langsung berkaitan dengan:
   - Identitas akun pengguna yang sedang aktif (nama akun terdaftar, email, status customer/admin, dan riwayat scan pengguna).
   - Kesehatan kulit wajah dan tubuh (masalah jerawat/acne, kemerahan/redness, flek hitam/hyperpigmentation, tekstur kulit, kerutan, komedo, hidrasi, dan perbaikan skin barrier).
   - Panduan perawatan kulit (skincare routine, urutan pemakaian, tipe kulit: kering, berminyak, kombinasi, sensitif, normal).
   - Perlindungan tabir surya (sunscreen, SPF, PA, proteksi radiasi sinar UV, takaran 2 jari, reapply berkala, physical vs chemical).
   - Bahan aktif skincare (Niacinamide, Salicylic Acid, Hyaluronic Acid, Retinol, Ceramide, Zinc Oxide, Vitamin C, Centella Asiatica, AHA/BHA, dll).
   - Panduan fitur SkinCheck ("Face Scan", "Sunscreen Checker", "Scan History").
   - Sapaan pembuka sopan atau ucapan terima kasih standar yang berkaitan dengan layanan.

2. KEBIJAKAN PENOLAKAN PERTANYAAN DI LUAR TOPIK:
   Jika pengguna menanyakan topik di luar konteks di atas (pemrograman/coding, matematika, politik, sejarah, resep makanan, otomotif, game, crypto/keuangan, dll.):
   - Anda WAJIB MENOLAK secara tegas namun tetap ramah dan profesional.
   - Contoh penolakan: "Mohon maaf, saya adalah Konsultan Kesehatan Kulit SkinCheck yang bertugas membantu seputar edukasi kondisi kulit, perawatan wajah (skincare), perlindungan sunscreen, dan platform SkinCheck. Saya tidak dapat melayani pertanyaan di luar lingkup dermatologi tersebut. Apakah ada yang ingin Anda diskusikan mengenai kesehatan kulit Anda hari ini?"

3. PANDUAN FORMAT & GAYA KOMUNIKASI (SANGAT PENTING):
   - MINIMALKAN PENGGUNAAN EMOJI: Hindari menaburkan emoji di setiap baris/kalimat. Gunakan bahasa yang tenang, dewasa, dan bernuansa klinis.
   - HINDARI GAYA ROBOTIK / SCI-FI: Jangan bersikap seperti robot fiksi ilmiah. Berbicaralah selayaknya praktisi dermatologi atau konsultan estetika profesional yang berbobot dan berbasis literatur medis.
   - PENJELASAN MENDALAM & KONTEKSTUAL (WALL OF TEXT SANGAT DIPERBOLEHKAN): Uraian penjelasan yang panjang, komprehensif, dan kaya konteks sangat diutamakan asalkan terstruktur rapi, relevan secara ilmiah, dan memberikan wawasan mendalam mengenai penyebab biologis (patofisiologi) kondisi kulit serta cara kerja bahan aktif yang dianjurkan.
   - Gunakan Bahasa Indonesia yang baik, teratur, santun, dan mudah dipahami.
   - Sertakan disclaimer medis bila terdapat keluhan radang akut agar diarahkan ke Dokter Spesialis Kulit & Kelamin (Sp.D.V.E).

4. GAYA KOMUNIKASI LANGSUNG & NATURAL (DILARANG NYEPAM SALAM/HALO):
   - JANGAN PERNAH mengawali setiap jawaban dengan kata sapaan berulang seperti "Halo", "Hai", "Halo [Nama]", dsb!
   - LANGSUNG JAWAB to-the-point ke inti pertanyaan secara mengalir, luwes, dan alami tanpa basa-basi salam yang diulang-ulang.
   - PENGECUALIAN: Anda HANYA diperbolehkan menyapa jika pengguna secara khusus baru pertama kali menyapa dengan pesan yang HANYA berisi salam (misal: "Halo", "Hai", "Selamat pagi"). Jika pengguna sedang bertanya hal/data/konsultasi, LANGSUNG SAJA JAWAB penjelasannya tanpa diawali "Halo".
   - PENTING: Larangan salam berulang ini BUKAN berarti dilarang menyebut nama akun pengguna saat pengguna secara sengaja menanyakan identitasnya (seperti "siapa saya?"). Ketika ditanya identitas, sebutkan nama akunnya secara lugas tanpa harus menambahkan sapaan basa-basi di depannya.
`;
}


let dynamicGeminiApiKey = GEMINI_API_KEY;
let dynamicPrimaryModel = PRIMARY_MODEL;
let dynamicFallbackModels = FALLBACK_MODELS;

async function getLiveGeminiConfig() {
  if (dbPool) {
    try {
      const [rows] = await dbPool.query(
        "SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('gemini_api_key', 'gemini_primary_model', 'gemini_fallback_models')"
      );
      for (const row of rows) {
        if (row.setting_key === 'gemini_api_key' && row.setting_value) {
          dynamicGeminiApiKey = row.setting_value.trim();
        }
        if (row.setting_key === 'gemini_primary_model' && row.setting_value) {
          dynamicPrimaryModel = row.setting_value.trim();
        }
        if (row.setting_key === 'gemini_fallback_models' && row.setting_value) {
          dynamicFallbackModels = row.setting_value.split(',').map(m => m.trim()).filter(Boolean);
        }
      }
    } catch (e) {
      // ignore, fallback to default
    }
  }
  return {
    apiKey: dynamicGeminiApiKey,
    chain: Array.from(new Set([dynamicPrimaryModel, ...dynamicFallbackModels]))
  };
}

/**
 * Call Gemini Generative Language API with specific model
 */
async function callGemini(modelName, contents, systemInstructionText, apiKey = dynamicGeminiApiKey) {
  const url = `https://generativelanguage.googleapis.com/v1beta/models/${modelName}:generateContent?key=${apiKey}`;
  
  const payload = {
    contents: contents,
    systemInstruction: {
      parts: [{ text: systemInstructionText }]
    },
    generationConfig: {
      temperature: 0.7,
      topP: 0.95,
      maxOutputTokens: 2048
    }
  };

  const response = await fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(payload)
  });

  const data = await response.json();
  return { status: response.status, ok: response.ok, data };
}

/**
 * Chat completion with fallback mechanism across models
 */
async function generateWithFallback(contents, systemInstructionText) {
  let lastError = null;
  const config = await getLiveGeminiConfig();

  for (const model of config.chain) {
    try {
      console.log(`[Chatbot] Attempting generation with model: ${model}`);
      const result = await callGemini(model, contents, systemInstructionText, config.apiKey);

      if (result.ok && result.data.candidates && result.data.candidates.length > 0) {
        const text = result.data.candidates[0].content?.parts?.[0]?.text;
        if (text) {
          const usage = result.data.usageMetadata || {};
          const promptTokens = Number(usage.promptTokenCount) || Math.ceil((JSON.stringify(contents).length + systemInstructionText.length) / 4);
          const completionTokens = Number(usage.candidatesTokenCount) || Math.ceil(text.length / 4);
          const totalTokens = Number(usage.totalTokenCount) || (promptTokens + completionTokens);

          return {
            text: text.trim(),
            model: model,
            tokens: {
              prompt: promptTokens,
              completion: completionTokens,
              total: totalTokens
            }
          };
        }
      }

      console.warn(`[Chatbot] Model ${model} returned non-success or empty content:`, result.data.error?.message || result.data);
      lastError = result.data.error?.message || `Model ${model} returned empty content`;
    } catch (err) {
      console.error(`[Chatbot] Error requesting model ${model}:`, err.message);
      lastError = err.message;
    }
  }

  throw new Error(`Semua model Gemini mengalami kendala (${lastError}). Silakan coba beberapa saat lagi.`);
}

// ─── API Routes ──────────────────────────────────────────────────

// Root / status
app.get('/', async (req, res) => {
  const liveDb = await getLiveDatabaseContext();
  res.json({
    status: 'online',
    service: 'SkinCheck Database-Aware Gemini AI Chatbot Server (Node.js)',
    version: '1.2.0',
    databaseConnected: liveDb.connected,
    stats: liveDb.stats || null,
    primaryModel: PRIMARY_MODEL,
    supportedModels: MODEL_CHAIN
  });
});

// Health check
app.get('/health', async (req, res) => {
  try {
    const liveDb = await getLiveDatabaseContext();
    res.json({
      status: 'healthy',
      databaseConnected: liveDb.connected,
      primaryModel: PRIMARY_MODEL,
      timestamp: new Date().toISOString()
    });
  } catch (err) {
    res.status(500).json({
      status: 'unhealthy',
      error: err.message
    });
  }
});

// Main Chat Endpoint
app.post('/api/chat', async (req, res) => {
  try {
    const { message, history, user } = req.body;

    if (!message || typeof message !== 'string' || !message.trim()) {
      return res.status(400).json({
        success: false,
        error: 'Pesan (message) wajib diisi.'
      });
    }

    // 1. Fetch live database context (only personal data if user is logged in)
    const liveDbContext = await getLiveDatabaseContext(user);
    const systemPrompt = buildSystemPrompt(liveDbContext);

    // 2. Build Gemini contents array from chat history
    const contents = [];

    if (Array.isArray(history)) {
      for (const item of history) {
        if (!item || !item.text) continue;
        const role = item.role === 'model' || item.role === 'assistant' ? 'model' : 'user';
        contents.push({
          role: role,
          parts: [{ text: String(item.text).trim() }]
        });
      }
    }

    // Append current user message
    contents.push({
      role: 'user',
      parts: [{ text: message.trim() }]
    });

    // 3. Generate AI response with database-awareness
    const startTime = Date.now();
    const result = await generateWithFallback(contents, systemPrompt);
    const durationMs = Date.now() - startTime;

    console.log(`[Chatbot] Success response generated using ${result.model} in ${durationMs}ms (Tokens: ${result.tokens.total})`);

    // 4. Log AI token usage to database for Admin monitoring
    if (dbPool) {
      try {
        const uId = user?.id ? Number(user.id) : null;
        const uRole = user?.role || (uId ? 'customer' : 'guest');
        await dbPool.query(`
          INSERT INTO ai_token_usage 
          (user_id, user_role, feature, model, prompt_tokens, completion_tokens, total_tokens, duration_ms, status)
          VALUES (?, ?, 'chat', ?, ?, ?, ?, ?, 'success')
        `, [uId, uRole, result.model, result.tokens.prompt, result.tokens.completion, result.tokens.total, durationMs]);
      } catch (logErr) {
        console.error('[Chatbot] Failed to log token usage to MySQL:', logErr.message);
      }
    }

    return res.json({
      success: true,
      reply: result.text,
      modelUsed: result.model,
      databaseIntegrated: liveDbContext.connected,
      durationMs: durationMs,
      tokens: result.tokens
    });

  } catch (error) {
    console.error('[Chatbot] /api/chat error:', error);
    return res.status(500).json({
      success: false,
      error: error.message || 'Terjadi kesalahan internal pada server chatbot.'
    });
  }
});

// Start Express Server
app.listen(PORT, () => {
  console.log('====================================================');
  console.log(`🚀 SkinCheck Chatbot Server running on http://localhost:${PORT}`);
  console.log(`🔑 Gemini API Key configured: ${GEMINI_API_KEY.substring(0, 8)}...`);
  console.log(`🤖 Primary Model: ${PRIMARY_MODEL}`);
  console.log(`🔄 Fallback Models: ${FALLBACK_MODELS.join(', ')}`);
  console.log(`🗄️ Database Integration: Active (MySQL on port ${process.env.DB_PORT || 3306})`);
  console.log('====================================================');
});
