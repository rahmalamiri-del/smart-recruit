from pathlib import Path
import hashlib,json
W=Path('H:/smart-recruit'); R=W/'output/latex/smartrecruit-memoire-kanban-uml'
# Only copied, unused artifacts in this new report directory are removed.
for name in ['main.bbl','review-main.tex','figures/architecture.tex','figures/data-model.tex','figures/deployment.tex','figures/sequence.tex','figures/usecases.tex']:
    target=(R/name).resolve();assert target.is_relative_to(R.resolve())
    if target.exists():target.unlink()
history=json.loads((R/'evidence/results.json').read_text(encoding='utf-8-sig'))
relevant=set(history['sha256'])|{'resources/css/app.css'}
relevant.update(str(p.relative_to(W)).replace('\\','/') for p in (W/'resources/views').rglob('*.blade.php'))
relevant.update(str(p.relative_to(W)).replace('\\','/') for p in (W/'database/migrations').glob('*.php'))
snapshot={'date':'2026-09-13','scope':'Read-only hashes of report-related code; not an execution of historical probes.','files':{}}
for name in sorted(relevant):
    f=W/name
    if not f.is_file():continue
    h=hashlib.sha256(f.read_bytes()).hexdigest()
    row={'sha256':h}
    if name in history['sha256']:row['matches_historical_2026_09_11']=h==history['sha256'][name]
    snapshot['files'][name]=row
(R/'evidence/source_snapshot_2026-09-13.json').write_text(json.dumps(snapshot,ensure_ascii=False,indent=2),encoding='utf-8')
(R/'README.md').write_text('''# Mémoire SmartRecruit - Rahma Amiri

Version révisée le 13 septembre 2026 : Kanban, conception UML et captures commentées.

Master Développement Logiciel, Institut Supérieur d'Informatique (ISI), Université Tunis El Manar. Stage MDL/26/12 chez Best Solutions, du 02/03/2026 au 02/07/2026. Encadrant professionnel : Hammami Houssem.

## Organisation du mémoire

1. Contexte du projet, cadre Kanban et fondements scientifiques.
2. Spécification des besoins et organisation du travail par Kanban.
3. Conception UML et architecture de SmartRecruit.
4. Réalisation de la plateforme et interfaces web.
5. Conception et fonctionnement du moteur de matching.
6. Validation, maîtrise du flux Kanban et perspectives.

La couverture, les remerciements, le résumé français, l'abstract, la note de périmètre, l'introduction, la conclusion, la bibliographie et quatre annexes complètent les chapitres.

Le flux Kanban est Réserve, Prêt, En cours, Vérification, Terminé. Les douze cartes K01 à K12 relient les exigences aux preuves attendues. La limite proposée est d'une carte En cours et d'une carte en Vérification ; une carte bloquée compte toujours dans le travail engagé. L'attente initiale de 85 % des cartes en cinq jours est une hypothèse de pilotage, pas une performance du stage.

Les sept modèles UML couvrent les cas d'utilisation, les classes, les composants, deux séquences, l'activité de traitement du CV et les états d'une candidature. Un tableau Kanban et une chaîne de calcul complètent les figures vectorielles. Le chapitre 4 présente douze captures réelles de rendu Blade/CSS sur des pages en paysage, chacune avec une courte description.

## Fichiers

- `main.tex` : point d'entrée, polices, styles et métadonnées.
- `frontmatter.tex`, `introduction.tex`, `conclusion.tex` : parties générales.
- `chapters/` : six chapitres et le fichier inclus `04-interfaces.tex`.
- `figures/` : neuf figures TikZ éditables et le logo de l'application.
- `figures/screenshots/` : douze PNG 1280 × 720 et leur manifeste.
- `appendices/annexes.tex` : dictionnaire, API, sondes et scénarios de consolidation.
- `references.bib` : références scientifiques et documentations officielles.
- `evidence/` : résultats historiques, empreintes de la révision et contrôle documentaire.

## Compilation sur Overleaf

Importer cette archive dans un nouveau projet, sélectionner `main.tex` comme document principal et **XeLaTeX** comme compilateur. La bibliographie utilise BibTeX / natbib. Les polices TeX Gyre Pagella, TeX Gyre Heros et Latin Modern sont fournies par les distributions TeX usuelles. Les pages de captures utilisent `pdflscape`.

Compilation locale avec TeX Live ou MiKTeX :

```text
latexmk -xelatex -interaction=nonstopmode main.tex
```

Ou :

```text
xelatex main.tex
bibtex main
xelatex main.tex
xelatex main.tex
```

Le PDF remis a été compilé avec Tectonic (XeTeX) :

```text
tectonic --keep-logs main.tex
```

Les figures TikZ et captures sont incluses dans l'archive : aucune génération d'image, accès à l'application ou connexion MySQL n'est nécessaire pour compiler.

## Provenance des preuves

Les captures ont été prises le 13 septembre 2026 dans un navigateur, sur le rendu des vues réelles du projet avec des données entièrement fictives. Les comptes, sociétés, documents et scores sont des fixtures de présentation. Les formulaires sont neutralisés dans cet aperçu ; aucune route métier, connexion à la base ou requête de scoring n'a été exécutée pour les images. Un état « connecté » du moteur dans une capture appartient également à ces fixtures.

`evidence/interfaces/render-results-2026-09-13.json` décrit le rendu de 19 gabarits et 32 variantes. `evidence/interfaces/render.php` permet de régénérer l'aperçu si le dossier du mémoire est placé dans `output/latex/smartrecruit-memoire-kanban-uml` du projet, avec ses dépendances Composer disponibles. Il crée un dossier `site/` à côté du script. Les PNG sont des captures de navigateur, indépendantes de la compilation LaTeX.

`evidence/results.json` conserve les sondes exécutées le 11 septembre 2026. Leurs empreintes, versions et valeurs numériques restent historiques. `evidence/source_snapshot_2026-09-13.json` consigne les empreintes du code relu pour cette révision et les différences avec ces résultats. Le label distant actuel est `python`, tandis que le résultat historique contient `python-ai`.

Les scripts `probes.php` et `probes.py` sont fournis pour la reproductibilité. Ils ne démarrent pas Laravel, ne sollicitent pas MySQL et utilisent Flask en mémoire. Avant de les rejouer, conserver une copie de `results.json`, car le script remplace ce fichier :

```text
python output/latex/smartrecruit-memoire-kanban-uml/evidence/probes.py
```

La nouvelle compilation est décrite par `evidence/report_validation.json`. Le fichier `original_report_validation_2026-09-11.json` concerne exclusivement le précédent mémoire de 84 pages.

## Périmètre rédactionnel

Le nom **Rahma Amiri** a été confirmé. Aucun jury, encadrant académique ou historique commercial de Best Solutions n'est inventé. Aucun gabarit institutionnel ISI n'a été fourni ; la mise en page est une proposition académique.

L'organisation Kanban est une reconstruction méthodologique du travail présenté. Aucun journal daté de cartes, débit, temps de cycle observé ou réunion de stage n'est supposé. La conception suit le code relu, notamment la persistance MySQL obligatoire, la suppression logique et la présélection automatique à partir de 70 pour une candidature reçue. Les classes UML sont conceptuelles, sans prétendre que le projet contient un modèle Eloquent par entité.

Le mémoire distingue le code observé, les sondes historiques, le rendu des interfaces et les tests restant à mener. Il ne prétend pas disposer d'une recette MySQL/Docker complète, d'un benchmark de charge ou d'une étude utilisateurs. Les captures illustrent les interfaces ; elles ne prouvent pas l'enregistrement de leurs formulaires. La version originale du mémoire reste conservée séparément.
''',encoding='utf-8')
(R/'evidence/README.md').write_text('''# Traçabilité de la révision

- `results.json` : résultats historiques du 11 septembre 2026, inchangés.
- `probes.php` / `probes.py` : sondes synthétiques associées à ces résultats ; les rejouer remplace le fichier de résultats.
- `source_snapshot_2026-09-13.json` : empreintes des fichiers pertinents relus pour cette révision ; ne constitue pas une campagne d'exécution.
- `interfaces/render-results-2026-09-13.json` : bilan de génération des vues avec fixtures fictives (19 gabarits, 32 variantes).
- `interfaces/render.php` : reproduction du rendu isolé depuis le projet, sans base ni dispatch des routes.
- `../figures/screenshots/manifest.json` : correspondance entre chaque capture et sa page d'aperçu.
- `original_report_validation_2026-09-11.json` : contrôle du précédent document de 84 pages, conservé pour éviter de l'attribuer au nouveau PDF.
- `report_validation.json` : contrôle du PDF révisé et son empreinte, généré à la livraison.

Les données des captures et les scores sont illustratifs. Les résultats du 11 septembre ne sont pas présentés comme réexécutés le 13 septembre. Les limites figurent également dans le mémoire.
''',encoding='utf-8')
print('Sources nettoyées et documentation préparée.')
