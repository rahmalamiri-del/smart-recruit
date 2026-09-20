from pathlib import Path
from PIL import Image,ImageOps,ImageDraw
from pypdf import PdfReader
import subprocess,json
root=Path('H:/smart-recruit')
base=root/'tmp/pdfs/kanban-uml'
out=base/'rendered';out.mkdir(exist_ok=True)
poppler=Path('C:/Users/hamma/.cache/codex-runtimes/codex-primary-runtime/dependencies/native/poppler/Library/bin/pdftoppm.exe')
pdf=PdfReader(base/'main.pdf')
texts=[p.extract_text() for p in pdf.pages]
(base/'page-text.json').write_text(json.dumps(texts,ensure_ascii=False,indent=2),encoding='utf-8')
print('PDF pages:',len(pdf.pages))
def sheet(files,dest,cols=4,thumb=(260,368)):
    rows=(len(files)+cols-1)//cols
    im=Image.new('RGB',(cols*(thumb[0]+16),rows*(thumb[1]+30)), '#dfe5e2')
    d=ImageDraw.Draw(im)
    for n,f in enumerate(files):
        src=Image.open(f).convert('RGB');src.thumbnail(thumb)
        x=(n%cols)*(thumb[0]+16)+8; y=(n//cols)*(thumb[1]+30)+22
        im.paste(src,(x+(thumb[0]-src.width)//2,y))
        d.text((x,y-17),f.stem,fill='black')
    im.save(dest)
screens=sorted((root/'output/latex/smartrecruit-memoire-kanban-uml/figures/screenshots').glob('*.png'))
sheet(screens,base/'interfaces-contact.png',3,(430,242))
subprocess.run([str(poppler),'-scale-to','900','-png',str(base/'main.pdf'),str(out/'page')],check=True,capture_output=True)
pages=sorted(out.glob('page-*.png'))
for i in range(0,len(pages),20): sheet(pages[i:i+20],base/f'contact-{i//20+1:02}.png')
for i,t in enumerate(texts,1):
    if any(k in t for k in ['Figure 3.', 'Figure 2.', 'Figure 4.', 'Figure 5.']):
        print(i, ' | '.join(line for line in t.splitlines() if 'Figure ' in line))
