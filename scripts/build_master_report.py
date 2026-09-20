from __future__ import annotations

import math
from pathlib import Path

from docx import Document
from docx.enum.section import WD_SECTION
from docx.enum.table import WD_ALIGN_VERTICAL, WD_TABLE_ALIGNMENT
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.oxml import OxmlElement
from docx.oxml.ns import qn
from docx.shared import Cm, Inches, Pt, RGBColor
from PIL import Image, ImageDraw, ImageFont


OUT = Path("output/documents/Rapport_de_Memoire_Master_Smart_Recruit_Complet.docx")
DIAGRAM_DIR = Path("output/documents/uml_diagrams")

BLUE = RGBColor(46, 116, 181)
DARK_BLUE = RGBColor(31, 77, 120)
NAVY = RGBColor(11, 37, 69)
MUTED = RGBColor(92, 101, 112)
LIGHT_BLUE = "E8EEF5"
LIGHT_GRAY = "F4F6F9"
WHITE = "FFFFFF"


def pil_font(size=24, bold=False):
    candidates = [
        "C:/Windows/Fonts/arialbd.ttf" if bold else "C:/Windows/Fonts/arial.ttf",
        "C:/Windows/Fonts/calibrib.ttf" if bold else "C:/Windows/Fonts/calibri.ttf",
        "arial.ttf",
    ]

    for candidate in candidates:
        try:
            return ImageFont.truetype(candidate, size)
        except OSError:
            continue

    return ImageFont.load_default()


def wrap_lines(draw, text, font, max_width):
    words = text.split()
    lines = []
    current = ""

    for word in words:
        candidate = f"{current} {word}".strip()
        width = draw.textbbox((0, 0), candidate, font=font)[2]

        if width <= max_width or not current:
            current = candidate
        else:
            lines.append(current)
            current = word

    if current:
        lines.append(current)

    return lines


def draw_text_center(draw, box, text, font, fill="#0B2545"):
    x1, y1, x2, y2 = box
    lines = wrap_lines(draw, text, font, x2 - x1 - 24)
    line_height = font.size + 5
    total_height = len(lines) * line_height
    y = y1 + ((y2 - y1) - total_height) / 2

    for line in lines:
        bbox = draw.textbbox((0, 0), line, font=font)
        x = x1 + ((x2 - x1) - (bbox[2] - bbox[0])) / 2
        draw.text((x, y), line, font=font, fill=fill)
        y += line_height


def draw_box(draw, box, title, body=None, fill="#FFFFFF", outline="#2E74B5"):
    draw.rounded_rectangle(box, radius=14, fill=fill, outline=outline, width=3)
    x1, y1, x2, y2 = box
    title_font = pil_font(25, True)
    body_font = pil_font(20)
    draw_text_center(draw, (x1 + 8, y1 + 8, x2 - 8, y1 + 48), title, title_font)

    if body:
        draw.line((x1, y1 + 58, x2, y1 + 58), fill=outline, width=2)
        lines = []
        for item in body:
            lines.extend(wrap_lines(draw, item, body_font, x2 - x1 - 32))
        y = y1 + 70
        for line in lines:
            draw.text((x1 + 18, y), line, font=body_font, fill="#17201B")
            y += 26


def draw_actor(draw, x, y, label):
    font = pil_font(22, True)
    draw.ellipse((x - 22, y, x + 22, y + 44), outline="#0B2545", width=3)
    draw.line((x, y + 44, x, y + 112), fill="#0B2545", width=3)
    draw.line((x - 42, y + 68, x + 42, y + 68), fill="#0B2545", width=3)
    draw.line((x, y + 112, x - 38, y + 158), fill="#0B2545", width=3)
    draw.line((x, y + 112, x + 38, y + 158), fill="#0B2545", width=3)
    bbox = draw.textbbox((0, 0), label, font=font)
    draw.text((x - (bbox[2] - bbox[0]) / 2, y + 170), label, font=font, fill="#0B2545")


def draw_arrow(draw, start, end, fill="#5C6570", width=3):
    draw.line((start[0], start[1], end[0], end[1]), fill=fill, width=width)
    angle = math.atan2(end[1] - start[1], end[0] - start[0])
    length = 14
    for delta in (math.pi / 7, -math.pi / 7):
        x = end[0] - length * math.cos(angle + delta)
        y = end[1] - length * math.sin(angle + delta)
        draw.line((end[0], end[1], x, y), fill=fill, width=width)


def base_canvas(width=1600, height=1000):
    image = Image.new("RGB", (width, height), "#FFFFFF")
    draw = ImageDraw.Draw(image)
    draw.rectangle((0, 0, width - 1, height - 1), outline="#DADCE0", width=3)
    return image, draw


def save_diagram(image, name):
    DIAGRAM_DIR.mkdir(parents=True, exist_ok=True)
    path = DIAGRAM_DIR / name
    image.save(path, quality=95)
    return path


def build_use_case_diagram():
    image, draw = base_canvas()
    title_font = pil_font(32, True)
    draw.text((40, 28), "Diagramme de cas d'utilisation - Smart-Recruit", font=title_font, fill="#0B2545")
    system = (260, 105, 1340, 910)
    draw.rounded_rectangle(system, radius=24, outline="#2E74B5", width=4, fill="#F8FAFC")
    draw.text((620, 118), "Systeme Smart-Recruit", font=pil_font(26, True), fill="#2E74B5")

    draw_actor(draw, 135, 230, "Etudiant")
    draw_actor(draw, 135, 595, "Recruteur")
    draw_actor(draw, 1465, 405, "Admin")

    cases = {
        "Uploader CV": (350, 190, 610, 280),
        "Postuler a une offre": (350, 330, 660, 420),
        "Consulter offres": (350, 470, 610, 560),
        "Creer offre": (940, 190, 1190, 280),
        "Consulter ranking": (900, 330, 1240, 420),
        "Accepter / refuser": (910, 470, 1230, 560),
        "Vue admin": (960, 650, 1180, 740),
        "Matching semantique IA": (610, 650, 930, 750),
    }

    for label, box in cases.items():
        draw.ellipse(box, fill="#FFFFFF", outline="#2E74B5", width=3)
        draw_text_center(draw, box, label, pil_font(21, True))

    for target in ["Uploader CV", "Postuler a une offre", "Consulter offres"]:
        box = cases[target]
        draw_arrow(draw, (195, 330 if target != "Consulter offres" else 370), (box[0], (box[1] + box[3]) // 2))
    for target in ["Creer offre", "Consulter ranking", "Accepter / refuser"]:
        box = cases[target]
        draw_arrow(draw, (195, 690), (box[0], (box[1] + box[3]) // 2))
    draw_arrow(draw, (1405, 500), (cases["Vue admin"][2], 695))
    draw_arrow(draw, (660, 375), (650, 650), fill="#B44D28")
    draw_arrow(draw, (900, 375), (860, 650), fill="#B44D28")
    draw_arrow(draw, (1005, 560), (880, 650), fill="#B44D28")
    draw.text((620, 785), "<<include>>", font=pil_font(20), fill="#B44D28")
    return save_diagram(image, "uml_use_case.png")


def build_class_diagram():
    image, draw = base_canvas(1800, 1150)
    draw.text((40, 28), "Diagramme de classes - Modele principal", font=pil_font(34, True), fill="#0B2545")
    boxes = {
        "User": ((80, 130, 390, 330), ["public_id", "name", "email", "role", "password"]),
        "StudentProfile": ((520, 120, 890, 360), ["headline", "education", "skills JSON", "cv_text", "cv_metadata JSON"]),
        "RecruiterProfile": ((520, 430, 890, 640), ["company_name", "position", "website"]),
        "Offer": ((1030, 430, 1390, 680), ["title", "company", "description", "required_skills JSON", "status"]),
        "Application": ((1030, 130, 1390, 340), ["status", "applied_at", "offer_id", "student_profile_id"]),
        "MatchScore": ((1490, 130, 1760, 360), ["score", "text_similarity", "semantic_coverage", "matched_skills JSON"]),
        "CvDocument": ((1030, 760, 1390, 970), ["original_name", "stored_path", "mime_type", "parser_result JSON"]),
    }
    for title, (box, attrs) in boxes.items():
        draw_box(draw, box, title, attrs, fill="#FFFFFF")

    def center(label, side):
        x1, y1, x2, y2 = boxes[label][0]
        if side == "right":
            return (x2, (y1 + y2) // 2)
        if side == "left":
            return (x1, (y1 + y2) // 2)
        if side == "top":
            return ((x1 + x2) // 2, y1)
        return ((x1 + x2) // 2, y2)

    links = [
        ("User", "right", "StudentProfile", "left", "1", "0..1"),
        ("User", "right", "RecruiterProfile", "left", "1", "0..1"),
        ("StudentProfile", "right", "Application", "left", "1", "*"),
        ("Offer", "top", "Application", "bottom", "1", "*"),
        ("Application", "right", "MatchScore", "left", "1", "0..1"),
        ("StudentProfile", "right", "CvDocument", "left", "1", "*"),
        ("RecruiterProfile", "right", "Offer", "left", "1", "*"),
    ]
    for a, aside, b, bside, ca, cb in links:
        p1, p2 = center(a, aside), center(b, bside)
        draw.line((p1[0], p1[1], p2[0], p2[1]), fill="#5C6570", width=3)
        draw.text((p1[0] + 8, p1[1] - 26), ca, font=pil_font(18, True), fill="#5C6570")
        draw.text((p2[0] - 34, p2[1] + 8), cb, font=pil_font(18, True), fill="#5C6570")

    return save_diagram(image, "uml_class.png")


def build_sequence_diagram():
    image, draw = base_canvas(1800, 1180)
    draw.text((40, 28), "Diagramme de sequence - Candidature avec CV et matching", font=pil_font(34, True), fill="#0B2545")
    participants = [
        ("Candidat", 150),
        ("Laravel", 440),
        ("CvParser", 710),
        ("Python IA", 980),
        ("MySQL", 1250),
        ("Recruteur", 1540),
    ]
    top, bottom = 130, 1080
    for label, x in participants:
        draw.rounded_rectangle((x - 105, top, x + 105, top + 62), radius=10, fill="#E8EEF5", outline="#2E74B5", width=3)
        draw_text_center(draw, (x - 100, top, x + 100, top + 62), label, pil_font(21, True))
        draw.line((x, top + 62, x, bottom), fill="#AAB3BF", width=2)

    steps = [
        (150, 440, 240, "Soumet infos + CV"),
        (440, 710, 330, "Valide et parse le CV"),
        (710, 440, 420, "Texte + competences"),
        (440, 1250, 510, "Cree profil + candidature"),
        (440, 980, 610, "POST /match"),
        (980, 440, 700, "Score + details"),
        (440, 1250, 790, "Sauvegarde MatchScore"),
        (1540, 440, 900, "Consulte ranking"),
        (440, 1540, 990, "Liste triee + actions"),
    ]

    for x1, x2, y, label in steps:
        draw_arrow(draw, (x1, y), (x2, y), fill="#0B2545")
        mid = (x1 + x2) / 2
        draw.text((mid - 120, y - 30), label, font=pil_font(18), fill="#17201B")

    return save_diagram(image, "uml_sequence.png")


def build_deployment_diagram():
    image, draw = base_canvas(1700, 1000)
    draw.text((40, 28), "Diagramme de deploiement - Architecture Smart-Recruit", font=pil_font(34, True), fill="#0B2545")
    draw.rounded_rectangle((250, 130, 1450, 890), radius=28, outline="#2E74B5", width=4, fill="#F8FAFC")
    draw.text((720, 150), "Docker Compose / Environnement local", font=pil_font(26, True), fill="#2E74B5")

    nodes = {
        "Navigateur": (60, 430, 250, 560),
        "Laravel PHP": (390, 250, 700, 410),
        "Microservice Python IA": (990, 250, 1340, 410),
        "MySQL": (990, 610, 1340, 770),
        "JSON fallback": (390, 610, 700, 770),
    }
    for label, box in nodes.items():
        fill = "#FFFFFF" if label != "JSON fallback" else "#FFF7ED"
        draw_box(draw, box, label, [], fill=fill)

    draw_arrow(draw, (250, 495), (390, 330))
    draw.text((255, 395), "HTTP 8080/8088", font=pil_font(18), fill="#5C6570")
    draw_arrow(draw, (700, 330), (990, 330))
    draw.text((760, 292), "REST /match /parse-cv", font=pil_font(18), fill="#5C6570")
    draw_arrow(draw, (700, 410), (990, 680))
    draw.text((790, 548), "SQL 3306/3307", font=pil_font(18), fill="#5C6570")
    draw_arrow(draw, (545, 410), (545, 610), fill="#B44D28")
    draw.text((575, 500), "secours si MySQL absent", font=pil_font(18), fill="#B44D28")
    draw_arrow(draw, (540, 250), (1150, 250), fill="#2E74B5")
    draw.text((740, 212), "AI_SERVICE_URL=http://ai-service:8010", font=pil_font(18), fill="#2E74B5")
    return save_diagram(image, "uml_deployment.png")


def build_uml_diagrams():
    return {
        "use_case": build_use_case_diagram(),
        "class": build_class_diagram(),
        "sequence": build_sequence_diagram(),
        "deployment": build_deployment_diagram(),
    }


def set_run_font(run, name="Calibri", size=None, color=None, bold=None, italic=None):
    run.font.name = name
    run._element.rPr.rFonts.set(qn("w:ascii"), name)
    run._element.rPr.rFonts.set(qn("w:hAnsi"), name)
    if size is not None:
        run.font.size = Pt(size)
    if color is not None:
        run.font.color.rgb = color
    if bold is not None:
        run.bold = bold
    if italic is not None:
        run.italic = italic


def paragraph_border_bottom(paragraph, color="D7DBE2", size="8"):
    pPr = paragraph._p.get_or_add_pPr()
    pBdr = pPr.find(qn("w:pBdr"))
    if pBdr is None:
        pBdr = OxmlElement("w:pBdr")
        pPr.append(pBdr)
    bottom = OxmlElement("w:bottom")
    bottom.set(qn("w:val"), "single")
    bottom.set(qn("w:sz"), size)
    bottom.set(qn("w:space"), "4")
    bottom.set(qn("w:color"), color)
    pBdr.append(bottom)


def set_cell_shading(cell, fill):
    tcPr = cell._tc.get_or_add_tcPr()
    shd = tcPr.find(qn("w:shd"))
    if shd is None:
        shd = OxmlElement("w:shd")
        tcPr.append(shd)
    shd.set(qn("w:fill"), fill)


def set_cell_margins(cell, top=80, start=120, bottom=80, end=120):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = tcPr.first_child_found_in("w:tcMar")
    if tcMar is None:
        tcMar = OxmlElement("w:tcMar")
        tcPr.append(tcMar)
    for m, v in [("top", top), ("start", start), ("bottom", bottom), ("end", end)]:
        node = tcMar.find(qn(f"w:{m}"))
        if node is None:
            node = OxmlElement(f"w:{m}")
            tcMar.append(node)
        node.set(qn("w:w"), str(v))
        node.set(qn("w:type"), "dxa")


def set_table_geometry(table, widths_inches):
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = False
    tbl = table._tbl
    tblPr = tbl.tblPr
    tblW = tblPr.find(qn("w:tblW"))
    if tblW is None:
        tblW = OxmlElement("w:tblW")
        tblPr.append(tblW)
    tblW.set(qn("w:type"), "dxa")
    tblW.set(qn("w:w"), str(sum(int(w * 1440) for w in widths_inches)))
    tblInd = tblPr.find(qn("w:tblInd"))
    if tblInd is None:
        tblInd = OxmlElement("w:tblInd")
        tblPr.append(tblInd)
    tblInd.set(qn("w:type"), "dxa")
    tblInd.set(qn("w:w"), "120")
    grid = tbl.tblGrid
    if grid is not None:
        for child in list(grid):
            grid.remove(child)
    else:
        grid = OxmlElement("w:tblGrid")
        tbl.insert(0, grid)
    for width in widths_inches:
        col = OxmlElement("w:gridCol")
        col.set(qn("w:w"), str(int(width * 1440)))
        grid.append(col)
    for row in table.rows:
        for idx, cell in enumerate(row.cells):
            cell.width = Inches(widths_inches[idx])
            tcPr = cell._tc.get_or_add_tcPr()
            tcW = tcPr.find(qn("w:tcW"))
            if tcW is None:
                tcW = OxmlElement("w:tcW")
                tcPr.append(tcW)
            tcW.set(qn("w:type"), "dxa")
            tcW.set(qn("w:w"), str(int(widths_inches[idx] * 1440)))
            set_cell_margins(cell)
            cell.vertical_alignment = WD_ALIGN_VERTICAL.CENTER


def set_repeat_table_header(row):
    trPr = row._tr.get_or_add_trPr()
    tblHeader = OxmlElement("w:tblHeader")
    tblHeader.set(qn("w:val"), "true")
    trPr.append(tblHeader)


def add_page_number(paragraph):
    paragraph.alignment = WD_ALIGN_PARAGRAPH.RIGHT
    run = paragraph.add_run("Page ")
    set_run_font(run, size=9, color=MUTED)
    fld_begin = OxmlElement("w:fldChar")
    fld_begin.set(qn("w:fldCharType"), "begin")
    instr = OxmlElement("w:instrText")
    instr.set(qn("xml:space"), "preserve")
    instr.text = "PAGE"
    fld_sep = OxmlElement("w:fldChar")
    fld_sep.set(qn("w:fldCharType"), "separate")
    fld_text = OxmlElement("w:t")
    fld_text.text = "1"
    fld_end = OxmlElement("w:fldChar")
    fld_end.set(qn("w:fldCharType"), "end")
    r = paragraph.add_run()._r
    r.append(fld_begin)
    r.append(instr)
    r.append(fld_sep)
    r.append(fld_text)
    r.append(fld_end)


def configure_document(doc):
    section = doc.sections[0]
    section.page_width = Inches(8.5)
    section.page_height = Inches(11)
    section.top_margin = Inches(1)
    section.bottom_margin = Inches(1)
    section.left_margin = Inches(1)
    section.right_margin = Inches(1)
    section.header_distance = Inches(0.492)
    section.footer_distance = Inches(0.492)

    styles = doc.styles
    normal = styles["Normal"]
    normal.font.name = "Calibri"
    normal._element.rPr.rFonts.set(qn("w:ascii"), "Calibri")
    normal._element.rPr.rFonts.set(qn("w:hAnsi"), "Calibri")
    normal.font.size = Pt(11)
    normal.paragraph_format.space_after = Pt(8)
    normal.paragraph_format.line_spacing = 1.25

    for name, size, color, before, after in [
        ("Heading 1", 16, BLUE, 18, 10),
        ("Heading 2", 13, BLUE, 12, 6),
        ("Heading 3", 12, DARK_BLUE, 8, 4),
    ]:
        style = styles[name]
        style.font.name = "Calibri"
        style._element.rPr.rFonts.set(qn("w:ascii"), "Calibri")
        style._element.rPr.rFonts.set(qn("w:hAnsi"), "Calibri")
        style.font.size = Pt(size)
        style.font.color.rgb = color
        style.font.bold = True
        style.paragraph_format.space_before = Pt(before)
        style.paragraph_format.space_after = Pt(after)
        style.paragraph_format.line_spacing = 1.25

    for list_style in ["List Bullet", "List Number"]:
        style = styles[list_style]
        style.font.name = "Calibri"
        style._element.rPr.rFonts.set(qn("w:ascii"), "Calibri")
        style._element.rPr.rFonts.set(qn("w:hAnsi"), "Calibri")
        style.font.size = Pt(11)
        style.paragraph_format.space_after = Pt(4)
        style.paragraph_format.line_spacing = 1.208

    header = section.header
    p = header.paragraphs[0]
    p.text = "Memoire de Master - Smart-Recruit"
    p.alignment = WD_ALIGN_PARAGRAPH.LEFT
    set_run_font(p.runs[0], size=9, color=MUTED)
    paragraph_border_bottom(p, "D7DBE2", "6")

    footer = section.footer
    p = footer.paragraphs[0]
    add_page_number(p)


def add_cover(doc):
    doc.add_paragraph()
    p = doc.add_paragraph("REPUBLIQUE TUNISIENNE")
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    set_run_font(p.runs[0], size=12, bold=True, color=NAVY)
    p = doc.add_paragraph("MINISTERE DE L'ENSEIGNEMENT SUPERIEUR ET DE LA RECHERCHE SCIENTIFIQUE")
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    set_run_font(p.runs[0], size=11, bold=True, color=NAVY)
    doc.add_paragraph()
    p = doc.add_paragraph("MEMOIRE DE FIN D'ETUDES DE MASTER")
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    set_run_font(p.runs[0], size=16, bold=True, color=BLUE)
    doc.add_paragraph()
    title = "Conception et realisation d'une plateforme de mise en relation etudiants-entreprises assistee par un moteur de recommandation semantique"
    p = doc.add_paragraph(title.upper())
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p.paragraph_format.space_after = Pt(14)
    set_run_font(p.runs[0], size=20, bold=True, color=NAVY)
    p = doc.add_paragraph("Projet Smart-Recruit")
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    set_run_font(p.runs[0], size=15, italic=True, color=MUTED)
    doc.add_paragraph()
    table = doc.add_table(rows=4, cols=2)
    set_table_geometry(table, [2.2, 4.3])
    rows = [
        ("Specialite", "Informatique - Software et Nouvelles Technologies"),
        ("Presente par", "Houssem Hammami"),
        ("Encadrant", "Enseignant - Encadrant academique"),
        ("Annee universitaire", "2025 - 2026"),
    ]
    for row, (label, value) in zip(table.rows, rows):
        row.cells[0].text = label
        row.cells[1].text = value
        set_cell_shading(row.cells[0], LIGHT_BLUE)
        for cell in row.cells:
            for p in cell.paragraphs:
                p.paragraph_format.space_after = Pt(2)
                for run in p.runs:
                    set_run_font(run, size=10.5, color=NAVY if cell is row.cells[0] else None, bold=cell is row.cells[0])
    doc.add_paragraph()
    p = doc.add_paragraph("Rapport revise et complete en version Word")
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    set_run_font(p.runs[0], size=11, color=MUTED)
    doc.add_page_break()


def add_heading(doc, text, level=1):
    return doc.add_heading(text, level=level)


def add_para(doc, text, style=None, align=None, bold_prefix=None):
    p = doc.add_paragraph(style=style)
    if bold_prefix and text.startswith(bold_prefix):
        r = p.add_run(bold_prefix)
        set_run_font(r, bold=True)
        r = p.add_run(text[len(bold_prefix):])
        set_run_font(r)
    else:
        r = p.add_run(text)
        set_run_font(r)
    if align is not None:
        p.alignment = align
    return p


def add_bullets(doc, items):
    for item in items:
        add_para(doc, item, style="List Bullet")


def add_paragraphs(doc, paragraphs):
    for text in paragraphs:
        add_para(doc, text)


def add_numbers(doc, items):
    for item in items:
        add_para(doc, item, style="List Number")


def add_caption(doc, text):
    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    r = p.add_run(text)
    set_run_font(r, size=9.5, italic=True, color=MUTED)
    p.paragraph_format.space_before = Pt(2)
    p.paragraph_format.space_after = Pt(10)


def add_diagram(doc, path, caption):
    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = p.add_run()
    run.add_picture(str(path), width=Inches(6.35))
    p.paragraph_format.space_before = Pt(4)
    p.paragraph_format.space_after = Pt(2)
    add_caption(doc, caption)


def add_table(doc, headers, rows, widths, caption=None):
    table = doc.add_table(rows=1, cols=len(headers))
    table.style = "Table Grid"
    set_table_geometry(table, widths)
    hdr = table.rows[0]
    set_repeat_table_header(hdr)
    for idx, h in enumerate(headers):
        cell = hdr.cells[idx]
        cell.text = h
        set_cell_shading(cell, LIGHT_BLUE)
        for p in cell.paragraphs:
            p.paragraph_format.space_after = Pt(0)
            for run in p.runs:
                set_run_font(run, size=9.5, bold=True, color=NAVY)
    for row_values in rows:
        row = table.add_row()
        for idx, value in enumerate(row_values):
            cell = row.cells[idx]
            cell.text = str(value)
            for p in cell.paragraphs:
                p.paragraph_format.space_after = Pt(0)
                p.paragraph_format.line_spacing = 1.15
                for run in p.runs:
                    set_run_font(run, size=9.3)
    set_table_geometry(table, widths)
    if caption:
        add_caption(doc, caption)
    return table


def add_callout(doc, title, body):
    table = doc.add_table(rows=1, cols=1)
    table.style = "Table Grid"
    set_table_geometry(table, [6.5])
    cell = table.cell(0, 0)
    set_cell_shading(cell, LIGHT_GRAY)
    p = cell.paragraphs[0]
    r = p.add_run(title)
    set_run_font(r, size=10.5, bold=True, color=NAVY)
    p.paragraph_format.space_after = Pt(3)
    p = cell.add_paragraph()
    r = p.add_run(body)
    set_run_font(r, size=10)
    p.paragraph_format.space_after = Pt(0)


def add_code_block(doc, code, caption=None):
    table = doc.add_table(rows=1, cols=1)
    table.style = "Table Grid"
    set_table_geometry(table, [6.5])
    cell = table.cell(0, 0)
    set_cell_shading(cell, "F8FAFC")
    p = cell.paragraphs[0]
    for line_index, line in enumerate(code.strip("\n").splitlines()):
        if line_index:
            p.add_run().add_break()
        r = p.add_run(line)
        set_run_font(r, name="Consolas", size=8.5, color=RGBColor(30, 41, 59))
    p.paragraph_format.space_after = Pt(0)
    if caption:
        add_caption(doc, caption)


def add_preliminaries(doc):
    add_heading(doc, "Dedicace", 1)
    add_para(doc, "Je dedie ce travail a ma famille, a mes enseignants et a toutes les personnes qui m'ont accompagne durant ce parcours universitaire. Leur soutien moral, leurs conseils et leur patience ont constitue un appui essentiel dans la realisation de ce projet.")
    add_heading(doc, "Remerciements", 1)
    add_para(doc, "Je tiens a exprimer ma gratitude a mon encadrant academique pour son accompagnement, ses remarques constructives et son exigence scientifique. Je remercie egalement les enseignants de la specialite Informatique - Software et Nouvelles Technologies pour la qualite de la formation dispensee.")
    add_para(doc, "Mes remerciements s'adressent aussi aux professionnels et camarades qui ont contribue, directement ou indirectement, a la validation des besoins fonctionnels et techniques de Smart-Recruit.")
    doc.add_page_break()

    add_heading(doc, "Resume", 1)
    add_para(doc, "La croissance du nombre de candidatures dans les plateformes de stages et d'emploi rend le tri manuel des CV de plus en plus couteux, lent et subjectif. Les systemes classiques de suivi des candidatures reposent principalement sur des recherches syntaxiques par mots-cles exacts. Cette approche elimine parfois des profils pertinents lorsque les competences sont exprimees par synonymes, abréviations ou formulations differentes.")
    add_para(doc, "Ce memoire presente la conception et la realisation de Smart-Recruit, une plateforme web de mise en relation entre etudiants et entreprises, enrichie par un moteur de recommandation semantique CV-offres. Le projet combine une application Laravel pour les fonctions metier, une base MySQL pour la persistance, un microservice Python expose par API REST pour le traitement NLP, et une configuration Docker Compose pour le deploiement distribue.")
    add_para(doc, "Le moteur de matching repose sur l'extraction de competences, la normalisation par alias, la vectorisation TF-IDF, la similarite cosinus et une couverture semantique par taxonomie. Il produit un score entre 0 et 100, accompagne des competences trouvees et manquantes afin de rendre le classement explicable pour le recruteur.")
    add_para(doc, "Mots-cles : recrutement numerique, NLP, TF-IDF, similarite cosinus, Laravel, Python, MySQL, microservices, Docker, recommandation semantique.")

    add_heading(doc, "Abstract", 1)
    add_para(doc, "The increasing volume of internship and job applications makes manual CV screening costly and error-prone. Traditional Applicant Tracking Systems usually rely on exact keyword matching, which can reject relevant candidates when skills are expressed through synonyms, abbreviations, or related technical concepts.")
    add_para(doc, "This thesis presents Smart-Recruit, a web platform connecting students and companies with a semantic CV-job matching engine. The solution combines a Laravel business application, a MySQL database, a Python REST microservice for NLP processing, and Docker Compose configuration for distributed deployment.")
    add_para(doc, "The matching engine uses skill extraction, alias normalization, TF-IDF vectorization, cosine similarity, and semantic category coverage. It returns a 0-100 score together with matched and missing skills, making candidate ranking explainable for recruiters.")
    add_para(doc, "Keywords: e-recruitment, NLP, TF-IDF, cosine similarity, Laravel, Python, MySQL, microservices, Docker, semantic recommendation.")
    doc.add_page_break()

    add_heading(doc, "Liste des abreviations", 1)
    add_table(doc, ["Abreviation", "Signification"], [
        ("API", "Application Programming Interface"),
        ("ATS", "Applicant Tracking System"),
        ("CV", "Curriculum Vitae"),
        ("DB", "Database"),
        ("ETL", "Extract, Transform, Load"),
        ("HTTP", "HyperText Transfer Protocol"),
        ("IA", "Intelligence Artificielle"),
        ("JSON", "JavaScript Object Notation"),
        ("MVC", "Model View Controller"),
        ("NLP", "Natural Language Processing"),
        ("OCR", "Optical Character Recognition"),
        ("REST", "Representational State Transfer"),
        ("SQL", "Structured Query Language"),
        ("TF-IDF", "Term Frequency - Inverse Document Frequency"),
        ("UML", "Unified Modeling Language"),
    ], [1.5, 5.0], "Tableau 1 - Liste des abreviations.")
    add_heading(doc, "Table des matieres", 1)
    toc_items = [
        "Introduction generale",
        "Chapitre 1 - Etat de l'art et fondements theoriques",
        "Chapitre 2 - Analyse des besoins et conception",
        "Chapitre 3 - Realisation et implementation",
        "Chapitre 4 - Tests, validation et evaluation",
        "Chapitre 5 - Discussion, limites et perspectives",
        "Conclusion generale",
        "Bibliographie",
        "Annexes techniques",
    ]
    add_numbers(doc, toc_items)
    add_heading(doc, "Liste des figures et tableaux", 1)
    add_bullets(doc, [
        "Figure 1 - Chaine de valeur de Smart-Recruit.",
        "Figure 2 - Architecture logique Laravel, Python et MySQL.",
        "Figure 3 - Sequence de candidature avec calcul du score.",
        "Figure 4 - Pipeline algorithmique du matching semantique.",
        "Figure UML-1 - Diagramme de cas d'utilisation.",
        "Figure UML-2 - Diagramme de classes.",
        "Figure UML-3 - Diagramme de sequence.",
        "Figure UML-4 - Diagramme de deploiement.",
        "Tableau 1 - Liste des abreviations.",
        "Tableau 2 - Comparaison des approches de matching.",
        "Tableau 3 - Besoins fonctionnels.",
        "Tableau 4 - Schema relationnel principal.",
        "Tableau 5 - Endpoints applicatifs.",
        "Tableau 6 - Jeux de tests et resultats attendus.",
    ])
    doc.add_page_break()


def add_introduction(doc):
    add_heading(doc, "Introduction generale", 1)
    add_para(doc, "La transformation numerique des processus de recrutement a modifie la maniere dont les entreprises identifient les talents et dont les etudiants accedent aux stages. Les plateformes web offrent un espace centralise pour publier des offres, deposer des CV et suivre les candidatures. Cependant, cette centralisation produit un volume important de donnees textuelles que les recruteurs doivent analyser rapidement.")
    add_para(doc, "Dans un contexte universitaire et professionnel, le tri manuel des CV devient une tache repetitive, chronophage et parfois subjective. Les recruteurs doivent comparer des descriptions d'offres avec des profils heterogenes, rediges selon des styles differents. Un meme savoir-faire peut apparaitre sous plusieurs formes : ReactJS, React, front-end moderne, interface web ou composants UI. Une recherche exacte ne suffit donc pas a representer la proximite reelle entre une offre et un candidat.")
    add_para(doc, "Le projet Smart-Recruit s'inscrit dans cette problematique. Il vise a concevoir une plateforme de mise en relation etudiants-entreprises qui ne se limite pas a stocker les candidatures, mais qui propose une aide a la decision basee sur un matching semantique. L'objectif est de produire un classement explicable des candidats pour chaque offre, en tenant compte a la fois des competences explicites, de la similarite textuelle et des familles technologiques.")
    add_heading(doc, "Problematique", 2)
    add_para(doc, "La question centrale de ce travail peut etre formulee ainsi : comment concevoir et realiser une plateforme web capable d'automatiser la preselections des candidatures de stages en exploitant le traitement automatique du langage naturel, tout en conservant une architecture logicielle maintenable, testable et deployable ?")
    add_heading(doc, "Objectifs du projet", 2)
    add_bullets(doc, [
        "Mettre en place une plateforme web pour gerer les profils etudiants, les offres, les candidatures et les decisions recruteur.",
        "Extraire automatiquement les informations utiles d'un CV : texte, competences, email, diplome et experience lorsque le format le permet.",
        "Calculer un score d'adequation semantique entre une offre et un CV sur une echelle de 0 a 100.",
        "Afficher un classement des candidats par offre, avec competences trouvees, competences manquantes et statut de candidature.",
        "Separer les responsabilites entre l'application metier Laravel, le microservice IA Python et la base de donnees MySQL.",
        "Prevoir un deploiement par Docker Compose et un mode de secours local lorsque le service IA ou MySQL est indisponible.",
    ])
    add_heading(doc, "Methodologie adoptee", 2)
    add_para(doc, "La demarche suivie combine une approche d'ingenierie logicielle et une approche experimentale. L'analyse fonctionnelle identifie les acteurs, les besoins et les contraintes. La conception UML precise les entites, les interactions et le deploiement. La realisation implemente un prototype operationnel. Enfin, la validation repose sur des scenarios fonctionnels et sur un jeu de donnees etendu couvrant plusieurs domaines de stages.")
    add_paragraphs(doc, [
        "La premiere phase du travail a consiste a formaliser le besoin. Le projet ne devait pas seulement afficher des offres et des candidats ; il devait proposer un mecanisme de comparaison capable de reduire l'effort de tri. Cette phase a conduit a identifier les donnees minimales : profils, CV, offres, candidatures, statuts et scores.",
        "La deuxieme phase a concerne la conception technique. Plusieurs choix etaient possibles : tout implementer dans Laravel, tout placer dans un service Python, ou separer les responsabilites. Le choix final retient une application Laravel pour les flux metier et un microservice Python pour l'IA, car cette separation correspond mieux aux competences de chaque ecosysteme.",
        "La troisieme phase a ete incrementale. Les fonctions de base ont ete implementees, puis enrichies : ajout du stockage MySQL, fallback JSON, parsing CV, matching local, service IA, candidature avec CV, decision recruteur, admin et dataset etendu. Cette progression a permis de garder un prototype toujours testable.",
        "La derniere phase a porte sur la validation et la documentation. Les routes, migrations, pages et scores ont ete verifies. Le rapport a ete revise afin d'aligner le discours academique avec le projet reel, notamment concernant les ports, les tables, le modele IA utilise et les limites actuelles.",
    ])
    add_heading(doc, "Structure du rapport", 2)
    add_para(doc, "Ce rapport est organise en cinq chapitres. Le premier chapitre presente l'etat de l'art des systemes de recrutement, du NLP et des architectures microservices. Le deuxieme chapitre detaille l'analyse des besoins et la conception. Le troisieme chapitre decrit la realisation technique. Le quatrieme chapitre expose les tests et la validation. Le cinquieme chapitre discute les limites et les perspectives.")
    add_callout(doc, "Fil conducteur du memoire", "Le rapport suit le cycle complet d'un projet logiciel : contexte, problematique, conception, implementation, verification, discussion et perspectives.")
    doc.add_page_break()


def add_chapter_1(doc):
    add_heading(doc, "Chapitre 1 - Etat de l'art et fondements theoriques", 1)
    add_heading(doc, "1.1 Recrutement numerique et limites des ATS classiques", 2)
    add_para(doc, "Les Applicant Tracking Systems constituent aujourd'hui une brique courante dans les organisations. Ils permettent de centraliser les offres, collecter les candidatures, organiser les statuts et filtrer les profils. Leur valeur operationnelle est reelle, mais leur logique de filtrage demeure souvent lexicale : le systeme recherche des chaines de caracteres ou des mots-cles exacts dans le CV.")
    add_para(doc, "Cette approche est simple et rapide, mais elle ne tient pas compte de la variabilite du langage. Un candidat peut mentionner J2EE alors que l'offre indique Java Enterprise ; un autre peut ecrire API REST alors que le recruteur attend backend ; un profil data peut citer Power BI et SQL sans employer explicitement l'expression data analysis. Le filtrage exact peut donc provoquer des faux negatifs.")
    add_para(doc, "A l'inverse, la simple presence d'un mot-cle ne garantit pas la maitrise. Un CV peut indiquer une initiation a Python sans etre pertinent pour une mission de data engineering. Le systeme doit donc combiner plusieurs signaux : competences detectees, frequence des termes, contexte technique, experience et famille de competences.")
    add_table(doc, ["Approche", "Principe", "Avantages", "Limites"], [
        ("SQL LIKE", "Recherche de chaines exactes", "Simple, rapide, facile a implementer", "Ne gere pas les synonymes ni le contexte"),
        ("Mots-cles ponderes", "Score selon presence de termes", "Plus fin qu'une recherche binaire", "Depend fortement du dictionnaire choisi"),
        ("TF-IDF + cosinus", "Vectorisation statistique et similarite angulaire", "Explicable, leger, adapte aux prototypes", "Moins performant sur les nuances profondes"),
        ("Embeddings", "Representations denses apprises", "Capte mieux le contexte et les synonymes", "Cout de calcul et complexite plus eleves"),
        ("BERT/CamemBERT", "Transformers contextuels", "Performance semantique avancee", "Besoin de donnees, ressources et evaluation rigoureuse"),
    ], [1.25, 1.55, 1.85, 1.85], "Tableau 2 - Comparaison des approches de matching CV-offre.")
    add_heading(doc, "1.2 Traitement automatique du langage naturel", 2)
    add_para(doc, "Le Traitement Automatique du Langage Naturel, ou NLP, regroupe les methodes permettant a un systeme informatique d'analyser, normaliser, comparer ou generer du texte humain. Dans Smart-Recruit, le NLP est utilise de maniere pragmatique : extraire le texte d'un CV, detecter des competences, normaliser des alias et calculer une proximite avec une offre.")
    add_paragraphs(doc, [
        "Un CV est un document semi-structure. Il contient generalement des rubriques relativement stables - formation, experience, competences, projets et coordonnees - mais leur ordre, leur vocabulaire et leur granularite changent fortement d'un candidat a l'autre. Cette variabilite rend difficile une extraction uniquement basee sur des positions fixes ou sur un modele de formulaire.",
        "Les offres de stage presentent la meme heterogeneite. Une entreprise peut decrire une mission backend en parlant de Laravel, API REST et MySQL, tandis qu'une autre peut utiliser les termes PHP, architecture MVC et base relationnelle. Pour un humain, ces formulations sont proches ; pour un filtre exact, elles sont souvent differentes. Le NLP intervient justement pour rapprocher ces formulations.",
        "Dans ce projet, l'objectif n'est pas de construire un assistant conversationnel ou un modele linguistique generatif. L'objectif est plus cible : comparer deux textes courts ou moyens et produire un signal utile au recruteur. Cette delimitation permet d'utiliser des methodes interpretable et peu couteuses en ressources, adaptees a un prototype academique.",
        "La normalisation joue un role central. Avant le calcul, les textes sont convertis en minuscules, les caracteres non pertinents sont reduits et les expressions equivalentes sont ramenees vers des competences canoniques. Par exemple, ReactJS, react.js et front-end react peuvent etre ramenes vers react. Cette etape reduit le bruit et augmente la robustesse du matching.",
    ])
    add_heading(doc, "1.2.1 Particularites linguistiques des CV", 3)
    add_paragraphs(doc, [
        "Les CV des etudiants en informatique melangent souvent plusieurs langues. Un candidat tunisien peut rediger une partie en francais, citer des frameworks en anglais et employer des abreviations issues du monde professionnel. Une approche strictement monolingue est donc insuffisante. Smart-Recruit evite de dependre d'un dictionnaire unique de langue naturelle et s'appuie plutot sur des alias techniques.",
        "Les competences techniques ont aussi une granularite variable. Un candidat peut indiquer Java, JEE, Spring Boot ou backend Java. Selon le niveau de detail de l'offre, toutes ces formulations peuvent etre pertinentes. La taxonomie de competences permet de capter une proximite de famille meme lorsque la competence exacte n'est pas identique.",
        "La presence de projets academiques dans les CV ajoute une autre difficulte. Un etudiant peut avoir realise une plateforme de stages avec React et Laravel sans se presenter explicitement comme developpeur full-stack. Le texte descriptif du projet devient alors une source importante pour detecter l'adequation.",
    ])
    add_heading(doc, "1.3 Vectorisation TF-IDF", 2)
    add_para(doc, "La vectorisation TF-IDF transforme un document textuel en vecteur numerique. La composante TF mesure la frequence d'un terme dans un document. La composante IDF reduit le poids des mots trop frequents dans l'ensemble du corpus. Ainsi, les termes caracteristiques d'un CV ou d'une offre pesent davantage que les mots generiques.")
    add_code_block(doc, """
TF(t, d) = frequence du terme t dans le document d / nombre total de termes de d
IDF(t, D) = log((1 + nombre de documents) / (1 + nombre de documents contenant t)) + 1
TF-IDF(t, d, D) = TF(t, d) * IDF(t, D)
""", "Figure 1 - Formulation simplifiee de la ponderation TF-IDF.")
    add_heading(doc, "1.4 Similarite cosinus", 2)
    add_para(doc, "Une fois l'offre et le CV representes sous forme de vecteurs, la similarite cosinus mesure le cosinus de l'angle entre ces vecteurs. Plus l'angle est faible, plus les documents partagent des caracteristiques textuelles. Cette mesure est particulierement adaptee aux textes de tailles differentes, car elle compare l'orientation des vecteurs plutot que leur longueur brute.")
    add_code_block(doc, """
cos(A, B) = (A . B) / (||A|| * ||B||)
score_textuel = cosinus * 100
""", "Figure 2 - Principe de la similarite cosinus.")
    add_heading(doc, "1.5 Matching semantique explicable", 2)
    add_para(doc, "Dans un contexte de recrutement, un score opaque est difficilement acceptable. Le recruteur doit comprendre pourquoi un profil apparait en tete ou pourquoi une candidature est jugee partielle. Smart-Recruit privilegie donc un moteur explicable : le score global est accompagne des competences trouvees, des competences manquantes, du taux de similarite textuelle et d'une couverture semantique par categorie.")
    add_paragraphs(doc, [
        "L'explicabilite repond a un besoin pratique et a un besoin ethique. Sur le plan pratique, elle aide le recruteur a preparer l'entretien : les competences manquantes deviennent des points a verifier. Sur le plan ethique, elle evite que le candidat soit evalue par une decision algorithmique incomprehensible.",
        "Un score de 82 % n'a de valeur que si le systeme peut indiquer pourquoi ce score est eleve. Dans Smart-Recruit, cette explication est fournie par les listes de competences correspondantes et manquantes. Un profil peut ainsi obtenir un score eleve parce qu'il couvre Java, Spring Boot, React, SQL et Git ; un autre peut obtenir un score moyen parce qu'il partage seulement SQL et Git.",
        "Cette approche permet aussi de limiter les erreurs d'interpretation. Un score faible ne signifie pas necessairement que le candidat est mauvais ; il signifie que le CV actuel ne correspond pas fortement a l'offre cible. Le recruteur garde donc la decision finale, tandis que le systeme fournit un ordre de lecture prioritaire.",
    ])
    add_heading(doc, "1.6 Architectures monolithiques et microservices", 2)
    add_para(doc, "Un monolithe regroupe dans une meme application l'interface, la logique metier, l'acces aux donnees et les traitements algorithmiques. Ce modele est simple au depart, mais il devient rigide lorsque les besoins evoluent. Les traitements NLP, l'extraction PDF et les calculs vectoriels appartiennent souvent a un ecosysteme Python different de l'ecosysteme web PHP.")
    add_para(doc, "L'approche microservices permet de separer ces responsabilites. Laravel gere les utilisateurs, les offres, les candidatures et les vues. Python traite l'IA et expose une API REST. MySQL conserve les donnees. Cette separation facilite les evolutions futures : remplacer l'algorithme TF-IDF par un modele BERT ne necessite pas de reecrire l'application metier.")
    add_callout(doc, "Choix retenu", "Smart-Recruit adopte une architecture hybride : une application Laravel principale, un microservice Python IA, une base MySQL et un fallback JSON pour la demonstration ou les contextes sans base disponible.")
    add_heading(doc, "1.7 Systeme de recommandation applique au recrutement", 2)
    add_paragraphs(doc, [
        "Un systeme de recommandation cherche a ordonner des elements selon leur pertinence pour un utilisateur ou un contexte. Dans le commerce electronique, il recommande des produits ; dans la formation, il recommande des cours ; dans Smart-Recruit, il recommande des candidats pour une offre. Le principe reste similaire : transformer des donnees heterogenes en signaux comparables.",
        "Le cas du recrutement impose toutefois une prudence particuliere. Une recommandation de candidat ne doit pas remplacer le jugement humain, car le CV ne represente qu'une partie du potentiel d'une personne. La plateforme doit donc etre concue comme un outil d'aide a la decision. Le ranking accelere la lecture initiale, mais l'entretien, les projets et les criteres humains restent indispensables.",
        "La valeur du systeme augmente lorsque les donnees sont structurees. Les competences saisies manuellement, les competences extraites du CV, le texte de l'offre et les statuts de candidature forment ensemble une base exploitable pour ameliorer les recommandations futures. A long terme, des retours recruteurs pourraient alimenter un apprentissage supervise.",
    ])
    add_heading(doc, "1.8 Positionnement scientifique du projet", 2)
    add_paragraphs(doc, [
        "Le projet se situe a l'intersection du genie logiciel, des bases de donnees, du traitement automatique du langage naturel et des systemes d'aide a la decision. Son interet ne reside pas uniquement dans l'algorithme de similarite, mais dans l'integration complete d'un processus metier : du depot de CV jusqu'a la decision recruteur.",
        "Le choix de TF-IDF et de la similarite cosinus est volontairement conservateur. Ces methodes sont anciennes, bien documentees et faciles a expliquer dans un contexte academique. Elles offrent une base de comparaison solide pour de futures methodes plus avancees telles que les embeddings ou les Transformers.",
        "Le projet demontre aussi que l'IA utile n'est pas toujours synonyme de modele lourd. Pour un prototype de preselections, une solution explicable, rapide et maintenable peut offrir plus de valeur immediate qu'un modele complexe difficile a deployer et a justifier.",
    ])
    doc.add_page_break()


def add_chapter_2(doc):
    add_heading(doc, "Chapitre 2 - Analyse des besoins et conception", 1)
    add_heading(doc, "2.1 Acteurs du systeme", 2)
    add_para(doc, "Le systeme est concu autour de trois roles principaux : etudiant, recruteur et administrateur. Le mode actuel propose une session de demonstration multi-roles afin de faciliter la soutenance et les tests rapides. Une authentification de production peut etre ajoutee ulterieurement avec Laravel Breeze, Fortify ou Sanctum.")
    add_table(doc, ["Acteur", "Responsabilites principales"], [
        ("Etudiant", "Creer un profil, deposer un CV, consulter les offres et postuler."),
        ("Recruteur", "Publier des offres, consulter le ranking, analyser les competences manquantes, accepter ou refuser une candidature."),
        ("Administrateur", "Observer la synthese de la plateforme, suivre les utilisateurs, offres et candidatures."),
        ("Microservice IA", "Calculer le score semantique, extraire les competences et retourner les details du matching."),
    ], [1.55, 4.95], "Tableau 3 - Acteurs et responsabilites.")
    add_heading(doc, "2.2 Besoins fonctionnels", 2)
    add_table(doc, ["Code", "Besoin", "Etat dans le projet"], [
        ("BF1", "Gestion des profils etudiants avec CV", "Inclus : /students/create et formulaire offre"),
        ("BF2", "Creation et consultation des offres", "Inclus : /offers et /offers/create"),
        ("BF3", "Candidature en un clic", "Inclus : bouton Candidature 1 clic"),
        ("BF4", "Candidature avec CV depuis une offre", "Inclus : formulaire Postuler avec CV"),
        ("BF5", "Calcul score CV-offre", "Inclus : moteur local et microservice Python"),
        ("BF6", "Ranking recruteur", "Inclus : page detail offre"),
        ("BF7", "Decision recruteur", "Inclus : accepter/refuser"),
        ("BF8", "Vue admin", "Inclus : /admin"),
    ], [0.7, 3.0, 2.8], "Tableau 4 - Besoins fonctionnels.")
    add_heading(doc, "2.3 Besoins non fonctionnels", 2)
    add_bullets(doc, [
        "Performance : le calcul doit rester fluide pour une demonstration et extensible pour un volume plus grand.",
        "Maintenabilite : les responsabilites sont separees entre stockage, parsing CV, matching et client IA.",
        "Resilience : si le microservice IA est indisponible, Laravel utilise un moteur local de secours.",
        "Portabilite : Docker Compose decrit une topologie reproductible Laravel, Python et MySQL.",
        "Explicabilite : le score ne doit pas etre une boite noire ; les competences trouvees et manquantes sont affichees.",
        "Securite : les uploads sont limites aux formats PDF/TXT/MD et a une taille maximale configuree.",
    ])
    add_heading(doc, "2.3.1 Contraintes de qualite logicielle", 3)
    add_paragraphs(doc, [
        "La maintenabilite constitue une contrainte forte du projet. Les traitements metier ne doivent pas etre disperses dans les vues. C'est pourquoi des classes de support regroupent les responsabilites techniques. SqlStore gere la persistance relationnelle, DemoStore gere le fallback JSON, CvParser gere l'extraction CV, SemanticMatcher gere le score local et AiClient gere la communication avec Python.",
        "La portabilite est egalement essentielle. Le projet doit pouvoir etre lance dans plusieurs contextes : poste local avec PHP et MySQL, environnement de demonstration sans Docker, ou environnement complet Docker Compose. Cette souplesse explique la presence d'un routeur server.php pour le serveur PHP integre et d'une configuration docker-compose.yml pour les conteneurs.",
        "La lisibilite du code est un objectif pedagogique. Les choix d'implementation restent simples afin de faciliter l'explication pendant la soutenance. Une architecture avec controllers, services, policies et jobs serait plus proche d'une production Laravel complete, mais la version actuelle privilegie la comprehension directe du flux.",
    ])
    add_heading(doc, "2.3.2 Regles de gestion", 3)
    add_table(doc, ["Regle", "Description"], [
        ("RG1", "Une offre peut recevoir plusieurs candidatures, mais un meme etudiant ne doit pas postuler deux fois a la meme offre."),
        ("RG2", "Une candidature possede un statut parmi submitted, shortlisted, interview ou rejected."),
        ("RG3", "Un score superieur ou egal a un seuil peut aider a la preselections, sans supprimer la decision humaine."),
        ("RG4", "Le score doit etre persiste afin de conserver une trace de l'analyse effectuee."),
        ("RG5", "Si le service IA est indisponible, le systeme doit rester utilisable grace au moteur local."),
        ("RG6", "Un CV scanne non lisible doit pouvoir etre complete par un texte colle manuellement."),
    ], [1.0, 5.5], "Tableau 4 bis - Regles de gestion principales.")
    add_heading(doc, "2.4 Chaine de valeur fonctionnelle", 2)
    add_table(doc, ["Etape", "Traitement", "Sortie"], [
        ("1", "Le recruteur publie une offre avec description et competences", "Offre stockee dans sr_offers"),
        ("2", "L'etudiant cree un profil ou postule avec CV", "Profil, CV et candidature stockes"),
        ("3", "Le parseur extrait texte, email, diplome, experience et competences", "Metadonnees CV"),
        ("4", "Le moteur IA compare offre et CV", "Score 0-100 et explication"),
        ("5", "Le recruteur consulte le ranking", "Liste triee par pertinence"),
        ("6", "Le recruteur accepte ou refuse", "Statut mis a jour"),
    ], [0.7, 3.6, 2.2], "Figure 3 - Chaine de valeur de Smart-Recruit.")
    add_heading(doc, "2.5 Modele de donnees", 2)
    add_para(doc, "Le modele relationnel adopte une separation claire entre utilisateurs, profils, documents CV, offres, candidatures et scores. Les attributs flexibles comme les competences, liens et resultats de parsing sont stockes au format JSON.")
    add_table(doc, ["Table", "Role", "Principaux champs"], [
        ("sr_users", "Comptes applicatifs", "public_id, name, email, role, password"),
        ("sr_student_profiles", "Informations etudiant", "headline, education, skills, cv_text, cv_metadata"),
        ("sr_recruiter_profiles", "Informations recruteur", "company_name, position, website"),
        ("sr_cv_documents", "Documents CV", "original_name, stored_path, parser_result"),
        ("sr_offers", "Offres publiees", "title, company, description, required_skills, status"),
        ("sr_applications", "Lien candidat-offre", "offer_id, student_profile_id, status, applied_at"),
        ("sr_match_scores", "Resultat IA", "score, text_similarity, semantic_coverage, matched_skills, missing_skills"),
    ], [1.35, 1.75, 3.4], "Tableau 5 - Schema relationnel principal.")
    add_heading(doc, "2.5.1 Justification du schema relationnel", 3)
    add_paragraphs(doc, [
        "La separation entre sr_users et sr_student_profiles evite de melanger l'identite applicative avec les informations professionnelles du candidat. Cette distinction est importante, car un utilisateur peut exister sans profil complet, et un profil peut evoluer sans modifier les donnees d'authentification.",
        "La table sr_offers isole les informations de l'offre et ses competences requises. Les competences sont stockees en JSON afin de garder de la flexibilite pendant la phase prototype. Une version production pourrait normaliser ces competences dans une table dediee afin de mieux gerer les referentiels et les statistiques.",
        "La table sr_applications joue le role de pivot entre le candidat et l'offre. Elle porte le statut et la date de candidature. Le score n'est pas directement stocke dans cette table afin de permettre de recalculer ou historiser plusieurs analyses dans sr_match_scores.",
        "La table sr_match_scores conserve les composants du score : similarite textuelle, couverture semantique, competences trouvees, competences manquantes et reponse brute. Cette structure rend le systeme auditable et facilite les comparaisons entre versions d'algorithme.",
    ])
    add_heading(doc, "2.6 Sequences principales", 2)
    add_para(doc, "La sequence de candidature avec CV commence par la validation du formulaire. Le fichier est deplace vers le stockage, le texte est extrait, les competences sont detectees, puis le profil et la candidature sont crees. Le moteur de matching calcule ensuite le score et l'enregistre dans la table des scores.")
    add_table(doc, ["Participant", "Action"], [
        ("Candidat", "Soumet les informations et le CV"),
        ("Laravel", "Valide les donnees et orchestre le processus"),
        ("CvParser", "Extrait texte et metadonnees"),
        ("SqlStore", "Insere profil, candidature et score"),
        ("AiClient", "Contacte le microservice Python si disponible"),
        ("Recruteur", "Consulte le classement et prend une decision"),
    ], [1.6, 4.9], "Figure 4 - Sequence simplifiee de candidature et matching.")
    add_heading(doc, "2.7 Architecture de deploiement", 2)
    add_para(doc, "La configuration Docker Compose prevoit trois services : laravel-app, ai-service et mysql-db. En local, l'application peut aussi fonctionner sans Docker, comme cela a ete verifie lors des tests sur le port 8088 pour Laravel et 8010 pour le service IA.")
    add_table(doc, ["Service", "Port", "Responsabilite"], [
        ("laravel-app", "8080 -> 8000", "Application web, routes, vues, persistance"),
        ("ai-service", "8010 -> 8010", "API /health, /match, /parse-cv"),
        ("mysql-db", "3307 -> 3306", "Base MySQL smart_recruit"),
    ], [1.6, 1.5, 3.4], "Figure 5 - Architecture logique de deploiement.")

    add_heading(doc, "2.8 Modelisation UML", 2)
    add_para(doc, "La conception UML complete la description textuelle du systeme. Elle permet de representer les acteurs, les classes principales, les interactions dynamiques et l'architecture de deploiement. Les diagrammes ci-dessous synthetisent les choix de conception retenus pour Smart-Recruit.")
    diagrams = build_uml_diagrams()

    add_heading(doc, "2.8.1 Diagramme de cas d'utilisation", 3)
    add_para(doc, "Le diagramme de cas d'utilisation identifie les interactions visibles entre les acteurs et le systeme. L'etudiant depose son CV et postule, le recruteur publie les offres et exploite le ranking, tandis que l'administrateur consulte la synthese de la plateforme.")
    add_diagram(doc, diagrams["use_case"], "Figure UML-1 - Diagramme de cas d'utilisation de Smart-Recruit.")

    add_heading(doc, "2.8.2 Diagramme de classes", 3)
    add_para(doc, "Le diagramme de classes represente la structure statique du coeur metier. Il met en evidence les relations entre utilisateurs, profils, offres, candidatures, documents CV et scores de matching.")
    add_diagram(doc, diagrams["class"], "Figure UML-2 - Diagramme de classes principal.")

    add_heading(doc, "2.8.3 Diagramme de sequence", 3)
    add_para(doc, "Le diagramme de sequence decrit le scenario central du projet : un candidat soumet un CV, Laravel orchestre le parsing et la persistance, le microservice IA calcule le score et le recruteur consulte ensuite le classement.")
    add_diagram(doc, diagrams["sequence"], "Figure UML-3 - Sequence candidature avec CV et matching semantique.")

    add_heading(doc, "2.8.4 Diagramme de deploiement", 3)
    add_para(doc, "Le diagramme de deploiement presente la separation entre le navigateur, l'application Laravel, le microservice Python IA, MySQL et le fallback JSON. Il illustre le deploiement cible par Docker Compose ainsi que le fonctionnement local.")
    add_diagram(doc, diagrams["deployment"], "Figure UML-4 - Diagramme de deploiement Smart-Recruit.")

    add_heading(doc, "2.9 Criteres d'acceptation", 2)
    add_paragraphs(doc, [
        "Pour qu'une fonctionnalite soit consideree comme terminee, elle doit etre visible dans l'interface, persister correctement ses donnees et produire un comportement verifiable. Par exemple, la candidature avec CV n'est complete que si le profil est cree, la candidature est inseree, le score est calcule et le candidat apparait dans le classement de l'offre.",
        "Les criteres d'acceptation servent aussi a distinguer le prototype fonctionnel des perspectives. L'OCR, par exemple, est mentionne comme evolution mais n'est pas integre. Il ne doit donc pas etre presente comme une fonctionnalite livree. Cette distinction renforce la credibilite du rapport.",
        "La compatibilite MySQL est un critere essentiel. Le projet ne doit pas se limiter a un fichier JSON. Les tables SQL doivent exister, les migrations doivent etre appliquees et la route /test-connection doit afficher storage=mysql lorsque la base est disponible.",
    ])
    add_table(doc, ["Fonctionnalite", "Critere d'acceptation"], [
        ("Postuler avec CV", "Le candidat est cree, le fichier est accepte, la candidature est visible et le score est calcule."),
        ("Ranking", "Les candidats sont tries par score decroissant et les details du matching sont affiches."),
        ("Decision recruteur", "Un clic sur Accepter ou Refuser modifie le statut sans supprimer la candidature."),
        ("Admin", "Le role admin accede a une synthese des utilisateurs, offres et candidatures."),
        ("Resilience IA", "L'application continue a fonctionner meme si Python ne repond pas."),
        ("Dataset", "Les donnees couvrent plusieurs domaines et permettent des comparaisons concretes."),
    ], [2.0, 4.5], "Tableau 5 bis - Criteres d'acceptation.")
    doc.add_page_break()


def add_chapter_3(doc):
    add_heading(doc, "Chapitre 3 - Realisation et implementation logicielle", 1)
    add_heading(doc, "3.1 Environnement technique", 2)
    add_para(doc, "Le prototype Smart-Recruit est developpe comme une application Laravel avec une organisation volontairement simple pour faciliter la demonstration. Les routes web pilotent les principaux parcours, tandis que les classes de support encapsulent les responsabilites techniques : stockage SQL, stockage JSON de secours, parsing CV, client IA et matching semantique.")
    add_table(doc, ["Composant", "Technologie", "Role"], [
        ("Application web", "Laravel / PHP", "Routes, validation, vues Blade, orchestration"),
        ("Base de donnees", "MySQL", "Persistance relationnelle des utilisateurs, offres, candidatures et scores"),
        ("Microservice IA", "Python Flask ou fallback standard-library", "Calcul de similarite et extraction CV"),
        ("Interface", "Blade, CSS", "Dashboard, profils, offres, ranking, admin"),
        ("Deploiement", "Docker Compose", "Topologie portable multi-services"),
        ("Fallback", "JSON local", "Mode demo si MySQL indisponible"),
    ], [1.6, 1.8, 3.1], "Tableau 6 - Stack technique de Smart-Recruit.")
    add_heading(doc, "3.2 Organisation applicative Laravel", 2)
    add_para(doc, "Le projet utilise un ensemble de routes claires : dashboard, login demo, profils etudiants, offres, candidature, statut candidature, API ranking et test de connexion. Le stockage est selectionne dynamiquement : si les tables MySQL sont disponibles, SqlStore est utilise ; sinon DemoStore prend le relais avec un fichier JSON.")
    add_table(doc, ["Route", "Methode", "Fonction"], [
        ("/", "GET", "Dashboard global"),
        ("/test-connection", "GET", "Controle Laravel vers Python et stockage"),
        ("/students/create", "GET/POST", "Creation profil et parsing CV"),
        ("/offers/create", "GET/POST", "Publication offre"),
        ("/offers/{offer}", "GET", "Ranking automatique par offre"),
        ("/offers/{offer}/apply", "POST", "Candidature un clic"),
        ("/offers/{offer}/apply-with-cv", "POST", "Candidature avec CV"),
        ("/applications/{application}/status", "POST", "Decision recruteur"),
        ("/admin", "GET", "Synthese admin"),
        ("/api/offers/{offer}/ranking", "GET", "API JSON du ranking"),
    ], [2.4, 1.0, 3.1], "Tableau 7 - Endpoints applicatifs.")
    add_heading(doc, "3.2.1 Selection dynamique du stockage", 3)
    add_paragraphs(doc, [
        "La fonction sr_store joue le role de point d'entree vers le stockage. Elle teste la disponibilite de MySQL et l'existence des tables principales. Lorsque ces conditions sont satisfaites, elle retourne une instance de SqlStore. Sinon, elle retourne DemoStore, qui lit et ecrit un fichier JSON local.",
        "Cette decision simplifie la demonstration. Sur une machine ou MySQL n'est pas encore configure, le projet reste visitable et coherent. Des que la base est prete, les memes vues utilisent les donnees SQL sans modifier l'interface. Ce comportement a ete utile lors de la remise en route du projet sur le poste local.",
        "SqlStore encapsule les operations de base : lecture globale, ajout d'etudiant, ajout d'offre, candidature, mise a jour de statut, recuperation des candidatures par offre et sauvegarde des scores. DemoStore expose des methodes similaires afin de respecter le meme contrat applicatif.",
    ])
    add_heading(doc, "3.2.2 Gestion des statuts de candidature", 3)
    add_paragraphs(doc, [
        "Les statuts permettent de suivre le cycle de vie d'une candidature. Le statut submitted indique une candidature recue. Le statut shortlisted peut etre attribue automatiquement lorsque le score depasse un seuil ou manuellement par le recruteur. Le statut interview correspond a une candidature acceptee pour la suite du processus, tandis que rejected indique un refus.",
        "La page de detail d'une offre affiche les boutons Accepter et Refuser pour les candidatures existantes. Cette action met a jour sr_applications.status et conserve le score. Le recruteur peut donc comparer les scores tout en conservant son propre jugement final.",
    ])
    add_heading(doc, "3.3 Parsing CV", 2)
    add_para(doc, "Le parsing CV est assure par la classe CvParser cote Laravel et par l'endpoint /parse-cv cote Python. Le parseur accepte PDF, TXT et MD. Pour les fichiers texte, le contenu est lu directement. Pour les PDF, la version PHP fournit un fallback leger, tandis que Python tente d'utiliser pdfminer puis PyPDF2.")
    add_para(doc, "Les informations extraites comprennent le texte brut, les emails, les numeros de telephone, les competences detectees, l'experience approximative et le niveau de diplome. Cette extraction est volontairement explicable et extensible.")
    add_paragraphs(doc, [
        "Le parseur PHP utilise une strategie defensive. Si le fichier est absent, le champ texte manuel peut servir de source. Si le fichier est present, son nom est securise avant stockage. Pour les fichiers PDF, l'extraction PHP reste limitee ; elle est surtout prevue comme secours. Le microservice Python est le parseur le plus approprie pour les PDF texte.",
        "La detection de l'experience repose sur des expressions regulieres recherchant des formulations comme deux ans d'experience ou 2 years. Cette estimation reste approximative, mais elle suffit pour un bonus leger dans le score local. Une version future pourrait extraire les dates de stages et calculer une experience plus fiable.",
        "La detection du diplome recherche des niveaux tels que licence, master, mastère, ingenieur, bachelor ou BTS. Le but n'est pas de remplacer une analyse RH detaillee, mais de renseigner automatiquement le profil et de fournir un contexte supplementaire au recruteur.",
    ])
    add_heading(doc, "3.4 Moteur de matching", 2)
    add_para(doc, "Le matching est calcule a partir de quatre signaux principaux : couverture des competences, similarite textuelle TF-IDF/cosinus, couverture des categories semantiques et bonus d'experience dans le moteur local PHP. Le service Python applique une ponderation proche et retourne un resultat normalise.")
    add_code_block(doc, """
score_php = 100 * (
    0.55 * couverture_competences
  + 0.30 * similarite_cosinus
  + 0.10 * couverture_semantique
  + bonus_experience
)
score_python = 100 * (
    0.58 * couverture_competences
  + 0.32 * similarite_cosinus
  + 0.10 * couverture_semantique
)
""", "Figure 6 - Formule de scoring utilisee par les moteurs PHP et Python.")
    add_para(doc, "Le choix d'un modele leger est justifie par la demonstration et l'explicabilite. Le recruteur ne recoit pas seulement un score ; il voit aussi les competences presentes dans les deux textes et celles qui restent a verifier en entretien.")
    add_heading(doc, "3.4.1 Alias et taxonomie de competences", 3)
    add_paragraphs(doc, [
        "La configuration smart_recruit.php contient deux structures importantes : skill_aliases et skill_taxonomy. Les alias relient plusieurs formulations a une competence canonique. Par exemple, Laravel et Symfony peuvent etre relies a php ; ReactJS et react.js peuvent etre relies a react ; Docker et CI/CD peuvent etre relies a devops.",
        "La taxonomie regroupe les competences dans des familles plus larges : backend, frontend, data, infrastructure, IA, mobile, security, tools et methods. Cette couche permet de donner un petit credit semantique lorsqu'un candidat ne possede pas exactement la competence requise mais reste dans une famille technique pertinente.",
        "Cette approche est moins fine qu'un modele de langue profond, mais elle est transparente. L'administrateur peut comprendre et modifier les alias. Le systeme est donc pedagogique et controlable, ce qui est important dans un projet de master oriente genie logiciel.",
    ])
    add_heading(doc, "3.4.2 Explicabilite du score", 3)
    add_paragraphs(doc, [
        "Le score est affiche avec plusieurs composantes : similarite textuelle, couverture semantique, algorithme utilise et source du calcul. La source peut etre python-ai lorsque le microservice repond, ou local-php lorsque le fallback est utilise. Cette information facilite le diagnostic pendant les tests.",
        "Le moteur genere aussi une phrase d'explication. Un score tres eleve indique une forte compatibilite. Un score moyen signale un profil pertinent avec des competences a verifier. Un score faible indique une compatibilite partielle ou insuffisante. Ces messages rendent le tableau de bord plus lisible pour un non-specialiste de l'IA.",
    ])
    add_heading(doc, "3.5 Microservice Python", 2)
    add_para(doc, "Le microservice Python expose trois endpoints : /health pour verifier l'etat du service, /match pour calculer le matching semantique et /parse-cv pour extraire le contenu d'un CV. L'application peut fonctionner avec Flask. Si Flask n'est pas installe, un serveur standard-library assure au minimum /health et /match.")
    add_code_block(doc, """
GET  /health      -> etat du service IA
POST /match       -> score, competences trouvees, competences manquantes
POST /parse-cv    -> texte et metadonnees extraites du CV
""", "Figure 7 - Contrat REST du microservice IA.")
    add_paragraphs(doc, [
        "Le microservice accepte un payload JSON pour /match. Ce choix evite de coupler le calcul du score a un upload de fichier. Laravel peut construire le texte de l'offre et le texte du candidat, puis envoyer les competences requises et candidates deja extraites. Le service Python reste ainsi specialise dans le calcul.",
        "L'endpoint /health joue un role important dans la supervision. La route Laravel /test-connection interroge cet endpoint et affiche la reponse. Cette route est utile pendant la soutenance pour prouver que les deux couches communiquent correctement.",
        "Le timeout de AiClient est volontairement court. L'application web ne doit pas rester bloquee longtemps si le service IA ne repond pas. Ce choix illustre une strategie de resilience : privilegier une degradation controlee plutot qu'une panne complete.",
    ])
    add_heading(doc, "3.6 Persistance MySQL et fallback JSON", 2)
    add_para(doc, "La persistance principale repose sur MySQL. Les migrations creent les tables sr_users, sr_student_profiles, sr_recruiter_profiles, sr_cv_documents, sr_offers, sr_applications et sr_match_scores. Un fallback JSON reste disponible pour les demonstrations rapides ou les environnements sans base SQL.")
    add_para(doc, "Le projet contient egalement un jeu de donnees etendu dans database/demo_dataset.php. Celui-ci permet de tester differents domaines de stages : cybersecurite, DevOps, mobile, BI, UX/UI, IoT, QA, SAP, marketing, finance, Laravel et data engineering.")
    add_table(doc, ["Indicateur", "Valeur observee"], [
        ("Utilisateurs", "25"),
        ("Profils etudiants", "23"),
        ("Offres", "14"),
        ("Candidatures", "56"),
        ("Scores calcules", "56"),
    ], [2.0, 4.5], "Tableau 8 - Jeu de donnees de demonstration synchronise dans MySQL.")
    add_heading(doc, "3.6.1 Synchronisation du dataset", 3)
    add_paragraphs(doc, [
        "Le fichier database/demo_dataset.php contient les profils, offres et candidatures supplementaires. La synchronisation est idempotente : elle ajoute les lignes manquantes sans supprimer les donnees existantes. Ce comportement evite d'ecraser les statuts modifies pendant les tests.",
        "Le dataset couvre plusieurs specialites afin de rendre le matching plus demonstratif. Chaque domaine possede une offre et plusieurs candidats proches ou partiels. Cela permet de montrer que le ranking n'est pas uniquement calcule sur un exemple unique, mais sur un ensemble plus varie.",
        "Les scores ont ete calcules pour les 56 candidatures presentes. Cela permet d'afficher immediatement les resultats stockes et d'illustrer la table sr_match_scores dans l'interface admin et les pages d'offres.",
    ])
    add_heading(doc, "3.7 Interface utilisateur", 2)
    add_para(doc, "L'interface est orientee demonstration et efficacite. Le dashboard resume les profils, offres, candidatures et l'etat du moteur IA. Les pages etudiants permettent de consulter les competences et le CV extrait. Les pages offres affichent la description, les competences requises, le formulaire de candidature avec CV, le classement automatique et les actions recruteur.")
    add_para(doc, "La page admin fournit une synthese en lecture seule des utilisateurs, profils, offres et candidatures. Elle permet de montrer le role administrateur sans introduire de risque lie a des suppressions pendant la demonstration.")
    add_heading(doc, "3.7.1 Ergonomie du ranking recruteur", 3)
    add_paragraphs(doc, [
        "La page offre concentre les elements essentiels a la decision : description de l'offre, competences requises, formulaire de candidature, classement des profils, score, explication, competences trouvees, competences manquantes et boutons de decision. Le recruteur n'a donc pas besoin de naviguer entre plusieurs ecrans pour prendre une premiere decision.",
        "Les badges de statut rendent le suivi lisible. Une candidature recue, preselectionnee, acceptee ou refusee est visible directement dans le classement. Ce choix rapproche le prototype d'un ATS reel, ou le statut de workflow est aussi important que le score.",
        "Le formulaire Postuler avec CV depuis l'offre reduit la friction pour le candidat. Il cree automatiquement le profil, enregistre la candidature et declenche le calcul du score. Cette fonctionnalite complete le parcours initial qui separait la creation de profil et la candidature.",
    ])
    add_heading(doc, "3.7.2 Vue administrateur", 3)
    add_paragraphs(doc, [
        "La vue admin est volontairement en lecture seule. Elle sert a controler la coherence du systeme : nombre d'utilisateurs, nombre de profils, nombre d'offres, nombre de candidatures et derniers statuts. Elle evite les operations destructives qui pourraient perturber une demonstration.",
        "Dans une version production, cette vue pourrait evoluer vers une administration complete : gestion des comptes, validation des entreprises, moderation des offres, parametrage des competences et consultation de statistiques avancees.",
    ])
    add_heading(doc, "3.8 Configuration Docker", 2)
    add_para(doc, "La configuration Docker Compose decrit les trois services necessaires a un deploiement complet. Dans le contexte local verifie, Docker n'etait pas disponible dans le terminal, mais le projet reste pret pour une execution sur une machine equipee de Docker Desktop.")
    add_code_block(doc, """
laravel-app -> port 8080, DB_HOST=mysql-db, AI_SERVICE_URL=http://ai-service:8010
ai-service  -> port 8010, API de matching
mysql-db    -> port 3307 sur l'hote, port 3306 dans le reseau Docker
""", "Figure 8 - Synthese de la configuration Docker Compose.")
    add_heading(doc, "3.9 Securite et robustesse", 2)
    add_paragraphs(doc, [
        "La securite des uploads est abordee par une validation des extensions et de la taille. Le formulaire accepte PDF, TXT et MD, avec une limite de taille. Le nom du fichier est normalise afin d'eviter les caracteres dangereux. Une version production devrait ajouter un stockage prive, une analyse antivirus et un controle plus strict des permissions.",
        "La validation Laravel protege les routes de creation contre les donnees incompletes. Les champs obligatoires comme le nom, l'email et le titre du profil sont verifies. Pour la candidature avec CV, le systeme exige au moins un fichier ou un texte de CV, ce qui evite d'inserer une candidature vide.",
        "La robustesse vient aussi du principe de fallback. Le projet ne s'effondre pas si le service Python est absent. Il ne s'effondre pas non plus si MySQL n'est pas configure au premier lancement. Cette conception progressive est utile pour les environnements pedagogiques et les demonstrations.",
    ])
    doc.add_page_break()


def add_chapter_4(doc):
    add_heading(doc, "Chapitre 4 - Tests, validation et evaluation", 1)
    add_heading(doc, "4.1 Strategie de validation", 2)
    add_para(doc, "La validation vise a verifier que la plateforme repond aux exigences fonctionnelles et que le moteur de matching produit des resultats coherents. Elle combine des tests de routes, des tests de connexion entre services, des controles de base de donnees, des scenarios metiers et des tests de pertinence sur plusieurs domaines de stages.")
    add_paragraphs(doc, [
        "La validation d'un systeme de recommandation ne se limite pas a verifier que les pages s'affichent. Il faut verifier la chaine complete : donnees saisies, stockage, parsing, appel IA, calcul du score, sauvegarde du resultat et affichage recruteur. Une erreur a une seule etape peut rendre le classement incorrect.",
        "Le protocole de test a donc ete organise en trois niveaux. Le premier niveau est technique : syntaxe PHP, routes Laravel, migrations et connexion aux services. Le deuxieme niveau est fonctionnel : creation de profils, offres, candidatures et decisions. Le troisieme niveau est semantique : coherence des scores entre domaines et profils.",
        "Les tests ont ete realises sur un environnement local avec MySQL actif, Laravel disponible sur 127.0.0.1:8088 et service IA disponible sur 127.0.0.1:8010. La route /test-connection a confirme que le stockage actif etait MySQL et que le microservice IA repondait correctement.",
    ])
    add_heading(doc, "4.2 Tests fonctionnels", 2)
    add_table(doc, ["Cas", "Procedure", "Resultat attendu"], [
        ("Connexion technique", "Ouvrir /test-connection", "storage=mysql et reponse IA ok"),
        ("Creation etudiant", "Remplir /students/create avec CV", "Profil cree et competences detectees"),
        ("Creation offre", "Publier une offre", "Offre visible et ranking disponible"),
        ("Candidature 1 clic", "Cliquer sur le bouton de l'offre", "Candidature ajoutee"),
        ("Candidature avec CV", "Remplir le formulaire offre", "Profil + candidature + score"),
        ("Decision recruteur", "Cliquer Accepter ou Refuser", "Statut mis a jour"),
        ("Vue admin", "Connexion admin puis /admin", "Synthese visible"),
    ], [1.7, 2.4, 2.4], "Tableau 9 - Tests fonctionnels principaux.")
    add_heading(doc, "4.3 Tests de pertinence semantique", 2)
    add_para(doc, "Le jeu de donnees etendu permet de verifier que les candidats les plus proches remontent naturellement selon les domaines. Par exemple, un profil SOC doit etre bien classe sur une offre cybersecurite, un profil Flutter sur une offre mobile, et un profil Power BI sur une offre BI.")
    add_table(doc, ["Offre", "Profil attendu", "Competences determinantes"], [
        ("Stage SOC Analyst Cybersecurite", "Selim Mansouri", "cybersecurity, linux, network, siem, python"),
        ("Stage DevOps Cloud Docker Kubernetes", "Nour Haddad", "docker, kubernetes, ci/cd, linux, aws"),
        ("Stage Developpement Mobile Flutter", "Mariem Ben Ali", "flutter, dart, firebase, mobile, api rest"),
        ("Stage BI Power BI", "Sami Jlassi", "power bi, sql, etl, data analysis, excel"),
        ("Stage Laravel API", "Walid Krichen", "php, laravel, mysql, api rest, git"),
        ("Stage Data Engineering ETL Airflow", "Farah Toumi", "python, etl, sql, airflow, data warehouse"),
    ], [2.2, 1.6, 2.7], "Tableau 10 - Scenarios de pertinence IA.")
    add_paragraphs(doc, [
        "Ces scenarios ne constituent pas une evaluation statistique exhaustive, mais ils permettent de verifier la logique generale du systeme. Lorsque l'offre et le CV partagent plusieurs competences explicites, le candidat doit apparaitre dans les premiers resultats. Lorsque le candidat partage seulement une famille de competences, son score doit etre intermediaire. Lorsqu'il n'existe presque aucun recouvrement, le score doit rester faible.",
        "L'interet du dataset multi-domaines est de creer des cas ambigus. Par exemple, un profil data engineering peut etre partiellement pertinent pour une offre BI, mais il ne devrait pas depasser un profil Power BI lorsque l'offre cite explicitement Power BI, Excel et ETL. De meme, un developpeur Laravel peut etre partiellement pertinent pour QA automation s'il connait Java ou API REST, mais il ne devrait pas depasser un profil Selenium.",
        "Le systeme permet ainsi de verifier non seulement les meilleurs cas, mais aussi les cas limites. Ces cas limites sont importants car ils montrent que le matching ne se reduit pas a une correspondance binaire.",
    ])
    add_heading(doc, "4.3.1 Indicateurs qualitatifs", 3)
    add_paragraphs(doc, [
        "Dans une evaluation future, il serait possible d'utiliser des indicateurs tels que precision@k, rappel@k et NDCG. Precision@k mesurerait la proportion de profils pertinents parmi les k premiers candidats. Rappel@k mesurerait la capacite du systeme a retrouver tous les profils pertinents. NDCG tiendrait compte de l'ordre exact dans le classement.",
        "Pour ce prototype, l'evaluation est qualitative et demonstrative. Les resultats sont observes via l'interface et les scores stockes. Cette approche est suffisante pour un premier prototype, mais elle devra etre completee par une annotation humaine si le projet devient un outil operationnel.",
    ])
    add_heading(doc, "4.4 Resultats de verification technique", 2)
    add_para(doc, "Les verifications realisees sur le projet montrent que les routes Laravel sont chargees correctement, que la migration principale est appliquee, que MySQL est actif et que le microservice IA repond.")
    add_table(doc, ["Controle", "Resultat"], [
        ("php -l routes/web.php", "Aucune erreur de syntaxe"),
        ("php artisan route:list", "19 routes chargees"),
        ("php artisan migrate:status", "Migration principale appliquee"),
        ("/test-connection", "SUCCESS, storage=mysql, IA ok"),
        ("Page offre cybersecurite", "Offre, candidat et actions recruteur visibles"),
        ("Admin", "Vue globale Smart-Recruit disponible"),
        ("Scores", "56 scores presents dans sr_match_scores"),
    ], [2.5, 4.0], "Tableau 11 - Resultats de validation technique.")
    add_heading(doc, "4.5 Tests de resilience", 2)
    add_para(doc, "La resilience est prise en compte a deux niveaux. Si le microservice Python ne repond pas, AiClient retourne une valeur nulle et le ranking peut continuer avec le moteur PHP local. Si MySQL n'est pas disponible ou si les tables sont absentes, sr_store bascule vers DemoStore et le fichier JSON de demonstration.")
    add_table(doc, ["Incident", "Comportement attendu", "Interet"], [
        ("Microservice IA indisponible", "Fallback moteur PHP", "Continuer a classer les profils"),
        ("MySQL indisponible", "Fallback JSON", "Garder une demo utilisable"),
        ("PDF difficile a lire", "Champ texte manuel exploitable", "Eviter une candidature vide"),
        ("Score eleve", "Statut peut passer en shortlisted", "Aide a la preselections"),
    ], [1.8, 2.35, 2.35], "Tableau 12 - Scenarios de resilience.")
    add_paragraphs(doc, [
        "Le choix d'une architecture avec fallback montre qu'une fonctionnalite IA ne doit pas etre un point unique de defaillance. Si l'IA devient indisponible, l'application doit continuer a accepter les candidatures. Dans un environnement professionnel, les scores pourraient etre recalcules plus tard par un job asynchrone.",
        "Le fallback JSON est surtout un outil de demonstration. Il n'est pas destine a remplacer MySQL en production. Son interet est de faciliter le demarrage du projet sur un poste ou la base n'est pas encore configuree, tout en gardant une experience utilisateur coherente.",
    ])
    add_heading(doc, "4.6 Analyse des resultats", 2)
    add_para(doc, "Les resultats confirment la pertinence d'une approche hybride : la couverture de competences garantit que les exigences explicites sont respectees, tandis que la similarite textuelle capte une proximite plus large. La taxonomie semantique corrige partiellement les differences de vocabulaire en regroupant les competences par familles telles que backend, frontend, data, infrastructure, IA, mobile ou securite.")
    add_para(doc, "L'existence d'un jeu de donnees multi-domaines rend les tests plus realistes. Les recruteurs peuvent comparer des profils proches, partiels ou eloignes et observer l'effet des competences manquantes. L'affichage du score stocke permet aussi d'expliquer les decisions et de conserver une trace historique.")
    add_heading(doc, "4.6.1 Interprétation des scores", 3)
    add_paragraphs(doc, [
        "Un score compris entre 80 et 100 peut etre interprete comme une forte adequation. Le candidat couvre la plupart des competences requises et partage un vocabulaire proche de l'offre. Dans ce cas, le recruteur peut prioriser l'analyse du CV et envisager un entretien.",
        "Un score entre 60 et 79 correspond a une adequation interessante mais incomplete. Le candidat peut posseder une base technique pertinente, mais certaines competences doivent etre verifiees. Cette zone est importante pour les profils juniors, car un stagiaire peut apprendre rapidement une competence manquante.",
        "Un score inferieur a 60 ne signifie pas automatiquement que le candidat doit etre rejete. Il indique que le CV ne correspond pas fortement a l'offre selon les informations disponibles. Le recruteur peut tout de meme conserver la candidature si d'autres criteres non textuels sont favorables.",
    ])
    add_heading(doc, "4.6.2 Apport du score explicable", 3)
    add_paragraphs(doc, [
        "L'affichage des competences manquantes transforme le score en outil de preparation d'entretien. Au lieu de lire un simple pourcentage, le recruteur sait quelles competences doivent etre confirmees : Docker, Kubernetes, SQL, API REST ou Power BI par exemple.",
        "Cette transparence limite aussi le risque d'une confiance excessive dans l'algorithme. Le recruteur voit que le score repose sur des signaux textuels et peut corriger l'interpretation si le CV est mal redige ou incomplet.",
    ])
    add_heading(doc, "4.7 Limites des tests", 2)
    add_para(doc, "La commande php artisan test n'est pas disponible dans la configuration actuelle, ce qui signifie qu'il n'existe pas encore de suite de tests automatisee Laravel. La validation repose donc sur des tests manuels et des controles techniques directs. Pour une version industrielle, il serait necessaire d'ajouter PHPUnit/Pest, des tests d'integration API et des tests de charge outilles.")
    add_heading(doc, "4.8 Plan de tests automatise propose", 2)
    add_paragraphs(doc, [
        "La prochaine etape de validation consisterait a ajouter des tests unitaires pour SemanticMatcher. Ces tests verifieraient la normalisation des alias, l'extraction de competences, la couverture semantique et le calcul du score sur des cas simples et reproductibles.",
        "Des tests d'integration devraient ensuite couvrir les routes Laravel. Par exemple, un test pourrait envoyer une candidature avec CV texte, verifier la creation du profil, l'insertion dans sr_applications et la presence d'un score dans sr_match_scores.",
        "Enfin, des tests API pourraient verifier le microservice Python independamment de Laravel. Ils enverraient des couples offre-CV connus et compareraient le score retourne a une plage attendue. Cette strategie permettrait de detecter une regression lors d'une evolution de l'algorithme.",
    ])
    doc.add_page_break()


def add_chapter_5(doc):
    add_heading(doc, "Chapitre 5 - Discussion, limites et perspectives", 1)
    add_heading(doc, "5.1 Apports du projet", 2)
    add_para(doc, "Smart-Recruit apporte une reponse concrete a un probleme frequent : comparer rapidement des offres et des CV rediges en langage naturel. La solution ne se contente pas de stocker des candidatures ; elle fournit un outil d'aide a la decision qui classe les profils et explicite les criteres du classement.")
    add_para(doc, "Sur le plan technique, le projet montre la complementarite entre Laravel et Python. Laravel fournit un cadre productif pour les vues, la validation, les routes et MySQL. Python fournit un espace plus naturel pour les traitements NLP. Le couplage par API REST rend le systeme evolutif.")
    add_paragraphs(doc, [
        "Sur le plan pedagogique, Smart-Recruit permet de mobiliser plusieurs competences de master : conception UML, modelisation relationnelle, developpement web, integration API, traitement de texte, architecture distribuee, validation logicielle et presentation de resultats.",
        "Sur le plan fonctionnel, le projet repond a un cas d'usage concret. Les etablissements, incubateurs ou entreprises qui recoivent beaucoup de candidatures peuvent utiliser un ranking pour prioriser la lecture. Le gain attendu n'est pas la suppression du travail humain, mais la reduction du temps passe sur le tri initial.",
        "Sur le plan scientifique, le projet illustre la valeur d'une approche explicable. Les algorithmes simples mais bien integres peuvent deja apporter une aide significative. Cette observation est importante dans un contexte ou l'on associe parfois l'IA uniquement a des modeles massifs.",
    ])
    add_heading(doc, "5.2 Limites actuelles", 2)
    add_bullets(doc, [
        "L'authentification est un mode demonstration multi-roles ; elle doit etre remplacee par une authentification de production.",
        "L'OCR n'est pas integre : les CV scannes sous forme d'image ne sont pas lus automatiquement.",
        "Les scores sont calcules de maniere synchrone ; cela convient a la demonstration mais pas aux gros volumes.",
        "La validation statistique n'utilise pas encore un corpus annote par des experts RH.",
        "Les modeles TF-IDF restent moins sensibles au contexte profond que les embeddings modernes.",
    ])
    add_paragraphs(doc, [
        "La limite la plus importante concerne la qualite des donnees. Un CV court, mal structure ou incomplet donnera un score moins fiable. De meme, une offre trop generale ne fournit pas suffisamment de signaux pour distinguer les candidats. Le systeme depend donc de la qualite redactionnelle des deux documents.",
        "Une autre limite concerne la couverture du dictionnaire de competences. Les alias actuels couvrent plusieurs domaines informatiques et metiers, mais ils ne sont pas exhaustifs. Une entreprise specialisee pourrait utiliser des technologies non referencees, ce qui reduirait la qualite du matching.",
        "Le score ne prend pas encore en compte des criteres non textuels comme la disponibilite, la mobilite, le niveau linguistique, les soft skills, la qualite des projets GitHub ou les resultats academiques. Ces criteres pourraient etre ajoutes sous forme de signaux complementaires.",
    ])
    add_heading(doc, "5.3 Perspectives d'amelioration", 2)
    add_numbers(doc, [
        "Integrer une authentification production avec gestion des mots de passe, verification email, droits par role et journalisation.",
        "Ajouter une couche OCR avec Tesseract ou un service specialise pour traiter les CV scannes.",
        "Migrer le matching vers des embeddings ou des Transformers comme BERT ou CamemBERT pour mieux capter la semantique contextuelle.",
        "Introduire Laravel Queue, Redis ou RabbitMQ pour rendre le calcul asynchrone et scalable.",
        "Ajouter un back-office de referentiels de competences afin que l'administrateur enrichisse les alias et taxonomies.",
        "Mettre en place une suite de tests automatises couvrant routes, stockage, matching et API Python.",
        "Ajouter des indicateurs analytiques pour les recruteurs : taux de candidatures qualifiees, competences rares, evolution des scores.",
    ])
    add_heading(doc, "5.3.1 Passage en production", 3)
    add_paragraphs(doc, [
        "Le passage en production demanderait d'abord de remplacer la session de demonstration par une authentification complete. Chaque utilisateur devrait posseder un compte securise, un mot de passe chiffre, une verification email et des droits adaptes a son role. Les recruteurs ne devraient voir que leurs propres offres et candidatures.",
        "Le stockage des fichiers CV devrait etre securise. Les fichiers devraient etre places dans un espace non public, accessibles uniquement aux utilisateurs autorises. Une politique de retention serait necessaire pour supprimer les CV apres une periode definie ou a la demande du candidat.",
        "Le calcul des scores devrait etre asynchrone pour les volumes importants. Lorsqu'un candidat postule, la candidature serait enregistree immediatement, puis un job calculerait le score en arriere-plan. L'interface afficherait un statut d'analyse en cours jusqu'a la disponibilite du score.",
    ])
    add_heading(doc, "5.3.2 Evolution algorithmique", 3)
    add_paragraphs(doc, [
        "Une evolution naturelle serait l'utilisation d'embeddings de phrases. Au lieu de representer les textes par des vecteurs de mots ponderes, un modele comme Sentence-BERT pourrait produire des representations denses captant davantage le sens global. Cette approche serait utile pour les phrases longues et les competences exprimees indirectement.",
        "Pour un contexte francophone, CamemBERT ou des modeles multilingues pourraient ameliorer la comprehension des CV en francais. Cependant, l'integration de ces modeles exige une evaluation plus rigoureuse, car un modele plus complexe n'est pas automatiquement meilleur dans un domaine donne.",
        "Une autre piste consiste a apprendre les ponderations du score a partir des decisions recruteurs. Si les recruteurs acceptent regulierement certains profils mal notes, le systeme pourrait ajuster ses poids. Cette approche transformerait progressivement le prototype en systeme adaptatif.",
    ])
    add_heading(doc, "5.4 Ouverture scientifique", 2)
    add_para(doc, "Le projet constitue une base pour des recherches plus avancees sur le matching candidat-offre. Une extension naturelle consisterait a comparer plusieurs modeles sur un corpus annote : TF-IDF, Word2Vec, Sentence-BERT, CamemBERT et modeles specialises RH. Les mesures pourraient inclure precision@k, rappel@k, NDCG et satisfaction recruteur.")
    add_heading(doc, "5.5 Considerations eth et responsabilite", 2)
    add_paragraphs(doc, [
        "L'utilisation d'un systeme de recommandation dans le recrutement doit etre encadree. Le risque principal est de transformer un score technique en decision automatique. Pour eviter cela, Smart-Recruit presente le score comme une aide a la lecture et laisse la decision finale au recruteur.",
        "La transparence est essentielle. Le candidat devrait pouvoir savoir que son CV est analyse automatiquement et que le score repose sur le texte fourni. Dans une version production, une politique de confidentialite devrait expliquer les donnees collectees, leur duree de conservation et les droits d'acces.",
        "Le systeme doit egalement eviter les biais. Si le dataset d'entrainement ou de test favorise certains types de profils, le ranking peut reproduire cette preference. Meme avec TF-IDF, le choix des mots-cles et des alias peut orienter les resultats. Une gouvernance des referentiels de competences serait donc necessaire.",
    ])
    doc.add_page_break()


def add_conclusion(doc):
    add_heading(doc, "Conclusion generale", 1)
    add_para(doc, "Ce memoire a presente la conception et la realisation de Smart-Recruit, une plateforme de mise en relation entre etudiants et entreprises assistee par un moteur de recommandation semantique. Le travail a permis de passer d'une logique de gestion classique des candidatures a une logique d'aide a la decision basee sur l'analyse du contenu des CV et des offres.")
    add_para(doc, "L'approche retenue combine une architecture web Laravel, une base MySQL, un microservice Python et un moteur de matching explicable. La plateforme prend en charge la creation de profils, l'upload de CV, la publication d'offres, la candidature, le ranking par offre et la decision recruteur. Le score produit entre 0 et 100 est accompagne des competences trouvees et manquantes, ce qui facilite l'interpretation.")
    add_para(doc, "La validation a montre que le projet est operationnel sur un jeu de donnees multi-domaines comprenant 23 profils etudiants, 14 offres, 56 candidatures et 56 scores. Les tests fonctionnels et techniques confirment la disponibilite de MySQL, la communication avec le service IA et le bon rendu des pages principales.")
    add_para(doc, "Smart-Recruit reste un prototype academique evolutif. Ses limites sont identifiees : authentification de demonstration, absence d'OCR, absence de file de traitement asynchrone et modele semantique encore leger. Ces limites ouvrent des perspectives riches vers une plateforme plus robuste, plus intelligente et plus proche des exigences d'un environnement professionnel.")
    doc.add_page_break()


def add_bibliography(doc):
    add_heading(doc, "Bibliographie", 1)
    references = [
        "Salton, G., & Buckley, C. (1988). Term-weighting approaches in automatic text retrieval. Information Processing & Management, 24(5), 513-523.",
        "Manning, C. D., Raghavan, P., & Schutze, H. (2008). Introduction to Information Retrieval. Cambridge University Press.",
        "Jurafsky, D., & Martin, J. H. Speech and Language Processing. Stanford University.",
        "Pedregosa, F. et al. (2011). Scikit-learn: Machine Learning in Python. Journal of Machine Learning Research, 12, 2825-2830.",
        "Devlin, J., Chang, M. W., Lee, K., & Toutanova, K. (2019). BERT: Pre-training of Deep Bidirectional Transformers for Language Understanding.",
        "Martin, L. et al. (2020). CamemBERT: a Tasty French Language Model.",
        "Fowler, M. (2014). Microservices: a definition of this new architectural term. martinfowler.com.",
        "Laravel Documentation. Routing, validation, file storage, database migrations and HTTP client.",
        "Docker Documentation. Docker Compose, service networking and container orchestration.",
        "MySQL Documentation. Relational schema design, indexes and constraints.",
        "spaCy Documentation. Industrial-strength Natural Language Processing in Python.",
        "LinkedIn Talent Solutions. Reports and resources on digital recruitment.",
        "Indeed Hiring Lab. Reports on labor market and recruitment trends.",
    ]
    for ref in references:
        add_para(doc, ref, style="List Number")
    doc.add_page_break()


def add_appendices(doc):
    add_heading(doc, "Annexes techniques", 1)
    add_heading(doc, "Annexe A - Pseudo-code du matching", 2)
    add_code_block(doc, """
Entrées : offre, profil candidat
1. Construire le texte de l'offre : titre + description + competences requises
2. Construire le texte candidat : titre profil + diplome + CV + competences
3. Normaliser les textes : minuscules, suppression bruit, alias competences
4. Extraire les competences canoniques
5. Calculer couverture_competences = competences_trouvees / competences_requises
6. Calculer similarite_cosinus via TF-IDF
7. Calculer couverture_semantique par taxonomie
8. Produire score 0-100 et explication
9. Sauvegarder le score dans sr_match_scores si une candidature existe
""", "Annexe A - Pseudo-code du moteur de matching.")
    add_heading(doc, "Annexe B - Configuration Docker Compose", 2)
    add_code_block(doc, """
services:
  laravel-app:
    ports: ["8080:8000"]
    environment:
      DB_HOST: mysql-db
      DB_DATABASE: smart_recruit
      AI_SERVICE_URL: http://ai-service:8010
  ai-service:
    ports: ["8010:8010"]
  mysql-db:
    image: mysql:8.0
    ports: ["3307:3306"]
""", "Annexe B - Extrait simplifie de docker-compose.yml.")
    add_heading(doc, "Annexe C - Donnees de demonstration", 2)
    add_table(doc, ["Domaine", "Exemple d'offre", "Profil fort attendu"], [
        ("Cybersecurite", "Stage SOC Analyst Cybersecurite", "Analyste SOC"),
        ("DevOps", "Docker Kubernetes", "DevOps Cloud Junior"),
        ("Mobile", "Flutter Firebase", "Developpeuse Mobile Flutter"),
        ("Business Intelligence", "Power BI", "Analyste BI"),
        ("UX/UI", "Produit SaaS", "UX UI Designer"),
        ("IoT", "ESP32 systemes embarques", "Ingenieur Embarque"),
        ("QA", "Selenium API", "QA Automation Tester"),
        ("SAP", "ABAP ERP", "Consultant SAP"),
        ("Marketing", "SEO Analytics", "Marketing Digital"),
        ("Finance", "Reporting Power BI", "Finance Data"),
        ("Backend", "Laravel API", "Developpeur Laravel"),
        ("Data Engineering", "ETL Airflow", "Data Engineer"),
    ], [1.8, 2.45, 2.25], "Annexe C - Couverture des domaines du dataset.")
    add_heading(doc, "Annexe D - Guide de lancement local", 2)
    add_numbers(doc, [
        "Verifier la configuration .env : DB_DATABASE, DB_USERNAME, DB_PASSWORD et AI_SERVICE_URL.",
        "Lancer MySQL local ou Docker si disponible.",
        "Executer php artisan migrate --force pour creer les tables.",
        "Lancer le microservice IA Python sur le port 8010.",
        "Lancer Laravel avec php -S 127.0.0.1:8088 -t . server.php.",
        "Ouvrir http://127.0.0.1:8088/test-connection pour verifier storage=mysql et IA ok.",
        "Tester les pages /offers, /students, /admin et les rankings par offre.",
    ])


def build():
    OUT.parent.mkdir(parents=True, exist_ok=True)
    doc = Document()
    configure_document(doc)
    add_cover(doc)
    add_preliminaries(doc)
    add_introduction(doc)
    add_chapter_1(doc)
    add_chapter_2(doc)
    add_chapter_3(doc)
    add_chapter_4(doc)
    add_chapter_5(doc)
    add_conclusion(doc)
    add_bibliography(doc)
    add_appendices(doc)
    doc.save(OUT)
    print(OUT)


if __name__ == "__main__":
    build()
