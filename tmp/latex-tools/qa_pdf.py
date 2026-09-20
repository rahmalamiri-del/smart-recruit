from pathlib import Path
import json, re, sys
from pypdf import PdfReader
from PIL import Image,ImageDraw,ImageFont
import pdfplumber
base=Path(sys.argv[1] if len(sys.argv)>1 else 'H:/smart-recruit/tmp/pdfs/memoire-qa')
reader=PdfReader('H:/smart-recruit/tmp/latex-tools/main.pdf')
texts=[p.extract_text() or '' for p in reader.pages]
metrics={'pages':len(texts),'words':sum(len(t.split()) for t in texts),'sparse_pages':[],'figure_pages':[],'bbox_issues':[]}
for n,t in enumerate(texts,1):
    if len(t.strip())<160:metrics['sparse_pages'].append({'page':n,'text':t})
    if re.search(r'Figure\s+[1-6]\.\d',t):metrics['figure_pages'].append(n)
with pdfplumber.open('H:/smart-recruit/tmp/latex-tools/main.pdf') as pdf:
    for n,page in enumerate(pdf.pages,1):
        for ch in page.chars:
            if ch['x0']<15 or ch['x1']>page.width-15 or ch['top']<15 or ch['bottom']>page.height-15:
                metrics['bbox_issues'].append({'page':n,'text':ch['text'],'bbox':[ch['x0'],ch['top'],ch['x1'],ch['bottom']]})
font=ImageFont.truetype('C:/Windows/Fonts/arial.ttf',18)
images=sorted(base.glob('page-*.png'))
for start in range(0,len(images),12):
    sheet=Image.new('RGB',(1200,1320),'#d7dfe6'); draw=ImageDraw.Draw(sheet)
    for offset,imgpath in enumerate(images[start:start+12]):
        im=Image.open(imgpath).convert('RGB'); im.thumbnail((280,395))
        col,row=offset%4,offset//4
        x,y=col*300+(300-im.width)//2,row*440+30
        sheet.paste(im,(x,y));draw.text((col*300+14,row*440+7),f'Page {start+offset+1}',font=font,fill='black')
    sheet.save(base/f'contact-{start//12+1:02}.png')
(base/'metrics.json').write_text(json.dumps(metrics,indent=2,ensure_ascii=False),encoding='utf8')
(base/'all-text.txt').write_text('\n\n'.join(f'PAGE {n+1}\n'+t for n,t in enumerate(texts)),encoding='utf8')
print(json.dumps(metrics,ensure_ascii=False,indent=2))
