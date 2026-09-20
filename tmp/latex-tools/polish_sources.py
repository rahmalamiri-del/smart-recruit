from pathlib import Path
import re
p=Path('H:/smart-recruit/output/latex/smartrecruit-memoire')
f=p/'chapters/04-realisation.tex'
s=f.read_text(encoding='utf-8-sig')
s=s.replace('le texte normalisé','le texte du CV extrait ou saisi')
s=s.replace('La liste des étudiants ne présente que les offres publiées',"La liste d'offres destinée aux étudiants ne présente que les offres publiées")
s=s.replace('Le chapitre suivant précise les essais',r'Le chapitre~\ref{chap:validation} précise les essais')
s=s.replace("Aucune capture artificielle n'est utilisée pour simuler une campagne de tests visuels ou une observation d'utilisateurs. ",'')
f.write_text(s,encoding='utf-8')
f=p/'chapters/02-besoins.tex'
s=f.read_text(encoding='utf-8-sig').replace("contrôle d'extension et de taille",'contrôle du type MIME et de la taille')
f.write_text(s,encoding='utf-8')
f=p/'chapters/03-conception.tex'
s=f.read_text(encoding='utf-8-sig').replace(r'contre les écritures concurrentes~\cite{mysql-constraints}',r'contre les écritures concurrentes~\cite{mysql-unique}')
f.write_text(s,encoding='utf-8')
f=p/'figures/usecases.tex'
f.write_text(f.read_text(encoding='utf-8-sig').replace('text width=2.3cm','text width=2.9cm'),encoding='utf-8')
f=p/'references.bib'
s=f.read_text(encoding='utf-8-sig')
for key in ['docker-compose','laravel-database','laravel-deployment','laravel-routing','laravel-validation','mysql-constraints','owasp-session','owasp-upload','flask-docs','pdfminer-docs','php-server']:
    s=s.replace('@misc{'+key+',','@misc{'+key+',\n year={s. d.},')
f.write_text(s,encoding='utf-8')
for f in list((p/'chapters').glob('*.tex'))+list((p/'appendices').glob('*.tex')):
    s=f.read_text(encoding='utf-8-sig')
    s=re.sub(r'(?<![A-Za-z])p\{(\d+(?:\.\d+)?(?:cm|\\textwidth))\}',r'P{\1}',s)
    f.write_text(s,encoding='utf-8')
print('Source polishing complete')
