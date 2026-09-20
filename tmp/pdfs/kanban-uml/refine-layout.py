from pathlib import Path
import re,json,shutil
R=Path('H:/smart-recruit/output/latex/smartrecruit-memoire-kanban-uml')
for f in (R/'figures').glob('*.tex'):
    t=f.read_text(encoding='utf-8')
    if not t.startswith('\\begingroup'):
        f.write_text('\\begingroup\\setstretch{1}\n'+t+'\n\\endgroup\n',encoding='utf-8')
f=R/'figures/uml-classes.tex';t=f.read_text(encoding='utf-8')
t=t.replace(r'\textbf{ScoreCorrespondance}',r'\textbf{Score de correspondance}')
t=re.sub(r'\\draw\[rel\] \(s.south west\).*?;',r'\\draw[rel] (s.south west)--++(-.5,-.4) node[left,font=\\footnotesize]{1} |-($(c.north west)+(0,.6)$)--node[left,pos=.5]{0..*}(c.north west);',t)
f.write_text(t,encoding='utf-8')
f=R/'chapters/02-besoins.tex';t=f.read_text(encoding='utf-8');start=t.index('La figure~\\ref{fig:usecases-besoins}');end=t.index('\\section{Spécification',start)
t=t[:start]+"Le diagramme UML des cas d'utilisation figure au chapitre~\\ref{chap:conception}, figure~\\ref{fig:uml-usecases}. Les scénarios suivants précisent les contrôles de propriété, les erreurs de validation et les conséquences d'une indisponibilité.\n\n"+t[end:];f.write_text(t,encoding='utf-8')
f=R/'chapters/03-conception.tex';t=f.read_text(encoding='utf-8')
t=t.replace('La figure~\\ref{fig:uml-etats} présente la distribution vers les quatre valeurs autorisées, et sépare cette dimension de la suppression logique.',"La figure~\\ref{fig:uml-etats} présente la distribution vers les quatre valeurs autorisées, et sépare cette dimension de la suppression logique. Elle montre également l'effet de \\code{saveMatchScore} : lorsqu'une candidature est encore reçue et que le score enregistré atteint 70, son statut devient présélectionné. Ce seuil applicatif n'est pas une probabilité. L'acceptation et le refus restent des actions humaines ; séparer la mise à jour du score et la présélection est une amélioration proposée.")
f.write_text(t,encoding='utf-8')
f=R/'chapters/04-realisation.tex';t=f.read_text(encoding='utf-8').replace("Le moteur ne prend pas la décision de recrutement : l'acteur habilité examine le profil, le CV et les éléments professionnels utiles avant de modifier le statut.","Le calcul peut déclencher une présélection automatique d'une candidature reçue à partir du seuil de 70. L'acceptation ou le refus relève ensuite de l'acteur habilité, qui examine le profil, le CV et les éléments professionnels utiles. Cette automatisation intermédiaire doit rester distincte d'une décision finale de recrutement.")
f.write_text(t,encoding='utf-8')
f=R/'main.tex';t=f.read_text(encoding='utf-8').replace(r'\usepackage{graphicx}',r'\usepackage{graphicx}'+'\n'+r'\usepackage{pdflscape}');f.write_text(t,encoding='utf-8')
f=R/'chapters/04-interfaces.tex';t=f.read_text(encoding='utf-8')
t=t.replace('\\clearpage\n\\subsection','\\begin{landscape}\n\\subsection')
t=t.replace(r'width=\textwidth',r'width=.94\linewidth,height=.74\textwidth,keepaspectratio')
t=t.replace(r'\end{figure}',r'\end{figure}'+'\n'+r'\end{landscape}')
t=re.sub(r'(\\subsection\{[^\n]+\}\n)([^\n]+)',r'\1{\\small\\setstretch{1.05}\n\2\\par}\n',t)
f.write_text(t,encoding='utf-8')
f=R/'chapters/05-moteur.tex';t=f.read_text(encoding='utf-8').replace('\\begin{figure}[htbp]','\\begin{figure}[H]');f.write_text(t,encoding='utf-8')
E=R/'evidence/interfaces';E.mkdir(exist_ok=True)
shutil.copyfile('H:/smart-recruit/tmp/restyle-preview/report.json',E/'render-results-2026-09-13.json')
src=Path('H:/smart-recruit/tmp/restyle-preview/render.php').read_text(encoding='utf-8')
src=src.replace('$projectRoot = dirname(__DIR__, 2);',"$projectRoot = dirname(__DIR__, 5);")
src=src.replace('php -d allow_url_fopen=0 tmp/restyle-preview/render.php','php -d allow_url_fopen=0 output/latex/smartrecruit-memoire-kanban-uml/evidence/interfaces/render.php')
src=src.replace('Then serve only tmp/restyle-preview/site with any static server.','Then serve only the site/ directory created next to this script.')
(E/'render.php').write_text(src,encoding='utf-8')
(R/'figures/uml-etats-candidature.tex').write_text(r'''\begingroup\setstretch{1}
\begin{tikzpicture}[font=\small,state/.style={draw=navy,rounded corners=6pt,fill=lightgray,align=center,minimum width=2.6cm,minimum height=1cm},tr/.style={-{Stealth[length=2mm]},draw=navy,line width=.65pt}]
\draw[navy,rounded corners=6pt] (-7,1.5) rectangle (6,-8.2);
\node[anchor=west,font=\bfseries] at (-6.6,1) {Candidature active};
\fill[navy] (0,.15) circle (.1);
\node[state,minimum width=3.5cm] (s) at (0,-1.1) {Reçue\\\texttt{submitted}};
\node[diamond,draw=navy,aspect=2,minimum width=.6cm] (choice) at (0,-3.1) {};
\node[state] (p) at (-3.2,-5) {Présélection\\\texttt{shortlisted}};
\node[state] (i) at (0,-5) {Acceptée\\\texttt{interview}};
\node[state] (r) at (3.2,-5) {Refusée\\\texttt{rejected}};
\draw[tr] (0,.05)--(s);
\draw[tr] (s)--node[right,font=\footnotesize]{modifierStatut(s)}(choice);
\draw[tr] (choice)-|node[pos=.68,left,font=\footnotesize]{[s=shortlisted]}(p);
\draw[tr] (choice)--node[right,font=\footnotesize]{[s=interview]}(i);
\draw[tr] (choice)-|node[pos=.68,right,font=\footnotesize]{[s=rejected]}(r);
\draw[tr] (choice.west)--(-4.8,-3.1)|-node[pos=.78,above,font=\footnotesize]{[s=submitted]}(s.west);
\draw[navy] (p.south)--(-3.2,-6.2)--(5.2,-6.2);
\draw[navy] (i.south)--(0,-6.2);\fill[navy] (0,-6.2) circle (.055);
\draw[navy] (r.south)--(3.2,-6.2);\fill[navy] (3.2,-6.2) circle (.055);
\draw[tr] (5.2,-6.2)--(5.2,-2.25)--node[above,font=\footnotesize]{modifierStatut(s)}(1,-2.25)--(choice.north east);
\draw[tr,draw=teal] (s.north west)--(-6.2,-.6)--(-6.2,-5)--(p.west);
\node[font=\footnotesize,text=teal,align=center,fill=white,text width=2.1cm] at (-6.1,-3.9) {score enregistré\\$[\mathrm{score}\geq70]$};
\node[align=center,font=\footnotesize,text width=11.6cm,text=midgray] at (-.4,-7.3) {Les mises à jour manuelles supposent un acteur habilité. Les retours et les changements entre les quatre valeurs sont admis ; aucun ordre linéaire n'est imposé.};
\node[state,minimum width=5.6cm] (del) at (-.5,-10.4) {Candidature supprimée logiquement\\statut métier conservé};
\draw[tr] (-3,-8.2)--node[left,align=right,font=\footnotesize]{suppression\\administrateur}(del.north west);
\draw[tr] (del.north east)--node[right,align=left,font=\footnotesize]{restauration\\statut antérieur}(2,-8.2);
\end{tikzpicture}
\endgroup
''',encoding='utf-8')
print('Mise en page affinée.')
