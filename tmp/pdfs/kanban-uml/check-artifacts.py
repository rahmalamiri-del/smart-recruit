from pathlib import Path
import json,re,hashlib
import pdfplumber
from pypdf import PdfReader
from PIL import Image,ImageDraw
W=Path('H:/smart-recruit');R=W/'output/latex/smartrecruit-memoire-kanban-uml';B=W/'tmp/pdfs/kanban-uml'
pdf=PdfReader(B/'main.pdf'); texts=[p.extract_text() or '' for p in pdf.pages]
alltext='\n'.join(texts)
assert pdf.metadata.author=='Rahma Amiri'
for phrase in ['Rahma Amiri','Hammami Houssem','Best Solutions','MDL/26/12','02 mars 2026','02 juillet 2026','Kanban','UML']:
    assert phrase in alltext,phrase
assert 'Rahma Lamiri' not in alltext
assert '??' not in alltext
log=(B/'main.log').read_text(encoding='utf-8',errors='replace')
for phrase in ['Overfull', 'Missing character:', 'undefined references', 'multiply defined', 'Citation `']:
    assert phrase not in log,phrase
blg=(B/'main.blg').read_text(encoding='utf-8',errors='replace');assert 'Warning' not in blg
alltex='\n'.join(p.read_text(encoding='utf-8') for p in R.rglob('*.tex'))
labels=re.findall(r'\\label\{([^}]+)\}',alltex)
refs=re.findall(r'\\(?:ref|pageref)\{([^}]+)\}',alltex)
assert len(labels)==len(set(labels))
assert not set(refs)-set(labels),set(refs)-set(labels)
for match in re.findall(r'\\input\{([^}]+)\}',alltex): assert (R/match).is_file(),match
for match in re.findall(r'\\includegraphics(?:\[[^\]]*\])?\{([^}]+)\}',alltex): assert (R/match).is_file(),match
assert len(list((R/'figures/screenshots').glob('*.png')))==12
assert len(list((R/'figures').glob('uml-*.tex')))==7
rotations=[i+1 for i,p in enumerate(pdf.pages) if p.rotation%360!=0]
assert len(rotations)==12
outside=[]
with pdfplumber.open(B/'main.pdf') as document:
    for i,p in enumerate(document.pages,1):
        for ch in p.chars:
            if ch['text'].strip() and (ch['x0'] < -1 or ch['x1']>p.width+1 or ch['top']< -1 or ch['bottom']>p.height+1):
                outside.append({'page':i,'text':ch['text']})
assert not outside,outside[:10]
info={'pages':len(pdf.pages),'chapters':6,'appendices':4,'uml_diagrams':7,'screenshots':12,'total_figures':len(re.findall(r'\\contentsline \{figure\}',(B/'main.lof').read_text(encoding='utf-8'))),'bibliography_entries_printed':len(re.findall(r'\\bibitem', (B/'main.bbl').read_text(encoding='utf-8'))),'landscape_pdf_pages':rotations,'all_references_resolved':True,'no_overflow_missing_glyphs_or_outside_text':True,'pdf_sha256':hashlib.sha256((B/'main.pdf').read_bytes()).hexdigest()}
(B/'qa-results.json').write_text(json.dumps(info,ensure_ascii=False,indent=2),encoding='utf-8')
# Rebuild overview sheets using exactly the current PDF's pages (older renders may remain in scratch).
pages=[B/'rendered'/f'page-{i:03}.png' for i in range(1,len(pdf.pages)+1)]
for offset in range(0,len(pages),20):
    files=pages[offset:offset+20]; rows=(len(files)+3)//4
    sheet=Image.new('RGB',(1104,rows*398),'#dfe5e2');draw=ImageDraw.Draw(sheet)
    for n,f in enumerate(files):
        im=Image.open(f).convert('RGB');im.thumbnail((260,368))
        x=n%4*276+8; y=n//4*398+22
        sheet.paste(im,(x+(260-im.width)//2,y));draw.text((x,y-17),f.stem,fill='black')
    sheet.save(B/f'final-contact-{offset//20+1:02}.png')
print(json.dumps(info,ensure_ascii=False,indent=2))
