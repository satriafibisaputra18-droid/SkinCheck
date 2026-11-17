import os
import subprocess
from markdown_it import MarkdownIt
from bs4 import BeautifulSoup

def build_pdf():
    base_dir = os.path.dirname(os.path.abspath(__file__))
    md_path = os.path.join(base_dir, "PRD_SRS_SkinCheck.md")
    html_path = os.path.join(base_dir, "PRD_SRS_SkinCheck.html")
    pdf_path = os.path.join(base_dir, "PRD_SRS_SkinCheck.pdf")

    with open(md_path, "r", encoding="utf-8") as f:
        md_text = f.read()

    # Markdown parser with tables enabled
    md = MarkdownIt().enable('table').enable('strikethrough')
    raw_html = md.render(md_text)

    # Parse with BeautifulSoup to inject academic structure and page breaks
    soup = BeautifulSoup(raw_html, 'html.parser')

    # Add IDs and page-break classes to BAB headers
    for h2 in soup.find_all('h2'):
        text = h2.get_text().strip()
        if text.startswith('BAB ') or text == 'DAFTAR ISI' or text == 'PENGESAHAN DOKUMEN':
            h2['class'] = h2.get('class', []) + ['chapter-break']

    content_html = str(soup)

    # HTML Document with Formal STMIK IKMI Academic Styling for A4 Print
    html_doc = f"""<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>PRD SRS — SkinCheck (Satria Fibi Saputra - 41257159)</title>
    <!-- KaTeX for math formulas -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/contrib/auto-render.min.js"
        onload="renderMathInElement(document.body, {{delimiters: [{{left: '$$', right: '$$', display: true}}, {{left: '$', right: '$', display: false}}]}});"></script>
    <style>
        @page {{
            size: A4 portrait;
            margin: 25mm 20mm 20mm 25mm;
            @bottom-right {{
                content: counter(page);
                font-family: 'Segoe UI', Arial, sans-serif;
                font-size: 9pt;
                color: #64748b;
            }}
            @top-right {{
                content: "SkinCheck — PRD / SRS IEEE 830";
                font-family: 'Segoe UI', Arial, sans-serif;
                font-size: 8.5pt;
                color: #94a3b8;
            }}
        }}

        @page:first {{
            margin-top: 15mm;
            @top-right {{ content: ""; }}
            @bottom-right {{ content: ""; }}
        }}

        * {{
            box-sizing: border-box;
        }}

        body {{
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.6;
            color: #1e293b;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }}

        /* Document Header / Cover Elements */
        .cover-header {{
            text-align: center;
            border-bottom: 3px double #0f172a;
            padding-bottom: 14pt;
            margin-bottom: 24pt;
        }}

        .cover-inst {{
            font-size: 11pt;
            font-weight: 700;
            letter-spacing: 1.5px;
            color: #334155;
            text-transform: uppercase;
            margin: 0;
        }}

        .cover-dept {{
            font-size: 13pt;
            font-weight: 800;
            letter-spacing: 1px;
            color: #0f172a;
            text-transform: uppercase;
            margin: 4pt 0 0 0;
        }}

        h1 {{
            font-size: 16pt;
            font-weight: 800;
            color: #0f172a;
            margin-top: 12pt;
            margin-bottom: 8pt;
            line-height: 1.3;
        }}

        /* Clean chapter headings */
        h2 {{
            font-size: 13pt;
            font-weight: 700;
            color: #047857;
            margin-top: 18pt;
            margin-bottom: 8pt;
            border-bottom: 1.5px solid #e2e8f0;
            padding-bottom: 4px;
            page-break-after: avoid;
        }}

        /* Explicit page breaks for chapters */
        .chapter-break {{
            page-break-before: always;
            padding-top: 10pt;
        }}

        h3 {{
            font-size: 11pt;
            font-weight: 700;
            color: #0f172a;
            margin-top: 12pt;
            margin-bottom: 4pt;
            page-break-after: avoid;
        }}

        h4 {{
            font-size: 10pt;
            font-weight: 600;
            color: #334155;
            margin-top: 8pt;
            margin-bottom: 2pt;
            page-break-after: avoid;
        }}

        p {{
            margin-top: 0;
            margin-bottom: 6pt;
            text-align: justify;
            text-justify: inter-word;
        }}

        ul, ol {{
            margin-top: 0;
            margin-bottom: 8pt;
            padding-left: 20px;
        }}

        li {{
            margin-bottom: 3pt;
        }}

        /* Tables Styling */
        table {{
            width: 100%;
            border-collapse: collapse;
            margin-top: 8pt;
            margin-bottom: 12pt;
            font-size: 9pt;
            page-break-inside: avoid;
        }}

        th, td {{
            border: 1px solid #cbd5e1;
            padding: 6px 9px;
            text-align: left;
            vertical-align: top;
        }}

        th {{
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 700;
            border-bottom: 2px solid #94a3b8;
        }}

        tr:nth-child(even) td {{
            background-color: #f8fafc;
        }}

        /* Preformatted Code & Diagrams */
        pre {{
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3.5px solid #059669;
            border-radius: 4px;
            padding: 8px 12px;
            font-family: Consolas, "Courier New", monospace;
            font-size: 8pt;
            line-height: 1.4;
            overflow-x: auto;
            margin-top: 6pt;
            margin-bottom: 10pt;
            page-break-inside: avoid;
            color: #0f172a;
        }}

        code {{
            font-family: Consolas, "Courier New", monospace;
            font-size: 8.5pt;
            background-color: #f1f5f9;
            color: #047857;
            padding: 1px 4px;
            border-radius: 3px;
        }}

        pre code {{
            background-color: transparent;
            padding: 0;
            color: inherit;
        }}

        blockquote {{
            border-left: 3.5px solid #0284c7;
            background-color: #f0f9ff;
            margin: 8pt 0;
            padding: 6pt 12pt;
            color: #0369a1;
            font-size: 9pt;
            border-radius: 0 4px 4px 0;
        }}

        hr {{
            border: 0;
            border-top: 1px solid #e2e8f0;
            margin: 12pt 0;
        }}

        strong {{
            color: #0f172a;
        }}

        @media print {{
            body {{
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }}
            a {{
                color: inherit;
                text-decoration: none;
            }}
        }}
    </style>
</head>
<body>
    <div class="cover-header">
        <p class="cover-inst">STMIK IKMI CIREBON — PROGRAM STUDI S1 TEKNIK INFORMATIKA</p>
        <p class="cover-dept">TUGAS PROYEK AKHIR MANDIRI REKAYASA PERANGKAT LUNAK (OBE)</p>
    </div>
{content_html}
</body>
</html>
"""

    with open(html_path, "w", encoding="utf-8") as f:
        f.write(html_doc)

    print(f"HTML generated at: {html_path}")

    # Invoke Google Chrome Headless to Print PDF
    chrome_path = r"C:\Program Files\Google\Chrome\Application\chrome.exe"
    cmd = [
        chrome_path,
        "--headless=new",
        "--disable-gpu",
        "--no-pdf-header-footer",
        f"--print-to-pdf={pdf_path}",
        html_path
    ]

    print("Running Chrome to generate PDF...")
    res = subprocess.run(cmd, capture_output=True, text=True)
    if res.returncode == 0 and os.path.exists(pdf_path):
        size_kb = os.path.getsize(pdf_path) / 1024
        print(f"SUCCESS: PDF generated successfully at {pdf_path} ({size_kb:.2f} KB)")
    else:
        print(f"ERROR generating PDF: {res.stderr}")

if __name__ == "__main__":
    build_pdf()
