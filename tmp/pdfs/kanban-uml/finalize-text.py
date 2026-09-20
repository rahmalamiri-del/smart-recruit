from pathlib import Path
import json
R=Path('H:/smart-recruit/output/latex/smartrecruit-memoire-kanban-uml')
def edit(p, a, b):
    f=R/p; t=f.read_text(encoding='utf-8'); assert a in t,(p,a[:100]); f.write_text(t.replace(a,b),encoding='utf-8')
edit('figures/uml-sequence-candidature.tex',r'Session, rôle,\offre et profil',r'Session, rôle,\\offre et profil')
edit('figures/uml-etats-candidature.tex',r'suppression\administrateur',r'suppression\\administrateur')
edit('figures/uml-etats-candidature.tex',r'restauration\statut antérieur',r'restauration\\statut antérieur')
edit('figures/uml-classes.tex',r'\draw[rel] (s.west)--++(-.65,0)|-node[left,pos=.05]{1} node[above,pos=.92]{0..*}(c.west);',r'\draw[rel] (s.south west)--++(0,-.35) node[right,font=\footnotesize]{1} --(2.3,-2.15)--(2.3,-6.6)--(3.8,-6.6)--node[left,pos=.75]{0..*}(3.8,-7.85)--(c.north west);')
edit('chapters/04-realisation.tex',r'Git, configuration et scripts & Artefacts de développement décrits lorsqu\'ils sont présents ; aucun historique de commits n\'est supposé.',r'Configuration et scripts & Paramétrage des services, lancement local et génération des ressources.') if False else None
edit('chapters/04-realisation.tex',"Git, configuration et scripts & Artefacts de développement décrits lorsqu'ils sont présents ; aucun historique de commits n'est supposé.","Configuration et scripts & Paramétrage des services, lancement local et génération des ressources.")

screens=[
('Connexion','01-connexion',"La page de connexion rassemble l'adresse électronique et le mot de passe dans un formulaire court. L'identité SmartRecruit et l'accès à la création de compte guident l'utilisateur avant l'ouverture de son espace. Cette interface correspond à la carte K01.","Interface de connexion à SmartRecruit"),
("Choix du profil à l'inscription",'02-inscription',"L'inscription commence par le choix du type de compte : candidat ou recruteur. Chaque option annonce le parcours associé et prépare le formulaire adapté. Le rôle d'administrateur ne fait pas partie des choix publics.","Sélection du type de compte lors de l'inscription"),
("Espace candidat",'05-espace-etudiant',"Le tableau de bord du candidat donne une vue d'ensemble de son profil et de son activité. Les accès aux offres et à la mise à jour du parcours facilitent les actions fréquentes. Le terme candidat employé à l'écran correspond au rôle étudiant du mémoire.","Tableau de bord du candidat avec données de démonstration"),
("Profil et informations du CV",'09-profil-etudiant',"La fiche rassemble les informations du parcours, les compétences et les éléments associés au CV. Cette restitution aide à relire les données utilisées lors du rapprochement avec une offre. La possibilité de corriger le profil complète l'extraction automatique décrite par la carte K04.","Présentation du profil candidat et de ses compétences"),
("Catalogue des offres",'06-offres-etudiant',"Les offres sont présentées avec les informations utiles à leur comparaison et un accès au détail. Des indicateurs de compatibilité et de suivi peuvent accompagner les résultats. Les valeurs visibles ici appartiennent au jeu illustratif ; elles ne mesurent pas la précision du moteur.","Catalogue des offres dans l'espace candidat"),
("Détail d'une offre",'07-detail-offre',"La page de détail expose la mission, les exigences et les informations de l'entreprise. Elle replace le rapprochement dans le contexte de l'offre et restitue la situation du candidat lorsqu'une candidature existe déjà. L'utilisateur peut ainsi comprendre le contenu du poste avant de poursuivre son suivi.","Consultation d'une offre et de ses exigences"),
("Espace recruteur",'04-espace-recruteur',"Le tableau de bord recruteur synthétise l'activité de ses offres et oriente vers leur gestion. Les filtres et les accès au vivier structurent le travail de consultation. Cette vue met en relation les cartes K05, K08 et K09, consacrées aux offres, aux décisions et au suivi.","Tableau de bord du recruteur"),
("Création d'une offre",'10-creation-offre',"Le formulaire permet de décrire une opportunité et les compétences recherchées. La capture montre sa partie supérieure, avec les premiers champs nécessaires à la publication. Les informations saisies alimentent ensuite la consultation de l'offre et sa comparaison avec les profils.","Partie supérieure du formulaire de création d'une offre"),
("Vivier des candidats",'12-profils',"La liste des profils permet au recruteur de parcourir le vivier et d'accéder aux fiches détaillées. Les informations synthétiques facilitent une première lecture des parcours. La présence d'une personne dans ce vivier ne signifie pas qu'elle a postulé à une offre donnée.","Consultation du vivier de profils par le recruteur"),
("Classement pour une offre",'08-classement',"Le classement associe chaque profil à un score et à des éléments d'explication, notamment les compétences rapprochées ou manquantes. Les actions de décision concernent les candidatures existantes. La capture est centrée sur cette section de la page ; les scores sont des valeurs de démonstration.","Section de classement des profils pour une offre"),
("Supervision administrative",'03-espace-administrateur',"L'espace administrateur fournit une vue transversale des comptes, des offres et des candidatures. Ses accès regroupent les fonctions de supervision et de gestion qui dépassent le périmètre d'un recruteur. Cette séparation rend visibles les responsabilités propres au troisième acteur UML.","Tableau de bord de l'administrateur"),
("Gestion des candidatures",'11-candidatures',"La liste administrative rassemble les candidatures, leur statut et les opérations de gestion. Les filtres permettent de retrouver les éléments actifs ou supprimés logiquement. Le statut métier et la présence dans la corbeille sont deux informations distinctes, conformément au diagramme d'états.","Suivi et gestion administrative des candidatures")]
text=r'''\section{Présentation des interfaces web clés}
\label{sec:interfaces-web}
Les douze captures suivantes ont été réalisées le 13 septembre 2026 dans un navigateur, à partir des gabarits Blade et de la feuille de style du projet. Un rendu local isolé leur fournit des comptes, offres et candidatures fictifs, sans connexion à la base métier. Les captures montrent donc les interfaces réellement générées par les vues, avec un jeu de présentation ; elles ne constituent ni des enregistrements de production, ni une recette des opérations de sauvegarde. Les scores affichés sont prédéfinis pour illustrer les composants visuels, et non calculés pendant la prise de vue.

Les images ont une résolution de 1\,280 par 720 pixels. Le manifeste livré précise leur fichier et la page correspondante. Les descriptions se concentrent sur le rôle de l'écran et les actions proposées ; les règles d'autorisation restent celles exposées dans les modèles et dans les routes. Les contrôles visuels de cette série concernent un écran d'ordinateur.

'''
for title,file,desc,caption in screens:
    text+='\\clearpage\n\\subsection{'+title+'}\n'+desc+'\n\n'+r'\begin{figure}[H]\centering'+'\n'+r'\includegraphics[width=\textwidth]{figures/screenshots/'+file+'.png}\n'+r'\caption{'+caption+'}\n'+r'\label{fig:interface-'+file+'}'+r'\end{figure}'+'\n\n'
(R/'chapters/04-interfaces.tex').write_text(text,encoding='utf-8')

f=R/'frontmatter.tex'; t=f.read_text(encoding='utf-8')
t=t.replace(r'{\Huge\sffamily\bfseries\color{navy}SmartRecruit\par}',r'\includegraphics[height=1.15cm]{figures/smartrecruit-icon.png}\par\vspace{0.15cm}'+'\n'+r'{\Huge\sffamily\bfseries\color{navy}SmartRecruit\par}')
t=t.replace("Le mémoire décrit les besoins, la conception des données et des échanges, l'implémentation des parcours et les mécanismes de recommandation.","Le mémoire organise le travail selon Kanban, avec un flux explicite, des limites de travail en cours et des critères de sortie. Sept diagrammes UML modélisent les acteurs, les données, les composants et les comportements. Douze captures commentées illustrent les parcours réalisés.")
t=t.replace("des points à corriger dans le stockage de secours et le déploiement", "des points à renforcer dans la cohérence des écritures et le déploiement")
t=t.replace('TF-IDF, similarité cosinus, Laravel, Python, explicabilité.', 'Kanban, UML, TF-IDF, similarité cosinus, Laravel, explicabilité.')
t=t.replace('The dissertation covers requirements, data and interaction design, implementation and the recommendation pipeline.', 'The work is organized using Kanban, with an explicit workflow, work-in-progress limits and completion criteria. Seven UML diagrams model actors, data, components and behavior. Twelve annotated browser screenshots document the main interfaces.')
t=t.replace('issues in fallback persistence and deployment', 'issues in write consistency and deployment')
t=t.replace('TF-IDF, cosine similarity, Laravel, Python, explainability.', 'Kanban, UML, TF-IDF, cosine similarity, Laravel, explainability.')
t=t.replace("Ces vérifications postérieures au stage constituent une évaluation rétrospective du prototype ; elles ne sont pas présentées comme un journal d'exécution établi pendant le stage.","La présente révision, datée du 13 septembre 2026, actualise la conception et les interfaces à partir du code disponible. Les sorties de sondes du 11 septembre sont conservées comme résultats historiques, sans être attribuées à une nouvelle exécution. Ces vérifications postérieures au stage constituent une évaluation rétrospective du prototype.")
t=t.replace("Les figures sont des schémas techniques construits à partir de l'implémentation ou de la conception explicitement proposée. Elles ne sont pas présentées comme des captures d'un déploiement en production.","Les figures réunissent des modèles UML, un tableau Kanban proposé et des captures réelles du rendu des vues sur des données fictives. Aucun journal Kanban daté n'ayant été fourni, son organisation est une reconstruction méthodologique et ses objectifs de flux sont proposés, sans mesures historiques inventées. Les captures du 13 septembre ne proviennent pas d'un déploiement en production.")
t=t.replace('SQL & Structured Query Language.', 'SLE & Service Level Expectation : attente de niveau de service du flux.\\\\\nSQL & Structured Query Language.')
t=t.replace('WSGI & Web Server Gateway Interface', 'WIP & Work in Progress : nombre de travaux commencés et non terminés.\\\\\nWSGI & Web Server Gateway Interface')
f.write_text(t,encoding='utf-8')

edit('chapters/05-moteur.tex',r'\code{local-php} ou \code{python-ai}',r'\code{local-php} ou \code{python}')
f=R/'chapters/05-moteur.tex'; t=f.read_text(encoding='utf-8'); pos=t.index('\\section')
t=t[:pos]+"Ce chapitre décrit les formules présentes dans le code examiné. Les exemples numériques sont des calculs illustratifs ou des sorties historiques explicitement identifiées dans le chapitre de validation. Les cartes K04 et K07 encadrent respectivement la qualité des données extraites et la restitution du score. Le label de provenance distant actuel est \\code{python} ; le fichier historique du 11 septembre conserve l'ancien label \\code{python-ai}.\n\n"+t[pos:]; f.write_text(t,encoding='utf-8')

f=R/'chapters/06-validation.tex'; t=f.read_text(encoding='utf-8')
t=t.replace('\\chapter{Validation du prototype et perspectives d\'amélioration}',"\\chapter{Validation, maîtrise du flux Kanban et perspectives}")
t=t.replace('Les contrôles présentés ont été réalisés lors de l\'analyse du code disponible en septembre 2026.',"Les sondes chiffrées et contrôles techniques ci-dessous proviennent de l'analyse des 10 et 11 septembre 2026. La révision du 13 septembre relit le code et ajoute les preuves de rendu des interfaces, sans rejouer la campagne historique ni qualifier les parcours MySQL.")
t=t.replace('Contrôles exécutés sur la version disponible en septembre 2026','Contrôles historiques des 10 et 11 septembre 2026')
t=t.replace('\\section{Comportements du moteur de rapprochement}',r'''\section{Vérifications de la révision et critères Kanban}
La nouvelle rédaction a été rapprochée des routes, du magasin SQL et des services présents le 13 septembre. Les modèles décrivent notamment la persistance MySQL obligatoire, la restauration logique et la revalidation du compte en session. Le rendu isolé a généré 19 gabarits et 32 variantes sans erreur signalée par le script ; douze vues ont été capturées dans le navigateur pour le chapitre~\ref{chap:realisation}. Ces observations établissent la disponibilité de rendus illustrés, sans prouver l'enregistrement des formulaires ou les droits par un essai HTTP de bout en bout.

Les sources ont évolué depuis les sondes : les anciennes empreintes ne qualifient donc pas toutes les classes actuelles. Le marqueur Python, par exemple, est devenu \code{python} au lieu de \code{python-ai}. Les chiffres du tableau suivant restent attachés au fichier historique, avec leurs entrées. Les formules expliquées au chapitre~\ref{chap:moteur} sont relues séparément. Cette distinction évite de confondre une révision documentaire et une nouvelle campagne expérimentale.

Dans Kanban, une preuve accompagne la carte avant son passage à Terminé. Le tableau~\ref{tab:validation-kanban} relie les travaux à des critères observables. Il ne constitue pas une liste de cartes déjà acceptées pendant le stage.
\begin{table}[H]\centering\small
\caption{Preuves de vérification et compléments nécessaires par capacité}
\label{tab:validation-kanban}
\begin{tabularx}{\textwidth}{@{}P{2cm}XX@{}}\toprule
Cartes & Éléments disponibles & Compléments avant une recette métier\\\midrule
K01--K02 & Routes et vues d'identité ; contrôles de rôle relus. & Matrice de droits, sessions et refus pour un compte tiers.\\
K03--K04 & Profil illustré ; sondes historiques d'extraction. & Import de fichiers synthétiques, correction et persistance relues.\\
K05--K06 & Formulaires, catalogue et séquence UML. & Création, fermeture, candidature concurrente et restauration en base jetable.\\
K07--K08 & Sondes historiques de score ; classement illustré. & Contrat partagé, cohérence explication/score et effets d'une décision.\\
K09--K10 & Tableaux de bord et administration capturés. & Filtres, suppressions et restitutions sur un jeu préparé.\\
K11--K12 & Lecture de la persistance et protocole d'essais. & Pannes, transactions, déploiement isolé et corpus annoté.\\
\bottomrule\end{tabularx}\end{table}

L'efficacité du flux se vérifiera par les dates de début et de fin de chaque carte, son âge lorsqu'elle reste ouverte et le nombre de cartes terminées par période. Aucun historique de ces événements n'est disponible pour calculer un temps de cycle moyen ou un débit du stage. L'attente initiale de cinq jours à 85\,\% présentée au chapitre~\ref{chap:besoins} est un objectif proposé à réviser après observation. Elle n'est ni une performance obtenue ni un engagement contractuel envers Best Solutions.

\section{Comportements du moteur de rapprochement}''')
t=t.replace('Sondes ciblées de comparaison des deux implémentations','Sondes historiques du 11 septembre comparant les deux implémentations')
t=t.replace("Le repli JSON utilise des séquences de lecture, modification et réécriture sans verrouillage global de l'opération. Des mises à jour concurrentes peuvent donc s'écraser. Un JSON invalide déclenche aussi la reconstruction du jeu de démonstration. Ces observations sont issues du code ; aucune perte de données existantes n'est affirmée. Pour la production, il convient de conserver un magasin autoritatif unique et de signaler son indisponibilité. Les essais de panne doivent vérifier qu'une erreur SQL ne conduit pas à annoncer une sauvegarde dans un univers de données différent.","La sélection actuelle du magasin utilise MySQL et signale son indisponibilité par une réponse 503. L'ancien magasin JSON demeure dans les sources pour la démonstration, mais n'est plus le repli de persistance des routes métier. Ce changement corrige la description de la version initiale du mémoire. Un test de panne reste nécessaire pour vérifier l'absence de confirmation trompeuse et la conservation d'une session cohérente. La création et la synchronisation de données de démonstration dans le magasin SQL restent un point à isoler du fonctionnement normal.")
t=t.replace("Les propositions sont de déclarer les proxies attendus et de vérifier la limitation avec la topologie réellement retenue.","Les propositions sont de déclarer les proxies attendus et de vérifier la limitation avec la topologie réellement retenue. La version actuelle relit le compte associé à la session pour prendre en compte un changement de rôle ou une suppression ; cette revalidation ne remplace pas les essais de fixation et d'invalidation.")
t=t.replace('Immédiate & Racine publique, isolation des données de démonstration, session et contrôle de candidature.', 'Immédiate & Racine publique, isolation des données de démonstration, session et contrôle de candidature (K02, K06, K11).')
t=t.replace('Fiabilité & Transactions, concurrence, diagnostics d\'extraction et contrat partagé des moteurs.',"Fiabilité & Transactions, concurrence, extraction et contrat partagé des moteurs (K04, K07, K11).")
t=t.replace('Pertinence & Corpus séparé, annotations professionnelles et comparaison des classements.', 'Pertinence & Corpus séparé, annotations professionnelles et comparaison des classements (K07, K12).')
t=t.replace('Exploitation & Déploiement isolé, sauvegarde, observabilité et campagne de performance.', 'Exploitation & Déploiement isolé, sauvegarde, observabilité et campagne de performance (K11, K12).')
f.write_text(t,encoding='utf-8')

edit('appendices/annexes.tex', 'output/latex/smartrecruit-memoire/evidence', 'output/latex/smartrecruit-memoire-kanban-uml/evidence')
edit('appendices/annexes.tex', 'Les suppressions en cascade assurent', 'Les opérations de suppression logique et de restauration utilisent des marqueurs applicatifs sans effacement immédiat. Les règles SQL suivantes concernent un effacement physique : les suppressions en cascade assurent')
edit('appendices/annexes.tex','"source": "python-ai"','"source": "python"')
edit('appendices/annexes.tex','Ce dernier contient les entrées, sorties, versions et empreintes SHA-256 des fichiers analysés.',"Ce dernier contient les entrées, sorties, versions et empreintes SHA-256 des fichiers analysés le 11 septembre. Il conserve le marqueur historique \\code{python-ai}, alors que le contrat actuel illustré ci-dessus emploie \\code{python}. Les sources ayant évolué, ces empreintes ne doivent pas être attribuées à toute la version du 13 septembre.")

bib=R/'references.bib'; t=bib.read_text(encoding='utf-8')
if '@misc{kanban-guide2025' not in t:
    t+='''\n@misc{kanban-guide2025,
  author = {{The Kanban Guide}},
  title = {The Kanban Guide},
  year = {2025},
  month = may,
  url = {https://kanbanguides.org/the-kanban-guide/2025.5/},
  note = {Version de mai 2025, consultée le 13 septembre 2026}
}\n'''
    bib.write_text(t,encoding='utf-8')

old=R/'evidence/report_validation.json'
if old.exists():
    old.rename(R/'evidence/original_report_validation_2026-09-11.json')
print('Texte finalisé, 12 captures référencées.')
