from pathlib import Path
p=Path('H:/smart-recruit/output/latex/smartrecruit-memoire/figures')
(p/'sequence.tex').write_text(r'''\begingroup\singlespacing
\begin{tikzpicture}[x=1cm,y=1cm]
\foreach \x/\t in {0/Recruteur,3.8/Laravel,7.4/MySQL,11/Python}{
\node[block,text width=2.1cm] at (\x,0) {\textbf{\t}};
\draw[dashed,midgray] (\x,-0.45)--(\x,-11.3);
}
\draw[arr] (0,-1)--node[note,above,fill=white,inner sep=2pt]{Consulter le classement}(3.8,-1);
\node[note,text width=3cm,anchor=west] at (4,-1.75){Vérifier le rôle\\et la propriété de l'offre};
\draw[arr] (3.8,-2.65)--node[note,above,fill=white,inner sep=2pt]{Lire les profils}(7.4,-2.65);
\draw[arr,dashed] (7.4,-3.35)--node[note,above,fill=white,inner sep=2pt]{Profils / candidatures}(3.8,-3.35);
\draw[arr] (3.8,-4.1)--node[note,above,fill=white,inner sep=2pt]{GET /health}(11,-4.1);
\draw[arr,dashed] (11,-4.8)--node[note,above,fill=white,inner sep=2pt]{État du service}(3.8,-4.8);
\draw[teal,rounded corners] (2.2,-5.3) rectangle (12.2,-9.7);
\node[note,anchor=north west,fill=white] at (2.35,-5.4){Pour chaque profil};
\node[note,anchor=west,text width=4cm] at (4,-6.15){Calcul PHP de secours};
\draw[arr] (3.8,-7)--node[note,above,fill=white,inner sep=2pt]{POST /match si Python disponible}(11,-7);
\draw[arr,dashed] (11,-7.8)--node[note,above,fill=white,inner sep=2pt]{Score et indicateurs}(3.8,-7.8);
\draw[arr] (3.8,-9)--node[note,above,text width=3.5cm,fill=white,inner sep=2pt]{Sauver si une candidature existe}(7.4,-9);
\draw[arr,dashed] (3.8,-10.55)--node[note,above,fill=white,inner sep=2pt]{Classement trié}(0,-10.55);
\node[note,text width=12cm] at (5.5,-12.1){Séquence observée de consultation. La candidature est une action distincte. La sauvegarde du score peut aussi déclencher une présélection dans le code actuel.};
\end{tikzpicture}
\endgroup
''',encoding='utf8')
(p/'data-model.tex').write_text(r'''\begingroup\singlespacing
\begin{tikzpicture}[x=1cm,y=1cm]
\node[block,text width=4.3cm] (u) at (0,0) {\textbf{sr\_users}\\id (PK), public\_id (UQ)\\email (UQ), role, password};
\node[block,text width=4.3cm] (s) at (-3.1,-2.8) {\textbf{sr\_student\_profiles}\\id (PK), user\_id (FK)\\skills, cv\_text, experience\_years};
\node[block,text width=4.3cm] (r) at (3.1,-2.8) {\textbf{sr\_recruiter\_profiles}\\id (PK), user\_id (FK)\\company\_name, position};
\node[block,text width=4.3cm] (cv) at (-3.1,-5.6) {\textbf{sr\_cv\_documents}\\id (PK), student\_profile\_id (FK)\\stored\_path, parser\_result};
\node[block,text width=4.3cm] (o) at (3.1,-5.6) {\textbf{sr\_offers}\\id (PK), recruiter\_profile\_id (FK)\\description, required\_skills, status};
\node[block,text width=6.5cm] (a) at (0,-8.6) {\textbf{sr\_applications}\\id (PK), offer\_id (FK), student\_profile\_id (FK)\\UQ(offer\_id, student\_profile\_id), status};
\node[block,text width=6.5cm] (m) at (0,-11.3) {\textbf{sr\_match\_scores}\\id (PK), application\_id (FK)\\score, matched\_skills, raw\_response};
\draw[arr] (u.south west) -- node[note,above left,fill=white,inner sep=2pt]{1 / 0..*} (s.north);
\draw[arr] (u.south east) -- node[note,above right,fill=white,inner sep=2pt]{1 / 0..*} (r.north);
\draw[arr] (s) -- node[note,left,fill=white,inner sep=2pt]{1 / 0..*} (cv);
\draw[arr] (r) -- node[note,right,fill=white,inner sep=2pt]{0..1 / 0..*} (o);
\draw[arr] (s.west) -- ++(-0.8,0) |- (a.west);
\node[note,fill=white,inner sep=2pt] at (-5.2,-7.7){1 / 0..*};
\draw[arr] (o.south) -- node[note,right,fill=white,inner sep=2pt]{1 / 0..*} (a.north east);
\draw[arr] (a) -- node[note,right,fill=white,inner sep=2pt]{1 / 0..*} (m);
\node[note,text width=12cm] at (0,-13.1) {Cardinalités autorisées par le schéma SQL. PK : clé primaire ; FK : clé étrangère ; UQ : unicité. Les contraintes métier supplémentaires sont discutées dans le texte.};
\end{tikzpicture}
\endgroup
''',encoding='utf8')
for name in ['architecture.tex','deployment.tex','matching-pipeline.tex','usecases.tex']:
    f=p/name
    s=f.read_text(encoding='utf-8-sig')
    if not s.startswith('\\begingroup'):
        f.write_text('\\begingroup\\singlespacing\n'+s+'\n\\endgroup\n',encoding='utf8')
print('Diagrams revised')
