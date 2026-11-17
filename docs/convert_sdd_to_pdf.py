import os
import subprocess
from markdown_it import MarkdownIt
from bs4 import BeautifulSoup

def build_sdd_pdf():
    base_dir = os.path.dirname(os.path.abspath(__file__))
    md_path = os.path.join(base_dir, "SDD_UML_SkinCheck.md")
    html_path = os.path.join(base_dir, "SDD_UML_SkinCheck.html")
    pdf_path = os.path.join(base_dir, "SDD_UML_SkinCheck.pdf")

    with open(md_path, "r", encoding="utf-8") as f:
        md_text = f.read()

    md = MarkdownIt().enable('table').enable('strikethrough')
    raw_html = md.render(md_text)

    soup = BeautifulSoup(raw_html, 'html.parser')

    # Convert ```mermaid code blocks to <div class="mermaid">
    for pre in soup.find_all('pre'):
        code = pre.find('code')
        if code:
            classes = code.get('class', [])
            if 'language-mermaid' in classes or any('mermaid' in c for c in classes):
                mermaid_div = soup.new_tag('div', **{'class': 'mermaid'})
                mermaid_div.string = code.get_text()
                pre.replace_with(mermaid_div)

    # Add chapter page breaks
    for h2 in soup.find_all('h2'):
        text = h2.get_text().strip()
        if text.startswith('BAB ') or text == 'DAFTAR ISI' or text == 'PENGESAHAN DOKUMEN':
            h2['class'] = h2.get('class', []) + ['chapter-break']

    content_html = str(soup)

    html_doc = f"""<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SDD UML — SkinCheck (Satria Fibi Saputra - 41257159)</title>
    <!-- KaTeX -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.js"></script>
    <!-- Local Mermaid JS for offline vector diagram rendering -->
    <script src="mermaid.min.js"></script>
    <script>
        mermaid.initialize({{
            startOnLoad: true,
            theme: 'neutral',
            themeVariables: {{
                fontFamily: 'Segoe UI, Arial, sans-serif',
                fontSize: '11px',
                primaryColor: '#e0f2fe',
                primaryBorderColor: '#0284c7',
                lineColor: '#334155'
            }}
        }});
    </script>
    <style>
        @page {{
            size: A4 portrait;
            margin: 22mm 18mm 20mm 22mm;
            @bottom-right {{
                content: counter(page);
                font-family: 'Segoe UI', Arial, sans-serif;
                font-size: 8.5pt;
                color: #64748b;
            }}
            @top-right {{
                content: "SkinCheck — Dokumen SDD & Pemodelan UML";
                font-family: 'Segoe UI', Arial, sans-serif;
                font-size: 8pt;
                color: #94a3b8;
            }}
        }}

        @page:first {{
            margin-top: 14mm;
            @top-right {{ content: ""; }}
            @bottom-right {{ content: ""; }}
        }}

        * {{ box-sizing: border-box; }}

        body {{
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 9.5pt;
            line-height: 1.55;
            color: #1e293b;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }}

        .cover-header {{
            text-align: center;
            border-bottom: 3px double #0f172a;
            padding-bottom: 12pt;
            margin-bottom: 20pt;
        }}

        .cover-inst {{
            font-size: 10.5pt;
            font-weight: 700;
            letter-spacing: 1.5px;
            color: #334155;
            text-transform: uppercase;
            margin: 0;
        }}

        .cover-dept {{
            font-size: 12pt;
            font-weight: 800;
            letter-spacing: 1px;
            color: #0f172a;
            text-transform: uppercase;
            margin: 4pt 0 0 0;
        }}

        h1 {{
            font-size: 15pt;
            font-weight: 800;
            color: #0f172a;
            margin-top: 10pt;
            margin-bottom: 6pt;
            line-height: 1.25;
        }}

        h2 {{
            font-size: 12pt;
            font-weight: 700;
            color: #047857;
            margin-top: 16pt;
            margin-bottom: 6pt;
            border-bottom: 1.5px solid #e2e8f0;
            padding-bottom: 3px;
            page-break-after: avoid;
        }}

        .chapter-break {{
            page-break-before: always;
            padding-top: 8pt;
        }}

        h3 {{
            font-size: 10.5pt;
            font-weight: 700;
            color: #0f172a;
            margin-top: 10pt;
            margin-bottom: 4pt;
            page-break-after: avoid;
        }}

        h4 {{
            font-size: 9.5pt;
            font-weight: 600;
            color: #334155;
            margin-top: 6pt;
            margin-bottom: 2pt;
            page-break-after: avoid;
        }}

        p {{
            margin-top: 0;
            margin-bottom: 5pt;
            text-align: justify;
            text-justify: inter-word;
        }}

        ul, ol {{
            margin-top: 0;
            margin-bottom: 6pt;
            padding-left: 18px;
        }}

        li {{ margin-bottom: 2pt; }}

        table {{
            width: 100%;
            border-collapse: collapse;
            margin-top: 6pt;
            margin-bottom: 10pt;
            font-size: 8.5pt;
            page-break-inside: avoid;
        }}

        th, td {{
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
            text-align: left;
            vertical-align: top;
        }}

        th {{
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 700;
            border-bottom: 2px solid #94a3b8;
        }}

        tr:nth-child(even) td {{ background-color: #f8fafc; }}

        pre {{
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3.5px solid #059669;
            border-radius: 4px;
            padding: 8px 10px;
            font-family: Consolas, "Courier New", monospace;
            font-size: 7.5pt;
            line-height: 1.35;
            overflow-x: auto;
            margin-top: 5pt;
            margin-bottom: 8pt;
            page-break-inside: avoid;
            color: #0f172a;
        }}

        code {{
            font-family: Consolas, "Courier New", monospace;
            font-size: 8pt;
            background-color: #f1f5f9;
            color: #047857;
            padding: 1px 3px;
            border-radius: 3px;
        }}

        pre code {{ background-color: transparent; padding: 0; color: inherit; }}

        /* Mermaid Diagram Container with tight page-friendly scaling */
        .mermaid {{
            margin: 6pt auto 10pt auto;
            padding: 6pt;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            text-align: center;
            page-break-inside: avoid;
            display: flex;
            justify-content: center;
            align-items: center;
        }}

        .mermaid svg {{
            max-width: 95% !important;
            max-height: 420px !important;
            height: auto !important;
            width: auto !important;
        }}

        hr {{
            border: 0;
            border-top: 1px solid #e2e8f0;
            margin: 10pt 0;
        }}

        strong {{ color: #0f172a; }}

        @media print {{
            body {{
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
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

    # Invoke Google Chrome Headless with delay for Mermaid SVG rendering
    chrome_path = r"C:\Program Files\Google\Chrome\Application\chrome.exe"
    cmd = [
        chrome_path,
        "--headless=new",
        "--disable-gpu",
        "--no-pdf-header-footer",
        "--allow-file-access-from-files",
        "--run-all-compositor-stages-before-draw",
        f"--print-to-pdf={pdf_path}",
        html_path
    ]

    print("Running Chrome to generate SDD PDF...")
    res = subprocess.run(cmd, capture_output=True, text=True)
    if res.returncode == 0 and os.path.exists(pdf_path):
        size_kb = os.path.getsize(pdf_path) / 1024
        print(f"SUCCESS: SDD PDF generated successfully at {pdf_path} ({size_kb:.2f} KB)")
    else:
        print(f"ERROR: {res.stderr}")

if __name__ == "__main__":
    build_sdd_pdf()
