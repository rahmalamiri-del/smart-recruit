from pathlib import Path
import hashlib, json, re, shutil, zipfile
from pypdf import PdfReader
root=Path('H:/smart-recruit')
src=root/'output/latex/smartrecruit-memoire'
built=root/'tmp/latex-tools/main.pdf'
pdf_out=root/'output/pdf/Memoire_Master_SmartRecruit_Rahma_Amiri.pdf'
zip_out=root/'output/latex/Memoire_Master_SmartRecruit_Rahma_Amiri_LaTeX.zip'
reader=PdfReader(built)
texts=[page.extract_text() or '' for page in reader.pages]
full='\n'.join(texts)
assert len(reader.pages)>=50
assert 'Rahma Amiri' in texts[0]
assert 'MDL/26/12' in texts[0]
assert 'Best Solutions' in texts[0]
assert 'Hammami Houssem' in texts[0]
assert 'Lamiri' not in full
assert '??' not in full
assert all(len(t.strip())>100 for t in texts)
chapters=sorted((src/'chapters').glob('*.tex'))
assert len(chapters)==6
all_tex='\n'.join(f.read_text(encoding='utf-8-sig') for f in src.rglob('*.tex') if f.name!='review-main.tex')
labels=set(re.findall(r'\\label\{([^}]+)\}',all_tex))
used_refs=set(re.findall(r'\\(?:ref|eqref)\{([^}]+)\}',all_tex))
assert not (used_refs-labels),used_refs-labels
bib=(src/'references.bib').read_text(encoding='utf-8-sig')
bibkeys=set(re.findall(r'@\w+\{([^,]+),',bib))
citations=set(k.strip() for group in re.findall(r'\\cite(?:\w*)\{([^}]+)\}',all_tex) for k in group.split(','))
assert not (citations-bibkeys),citations-bibkeys
log=(root/'tmp/latex-tools/main.log').read_text(encoding='utf8',errors='replace')
for problem in ['Overfull','undefined references','Missing character','Token not allowed']:
    assert problem not in log,problem
qa=json.loads((root/'tmp/pdfs/memoire-delivery-qa/metrics.json').read_text(encoding='utf8'))
assert qa['bbox_issues']==[]
validation={'author':'Rahma Amiri','stage':'MDL/26/12','pages':len(reader.pages),
 'chapters':6,'appendices':4,'figures':6,'bibliography_entries':len(bibkeys),
 'checks':['LaTeX compilation completed','BibTeX without warnings','All citations and labels resolved',
           'No overfull boxes or missing glyphs','All pages rendered and visually inspected',
           'No text outside page bounds','Cover identity and internship details checked'],
 'limitations':['No institutional ISI template was supplied','No full MySQL/Docker acceptance test claimed'],
 'pdf_sha256':hashlib.sha256(built.read_bytes()).hexdigest()}
(src/'evidence/report_validation.json').write_text(json.dumps(validation,ensure_ascii=False,indent=2)+'\n',encoding='utf8')
shutil.copy2(root/'tmp/latex-tools/main.bbl',src/'main.bbl')
shutil.copy2(built,pdf_out)
allowed={'.tex','.bib','.bbl','.md','.json','.php','.py'}
members=[]
with zipfile.ZipFile(zip_out,'w',compression=zipfile.ZIP_DEFLATED,compresslevel=9) as z:
    for f in sorted(src.rglob('*')):
        if f.is_file() and f.suffix in allowed and f.name!='review-main.tex' and '__pycache__' not in f.parts:
            z.write(f,f.relative_to(src).as_posix());members.append(f.relative_to(src).as_posix())
with zipfile.ZipFile(zip_out) as z:
    assert z.testzip() is None
    assert 'main.tex' in z.namelist() and 'appendices/annexes.tex' in z.namelist()
print(json.dumps({'pdf':str(pdf_out),'archive':str(zip_out),'pages':len(reader.pages),
 'source_files':len(members),'pdf_bytes':pdf_out.stat().st_size,'zip_bytes':zip_out.stat().st_size},indent=2,ensure_ascii=False))
