from pathlib import Path
R=Path('H:/smart-recruit/output/latex/smartrecruit-memoire-kanban-uml/figures')
(R/'uml-classes.tex').write_text(r'''\begingroup\setstretch{1}
\begin{tikzpicture}[font=\fontsize{9}{11}\selectfont,
cl/.style={draw=navy,fill=lightgray,align=left,text width=4.4cm,inner sep=7pt},
rel/.style={draw=navy,line width=.65pt}]
\node[cl] (u) at (0,0) {\textbf{Compte}\\\rule{4.35cm}{.3pt}\\id : entier\\identifiantPublic : chaîne\\nom, email : chaîne\\rôle : Rôle\\motDePasse : hash};
\node[cl] (s) at (6,0) {\textbf{ProfilÉtudiant}\\\rule{4.35cm}{.3pt}\\id : entier\\titre, formation : chaîne\\compétences : liste\\texteCV : texte\\expérience : entier};
\node[cl] (r) at (12,0) {\textbf{ProfilRecruteur}\\\rule{4.35cm}{.3pt}\\id : entier\\entreprise : chaîne\\poste : chaîne\\siteWeb : URL};
\node[cl] (d) at (6,-5.5) {\textbf{DocumentCV}\\\rule{4.35cm}{.3pt}\\id : entier\\nomOriginal : chaîne\\chemin : chaîne\\typeMIME : chaîne\\résultatExtraction : JSON};
\node[cl] (o) at (12,-5.5) {\textbf{Offre}\\\rule{4.35cm}{.3pt}\\id : entier\\titre, description : texte\\compétences : liste\\statut : ÉtatOffre\\localisation : chaîne};
\node[cl] (c) at (6,-11) {\textbf{Candidature}\\\rule{4.35cm}{.3pt}\\id : entier\\statut : ÉtatCandidature\\dateCandidature : date\\suppression : marqueur};
\node[cl] (m) at (0,-11) {\textbf{Score de correspondance}\\\rule{4.35cm}{.3pt}\\score : entier\\similaritéTexte : entier\\couverture : entier\\réponseBrute : JSON};
\draw[rel] (u.east)--node[above,pos=.15]{1} node[above,pos=.85]{0..*}(s.west);
\draw[rel] (u.north)--++(0,1.15)-|node[above,pos=.1]{1} node[right,pos=.96]{0..*}(r.north);
\draw[rel] (s.south)--node[left,pos=.12]{1} node[left,pos=.88]{0..*}(d.north);
\draw[rel] (r.south)--node[left,pos=.12]{0..1} node[left,pos=.88]{0..*}(o.north);
\draw[rel] (s.south west)--++(-.45,-.45) node[left]{1} |-($(c.north west)+(0,.75)$)--node[left,pos=.7]{0..*}(c.north west);
\draw[rel] (o.south)--++(0,-1.0)-|node[right,pos=.05]{1} node[right,pos=.93]{0..*}(c.north);
\draw[rel] (c.west)--node[above,pos=.15]{1} node[above,pos=.85]{0..*}(m.east);
\node[align=left,font=\fontsize{9}{11}\selectfont,text=midgray,text width=16cm,anchor=north west] at (-2.15,-13.5) {Contrainte : un couple (offre, profil étudiant) identifie au plus une candidature.\\Les multiplicités compte/profil suivent les contraintes SQL observées ; l'interface crée un profil adapté au rôle.};
\end{tikzpicture}
\endgroup
''',encoding='utf-8')
(R/'kanban-board.tex').write_text(r'''\begingroup\setstretch{1}
\begin{tikzpicture}[x=1cm,y=1cm,
kbcol/.style={draw=navy!30,rounded corners=2pt,fill=lightgray,minimum width=3.3cm,minimum height=5cm},
kbtitle/.style={font=\sffamily\bfseries\fontsize{10}{12}\selectfont,text=navy,align=center,text width=3.1cm},
kbtext/.style={font=\fontsize{9}{11}\selectfont,align=center,text width=2.95cm,text=midgray},
kbcap/.style={font=\sffamily\bfseries\fontsize{9}{11}\selectfont,text=teal,align=center,text width=3cm},
kbarrow/.style={-{Stealth[length=1.6mm]},draw=navy,line width=.6pt}]
\foreach \x in {1.65,5.25,8.85,12.45,16.05}{\node[kbcol] at (\x,0) {};}
\node[kbtitle] at (1.65,1.9) {Réserve};\node[kbtitle] at (5.25,1.9) {Prêt};
\node[kbtitle] at (8.85,1.9) {En cours};\node[kbtitle] at (12.45,1.9) {Vérification};\node[kbtitle] at (16.05,1.9) {Terminé};
\node[kbcap] at (1.65,.95) {Besoins à ordonner};\node[kbcap] at (5.25,.95) {Plafond : 3 cartes};
\node[kbcap] at (8.85,.95) {WIP : 1 carte};\node[kbcap] at (12.45,.95) {WIP : 1 carte};\node[kbcap] at (16.05,.95) {Preuves conservées};
\node[kbtext] at (1.65,-.85) {Valeur attendue\\Périmètre à préciser\\Dépendances à lever};
\node[kbtext] at (5.25,-.85) {Critère observable\\Prochaine action\\Sélection limitée};
\node[kbtext] at (8.85,-.85) {Une réalisation\\à la fois\\Début enregistré};
\node[kbtext] at (12.45,-.85) {Contrôles ciblés\\Relecture et reprise\\Preuve associée};
\node[kbtext] at (16.05,-.85) {Critères satisfaits\\Documentation à jour\\Fin enregistrée};
\foreach \x in {3.3,6.9,10.5,14.1}{\draw[kbarrow] (\x,1.9)--++(.3,0);}
\draw[teal,dashed,line width=.7pt] (7.08,-2.7) rectangle (14.22,2.8);
\node[font=\sffamily\bfseries\small,text=teal,fill=white,inner sep=3pt] at (10.65,2.8) {WIP global maximal : 2};
\node[font=\scriptsize,text=teal] at (7.08,-3.05) {DÉBUT};\node[font=\scriptsize,text=teal] at (14.22,-3.05) {FIN};
\draw[{Stealth[length=1.4mm]}-{Stealth[length=1.4mm]},draw=teal] (7.25,-3.55)--(14.05,-3.55);
\node[font=\footnotesize,text=midgray,fill=white,inner sep=3pt] at (10.65,-3.55) {Temps de cycle};
\node[draw=navy!25,rounded corners=2pt,fill=white,align=left,text width=17cm,inner sep=9pt,font=\fontsize{9}{11}\selectfont,text=navy] at (8.85,-5) {\textbf{Politique de tirage :} commencer uniquement si une place est disponible.\\\textbf{Blocage :} la carte reste dans sa colonne et dans le WIP.\\\textbf{SLE initial proposé :} 85\,\% des cartes en cinq jours calendaires ou moins.};
\end{tikzpicture}
\endgroup
''',encoding='utf-8')
f=R/'uml-etats-candidature.tex';t=f.read_text(encoding='utf-8')
t=t.replace('node[right,font=\\footnotesize]{modifierStatut(s)}','node[right,pos=.25,font=\\footnotesize]{modifierStatut(s)}')
t=t.replace('(5.2,-2.25)', '(5.2,-2.7)').replace('(1,-2.25)', '(1,-2.7)')
t=t.replace('at (-6.1,-3.9)', 'at (-6.1,-2.2)')
f.write_text(t,encoding='utf-8')
f=R/'uml-cas-utilisation.tex';t=f.read_text(encoding='utf-8').replace('(12.4,-12.5)','(13,-12.5)');f.write_text(t,encoding='utf-8')
f=R/'uml-activite-cv.tex';t=f.read_text(encoding='utf-8').replace(r'font=\small',r'font=\fontsize{10}{12}\selectfont').replace(r'node[right]{[oui]}',r'node[right,pos=.2]{[oui]}').replace('Afficher l\'avertissement','Afficher un avertissement').replace('Associer texte extrait et texte manuel','Associer les deux sources de texte');f.write_text(t,encoding='utf-8')
print('Diagrammes corrigés.')
