# -*- coding: utf-8 -*-
"""
Publication-grade custom SVG vector diagrams for SkinCheck SDD.
Styled with Deep Navy (#1B365D), Ice Blue (#EBF3FA), Soft Mint (#E6F4F1),
and Warm Cream (#FEF9E7) matching the formal STMIK IKMI Cirebon report aesthetic.
"""

NAVY = "#1B365D"
BLUE_FILL = "#EBF3FA"
BLUE_MID = "#D6E6F5"
GOLD_FILL = "#FEF9E7"
GOLD_STROKE = "#B7791F"
GREEN_FILL = "#E8F5E9"
GREEN_STROKE = "#2E7D32"
PURPLE_FILL = "#F3E8FF"
PURPLE_STROKE = "#6B21A8"
GRAY_FILL = "#F1F5F9"
TEXT = "#0F172A"
MUTED = "#475569"

SVG_DEFS = """
<defs>
  <marker id="arr" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="6.5" markerHeight="6.5" orient="auto-start-reverse">
    <path d="M 0 1 L 10 5 L 0 9 z" fill="#1B365D"/>
  </marker>
  <marker id="arr-open" viewBox="0 0 12 12" refX="10" refY="6" markerWidth="8" markerHeight="8" orient="auto">
    <path d="M 1 1 L 10 6 L 1 11 z" fill="#ffffff" stroke="#1B365D" stroke-width="1.3"/>
  </marker>
  <marker id="arr-dash" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="6" markerHeight="6" orient="auto">
    <path d="M 0 1 L 10 5 L 0 9" fill="none" stroke="#1B365D" stroke-width="1.5"/>
  </marker>
</defs>
"""

def esc(s: str) -> str:
    return (s.replace("&", "&amp;")
             .replace("<", "&lt;")
             .replace(">", "&gt;")
             .replace('"', "&quot;"))

def wrap_svg(w: int, h: int, body: str) -> str:
    return f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {w} {h}" width="{w}" height="{h}" style="font-family: Segoe UI, Calibri, Arial, sans-serif;">{SVG_DEFS}{body}</svg>'

def stick_actor(cx: int, cy: int, label_lines: list[str]) -> str:
    parts = [
        f'<circle cx="{cx}" cy="{cy}" r="11" fill="#ffffff" stroke="{NAVY}" stroke-width="1.6"/>',
        f'<line x1="{cx}" y1="{cy+11}" x2="{cx}" y2="{cy+38}" stroke="{NAVY}" stroke-width="1.6"/>',
        f'<line x1="{cx-16}" y1="{cy+20}" x2="{cx+16}" y2="{cy+20}" stroke="{NAVY}" stroke-width="1.6"/>',
        f'<line x1="{cx}" y1="{cy+38}" x2="{cx-13}" y2="{cy+56}" stroke="{NAVY}" stroke-width="1.6"/>',
        f'<line x1="{cx}" y1="{cy+38}" x2="{cx+13}" y2="{cy+56}" stroke="{NAVY}" stroke-width="1.6"/>',
    ]
    for i, line in enumerate(label_lines):
        parts.append(f'<text x="{cx}" y="{cy+72 + i*13}" text-anchor="middle" font-size="10.5" font-weight="700" fill="{TEXT}">{esc(line)}</text>')
    return "\n".join(parts)

def uc_oval(cx: int, cy: int, rx: int, ry: int, lines: list[str], fill: str = BLUE_FILL) -> str:
    parts = [f'<ellipse cx="{cx}" cy="{cy}" rx="{rx}" ry="{ry}" fill="{fill}" stroke="{NAVY}" stroke-width="1.3"/>']
    start_y = cy - (len(lines) - 1) * 6.5 + 3.5
    for i, line in enumerate(lines):
        parts.append(f'<text x="{cx}" y="{start_y + i*13}" text-anchor="middle" font-size="9.5" fill="{TEXT}">{esc(line)}</text>')
    return "\n".join(parts)

def uml_class_box(x: int, y: int, w: int, title: str, stereotype: str, attrs: list[str], methods: list[str]) -> tuple[str, int]:
    header_h = 34 if stereotype else 22
    attr_h = max(16, len(attrs) * 13 + 8) if attrs else 0
    meth_h = max(16, len(methods) * 13 + 8) if methods else 0
    total_h = header_h + attr_h + meth_h
    out = []
    # Outer box
    out.append(f'<rect x="{x}" y="{y}" width="{w}" height="{total_h}" fill="#ffffff" stroke="{NAVY}" stroke-width="1.3"/>')
    # Header
    out.append(f'<rect x="{x}" y="{y}" width="{w}" height="{header_h}" fill="{BLUE_MID}" stroke="{NAVY}" stroke-width="1.3"/>')
    if stereotype:
        out.append(f'<text x="{x+w/2}" y="{y+13}" text-anchor="middle" font-size="8.5" font-style="italic" fill="{NAVY}">{esc(stereotype)}</text>')
        out.append(f'<text x="{x+w/2}" y="{y+26}" text-anchor="middle" font-size="10" font-weight="700" fill="{TEXT}">{esc(title)}</text>')
    else:
        out.append(f'<text x="{x+w/2}" y="{y+15}" text-anchor="middle" font-size="10" font-weight="700" fill="{TEXT}">{esc(title)}</text>')
    cur_y = y + header_h
    if attrs:
        out.append(f'<line x1="{x}" y1="{cur_y}" x2="{x+w}" y2="{cur_y}" stroke="{NAVY}" stroke-width="1"/>')
        for i, a in enumerate(attrs):
            out.append(f'<text x="{x+6}" y="{cur_y + 12 + i*13}" font-size="8.5" fill="{TEXT}">{esc(a)}</text>')
        cur_y += attr_h
    if methods:
        out.append(f'<line x1="{x}" y1="{cur_y}" x2="{x+w}" y2="{cur_y}" stroke="{NAVY}" stroke-width="1"/>')
        for i, m in enumerate(methods):
            out.append(f'<text x="{x+6}" y="{cur_y + 12 + i*13}" font-size="8.5" fill="{TEXT}">{esc(m)}</text>')
    return "\n".join(out), total_h


# =====================================================================
# GAMBAR 1: ARSITEKTUR SISTEM BERLAPIS SKINCHECK
# =====================================================================
def svg_architecture() -> str:
    body = f"""
    <text x="410" y="24" text-anchor="middle" font-size="15" font-weight="700" fill="{NAVY}">Diagram Arsitektur Sistem SKINCHECK</text>

    <!-- LAPISAN 1: KLIEN -->
    <rect x="30" y="38" width="760" height="84" rx="6" fill="{BLUE_FILL}" stroke="{NAVY}" stroke-width="1.4"/>
    <text x="44" y="56" font-size="10.5" font-weight="700" fill="{NAVY}">LAPISAN 1 — KLIEN (Browser Modern • Zero-Install)</text>
    <rect x="44" y="65" width="228" height="46" rx="4" fill="#ffffff" stroke="{NAVY}" stroke-width="1"/>
    <text x="158" y="84" text-anchor="middle" font-size="9.5" font-weight="600" fill="{TEXT}">Halaman Publik, Portal Customer,</text>
    <text x="158" y="99" text-anchor="middle" font-size="9.5" fill="{MUTED}">&amp; Panel Admin (HTML5 / CSS3 / JS)</text>

    <rect x="290" y="65" width="240" height="46" rx="4" fill="#ffffff" stroke="{NAVY}" stroke-width="1"/>
    <text x="410" y="84" text-anchor="middle" font-size="9.5" font-weight="600" fill="{TEXT}">Smart Camera HUD Viewfinder</text>
    <text x="410" y="99" text-anchor="middle" font-size="9.5" fill="{MUTED}">WebRTC + Canvas Luminance Check</text>

    <rect x="548" y="65" width="228" height="46" rx="4" fill="#ffffff" stroke="{NAVY}" stroke-width="1"/>
    <text x="662" y="84" text-anchor="middle" font-size="9.5" font-weight="600" fill="{TEXT}">Chatbot Widget &amp; Sesi</text>
    <text x="662" y="99" text-anchor="middle" font-size="9.5" fill="{MUTED}">PHP Session Cookie + JSON AJAX</text>

    <!-- Arrows 1->2 -->
    <line x1="158" y1="122" x2="158" y2="144" stroke="{NAVY}" stroke-width="1.5" marker-end="url(#arr)"/>
    <line x1="410" y1="122" x2="410" y2="144" stroke="{NAVY}" stroke-width="1.5" marker-end="url(#arr)"/>
    <line x1="662" y1="122" x2="662" y2="144" stroke="{NAVY}" stroke-width="1.5" marker-end="url(#arr)"/>

    <!-- LAPISAN 2: ROUTER & GUARD -->
    <rect x="30" y="146" width="760" height="52" rx="6" fill="{GOLD_FILL}" stroke="{GOLD_STROKE}" stroke-width="1.4"/>
    <text x="44" y="165" font-size="10.5" font-weight="700" fill="{GOLD_STROKE}">LAPISAN 2 — ROUTING &amp; KEAMANAN (app/core/App.php • AuthGuard.php)</text>
    <text x="410" y="185" text-anchor="middle" font-size="9.5" fill="{TEXT}">Front Controller Routing (/controller/method/params) • Proteksi RBAC (Customer / Admin) • Ownership-Scoped Streaming</text>

    <line x1="410" y1="198" x2="410" y2="220" stroke="{NAVY}" stroke-width="1.5" marker-end="url(#arr)"/>

    <!-- LAPISAN 3: CONTROLLERS -->
    <rect x="30" y="222" width="760" height="128" rx="6" fill="{BLUE_FILL}" stroke="{NAVY}" stroke-width="1.4"/>
    <text x="44" y="241" font-size="10.5" font-weight="700" fill="{NAVY}">LAPISAN 3 — SERVER APLIKASI MVC (PHP 8.1+ • Apache XAMPP / Vercel Serverless)</text>

    <rect x="44" y="250" width="228" height="40" rx="4" fill="#ffffff" stroke="{NAVY}" stroke-width="1"/>
    <text x="158" y="267" text-anchor="middle" font-size="9.5" font-weight="700" fill="{NAVY}">AuthController &amp; HomeController</text>
    <text x="158" y="281" text-anchor="middle" font-size="8.8" fill="{MUTED}">login, register, verifyOtp, resetPassword</text>

    <rect x="290" y="250" width="240" height="40" rx="4" fill="#ffffff" stroke="{NAVY}" stroke-width="1"/>
    <text x="410" y="267" text-anchor="middle" font-size="9.5" font-weight="700" fill="{NAVY}">ScanController &amp; SunscreenController</text>
    <text x="410" y="281" text-anchor="middle" font-size="8.8" fill="{MUTED}">submitScan, submitCheck, serveImage, serveHeatmap</text>

    <rect x="548" y="250" width="228" height="40" rx="4" fill="#ffffff" stroke="{NAVY}" stroke-width="1"/>
    <text x="662" y="267" text-anchor="middle" font-size="9.5" font-weight="700" fill="{NAVY}">ChatbotController &amp; AdminController</text>
    <text x="662" y="281" text-anchor="middle" font-size="8.8" fill="{MUTED}">chat, dashboard, aiAssistant, settings, testApiKey</text>

    <rect x="44" y="298" width="732" height="38" rx="4" fill="#ffffff" stroke="{NAVY}" stroke-width="1"/>
    <text x="410" y="314" text-anchor="middle" font-size="9.5" font-weight="700" fill="{NAVY}">CustomerController &amp; 6 Model Relasional (app/models/*)</text>
    <text x="410" y="328" text-anchor="middle" font-size="8.8" fill="{MUTED}">User • Scan • Product • AiTokenUsage • ActivityLog • Setting  |  26 Berkas View PHP (18 Halaman + 8 Template)</text>

    <line x1="210" y1="350" x2="210" y2="372" stroke="{NAVY}" stroke-width="1.5" marker-end="url(#arr)"/>
    <line x1="610" y1="350" x2="610" y2="372" stroke="{NAVY}" stroke-width="1.5" marker-end="url(#arr)"/>

    <!-- LAPISAN 4: LOGIKA BISNIS SOLID & AI -->
    <rect x="30" y="374" width="760" height="122" rx="6" fill="{GREEN_FILL}" stroke="{GREEN_STROKE}" stroke-width="1.4"/>
    <text x="44" y="393" font-size="10.5" font-weight="700" fill="{GREEN_STROKE}">LAPISAN 4 — LOGIKA BISNIS SOLID, COMPUTER VISION &amp; AI ENGINE (app/services/ • chatbot-server/)</text>

    <rect x="44" y="402" width="174" height="62" rx="4" fill="#ffffff" stroke="{GREEN_STROKE}" stroke-width="1"/>
    <text x="131" y="418" text-anchor="middle" font-size="9" font-weight="700" fill="{TEXT}">SkinAnalysisService</text>
    <text x="131" y="432" text-anchor="middle" font-size="8.3" fill="{MUTED}">OnnxSkinAnalysisProvider</text>
    <text x="131" y="444" text-anchor="middle" font-size="8.3" fill="{MUTED}">ComputerVisionSkinProvider</text>
    <text x="131" y="456" text-anchor="middle" font-size="8.3" fill="{MUTED}">(MobileNetV3 predict.py)</text>

    <rect x="230" y="402" width="174" height="62" rx="4" fill="#ffffff" stroke="{GREEN_STROKE}" stroke-width="1"/>
    <text x="317" y="418" text-anchor="middle" font-size="9" font-weight="700" fill="{TEXT}">SunscreenAnalysisService</text>
    <text x="317" y="432" text-anchor="middle" font-size="8.3" fill="{MUTED}">RGBSunscreenProvider</text>
    <text x="317" y="444" text-anchor="middle" font-size="8.3" fill="{MUTED}">UVSunscreenProvider</text>
    <text x="317" y="456" text-anchor="middle" font-size="8.3" fill="{MUTED}">(Heatmap Generator)</text>

    <rect x="416" y="402" width="174" height="62" rx="4" fill="#ffffff" stroke="{GREEN_STROKE}" stroke-width="1"/>
    <text x="503" y="418" text-anchor="middle" font-size="9" font-weight="700" fill="{TEXT}">Node.js Chatbot Engine</text>
    <text x="503" y="432" text-anchor="middle" font-size="8.3" fill="{MUTED}">chatbot-server/server.js</text>
    <text x="503" y="444" text-anchor="middle" font-size="8.3" fill="{MUTED}">Context Injection + Fallback</text>
    <text x="503" y="456" text-anchor="middle" font-size="8.3" fill="{MUTED}">Token Usage Telemetry</text>

    <rect x="602" y="402" width="174" height="62" rx="4" fill="#ffffff" stroke="{GREEN_STROKE}" stroke-width="1"/>
    <text x="689" y="418" text-anchor="middle" font-size="9" font-weight="700" fill="{TEXT}">Security &amp; Comms Services</text>
    <text x="689" y="432" text-anchor="middle" font-size="8.3" fill="{MUTED}">ImageValidator, ImageStorage</text>
    <text x="689" y="444" text-anchor="middle" font-size="8.3" fill="{MUTED}">TelegramService, EmailService</text>
    <text x="689" y="456" text-anchor="middle" font-size="8.3" fill="{MUTED}">FonnteService (WhatsApp)</text>

    <text x="410" y="485" text-anchor="middle" font-size="8.8" fill="{GREEN_STROKE}">Seluruh kueri database memakai PDO Prepared Statements • Kontrak Antarmuka SOLID (DIP &amp; OCP) • Penyimpanan Privat</text>

    <line x1="210" y1="496" x2="210" y2="518" stroke="{NAVY}" stroke-width="1.5" marker-end="url(#arr)"/>
    <line x1="610" y1="496" x2="610" y2="518" stroke="{NAVY}" stroke-width="1.5" marker-end="url(#arr)"/>

    <!-- LAPISAN 5: DATA & EKSTERNAL -->
    <rect x="30" y="520" width="368" height="90" rx="6" fill="{PURPLE_FILL}" stroke="{PURPLE_STROKE}" stroke-width="1.4"/>
    <text x="44" y="539" font-size="10.5" font-weight="700" fill="{PURPLE_STROKE}">DATA &amp; PENYIMPANAN PRIVAT</text>
    <text x="214" y="560" text-anchor="middle" font-size="9.5" font-weight="600" fill="{TEXT}">MySQL 8.0 (InnoDB) — Database skincheck</text>
    <text x="214" y="575" text-anchor="middle" font-size="8.8" fill="{MUTED}">8 Tabel Relasional • 4 Foreign Key • PDO Driver</text>
    <text x="214" y="593" text-anchor="middle" font-size="8.8" fill="{MUTED}">Local Private Storage: storage/scans/ (.htaccess denied)</text>

    <rect x="422" y="520" width="368" height="90" rx="6" fill="{PURPLE_FILL}" stroke="{PURPLE_STROKE}" stroke-width="1.4"/>
    <text x="436" y="539" font-size="10.5" font-weight="700" fill="{PURPLE_STROKE}">LAYANAN EKSTERNAL (HTTPS REST API)</text>
    <text x="606" y="560" text-anchor="middle" font-size="9.5" font-weight="600" fill="{TEXT}">Google Gemini Flash API (LLM &amp; Admin Analytics)</text>
    <text x="606" y="575" text-anchor="middle" font-size="8.8" fill="{MUTED}">Telegram Bot API (@Kode_OTP_SkinCheckBot)</text>
    <text x="606" y="593" text-anchor="middle" font-size="8.8" fill="{MUTED}">Resend Email API &amp; Fonnte WhatsApp OTP Gateway</text>

    <!-- DEVOPS BLOCK -->
    <rect x="30" y="624" width="760" height="50" rx="6" fill="{GRAY_FILL}" stroke="{MUTED}" stroke-width="1.3"/>
    <text x="44" y="643" font-size="10" font-weight="700" fill="{NAVY}">DEVOPS &amp; CI/CD</text>
    <text x="410" y="659" text-anchor="middle" font-size="9" fill="{TEXT}">GitHub Actions (.github/workflows/main.yml): PHP 8.2 Lint → Node 18 Check → Python 3.10 Compile → 42 Unit Tests (run_all_tests.php) • Status: HIJAU</text>
    """
    return wrap_svg(820, 686, body)


# =====================================================================
# GAMBAR 2: USE CASE KESELURUHAN (LANDSCAPE)
# =====================================================================
def svg_usecase_overview() -> str:
    body = f"""
    <text x="475" y="24" text-anchor="middle" font-size="15" font-weight="700" fill="{NAVY}">Diagram Use Case Keseluruhan — Sistem SKINCHECK</text>
    <text x="865" y="45" text-anchor="middle" font-size="9.5" font-style="italic" fill="{MUTED}">Sistem Eksternal (24 Jam)</text>

    <!-- System Boundary -->
    <rect x="160" y="38" width="625" height="450" fill="#ffffff" stroke="{NAVY}" stroke-width="1.8"/>
    <text x="472" y="58" text-anchor="middle" font-size="11.5" font-weight="700" fill="{NAVY}">Sistem SKINCHECK</text>

    <!-- Left Actors -->
    {stick_actor(70, 82, ["Guest", "(Tamu)"])}
    {stick_actor(70, 228, ["Customer", "(Pelanggan)"])}
    {stick_actor(70, 385, ["Admin", "(Eksekutif)"])}

    <!-- Right External System Actors -->
    {stick_actor(865, 130, ["OTP & Comms", "(Telegram/Email/WA)"])}
    {stick_actor(865, 340, ["AI Services", "(ONNX & Gemini)"])}

    <!-- Ovals Column 1 (x=295) -->
    {uc_oval(295, 105, 96, 26, ["Lihat Beranda &", "Info Fitur (F-01)"])}
    {uc_oval(295, 185, 96, 28, ["Registrasi & Verifikasi", "OTP Multi-Kanal (F-02–F-04)"])}
    {uc_oval(295, 268, 96, 26, ["Login, Reset Sandi", "& Logout (F-05–F-07)"])}
    {uc_oval(295, 350, 96, 26, ["Kelola Profil &", "Avatar (F-08)"])}
    {uc_oval(295, 435, 96, 28, ["Kelola Customer,", "Katalog & Audit (F-25–F-28)"])}

    <!-- Ovals Column 2 (x=515) -->
    {uc_oval(515, 115, 96, 28, ["Pemindaian Sawar Kulit", "4 Pilar via HUD (F-09–F-13)"])}
    {uc_oval(515, 200, 96, 28, ["Cek Kerataan Sunscreen", "& Heatmap (F-14–F-17)"])}
    {uc_oval(515, 285, 96, 28, ["Riwayat Progres &", "Rekomendasi (F-18–F-20)"])}
    {uc_oval(515, 368, 96, 28, ["Chatbot AI Skincare", "Gemini (F-21–F-24)"])}
    {uc_oval(515, 445, 96, 26, ["Monitor Token AI &", "Settings API (F-29–F-34)"])}

    <!-- Ovals Column 3 (x=695) -->
    {uc_oval(695, 240, 74, 28, ["Akses Citra", "Terkontrol (F-19)"])}
    {uc_oval(695, 368, 74, 28, ["Executive AI", "Analytics (F-30)"])}

    <!-- Associations: Guest -->
    <line x1="92" y1="102" x2="199" y2="105" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="92" y1="110" x2="199" y2="180" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="92" y1="118" x2="199" y2="262" stroke="{NAVY}" stroke-width="1.1"/>

    <!-- Associations: Customer -->
    <line x1="92" y1="242" x2="199" y2="190" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="92" y1="250" x2="199" y2="268" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="92" y1="258" x2="199" y2="348" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="92" y1="246" x2="420" y2="122" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="92" y1="252" x2="420" y2="202" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="92" y1="260" x2="420" y2="285" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="92" y1="268" x2="420" y2="365" stroke="{NAVY}" stroke-width="1.1"/>

    <!-- Associations: Admin -->
    <line x1="92" y1="400" x2="199" y2="276" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="92" y1="412" x2="199" y2="435" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="92" y1="418" x2="420" y2="445" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="92" y1="408" x2="622" y2="374" stroke="{NAVY}" stroke-width="1.1"/>

    <!-- Internal links -->
    <line x1="611" y1="280" x2="624" y2="255" stroke="{NAVY}" stroke-width="1" stroke-dasharray="4,3"/>

    <!-- External links: OTP -->
    <line x1="391" y1="185" x2="843" y2="152" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="391" y1="265" x2="843" y2="162" stroke="{NAVY}" stroke-width="1.1"/>

    <!-- External links: AI Services -->
    <line x1="611" y1="118" x2="843" y2="345" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="611" y1="204" x2="843" y2="352" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="611" y1="368" x2="843" y2="362" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="769" y1="368" x2="843" y2="368" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="611" y1="445" x2="843" y2="375" stroke="{NAVY}" stroke-width="1.1"/>
    """
    return wrap_svg(950, 505, body)


# =====================================================================
# GAMBAR 3: USE CASE — GUEST
# =====================================================================
def svg_usecase_guest() -> str:
    body = f"""
    <text x="350" y="24" text-anchor="middle" font-size="14" font-weight="700" fill="{NAVY}">Diagram Use Case — Guest (Tamu)</text>
    <rect x="175" y="40" width="490" height="270" fill="#ffffff" stroke="{NAVY}" stroke-width="1.6"/>
    <text x="420" y="60" text-anchor="middle" font-size="11" font-weight="700" fill="{NAVY}">Sistem SKINCHECK</text>

    {stick_actor(80, 145, ["Guest (Tamu)"])}

    {uc_oval(310, 108, 108, 25, ["Lihat Beranda &", "Edukasi Fitur (F-01)"])}
    {uc_oval(545, 108, 100, 25, ["Daftar Akun Baru", "(F-02)"])}
    {uc_oval(310, 182, 108, 25, ["Verifikasi OTP", "Telegram/Email/WA (F-03)"])}
    {uc_oval(545, 182, 100, 25, ["Masuk ke Akun", "(Login) (F-05)"])}
    {uc_oval(420, 258, 120, 25, ["Minta Reset Kata Sandi", "via Tautan/Token (F-06)"])}

    <line x1="100" y1="162" x2="203" y2="112" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="100" y1="165" x2="446" y2="115" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="100" y1="170" x2="203" y2="182" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="100" y1="174" x2="446" y2="182" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="100" y1="180" x2="302" y2="255" stroke="{NAVY}" stroke-width="1.1"/>
    """
    return wrap_svg(700, 325, body)


# =====================================================================
# GAMBAR 4: USE CASE — CUSTOMER
# =====================================================================
def svg_usecase_customer() -> str:
    body = f"""
    <text x="360" y="24" text-anchor="middle" font-size="14" font-weight="700" fill="{NAVY}">Diagram Use Case — Customer (Pelanggan)</text>
    <rect x="175" y="40" width="510" height="430" fill="#ffffff" stroke="{NAVY}" stroke-width="1.6"/>
    <text x="430" y="60" text-anchor="middle" font-size="11" font-weight="700" fill="{NAVY}">Sistem SKINCHECK</text>

    {stick_actor(80, 215, ["Customer", "(Pelanggan)"])}

    {uc_oval(305, 100, 108, 24, ["Lihat Dashboard &", "Ringkasan Skor (F-18)"])}
    {uc_oval(555, 100, 108, 24, ["Kelola Profil &", "Upload Avatar (F-08)"])}

    {uc_oval(305, 165, 108, 25, ["Buka Kamera HUD &", "Validasi Cahaya (F-09)"])}
    {uc_oval(555, 165, 108, 25, ["Analisis 4 Pilar", "Sawar Kulit AI (F-10–F-12)"])}

    {uc_oval(305, 232, 108, 25, ["Cek Kerataan", "Sunscreen (F-14–F-16)"])}
    {uc_oval(555, 232, 108, 25, ["Lihat Heatmap &", "Area Rawan UV (F-17)"])}

    {uc_oval(305, 298, 108, 25, ["Lihat Riwayat &", "Detail Diagnostik (F-19)"])}
    {uc_oval(555, 298, 108, 25, ["Lihat Rekomendasi", "Produk Skincare (F-20)"])}

    {uc_oval(305, 365, 108, 25, ["Konsultasi Chatbot AI", "Gemini (F-21–F-24)"])}
    {uc_oval(555, 365, 108, 25, ["Coba Ulang Analisis", "Gagal (Retry) (F-13)"])}

    {uc_oval(430, 428, 105, 22, ["Keluar dari Akun", "(Logout) (F-07)"])}

    <line x1="100" y1="230" x2="198" y2="105" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="100" y1="232" x2="448" y2="108" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="100" y1="236" x2="198" y2="168" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="100" y1="239" x2="448" y2="170" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="100" y1="242" x2="198" y2="232" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="100" y1="245" x2="448" y2="234" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="100" y1="248" x2="198" y2="298" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="100" y1="251" x2="448" y2="298" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="100" y1="255" x2="198" y2="365" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="100" y1="258" x2="448" y2="365" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="100" y1="262" x2="326" y2="426" stroke="{NAVY}" stroke-width="1.1"/>
    """
    return wrap_svg(710, 485, body)


# =====================================================================
# GAMBAR 5: USE CASE — ADMIN
# =====================================================================
def svg_usecase_admin() -> str:
    body = f"""
    <text x="360" y="24" text-anchor="middle" font-size="14" font-weight="700" fill="{NAVY}">Diagram Use Case — Admin (Administrator Eksekutif)</text>
    <rect x="175" y="40" width="510" height="390" fill="#ffffff" stroke="{NAVY}" stroke-width="1.6"/>
    <text x="430" y="60" text-anchor="middle" font-size="11" font-weight="700" fill="{NAVY}">Sistem SKINCHECK</text>

    {stick_actor(80, 195, ["Admin", "(Eksekutif)"])}

    {uc_oval(305, 102, 108, 24, ["Masuk Panel Admin", "(F-05, F-25)"])}
    {uc_oval(555, 102, 108, 24, ["Lihat Executive KPI", "Dashboard (F-25)"])}

    {uc_oval(305, 170, 108, 25, ["Kelola Akun Customer", "(Registry) (F-26)"])}
    {uc_oval(555, 170, 108, 25, ["Kelola Katalog", "Produk Skincare (F-27)"])}

    {uc_oval(305, 238, 108, 25, ["Tinjau Audit Log", "Pemindaian (F-28)"])}
    {uc_oval(555, 238, 108, 25, ["Pantau Konsumsi Token", "Gemini Live (F-29)"])}

    {uc_oval(305, 306, 108, 25, ["Konsultasi Executive", "AI Analytics (F-30)"])}
    {uc_oval(555, 306, 108, 25, ["Ubah Konfigurasi API", "Terpusat (F-31–F-33)"])}

    {uc_oval(305, 376, 108, 25, ["Uji Koneksi Live AJAX", "Telegram/Gemini/WA (F-34)"])}
    {uc_oval(555, 376, 108, 24, ["Keluar dari Panel", "(Logout) (F-07)"])}

    <line x1="100" y1="210" x2="198" y2="106" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="100" y1="213" x2="448" y2="108" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="100" y1="216" x2="198" y2="172" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="100" y1="219" x2="448" y2="174" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="100" y1="223" x2="198" y2="238" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="100" y1="226" x2="448" y2="238" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="100" y1="230" x2="198" y2="304" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="100" y1="233" x2="448" y2="304" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="100" y1="238" x2="198" y2="374" stroke="{NAVY}" stroke-width="1.1"/>
    <line x1="100" y1="242" x2="448" y2="374" stroke="{NAVY}" stroke-width="1.1"/>
    """
    return wrap_svg(710, 445, body)


# =====================================================================
# GAMBAR 6: ACTIVITY DIAGRAM (SWIMLANE) — PEMINDAIAN SAWAR KULIT
# =====================================================================
def svg_activity_face_scan() -> str:
    body = f"""
    <text x="380" y="22" text-anchor="middle" font-size="14" font-weight="700" fill="{NAVY}">Diagram Activity — Alur Pemindaian Sawar Kulit (F-09 s.d. F-13)</text>

    <!-- Swimlanes -->
    <rect x="20" y="36" width="230" height="535" fill="#ffffff" stroke="{NAVY}" stroke-width="1.3"/>
    <rect x="250" y="36" width="250" height="535" fill="#ffffff" stroke="{NAVY}" stroke-width="1.3"/>
    <rect x="500" y="36" width="240" height="535" fill="#ffffff" stroke="{NAVY}" stroke-width="1.3"/>

    <rect x="20" y="36" width="230" height="26" fill="{BLUE_MID}" stroke="{NAVY}" stroke-width="1.3"/>
    <rect x="250" y="36" width="250" height="26" fill="{BLUE_MID}" stroke="{NAVY}" stroke-width="1.3"/>
    <rect x="500" y="36" width="240" height="26" fill="{BLUE_MID}" stroke="{NAVY}" stroke-width="1.3"/>

    <text x="135" y="53" text-anchor="middle" font-size="10.5" font-weight="700" fill="{NAVY}">Customer (Browser HUD)</text>
    <text x="375" y="53" text-anchor="middle" font-size="10.5" font-weight="700" fill="{NAVY}">ScanController &amp; Validator</text>
    <text x="620" y="53" text-anchor="middle" font-size="10.5" font-weight="700" fill="{NAVY}">AI Engine (ONNX/CV) &amp; DB</text>

    <!-- Start Node -->
    <circle cx="135" cy="82" r="9" fill="{TEXT}"/>
    <line x1="135" y1="91" x2="135" y2="108" stroke="{NAVY}" stroke-width="1.3" marker-end="url(#arr)"/>

    <!-- Step 1 -->
    <rect x="40" y="110" width="190" height="38" rx="10" fill="{BLUE_FILL}" stroke="{NAVY}" stroke-width="1.2"/>
    <text x="135" y="126" text-anchor="middle" font-size="9" fill="{TEXT}">Buka Face Scan &amp; Aktifkan</text>
    <text x="135" y="139" text-anchor="middle" font-size="9" fill="{TEXT}">Kamera WebRTC (F-09)</text>

    <line x1="135" y1="148" x2="135" y2="168" stroke="{NAVY}" stroke-width="1.3" marker-end="url(#arr)"/>

    <!-- Decision 1: Lighting -->
    <polygon points="135,170 200,196 135,222 70,196" fill="{GOLD_FILL}" stroke="{GOLD_STROKE}" stroke-width="1.3"/>
    <text x="135" y="194" text-anchor="middle" font-size="8.5" font-weight="600" fill="{TEXT}">Cahaya &amp; Oval</text>
    <text x="135" y="205" text-anchor="middle" font-size="8.5" font-weight="600" fill="{TEXT}">Ideal?</text>

    <!-- Loop back Tidak -->
    <polyline points="70,196 32,196 32,129 38,129" fill="none" stroke="{NAVY}" stroke-width="1.1" marker-end="url(#arr)"/>
    <text x="50" y="190" font-size="8" fill="{MUTED}">Tidak</text>

    <!-- Ya -> Snap -->
    <line x1="135" y1="222" x2="135" y2="246" stroke="{NAVY}" stroke-width="1.3" marker-end="url(#arr)"/>
    <text x="143" y="238" font-size="8" fill="{MUTED}">Ya</text>

    <rect x="40" y="248" width="190" height="38" rx="10" fill="{BLUE_FILL}" stroke="{NAVY}" stroke-width="1.2"/>
    <text x="135" y="264" text-anchor="middle" font-size="9" fill="{TEXT}">Potret Wajah &amp; Kirim POST</text>
    <text x="135" y="277" text-anchor="middle" font-size="9" fill="{TEXT}">/scan/submitScan</text>

    <!-- Arrow to Col 2 -->
    <line x1="230" y1="267" x2="275" y2="267" stroke="{NAVY}" stroke-width="1.3" marker-end="url(#arr)"/>

    <!-- Col 2: ImageValidator -->
    <rect x="277" y="246" width="196" height="42" rx="10" fill="{BLUE_FILL}" stroke="{NAVY}" stroke-width="1.2"/>
    <text x="375" y="263" text-anchor="middle" font-size="9" fill="{TEXT}">Validasi MIME, Ukuran, Resolusi</text>
    <text x="375" y="277" text-anchor="middle" font-size="9" fill="{TEXT}">(ImageValidator) &amp; FaceDetector</text>

    <line x1="375" y1="288" x2="375" y2="310" stroke="{NAVY}" stroke-width="1.3" marker-end="url(#arr)"/>

    <!-- Decision 2: Valid? -->
    <polygon points="375,312 438,336 375,360 312,336" fill="{GOLD_FILL}" stroke="{GOLD_STROKE}" stroke-width="1.3"/>
    <text x="375" y="339" text-anchor="middle" font-size="8.5" font-weight="600" fill="{TEXT}">Citra Valid?</text>

    <!-- Tidak -> Error -->
    <line x1="312" y1="336" x2="225" y2="336" stroke="{NAVY}" stroke-width="1.2" marker-end="url(#arr)"/>
    <text x="272" y="330" font-size="8" fill="{MUTED}">Tidak</text>
    <rect x="40" y="317" width="183" height="38" rx="10" fill="{GOLD_FILL}" stroke="{GOLD_STROKE}" stroke-width="1.2"/>
    <text x="131" y="333" text-anchor="middle" font-size="8.8" fill="{TEXT}">Tampilkan Pesan Gagal</text>
    <text x="131" y="346" text-anchor="middle" font-size="8.8" fill="{TEXT}">&amp; Minta Foto Ulang</text>

    <!-- Ya -> Save Storage -->
    <line x1="375" y1="360" x2="375" y2="384" stroke="{NAVY}" stroke-width="1.3" marker-end="url(#arr)"/>
    <text x="383" y="375" font-size="8" fill="{MUTED}">Ya</text>

    <rect x="277" y="386" width="196" height="42" rx="10" fill="{BLUE_FILL}" stroke="{NAVY}" stroke-width="1.2"/>
    <text x="375" y="403" text-anchor="middle" font-size="9" fill="{TEXT}">Simpan Privat (ImageStorage)</text>
    <text x="375" y="417" text-anchor="middle" font-size="9" fill="{TEXT}">&amp; Buat Record Pending DB</text>

    <!-- Arrow to Col 3 -->
    <polyline points="473,407 490,407 490,145 520,145" fill="none" stroke="{NAVY}" stroke-width="1.3" marker-end="url(#arr)"/>

    <!-- Col 3: AI Inference -->
    <rect x="522" y="122" width="198" height="46" rx="10" fill="{GREEN_FILL}" stroke="{GREEN_STROKE}" stroke-width="1.2"/>
    <text x="621" y="139" text-anchor="middle" font-size="9" font-weight="600" fill="{TEXT}">SkinAnalysisService (F-10)</text>
    <text x="621" y="152" text-anchor="middle" font-size="8.5" fill="{MUTED}">OnnxProvider (MobileNetV3) /</text>
    <text x="621" y="163" text-anchor="middle" font-size="8.5" fill="{MUTED}">Fallback CV Engine (max 2 retry)</text>

    <line x1="621" y1="168" x2="621" y2="195" stroke="{NAVY}" stroke-width="1.3" marker-end="url(#arr)"/>

    <polygon points="621,197 686,223 621,249 556,223" fill="{GOLD_FILL}" stroke="{GOLD_STROKE}" stroke-width="1.3"/>
    <text x="621" y="220" text-anchor="middle" font-size="8.5" font-weight="600" fill="{TEXT}">Inferensi</text>
    <text x="621" y="232" text-anchor="middle" font-size="8.5" font-weight="600" fill="{TEXT}">Sukses?</text>

    <!-- Fail branch -->
    <polyline points="686,223 728,223 728,312 720,312" fill="none" stroke="{NAVY}" stroke-width="1.1" marker-end="url(#arr)"/>
    <text x="698" y="217" font-size="8" fill="{MUTED}">Tidak</text>

    <rect x="522" y="290" width="198" height="42" rx="10" fill="{GOLD_FILL}" stroke="{GOLD_STROKE}" stroke-width="1.2"/>
    <text x="621" y="307" text-anchor="middle" font-size="8.8" fill="{TEXT}">Tandai status='failed' &amp;</text>
    <text x="621" y="320" text-anchor="middle" font-size="8.8" fill="{TEXT}">Aktifkan Tombol Retry (F-13)</text>

    <!-- Success branch -->
    <line x1="621" y1="249" x2="621" y2="352" stroke="{NAVY}" stroke-width="1.3" marker-end="url(#arr)"/>
    <text x="598" y="272" font-size="8" fill="{MUTED}">Ya</text>

    <rect x="522" y="354" width="198" height="48" rx="10" fill="{BLUE_FILL}" stroke="{NAVY}" stroke-width="1.2"/>
    <text x="621" y="370" text-anchor="middle" font-size="8.8" fill="{TEXT}">Hitung 4 Pilar (Acne, Redness,</text>
    <text x="621" y="383" text-anchor="middle" font-size="8.8" fill="{TEXT}">Texture, Hyperpig) + Overall</text>
    <text x="621" y="395" text-anchor="middle" font-size="8.8" fill="{TEXT}">&amp; Simpan ke analysis_results</text>

    <line x1="621" y1="402" x2="621" y2="430" stroke="{NAVY}" stroke-width="1.3" marker-end="url(#arr)"/>

    <rect x="522" y="432" width="198" height="40" rx="10" fill="{BLUE_FILL}" stroke="{NAVY}" stroke-width="1.2"/>
    <text x="621" y="449" text-anchor="middle" font-size="8.8" fill="{TEXT}">Cocokkan Katalog Skincare</text>
    <text x="621" y="462" text-anchor="middle" font-size="8.8" fill="{TEXT}">(Product::getProductsByConcern)</text>

    <!-- Back to Customer result -->
    <polyline points="522,452 225,452" fill="none" stroke="{NAVY}" stroke-width="1.3" marker-end="url(#arr)"/>

    <rect x="40" y="432" width="183" height="42" rx="10" fill="{GREEN_FILL}" stroke="{GREEN_STROKE}" stroke-width="1.3"/>
    <text x="131" y="449" text-anchor="middle" font-size="9" font-weight="600" fill="{TEXT}">Tampilkan Detail Skor 4 Pilar</text>
    <text x="131" y="463" text-anchor="middle" font-size="9" fill="{TEXT}">&amp; Rekomendasi (F-12, F-20)</text>

    <line x1="131" y1="474" x2="131" y2="515" stroke="{NAVY}" stroke-width="1.3" marker-end="url(#arr)"/>
    <polyline points="131,355 131,418 75,418 75,528 118,528" fill="none" stroke="{NAVY}" stroke-width="1" stroke-dasharray="3,2"/>

    <!-- End Node -->
    <circle cx="131" cy="528" r="11" fill="none" stroke="{TEXT}" stroke-width="1.6"/>
    <circle cx="131" cy="528" r="6.5" fill="{TEXT}"/>
    """
    return wrap_svg(760, 582, body)


# =====================================================================
# GAMBAR 7: ACTIVITY DIAGRAM — SUNSCREEN CHECK
# =====================================================================
def svg_activity_sunscreen() -> str:
    body = f"""
    <text x="370" y="22" text-anchor="middle" font-size="14" font-weight="700" fill="{NAVY}">Diagram Activity — Pemeriksaan Kerataan Sunscreen &amp; Heatmap (F-14 s.d. F-17)</text>

    <circle cx="370" cy="46" r="9" fill="{TEXT}"/>
    <line x1="370" y1="55" x2="370" y2="75" stroke="{NAVY}" stroke-width="1.3" marker-end="url(#arr)"/>

    <rect x="195" y="77" width="350" height="36" rx="10" fill="{BLUE_FILL}" stroke="{NAVY}" stroke-width="1.2"/>
    <text x="370" y="99" text-anchor="middle" font-size="9.5" fill="{TEXT}">Pengguna mengoleskan sunscreen &amp; mengambil foto wajah via HUD (F-14)</text>

    <line x1="370" y1="113" x2="370" y2="134" stroke="{NAVY}" stroke-width="1.3" marker-end="url(#arr)"/>

    <rect x="195" y="136" width="350" height="38" rx="10" fill="{BLUE_FILL}" stroke="{NAVY}" stroke-width="1.2"/>
    <text x="370" y="153" text-anchor="middle" font-size="9.2" fill="{TEXT}">[SunscreenController] Validasi gambar (ImageValidator) &amp; simpan privat</text>
    <text x="370" y="166" text-anchor="middle" font-size="8.8" fill="{MUTED}">Panggil SunscreenAnalysisService → RGBSunscreenAnalysisProvider</text>

    <line x1="370" y1="174" x2="370" y2="196" stroke="{NAVY}" stroke-width="1.3" marker-end="url(#arr)"/>

    <rect x="195" y="198" width="350" height="40" rx="10" fill="{GREEN_FILL}" stroke="{GREEN_STROKE}" stroke-width="1.2"/>
    <text x="370" y="215" text-anchor="middle" font-size="9.2" font-weight="600" fill="{TEXT}">[RGB Provider] Segmentasi Kontur Wajah &amp; Analisis Reflektansi RGB</text>
    <text x="370" y="229" text-anchor="middle" font-size="8.8" fill="{MUTED}">Hitung Coverage Score (0–100%), Confidence, &amp; Low Coverage Areas (F-15)</text>

    <line x1="370" y1="238" x2="370" y2="260" stroke="{NAVY}" stroke-width="1.3" marker-end="url(#arr)"/>

    <polygon points="370,262 455,288 370,314 285,288" fill="{GOLD_FILL}" stroke="{GOLD_STROKE}" stroke-width="1.3"/>
    <text x="370" y="286" text-anchor="middle" font-size="8.8" font-weight="600" fill="{TEXT}">Confidence</text>
    <text x="370" y="298" text-anchor="middle" font-size="8.8" font-weight="600" fill="{TEXT}">&ge; 0.60?</text>

    <!-- Tidak branch -->
    <line x1="455" y1="288" x2="545" y2="288" stroke="{NAVY}" stroke-width="1.2" marker-end="url(#arr)"/>
    <text x="495" y="282" font-size="8.5" fill="{MUTED}">Tidak</text>
    <rect x="547" y="266" width="175" height="44" rx="8" fill="{GOLD_FILL}" stroke="{GOLD_STROKE}" stroke-width="1.2"/>
    <text x="634" y="284" text-anchor="middle" font-size="8.8" fill="{TEXT}">Aktifkan Flag Low Confidence</text>
    <text x="634" y="298" text-anchor="middle" font-size="8.5" fill="{MUTED}">+ Catatan Saran Cahaya</text>
    <polyline points="634,310 634,358 547,358" fill="none" stroke="{NAVY}" stroke-width="1.1" marker-end="url(#arr)"/>

    <!-- Ya branch -->
    <line x1="370" y1="314" x2="370" y2="338" stroke="{NAVY}" stroke-width="1.3" marker-end="url(#arr)"/>
    <text x="355" y="329" font-size="8.5" fill="{MUTED}">Ya</text>

    <rect x="195" y="340" width="350" height="40" rx="10" fill="{BLUE_FILL}" stroke="{NAVY}" stroke-width="1.2"/>
    <text x="370" y="357" text-anchor="middle" font-size="9.2" fill="{TEXT}">Bangkitkan Citra Visual Heatmap Gradien (PNG/JPG) &amp; Simpan Privat (F-16)</text>
    <text x="370" y="371" text-anchor="middle" font-size="8.8" fill="{MUTED}">Sematkan Sanggahan Non-UV: "Estimasi visual berbasis AI, bukan UV langsung"</text>

    <line x1="370" y1="380" x2="370" y2="402" stroke="{NAVY}" stroke-width="1.3" marker-end="url(#arr)"/>

    <rect x="195" y="404" width="350" height="38" rx="10" fill="{BLUE_FILL}" stroke="{NAVY}" stroke-width="1.2"/>
    <text x="370" y="421" text-anchor="middle" font-size="9.2" fill="{TEXT}">Simpan ke tabel sunscreen_results &amp; Render Halaman Hasil (F-17)</text>
    <text x="370" y="434" text-anchor="middle" font-size="8.8" fill="{MUTED}">Streaming Heatmap aman via /sunscreen/serveHeatmap (Ownership-Scoped)</text>

    <line x1="370" y1="442" x2="370" y2="466" stroke="{NAVY}" stroke-width="1.3" marker-end="url(#arr)"/>
    <circle cx="370" cy="479" r="11" fill="none" stroke="{TEXT}" stroke-width="1.6"/>
    <circle cx="370" cy="479" r="6.5" fill="{TEXT}"/>
    """
    return wrap_svg(740, 502, body)


# =====================================================================
# GAMBAR 8: ACTIVITY DIAGRAM — CHATBOT AI GEMINI
# =====================================================================
def svg_activity_chatbot() -> str:
    body = f"""
    <text x="370" y="22" text-anchor="middle" font-size="14" font-weight="700" fill="{NAVY}">Diagram Activity — Alur Chat AI Gemini &amp; Audit Token (F-21 s.d. F-24, F-29–F-30)</text>

    <circle cx="370" cy="46" r="9" fill="{TEXT}"/>
    <line x1="370" y1="55" x2="370" y2="75" stroke="{NAVY}" stroke-width="1.3" marker-end="url(#arr)"/>

    <rect x="180" y="77" width="380" height="36" rx="10" fill="{BLUE_FILL}" stroke="{NAVY}" stroke-width="1.2"/>
    <text x="370" y="99" text-anchor="middle" font-size="9.3" fill="{TEXT}">Pengguna (Customer / Admin) mengirim pertanyaan via Widget Chatbot (POST /chatbot/chat)</text>

    <line x1="370" y1="113" x2="370" y2="133" stroke="{NAVY}" stroke-width="1.3" marker-end="url(#arr)"/>

    <rect x="180" y="135" width="380" height="42" rx="10" fill="{BLUE_FILL}" stroke="{NAVY}" stroke-width="1.2"/>
    <text x="370" y="152" text-anchor="middle" font-size="9.2" fill="{TEXT}">[ChatbotController] Resolusi Peran (Customer vs Admin Clearance) &amp; Ambil Konteks DB:</text>
    <text x="370" y="166" text-anchor="middle" font-size="8.6" fill="{MUTED}">Customer → Skor 4 Pilar &amp; Katalog Produk  |  Admin → KPI Global, Tren Keluhan, Katalog</text>

    <line x1="370" y1="177" x2="370" y2="197" stroke="{NAVY}" stroke-width="1.3" marker-end="url(#arr)"/>

    <rect x="180" y="199" width="380" height="38" rx="10" fill="{BLUE_FILL}" stroke="{NAVY}" stroke-width="1.2"/>
    <text x="370" y="216" text-anchor="middle" font-size="9.2" fill="{TEXT}">Susun System Prompt Sadar-Konteks + Baca Konfigurasi API dari tabel settings</text>
    <text x="370" y="229" text-anchor="middle" font-size="8.6" fill="{MUTED}">Kirim payload ke Google Gemini Flash API (langsung PHP cURL / Node.js Microservice)</text>

    <line x1="370" y1="237" x2="370" y2="259" stroke="{NAVY}" stroke-width="1.3" marker-end="url(#arr)"/>

    <polygon points="370,261 460,289 370,317 280,289" fill="{GOLD_FILL}" stroke="{GOLD_STROKE}" stroke-width="1.3"/>
    <text x="370" y="286" text-anchor="middle" font-size="8.8" font-weight="600" fill="{TEXT}">Provider Utama</text>
    <text x="370" y="298" text-anchor="middle" font-size="8.8" font-weight="600" fill="{TEXT}">Berhasil (200)?</text>

    <!-- Fallback branch -->
    <line x1="460" y1="289" x2="545" y2="289" stroke="{NAVY}" stroke-width="1.2" marker-end="url(#arr)"/>
    <text x="496" y="283" font-size="8.5" fill="{MUTED}">Tidak</text>
    <rect x="547" y="263" width="175" height="52" rx="8" fill="{GOLD_FILL}" stroke="{GOLD_STROKE}" stroke-width="1.2"/>
    <text x="634" y="280" text-anchor="middle" font-size="8.6" font-weight="600" fill="{TEXT}">Coba Model Fallback</text>
    <text x="634" y="293" text-anchor="middle" font-size="8.3" fill="{MUTED}">Jika gagal (429/Timeout) →</text>
    <text x="634" y="305" text-anchor="middle" font-size="8.3" fill="{MUTED}">Respons Ramah (F-24)</text>
    <polyline points="634,315 634,368 562,368" fill="none" stroke="{NAVY}" stroke-width="1.1" marker-end="url(#arr)"/>

    <!-- Ya branch -->
    <line x1="370" y1="317" x2="370" y2="342" stroke="{NAVY}" stroke-width="1.3" marker-end="url(#arr)"/>
    <text x="355" y="332" font-size="8.5" fill="{MUTED}">Ya</text>

    <rect x="180" y="344" width="380" height="44" rx="10" fill="{GREEN_FILL}" stroke="{GREEN_STROKE}" stroke-width="1.2"/>
    <text x="370" y="361" text-anchor="middle" font-size="9.2" font-weight="600" fill="{TEXT}">Ekstrak Jawaban AI &amp; usageMetadata (prompt_tokens, completion_tokens)</text>
    <text x="370" y="375" text-anchor="middle" font-size="8.6" fill="{MUTED}">Catat ke tabel ai_token_usage (AiTokenUsage::recordUsage) untuk Token Monitor (F-29)</text>

    <line x1="370" y1="388" x2="370" y2="410" stroke="{NAVY}" stroke-width="1.3" marker-end="url(#arr)"/>

    <rect x="180" y="412" width="380" height="36" rx="10" fill="{BLUE_FILL}" stroke="{NAVY}" stroke-width="1.2"/>
    <text x="370" y="434" text-anchor="middle" font-size="9.2" fill="{TEXT}">Kembalikan Respons JSON ke Browser &amp; Render Markdown Balasan di Widget Chat</text>

    <line x1="370" y1="448" x2="370" y2="470" stroke="{NAVY}" stroke-width="1.3" marker-end="url(#arr)"/>
    <circle cx="370" cy="483" r="11" fill="none" stroke="{TEXT}" stroke-width="1.6"/>
    <circle cx="370" cy="483" r="6.5" fill="{TEXT}"/>
    """
    return wrap_svg(740, 506, body)


# =====================================================================
# GAMBAR 9: CLASS DIAGRAM — DOMAIN INTI & ENTITAS MODEL
# =====================================================================
def svg_class_domain() -> str:
    c1, _ = uml_class_box(25, 48, 220, "User", "«model / app/models/User.php»",
        ["+ id: INT PK", "+ name: VARCHAR(255)", "+ email: VARCHAR(255) UK", "- password_hash: VARCHAR(255)", "+ role: ENUM('customer','admin')", "+ telegram_chat_id: VARCHAR(100)", "+ profile_image: VARCHAR(255)"],
        ["+ login(email, password)", "+ register(data)", "+ updateProfile(id, data)", "+ setPasswordResetToken(email)"])

    c2, _ = uml_class_box(295, 48, 225, "Scan", "«model / app/models/Scan.php»",
        ["+ id: INT PK", "+ user_id: INT FK", "+ image_path: VARCHAR(255)", "+ status: VARCHAR(50)", "+ image_quality_score: DECIMAL"],
        ["+ createPendingScan(userId, path)", "+ saveAnalysisResult(scanId, res)", "+ saveSunscreenResult(scanId, res)", "+ getScansByUserId(userId)", "+ getAdminMetrics()"])

    c3, _ = uml_class_box(570, 48, 225, "Product", "«model / app/models/Product.php»",
        ["+ id: INT PK", "+ brand: VARCHAR(100)", "+ name: VARCHAR(255)", "+ category: VARCHAR(100)", "+ skin_concern: VARCHAR(255)", "+ ingredients: TEXT", "+ price: INT"],
        ["+ getProducts()", "+ getProductsByConcern(concern)", "+ searchProducts(keyword)", "+ addProduct(data)"])

    c4, _ = uml_class_box(25, 295, 220, "AiTokenUsage", "«model / AiTokenUsage.php»",
        ["+ id: INT PK", "+ user_id: INT FK", "+ user_role: VARCHAR(50)", "+ model: VARCHAR(100)", "+ prompt_tokens: INT", "+ completion_tokens: INT", "+ total_tokens: INT"],
        ["+ recordUsage(payload)", "+ getTokenMetrics()"])

    c5, _ = uml_class_box(295, 280, 225, "AnalysisResult", "«value object / services»",
        ["+ acneScore: INT (0-100)", "+ rednessScore: INT (0-100)", "+ textureScore: INT (0-100)", "+ hyperpigmentationScore: INT", "+ overallScore: INT (0-100)", "+ confidence: FLOAT", "+ status: STRING"],
        ["+ toArray(): array", "+ toJson(): string"])

    c6, _ = uml_class_box(570, 280, 225, "SunscreenResult", "«value object / services»",
        ["+ coverageScore: INT (0-100)", "+ coverageStatus: STRING", "+ heatmapPath: STRING", "+ lowCoverageAreas: ARRAY", "+ analysisMethod: STRING", "+ hardwareType: STRING"],
        ["+ toArray(): array", "+ isLowConfidence(): bool"])

    body = f"""
    <text x="410" y="24" text-anchor="middle" font-size="14" font-weight="700" fill="{NAVY}">Diagram Class — Domain Inti &amp; Entitas Model SKINCHECK</text>
    {c1}
    {c2}
    {c3}
    {c4}
    {c5}
    {c6}

    <!-- Relations -->
    <line x1="245" y1="130" x2="295" y2="130" stroke="{NAVY}" stroke-width="1.3"/>
    <text x="255" y="123" font-size="9" fill="{NAVY}">1</text>
    <text x="282" y="123" font-size="9" fill="{NAVY}">*</text>

    <line x1="135" y1="242" x2="135" y2="295" stroke="{NAVY}" stroke-width="1.3"/>
    <text x="142" y="258" font-size="9" fill="{NAVY}">1</text>
    <text x="142" y="288" font-size="9" fill="{NAVY}">*</text>

    <line x1="407" y1="238" x2="407" y2="280" stroke="{NAVY}" stroke-width="1.3"/>
    <text x="414" y="252" font-size="9" fill="{NAVY}">1</text>
    <text x="414" y="274" font-size="9" fill="{NAVY}">0..1</text>

    <polyline points="520,210 545,210 545,350 570,350" fill="none" stroke="{NAVY}" stroke-width="1.3"/>
    <text x="526" y="204" font-size="9" fill="{NAVY}">1</text>
    <text x="548" y="344" font-size="9" fill="{NAVY}">0..1</text>

    <line x1="520" y1="130" x2="570" y2="130" stroke="{NAVY}" stroke-width="1.2" stroke-dasharray="5,3" marker-end="url(#arr-dash)"/>
    <text x="545" y="122" text-anchor="middle" font-size="8" fill="{MUTED}">uses</text>
    """
    return wrap_svg(820, 488, body)


# =====================================================================
# GAMBAR 10: CLASS DIAGRAM — ARSITEKTUR SOLID SERVIS AI & PROVIDER
# =====================================================================
def svg_class_ai_solid() -> str:
    i1, _ = uml_class_box(30, 46, 240, "SkinAnalysisProviderInterface", "«interface»",
        [], ["+ analyze(imagePath): AnalysisResult", "+ getProviderName(): string"])
    i2, _ = uml_class_box(300, 46, 245, "SunscreenAnalysisProviderInterface", "«interface»",
        [], ["+ analyze(imagePath, userId): SunscreenResult", "+ getProviderName(): string", "+ getHardwareType(): string"])
    i3, _ = uml_class_box(575, 46, 215, "FaceDetectorInterface", "«interface»",
        [], ["+ detect(imagePath): array"])

    p1, _ = uml_class_box(20, 160, 130, "OnnxSkinProvider", "«implements»",
        ["- scriptPath: string"], ["+ analyze(): AnalysisResult"])
    p2, _ = uml_class_box(160, 160, 135, "CVSkinProvider", "«implements»",
        ["- gdEngine: resource"], ["+ analyze(): AnalysisResult"])

    p3, _ = uml_class_box(305, 160, 122, "RGBSunscreen", "«implements»",
        ["- storage: Storage"], ["+ analyze(): Result"])
    p4, _ = uml_class_box(437, 160, 118, "UVSunscreen", "«implements»",
        ["- uvMode: bool"], ["+ analyze(): Result"])

    p5, _ = uml_class_box(585, 160, 195, "NullFaceDetector", "«implements»",
        [], ["+ detect(imagePath): array"])

    s1, _ = uml_class_box(40, 295, 255, "SkinAnalysisService", "«domain service / DIP»",
        ["- provider: SkinAnalysisProviderInterface", "- maxRetries: int = 2"],
        ["+ __construct(provider)", "+ analyzeScan(scanId, path): AnalysisResult"])

    s2, _ = uml_class_box(335, 295, 265, "SunscreenAnalysisService", "«domain service / DIP»",
        ["- provider: SunscreenAnalysisProviderInterface"],
        ["+ __construct(provider)", "+ analyzeSunscreen(scanId, path): SunscreenResult"])

    body = f"""
    <text x="410" y="24" text-anchor="middle" font-size="14" font-weight="700" fill="{NAVY}">Diagram Class — Arsitektur SOLID Servis AI &amp; Provider</text>
    {i1}{i2}{i3}
    {p1}{p2}{p3}{p4}{p5}
    {s1}{s2}

    <!-- Realization arrows (dashed hollow triangle) -->
    <polyline points="85,160 85,134 150,134 150,115" fill="none" stroke="{NAVY}" stroke-width="1.2" stroke-dasharray="4,3" marker-end="url(#arr-open)"/>
    <polyline points="227,160 227,134 150,134" fill="none" stroke="{NAVY}" stroke-width="1.2" stroke-dasharray="4,3"/>

    <polyline points="366,160 366,136 422,136 422,126" fill="none" stroke="{NAVY}" stroke-width="1.2" stroke-dasharray="4,3" marker-end="url(#arr-open)"/>
    <polyline points="496,160 496,136 422,136" fill="none" stroke="{NAVY}" stroke-width="1.2" stroke-dasharray="4,3"/>

    <line x1="682" y1="160" x2="682" y2="105" stroke="{NAVY}" stroke-width="1.2" stroke-dasharray="4,3" marker-end="url(#arr-open)"/>

    <!-- Dependency injection from Services to Interfaces -->
    <polyline points="165,295 165,260 15,260 15,82 28,82" fill="none" stroke="{NAVY}" stroke-width="1.2" stroke-dasharray="5,3" marker-end="url(#arr-dash)"/>
    <polyline points="465,295 465,260 562,260 562,82 547,82" fill="none" stroke="{NAVY}" stroke-width="1.2" stroke-dasharray="5,3" marker-end="url(#arr-dash)"/>

    <!-- Note box -->
    <rect x="615" y="280" width="185" height="108" rx="4" fill="{GOLD_FILL}" stroke="{GOLD_STROKE}" stroke-width="1.1"/>
    <text x="625" y="298" font-size="8.8" font-weight="700" fill="{GOLD_STROKE}">Kepatuhan Prinsip SOLID:</text>
    <text x="625" y="313" font-size="8.2" fill="{TEXT}">• SRP: 1 kelas = 1 tugas tunggal</text>
    <text x="625" y="326" font-size="8.2" fill="{TEXT}">• OCP: Provider baru tanpa ubah</text>
    <text x="633" y="337" font-size="8.2" fill="{TEXT}">Service / Controller</text>
    <text x="625" y="350" font-size="8.2" fill="{TEXT}">• LSP: ONNX ↔ CV dapat ditukar</text>
    <text x="625" y="363" font-size="8.2" fill="{TEXT}">• ISP &amp; DIP: Bergantung interface</text>
    """
    return wrap_svg(820, 412, body)


# =====================================================================
# GAMBAR 11: CLASS DIAGRAM — MODUL PENDUKUNG & KOMUNIKASI
# =====================================================================
def svg_class_support() -> str:
    b1, _ = uml_class_box(25, 48, 180, "AuthGuard", "«app/core/AuthGuard.php»",
        [], ["+ requireLogin()", "+ requireCustomer()", "+ requireAdmin()", "+ isAuthenticated(): bool"])
    b2, _ = uml_class_box(225, 48, 180, "ImageValidator", "«app/services»",
        ["- maxBytes: int", "- allowedMimes: array"], ["+ validateUpload(file)", "+ validateBase64(data)", "+ checkResolution()"])
    b3, _ = uml_class_box(425, 48, 180, "ImageStorage", "«app/services»",
        ["- storageRoot: string"], ["+ saveScan(userId, bytes)", "+ saveHeatmap(userId, img)", "+ streamScoped(path)"])
    b4, _ = uml_class_box(625, 48, 175, "Setting", "«app/models/Setting.php»",
        ["+ setting_key: VARCHAR", "+ is_secret: TINYINT"], ["+ getVal(key, default)", "+ updateMultiple(arr)", "+ testGemini() / testTelegram()"])

    b5, _ = uml_class_box(25, 225, 180, "TelegramService", "«app/services»",
        ["- botToken: string"], ["+ sendOtp(chatId, otp)", "+ testConnection()"])
    b6, _ = uml_class_box(225, 225, 180, "EmailService", "«app/services»",
        ["- apiKey: string", "- fromEmail: string"], ["+ sendOtpEmail(to, otp)", "+ sendResetLink(to, url)"])
    b7, _ = uml_class_box(425, 225, 180, "FonnteService", "«app/services»",
        ["- token: string"], ["+ sendWhatsAppOtp(hp, otp)", "+ testConnection()"])
    b8, _ = uml_class_box(625, 225, 175, "ActivityLog", "«app/models»",
        ["+ user_id: INT", "+ activity: VARCHAR", "+ ip_address: VARCHAR"], ["+ log(userId, action)"])

    body = f"""
    <text x="410" y="24" text-anchor="middle" font-size="14" font-weight="700" fill="{NAVY}">Diagram Class — Modul Pendukung (Keamanan, Validasi, Penyimpanan &amp; OTP)</text>
    {b1}{b2}{b3}{b4}{b5}{b6}{b7}{b8}

    <line x1="625" y1="265" x2="605" y2="265" stroke="none"/>
    <polyline points="115,225 115,205 712,205 712,175" fill="none" stroke="{NAVY}" stroke-width="1.1" stroke-dasharray="4,3" marker-end="url(#arr-dash)"/>
    <line x1="315" y1="225" x2="315" y2="205" stroke="{NAVY}" stroke-width="1.1" stroke-dasharray="4,3"/>
    <line x1="515" y1="225" x2="515" y2="205" stroke="{NAVY}" stroke-width="1.1" stroke-dasharray="4,3"/>
    <text x="415" y="199" text-anchor="middle" font-size="8.2" fill="{MUTED}">Semua layanan komunikasi membaca API key &amp; token secara dinamis dari tabel settings</text>
    """
    return wrap_svg(820, 365, body)


# =====================================================================
# GAMBAR 12: SEQUENCE DIAGRAM — ANALISIS SAWAR KULIT END-TO-END
# =====================================================================
def svg_sequence_scan() -> str:
    lifelines = [
        (65, "Browser\n(HUD)"),
        (185, "Scan\nController"),
        (305, "Image\nValidator"),
        (420, "Image\nStorage"),
        (540, "SkinAnalysis\nService"),
        (665, "ONNX / CV\nProvider"),
        (780, "MySQL\nDatabase"),
    ]
    parts = [f'<text x="425" y="22" text-anchor="middle" font-size="14" font-weight="700" fill="{NAVY}">Diagram Sequence — Pemindaian &amp; Analisis Sawar Kulit (F-09 s.d. F-13)</text>']

    # Draw lifelines
    for lx, label in lifelines:
        lines = label.split("\n")
        parts.append(f'<rect x="{lx-46}" y="38" width="92" height="34" rx="3" fill="{BLUE_MID}" stroke="{NAVY}" stroke-width="1.3"/>')
        parts.append(f'<text x="{lx}" y="52" text-anchor="middle" font-size="8.8" font-weight="700" fill="{NAVY}">{esc(lines[0])}</text>')
        parts.append(f'<text x="{lx}" y="64" text-anchor="middle" font-size="8.8" font-weight="700" fill="{NAVY}">{esc(lines[1])}</text>')
        parts.append(f'<line x1="{lx}" y1="72" x2="{lx}" y2="475" stroke="{NAVY}" stroke-width="1" stroke-dasharray="5,4"/>')

    def msg(y, x1, x2, text, dashed=False):
        style = 'stroke-dasharray="5,3"' if dashed else ''
        tx = (x1 + x2) / 2
        return (f'<line x1="{x1}" y1="{y}" x2="{x2}" y2="{y}" stroke="{NAVY}" stroke-width="1.2" {style} marker-end="url(#arr)"/>'
                f'<text x="{tx}" y="{y-4}" text-anchor="middle" font-size="8.3" fill="{TEXT}">{esc(text)}</text>')

    parts.append(msg(98, 65, 185, "1. POST /scan/submitScan"))
    parts.append(msg(126, 185, 305, "2. validate(imagePayload)"))
    parts.append(msg(150, 305, 185, "3. Valid (MIME, Resolusi OK)", dashed=True))
    parts.append(msg(178, 185, 420, "4. saveScan(userId, bytes)"))
    parts.append(msg(202, 420, 185, "5. storagePath (privat)", dashed=True))
    parts.append(msg(228, 185, 780, "6. INSERT skin_scans (status='pending')"))
    parts.append(msg(252, 780, 185, "7. return scanId", dashed=True))
    parts.append(msg(280, 185, 540, "8. analyzeScan(scanId, storagePath)"))
    parts.append(msg(306, 540, 665, "9. analyze(storagePath) [ONNX / CV]"))

    # Alt block for retry
    parts.append(f'<rect x="505" y="320" width="215" height="46" fill="none" stroke="{GOLD_STROKE}" stroke-width="1" stroke-dasharray="4,2"/>')
    parts.append(f'<rect x="505" y="320" width="32" height="14" fill="{GOLD_FILL}" stroke="{GOLD_STROKE}" stroke-width="1"/>')
    parts.append(f'<text x="521" y="330" text-anchor="middle" font-size="8" font-weight="700" fill="{GOLD_STROKE}">alt</text>')
    parts.append(f'<text x="625" y="331" text-anchor="middle" font-size="7.8" fill="{MUTED}">[timeout → retry maks 2x]</text>')
    parts.append(msg(352, 665, 540, "10. AnalysisResult (4 pilar)", dashed=True))

    parts.append(msg(384, 540, 185, "11. return AnalysisResult", dashed=True))
    parts.append(msg(410, 185, 780, "12. INSERT analysis_results & UPDATE status='completed'"))
    parts.append(msg(436, 780, 185, "13. OK + Produk Rekomendasi", dashed=True))
    parts.append(msg(462, 185, 65, "14. Redirect /customer/scanDetail/{scanId}", dashed=True))

    return wrap_svg(850, 490, "\n".join(parts))


# =====================================================================
# GAMBAR 13: SEQUENCE DIAGRAM — CHATBOT AI GEMINI END-TO-END
# =====================================================================
def svg_sequence_chatbot() -> str:
    lifelines = [
        (70, "Browser\nWidget"),
        (205, "Chatbot\nController"),
        (345, "MySQL\n(Scan/Prod)"),
        (485, "Node.js\nEngine"),
        (630, "Google\nGemini API"),
        (770, "AiToken\nUsage DB"),
    ]
    parts = [f'<text x="425" y="22" text-anchor="middle" font-size="14" font-weight="700" fill="{NAVY}">Diagram Sequence — Konsultasi Chatbot AI Gemini &amp; Audit Token (F-21 s.d. F-30)</text>']

    for lx, label in lifelines:
        lines = label.split("\n")
        parts.append(f'<rect x="{lx-48}" y="38" width="96" height="34" rx="3" fill="{BLUE_MID}" stroke="{NAVY}" stroke-width="1.3"/>')
        parts.append(f'<text x="{lx}" y="52" text-anchor="middle" font-size="8.8" font-weight="700" fill="{NAVY}">{esc(lines[0])}</text>')
        parts.append(f'<text x="{lx}" y="64" text-anchor="middle" font-size="8.8" font-weight="700" fill="{NAVY}">{esc(lines[1])}</text>')
        parts.append(f'<line x1="{lx}" y1="72" x2="{lx}" y2="438" stroke="{NAVY}" stroke-width="1" stroke-dasharray="5,4"/>')

    def msg(y, x1, x2, text, dashed=False):
        style = 'stroke-dasharray="5,3"' if dashed else ''
        tx = (x1 + x2) / 2
        return (f'<line x1="{x1}" y1="{y}" x2="{x2}" y2="{y}" stroke="{NAVY}" stroke-width="1.2" {style} marker-end="url(#arr)"/>'
                f'<text x="{tx}" y="{y-4}" text-anchor="middle" font-size="8.3" fill="{TEXT}">{esc(text)}</text>')

    parts.append(msg(100, 70, 205, "1. POST /chatbot/chat (pesan)"))
    parts.append(msg(128, 205, 345, "2. SELECT skor scan terakhir & katalog"))
    parts.append(msg(154, 345, 205, "3. Data konteks + API key settings", dashed=True))
    parts.append(msg(182, 205, 205, ""))
    parts.append(f'<polyline points="205,176 250,176 250,196 207,196" fill="none" stroke="{NAVY}" stroke-width="1.1" marker-end="url(#arr)"/>')
    parts.append(f'<text x="295" y="189" font-size="8.2" fill="{TEXT}">4. Injeksi System Prompt per peran</text>')

    parts.append(msg(224, 205, 485, "5. Forward payload + context"))
    parts.append(msg(252, 485, 630, "6. POST generateContent (Gemini Flash)"))

    # Alt fallback
    parts.append(f'<rect x="455" y="266" width="215" height="44" fill="none" stroke="{GOLD_STROKE}" stroke-width="1" stroke-dasharray="4,2"/>')
    parts.append(f'<rect x="455" y="266" width="32" height="14" fill="{GOLD_FILL}" stroke="{GOLD_STROKE}" stroke-width="1"/>')
    parts.append(f'<text x="471" y="276" text-anchor="middle" font-size="8" font-weight="700" fill="{GOLD_STROKE}">alt</text>')
    parts.append(f'<text x="575" y="277" text-anchor="middle" font-size="7.8" fill="{MUTED}">[429/error → model fallback]</text>')
    parts.append(msg(296, 630, 485, "7. Balasan AI + usageMetadata", dashed=True))

    parts.append(msg(330, 485, 205, "8. JSON (reply, prompt/output tokens)", dashed=True))
    parts.append(msg(360, 205, 770, "9. INSERT ai_token_usage (recordUsage)"))
    parts.append(msg(386, 770, 205, "10. Logged OK", dashed=True))
    parts.append(msg(416, 205, 70, "11. JSON Respons Markdown ke Widget", dashed=True))

    return wrap_svg(850, 452, "\n".join(parts))


# =====================================================================
# GAMBAR 14: ERD 8 TABEL LENGKAP (LANDSCAPE)
# =====================================================================
def erd_box(x: int, y: int, w: int, title: str, cols: list[str]) -> tuple[str, int]:
    h = 22 + len(cols) * 12.5 + 8
    out = [
        f'<rect x="{x}" y="{y}" width="{w}" height="{h}" fill="#ffffff" stroke="{NAVY}" stroke-width="1.3"/>',
        f'<rect x="{x}" y="{y}" width="{w}" height="22" fill="{BLUE_MID}" stroke="{NAVY}" stroke-width="1.3"/>',
        f'<text x="{x+w/2}" y="{y+15}" text-anchor="middle" font-size="9.5" font-weight="700" fill="{NAVY}">{esc(title)}</text>',
    ]
    for i, c in enumerate(cols):
        weight = "700" if "PK" in c or "FK" in c else "400"
        out.append(f'<text x="{x+6}" y="{y + 34 + i*12.5}" font-size="8.2" font-weight="{weight}" fill="{TEXT}">{esc(c)}</text>')
    return "\n".join(out), int(h)

def svg_erd_full() -> str:
    t1, _ = erd_box(25, 48, 210, "users", [
        "id: INT PK", "name: VARCHAR(255)", "email: VARCHAR(255) UK", "password_hash: VARCHAR(255)",
        "profile_image: VARCHAR(255)", "telegram_chat_id: VARCHAR(100)", "telegram_username: VARCHAR(100)",
        "reset_token: VARCHAR(255)", "reset_expires_at: DATETIME", "role: ENUM('customer','admin')",
        "status: VARCHAR(50)", "created_at / updated_at"
    ])
    t2, _ = erd_box(290, 48, 205, "skin_scans", [
        "id: INT PK", "user_id: INT FK → users.id", "image_path: VARCHAR(255)",
        "status: VARCHAR(50)", "image_quality_score: DEC(5,2)", "created_at: TIMESTAMP"
    ])
    t3, _ = erd_box(550, 48, 210, "analysis_results", [
        "id: INT PK", "skin_scan_id: INT FK → skin_scans", "acne_score: INT",
        "redness_score: INT", "texture_score: INT", "hyperpigmentation_score: INT",
        "overall_score: INT", "ai_response: TEXT", "created_at: TIMESTAMP"
    ])
    t4, _ = erd_box(550, 218, 210, "sunscreen_results", [
        "id: INT PK", "skin_scan_id: INT FK → skin_scans", "coverage_score: INT",
        "coverage_status: VARCHAR(50)", "heatmap_path: VARCHAR(255)", "low_coverage_areas: TEXT",
        "analysis_method: VARCHAR(100)", "hardware_type: VARCHAR(100)", "created_at: TIMESTAMP"
    ])
    t5, _ = erd_box(25, 255, 210, "activity_logs", [
        "id: INT PK", "user_id: INT FK → users.id", "activity: VARCHAR(255)",
        "ip_address: VARCHAR(45)", "created_at: TIMESTAMP"
    ])
    t6, _ = erd_box(290, 218, 205, "ai_token_usage", [
        "id: INT PK", "user_id: INT (ref)", "user_role: VARCHAR(50)", "feature: VARCHAR(100)",
        "model: VARCHAR(100)", "prompt_tokens: INT", "completion_tokens: INT",
        "total_tokens: INT", "duration_ms: INT", "status: VARCHAR(50)", "created_at: TIMESTAMP"
    ])
    t7, _ = erd_box(25, 378, 210, "products", [
        "id: INT PK", "brand: VARCHAR(100)", "name: VARCHAR(255)", "category: VARCHAR(100)",
        "skin_concern: VARCHAR(255)", "skin_type: VARCHAR(100)", "ingredients: TEXT",
        "price: INT", "status: VARCHAR(50)"
    ])
    t8, _ = erd_box(290, 405, 205, "settings", [
        "id: INT PK", "setting_key: VARCHAR(100) UK", "setting_value: TEXT",
        "setting_group: VARCHAR(50)", "setting_label: VARCHAR(255)", "is_secret: TINYINT(1)",
        "updated_at: TIMESTAMP"
    ])

    body = f"""
    <text x="410" y="24" text-anchor="middle" font-size="14.5" font-weight="700" fill="{NAVY}">Entity Relationship Diagram (ERD) — Basis Data SKINCHECK (8 Tabel, 4 Foreign Key)</text>
    {t1}{t2}{t3}{t4}{t5}{t6}{t7}{t8}

    <!-- FK Relations -->
    <!-- users -> skin_scans (1:N) -->
    <line x1="235" y1="88" x2="290" y2="88" stroke="{NAVY}" stroke-width="1.4" marker-end="url(#arr)"/>
    <text x="242" y="82" font-size="8.5" font-weight="700" fill="{NAVY}">1</text>
    <text x="275" y="82" font-size="8.5" font-weight="700" fill="{NAVY}">*</text>

    <!-- skin_scans -> analysis_results (1:1) -->
    <line x1="495" y1="88" x2="550" y2="88" stroke="{NAVY}" stroke-width="1.4" marker-end="url(#arr)"/>
    <text x="502" y="82" font-size="8.5" font-weight="700" fill="{NAVY}">1</text>
    <text x="530" y="82" font-size="8.5" font-weight="700" fill="{NAVY}">0..1</text>

    <!-- skin_scans -> sunscreen_results (1:1) -->
    <polyline points="495,128 522,128 522,255 550,255" fill="none" stroke="{NAVY}" stroke-width="1.4" marker-end="url(#arr)"/>
    <text x="502" y="122" font-size="8.5" font-weight="700" fill="{NAVY}">1</text>
    <text x="526" y="248" font-size="8.5" font-weight="700" fill="{NAVY}">0..1</text>

    <!-- users -> activity_logs (1:N SET NULL) -->
    <line x1="130" y1="226" x2="130" y2="255" stroke="{NAVY}" stroke-width="1.4" marker-end="url(#arr)"/>
    <text x="137" y="242" font-size="8.5" font-weight="700" fill="{NAVY}">1..*</text>

    <!-- users -> ai_token_usage (logical ref) -->
    <polyline points="235,195 262,195 262,255 290,255" fill="none" stroke="{MUTED}" stroke-width="1.1" stroke-dasharray="4,3" marker-end="url(#arr-dash)"/>
    <text x="266" y="218" font-size="7.5" fill="{MUTED}">ref</text>

    <!-- Legend -->
    <rect x="550" y="385" width="210" height="125" rx="4" fill="{GOLD_FILL}" stroke="{GOLD_STROKE}" stroke-width="1.1"/>
    <text x="560" y="403" font-size="9" font-weight="700" fill="{GOLD_STROKE}">Catatan Skema &amp; Integritas:</text>
    <text x="560" y="418" font-size="8.2" fill="{TEXT}">• Garis solid = Foreign Key aktif</text>
    <text x="560" y="431" font-size="8.2" fill="{TEXT}">• Garis putus = Referensi logis</text>
    <text x="560" y="445" font-size="8.2" fill="{TEXT}">• ON DELETE CASCADE pada</text>
    <text x="568" y="457" font-size="8.2" fill="{TEXT}">skin_scans, analysis, sunscreen</text>
    <text x="560" y="471" font-size="8.2" fill="{TEXT}">• ON DELETE SET NULL pada</text>
    <text x="568" y="483" font-size="8.2" fill="{TEXT}">activity_logs (audit tetap utuh)</text>
    <text x="560" y="498" font-size="8.2" fill="{TEXT}">• products &amp; settings: mandiri</text>
    """
    return wrap_svg(785, 535, body)
