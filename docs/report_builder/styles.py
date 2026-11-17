# -*- coding: utf-8 -*-
"""
Shared Academic CSS Styles matching the formal STMIK IKMI Cirebon OBE Report format
(Deep Navy #1B365D accents, Georgia serif body, clean sans-serif tables & diagrams).
"""

def get_css(header_title: str, footer_label: str = "SKINCHECK — Platform Analisis Kulit & Sunscreen Berbasis AI • Satria Fibi Saputra (41257159)") -> str:
    return f"""
    @page {{
        size: A4 portrait;
        margin: 22mm 20mm 22mm 22mm;
        @top-right {{
            content: "{header_title}";
            font-family: "Segoe UI", Calibri, Arial, sans-serif;
            font-size: 8.5pt;
            color: #64748b;
            border-bottom: 0.5pt solid #cbd5e1;
            padding-bottom: 3pt;
            margin-bottom: 6pt;
        }}
        @bottom-center {{
            content: "{footer_label} • Halaman " counter(page);
            font-family: "Segoe UI", Calibri, Arial, sans-serif;
            font-size: 8.5pt;
            color: #64748b;
            border-top: 0.5pt solid #cbd5e1;
            padding-top: 4pt;
        }}
    }}

    @page :first {{
        margin: 20mm 20mm 20mm 20mm;
        @top-right {{
            content: "{header_title}";
            font-family: "Segoe UI", Calibri, Arial, sans-serif;
            font-size: 8.5pt;
            color: #64748b;
        }}
        @bottom-center {{
            content: "{footer_label} • Halaman " counter(page);
            font-family: "Segoe UI", Calibri, Arial, sans-serif;
            font-size: 8.5pt;
            color: #64748b;
        }}
    }}

    @page landscape_page {{
        size: A4 landscape;
        margin: 18mm 18mm 18mm 18mm;
        @top-right {{
            content: "{header_title}";
            font-family: "Segoe UI", Calibri, Arial, sans-serif;
            font-size: 8.5pt;
            color: #64748b;
        }}
        @bottom-center {{
            content: "{footer_label} • Halaman " counter(page);
            font-family: "Segoe UI", Calibri, Arial, sans-serif;
            font-size: 8.5pt;
            color: #64748b;
        }}
    }}

    * {{
        box-sizing: border-box;
    }}

    body {{
        font-family: Georgia, Cambria, "Times New Roman", serif;
        font-size: 10.2pt;
        line-height: 1.56;
        color: #1e293b;
        margin: 0;
        padding: 0;
        background: #ffffff;
    }}

    /* === COVER PAGE === */
    .cover-page {{
        page-break-after: always;
        text-align: center;
        padding-top: 12pt;
    }}

    .cover-inst {{
        font-family: Georgia, Cambria, serif;
        font-size: 12pt;
        font-weight: 700;
        color: #1e293b;
        letter-spacing: 0.6px;
        margin: 0 0 3pt 0;
        text-transform: uppercase;
    }}

    .cover-rule {{
        border: none;
        border-top: 2.5pt solid #1B365D;
        margin: 14pt 0 18pt 0;
    }}

    .cover-doctype {{
        font-family: Georgia, Cambria, serif;
        font-size: 14pt;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 4pt 0;
        letter-spacing: 0.4px;
    }}

    .cover-acronym {{
        font-family: Georgia, Cambria, serif;
        font-size: 11.5pt;
        color: #334155;
        margin: 4pt 0 18pt 0;
    }}

    .cover-product {{
        font-family: Georgia, Cambria, serif;
        font-size: 26pt;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: 1px;
        margin: 0 0 6pt 0;
    }}

    .cover-subtitle {{
        font-family: Georgia, Cambria, serif;
        font-size: 12.5pt;
        color: #1e293b;
        margin: 0 0 6pt 0;
    }}

    .cover-std {{
        font-family: Georgia, Cambria, serif;
        font-size: 10.5pt;
        font-style: italic;
        color: #334155;
        margin: 0 0 22pt 0;
    }}

    .meta-table {{
        width: 100%;
        border-collapse: collapse;
        margin: 0 auto 20pt auto;
        font-family: "Segoe UI", Calibri, Arial, sans-serif;
        font-size: 9.5pt;
        text-align: left;
    }}

    .meta-table th {{
        background-color: #1B365D;
        color: #ffffff;
        padding: 7pt 10pt;
        border: 0.75pt solid #1B365D;
        font-weight: 600;
    }}

    .meta-table td {{
        padding: 5.5pt 10pt;
        border: 0.75pt solid #64748b;
        vertical-align: top;
        color: #0f172a;
    }}

    .meta-table td:first-child {{
        width: 34%;
        font-weight: 500;
        background-color: #ffffff;
    }}

    .cover-note {{
        font-family: Georgia, Cambria, serif;
        font-size: 9.8pt;
        font-style: italic;
        color: #334155;
        text-align: center;
        max-width: 92%;
        margin: 14pt auto 0 auto;
        line-height: 1.5;
    }}

    /* === HEADINGS === */
    h1 {{
        font-family: "Segoe UI", Calibri, Arial, sans-serif;
        font-size: 15.5pt;
        font-weight: 700;
        color: #1B365D;
        margin: 18pt 0 10pt 0;
        page-break-after: avoid;
    }}

    h1.chapter-start {{
        page-break-before: always;
        margin-top: 4pt;
    }}

    h2 {{
        font-family: "Segoe UI", Calibri, Arial, sans-serif;
        font-size: 12pt;
        font-weight: 700;
        color: #1B365D;
        margin: 14pt 0 6pt 0;
        page-break-after: avoid;
    }}

    h3 {{
        font-family: "Segoe UI", Calibri, Arial, sans-serif;
        font-size: 10.5pt;
        font-weight: 700;
        color: #1e3a5f;
        margin: 11pt 0 5pt 0;
        page-break-after: avoid;
    }}

    p {{
        margin: 0 0 8pt 0;
        text-align: justify;
    }}

    ul, ol {{
        margin: 0 0 9pt 0;
        padding-left: 18pt;
    }}

    li {{
        margin-bottom: 3.5pt;
        text-align: justify;
    }}

    /* === DATA TABLES === */
    table.data-table {{
        width: 100%;
        border-collapse: collapse;
        margin: 8pt 0 12pt 0;
        font-family: "Segoe UI", Calibri, Arial, sans-serif;
        font-size: 8.8pt;
        line-height: 1.42;
    }}

    table.data-table thead {{
        display: table-header-group;
    }}

    table.data-table tr {{
        page-break-inside: avoid;
    }}

    table.data-table th {{
        background-color: #1B365D;
        color: #ffffff;
        font-weight: 600;
        text-align: left;
        padding: 5.5pt 7pt;
        border: 0.75pt solid #1B365D;
    }}

    table.data-table td {{
        padding: 5pt 7pt;
        border: 0.75pt solid #64748b;
        vertical-align: top;
        color: #0f172a;
        word-wrap: break-word;
    }}

    /* === CALLOUT / HONESTY NOTES === */
    .callout {{
        background-color: #FEF9E7;
        border-left: 3.5pt solid #D4A017;
        padding: 8pt 12pt;
        margin: 10pt 0 12pt 0;
        font-family: Georgia, Cambria, serif;
        font-size: 9.5pt;
        font-style: italic;
        color: #334155;
        page-break-inside: avoid;
    }}

    /* === TOC & LIST OF FIGURES === */
    .toc-section {{
        page-break-after: always;
    }}

    .toc-list {{
        list-style: none;
        padding: 0;
        margin: 6pt 0 0 0;
        font-family: Georgia, Cambria, serif;
        font-size: 9.8pt;
    }}

    .toc-item {{
        display: flex;
        align-items: baseline;
        margin-bottom: 3.5pt;
    }}

    .toc-item.level-1 {{
        font-weight: 700;
        color: #0f172a;
        margin-top: 5pt;
    }}

    .toc-item.level-2 {{
        padding-left: 16pt;
        font-weight: 400;
        color: #1e293b;
    }}

    .toc-title {{
        flex-shrink: 0;
        max-width: 88%;
    }}

    .toc-dots {{
        flex: 1;
        border-bottom: 1pt dotted #94a3b8;
        margin: 0 5pt;
        position: relative;
        top: -2pt;
    }}

    .toc-page {{
        flex-shrink: 0;
        font-family: "Segoe UI", Calibri, Arial, sans-serif;
        font-size: 9pt;
        color: #334155;
        min-width: 18pt;
        text-align: right;
    }}

    /* === FIGURES & DIAGRAMS === */
    .figure-box {{
        margin: 10pt 0 14pt 0;
        text-align: center;
        page-break-inside: avoid;
    }}

    .figure-box svg {{
        max-width: 100%;
        height: auto;
        display: block;
        margin: 0 auto;
    }}

    .figure-box img.ui-shot {{
        max-width: 96%;
        max-height: 195mm;
        border: 1pt solid #cbd5e1;
        border-radius: 4pt;
        box-shadow: 0 2pt 8pt rgba(15, 23, 42, 0.08);
        display: block;
        margin: 0 auto;
    }}

    .figure-caption {{
        font-family: Georgia, Cambria, serif;
        font-size: 9.2pt;
        font-style: italic;
        color: #334155;
        margin-top: 6pt;
        text-align: center;
    }}

    .landscape-wrap {{
        page: landscape_page;
        page-break-before: always;
        page-break-after: always;
    }}

    /* === CODE BLOCKS === */
    pre.code-block {{
        background-color: #f8fafc;
        border: 0.75pt solid #cbd5e1;
        border-left: 3pt solid #1B365D;
        padding: 8pt 10pt;
        font-family: Consolas, "Courier New", monospace;
        font-size: 8.2pt;
        line-height: 1.42;
        color: #0f172a;
        overflow-x: auto;
        white-space: pre-wrap;
        margin: 8pt 0 11pt 0;
        page-break-inside: avoid;
    }}

    code {{
        font-family: Consolas, "Courier New", monospace;
        font-size: 8.5pt;
        color: #1B365D;
        background: #f1f5f9;
        padding: 0.5pt 3pt;
        border-radius: 2pt;
    }}

    pre.code-block code {{
        background: transparent;
        padding: 0;
        color: inherit;
    }}

    .end-marker {{
        text-align: center;
        font-style: italic;
        color: #475569;
        margin-top: 20pt;
        font-size: 10pt;
    }}

    @media print {{
        body {{
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }}
    }}
    """
