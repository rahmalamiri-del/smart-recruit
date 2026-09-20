from pathlib import Path
import json,shutil,hashlib,zipfile
W=Path('H:/smart-recruit');R=W/'output/latex/smartrecruit-memoire-kanban-uml';B=W/'tmp/pdfs/kanban-uml'
qa=json.loads((B/'qa-results.json').read_text(encoding='utf-8'))
assert hashlib.sha256((B/'main.pdf').read_bytes()).hexdigest()==qa['pdf_sha256']
qa.update({'author':'Rahma Amiri','stage':'MDL/26/12','revision':'2026-09-13','visual_review':'All 102 rendered pages reviewed in overview sheets; UML, Kanban and screenshot pages additionally inspected at larger size.','checks':['LaTeX and BibTeX completed','All citations and labels resolved','No overfull boxes or missing glyphs','No text outside PDF page bounds','12 landscape interface pages','All included figures and inputs present','Identity and internship details verified'],'limitations':['Kanban workflow reconstructed, no historical flow metrics supplied','Screenshots use real Blade/CSS renders with fictitious presentation data','Numerical probes retained as historical 2026-09-11 results','No new end-to-end MySQL or deployment acceptance campaign']})
(R/'evidence/report_validation.json').write_text(json.dumps(qa,ensure_ascii=False,indent=2),encoding='utf-8')
final=W/'output/pdf/Memoire_Master_SmartRecruit_Rahma_Amiri_Kanban_UML.pdf';final.parent.mkdir(exist_ok=True,parents=True)
shutil.copyfile(B/'main.pdf',final)
archive=W/'output/latex/Memoire_Master_SmartRecruit_Rahma_Amiri_Kanban_UML_LaTeX.zip'
allowed={'.tex','.bib','.md','.json','.php','.py','.png'}
files=[f for f in R.rglob('*') if f.is_file() and f.suffix in allowed and '__pycache__' not in f.parts]
with zipfile.ZipFile(archive,'w',zipfile.ZIP_DEFLATED) as z:
    for f in sorted(files):z.write(f,f.relative_to(R).as_posix())
with zipfile.ZipFile(archive) as z:
    assert z.testzip() is None
    assert 'main.tex' in z.namelist()
    assert sum(n.startswith('figures/screenshots/') and n.endswith('.png') for n in z.namelist())==12
assert hashlib.sha256(final.read_bytes()).hexdigest()==qa['pdf_sha256']
print(json.dumps({'pdf':str(final),'pages':qa['pages'],'bytes':final.stat().st_size,'archive':str(archive),'archive_files':len(files),'archive_bytes':archive.stat().st_size},ensure_ascii=False,indent=2))
