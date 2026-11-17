# -*- coding: utf-8 -*-
"""
Two-pass publication PDF builder for SkinCheck OBE Final Report.
Pass 1: Generates PDFs to detect exact physical page numbers for every heading & figure.
Pass 2: Injects exact verified page numbers into Daftar Isi & Daftar Gambar and regenerates.
Finally merges Part 1 (PRD) + Part 2 (SDD + UML + UI/UX Figma) + Part 3 (Testing & CI/CD)
into the single official submission PDF.
"""
import os
import subprocess
from pypdf import PdfReader, PdfWriter

from part1_prd import build_prd_html
from part2_sdd import build_sdd_html
from part3_testing import build_testing_html

CHROME = r"C:\Program Files\Google\Chrome\Application\chrome.exe"
DOCS_DIR = os.path.abspath(os.path.join(os.path.dirname(__file__), ".."))

def render_chrome_pdf(html_path: str, pdf_path: str) -> None:
    cmd = [
        CHROME,
        "--headless=new",
        "--disable-gpu",
        "--no-pdf-header-footer",
        "--allow-file-access-from-files",
        "--run-all-compositor-stages-before-draw",
        f"--print-to-pdf={pdf_path}",
        html_path,
    ]
    res = subprocess.run(cmd, capture_output=True, text=True)
    if res.returncode != 0 or not os.path.exists(pdf_path):
        raise RuntimeError(f"Chrome PDF failed for {html_path}: {res.stderr}")

def scan_page_numbers(pdf_path: str, keys: list[str], skip_toc_pages: int = 3) -> dict[str, str]:
    reader = PdfReader(pdf_path)
    page_texts = [p.extract_text() or "" for p in reader.pages]
    found = {}
    for key in keys:
        # For Riwayat Revisi & Daftar Isi -> search from page 2 onwards (index 1)
        # For Daftar Gambar -> search for the actual Daftar Gambar heading page (where "Seluruh diagram teknis" appears)
        if key == "Daftar Gambar":
            for idx in range(1, len(page_texts)):
                if "Seluruh diagram teknis" in page_texts[idx]:
                    found[key] = str(idx + 1)
                    break
            continue
        start_idx = 1 if key in ("Riwayat Revisi", "Daftar Isi") else skip_toc_pages
        for idx in range(start_idx, len(page_texts)):
            # Normalize whitespace for matching
            norm = " ".join(page_texts[idx].split())
            if key in norm:
                found[key] = str(idx + 1)
                break
        if key not in found:
            # Fallback search from page 2
            for idx in range(1, len(page_texts)):
                norm = " ".join(page_texts[idx].split())
                if key in norm:
                    found[key] = str(idx + 1)
                    break
    return found

def build_part(name: str, html_builder, keys: list[str], out_pdf_name: str, skip_toc: int = 2) -> str:
    html_path = os.path.join(DOCS_DIR, f"_build_{name}.html")
    pdf_path = os.path.join(DOCS_DIR, out_pdf_name)

    # Pass 1
    with open(html_path, "w", encoding="utf-8") as f:
        f.write(html_builder({}))
    render_chrome_pdf(html_path, pdf_path)

    # Scan real page numbers
    page_map = scan_page_numbers(pdf_path, keys, skip_toc_pages=skip_toc)

    # Pass 2 with exact verified page numbers
    with open(html_path, "w", encoding="utf-8") as f:
        f.write(html_builder(page_map))
    render_chrome_pdf(html_path, pdf_path)

    # Cleanup temp html
    if os.path.exists(html_path):
        os.remove(html_path)

    pages = len(PdfReader(pdf_path).pages)
    size_kb = os.path.getsize(pdf_path) / 1024
    print(f"[OK] {out_pdf_name}: {pages} pages ({size_kb:.1f} KB)")
    return pdf_path

def main():
    prd_keys = [
        "Riwayat Revisi", "Daftar Isi",
        "1. Pendahuluan", "1.1 Tujuan", "1.2 Ruang Lingkup", "1.3 Definisi, Akronim, dan Singkatan", "1.4 Referensi", "1.5 Gambaran Dokumen",
        "2. Gambaran Umum", "2.1 Latar Belakang Masalah", "2.2 Deskripsi Produk", "2.3 Pengguna dan Peran", "2.4 Lingkungan Operasi", "2.5 Batasan Desain dan Implementasi", "2.6 Asumsi dan Ketergantungan",
        "3. Kebutuhan Fungsional", "3.1 Autentikasi", "3.2 Pemindaian Sawar Kulit", "3.3 Pemeriksaan Kerataan Sunscreen", "3.4 Riwayat Progres", "3.5 AI Skincare Assistant", "3.6 Panel Administrator Eksekutif",
        "4. Kebutuhan Non-Fungsional", "4.1 Kinerja", "4.2 Keamanan dan Privasi", "4.3 Keandalan dan Ketersediaan", "4.4 Kegunaan", "4.5 Keterpeliharaan", "4.6 Portabilitas",
        "5. Model Data", "5.1 Ringkasan Skema Basis Data", "5.2 Tabel per Domain", "5.3 Relasi Utama",
        "6. Antarmuka Sistem", "7. Batasan, Risiko",
        "Lampiran A", "Lampiran B", "Lampiran C"
    ]

    sdd_keys = [
        "Riwayat Revisi", "Daftar Isi", "Daftar Gambar",
        "1. Pendahuluan", "1.1 Tujuan", "1.2 Referensi",
        "2. Arsitektur Sistem", "2.1 Gambaran Arsitektur", "2.2 Tumpukan Teknologi", "2.3 Struktur Proyek", "2.4 Pola Desain", "2.5 Aliran Data Utama",
        "3. Diagram Use Case", "3.1 Aktor dan Ringkasan", "3.2 Guest", "3.3 Customer", "3.4 Admin", "3.5 Skenario Use Case Utama",
        "4. Diagram Activity", "4.1 Pemindaian Sawar Kulit", "4.2 Pemeriksaan Kerataan Sunscreen", "4.3 Konsultasi Chatbot AI",
        "5. Diagram Class", "5.1 Domain Inti", "5.2 Arsitektur SOLID", "5.3 Modul Pendukung",
        "6. Diagram Sequence", "6.1 Analisis Sawar Kulit", "6.2 Konsultasi Chatbot AI",
        "7. Entity Relationship Diagram", "7.1 Diagram Relasi", "7.2 Daftar Lengkap Foreign Key",
        "8. Desain Antarmuka", "8.1 Prinsip Desain", "8.2 Inventaris Layar", "8.3 Alur Navigasi", "8.4 Tangkapan Layar UI", "8.5 Spesifikasi Prototipe Interaktif Figma",
        "9. Keamanan & Keputusan Desain",
        "Lampiran A", "Lampiran B", "Lampiran C",
        "Gambar 1 —", "Gambar 2 —", "Gambar 3 —", "Gambar 4 —", "Gambar 5 —",
        "Gambar 6 —", "Gambar 7 —", "Gambar 8 —", "Gambar 9 —", "Gambar 10 —",
        "Gambar 11 —", "Gambar 12 —", "Gambar 13 —", "Gambar 14 —", "Gambar 15 —",
        "Gambar 16 —", "Gambar 17 —", "Gambar 18 —", "Gambar 19 —"
    ]

    test_keys = [
        "Riwayat Revisi", "Daftar Isi",
        "1. Pendahuluan", "1.1 Tujuan", "1.2 Referensi",
        "2. Strategi Pengujian", "2.1 Prinsip", "2.2 Lingkungan Pengujian",
        "3. Inventaris Skrip Pengujian", "3.1 Ringkasan", "3.2 Rincian 21 Kasus Uji — Analisis", "3.3 Rincian 21 Kasus Uji — Sunscreen", "3.4 Skrip Pengujian Manual",
        "4. Pipeline CI/CD", "4.1 Gambaran", "4.2 Yang Sengaja Tidak Dijalankan",
        "5. Riwayat Kendala", "6. Bukti Hasil Pengujian", "7. Keterbatasan",
        "Lampiran A", "Lampiran B"
    ]

    p1 = build_part("prd", build_prd_html, prd_keys, "PRD_SRS_SkinCheck.pdf", skip_toc=3)
    p2 = build_part("sdd", build_sdd_html, sdd_keys, "SDD_UML_SkinCheck.pdf", skip_toc=4)
    p3 = build_part("testing", build_testing_html, test_keys, "LAPORAN_PENGUJIAN_CICD_SkinCheck.pdf", skip_toc=3)

    # Merge into single official submission PDF
    merged_path = os.path.join(DOCS_DIR, "PROYEK_AKHIR_MANDIRI_SKINCHECK_SATRIA_FIBI_SAPUTRA_41257159.pdf")
    writer = PdfWriter()
    total_pages = 0
    for part_pdf in (p1, p2, p3):
        reader = PdfReader(part_pdf)
        for page in reader.pages:
            writer.add_page(page)
        total_pages += len(reader.pages)

    with open(merged_path, "wb") as f_out:
        writer.write(f_out)

    size_mb = os.path.getsize(merged_path) / (1024 * 1024)
    print(f"\n[DONE] Master Combined PDF: {merged_path}")
    print(f"       Total Pages: {total_pages} | Size: {size_mb:.2f} MB")

if __name__ == "__main__":
    main()
