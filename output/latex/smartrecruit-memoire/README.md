# Mémoire SmartRecruit -- Rahma Amiri

Mémoire de Master Développement Logiciel, Institut Supérieur d'Informatique (ISI), Université Tunis El Manar. Stage MDL/26/12, Best Solutions, du 02/03/2026 au 02/07/2026. Encadrement professionnel : Hammami Houssem.

## Contenu

- `main.tex` : document principal et mise en page.
- `frontmatter.tex` : couverture, remerciements, résumés, note de périmètre, sommaire et listes.
- `chapters/` : six chapitres rédigés.
- `figures/` : six diagrammes vectoriels TikZ éditables.
- `appendices/annexes.tex` : dictionnaire, contrats API, reproduction et validation complémentaire.
- `references.bib` : bibliographie scientifique et documentation officielle.
- `evidence/` : sondes synthétiques et résultats du code examiné.

## Compilation sur Overleaf

Importer l'archive de sources dans un nouveau projet. Choisir `main.tex` comme document principal et **XeLaTeX** comme compilateur. Les packages utilisés sont classiques (TikZ, fontspec, babel, natbib, listings, booktabs). Overleaf traite automatiquement BibTeX avec latexmk.

## Compilation locale

Avec TeX Live ou MiKTeX :

```text
latexmk -xelatex -interaction=nonstopmode main.tex
```

Sans latexmk :

```text
xelatex main.tex
bibtex main
xelatex main.tex
xelatex main.tex
```

Avec Tectonic (qui télécharge les dépendances nécessaires à la première utilisation) :

```text
tectonic main.tex --keep-logs
```

Le document possède également une branche de polices pour pdfLaTeX ; le PDF remis a été composé avec le moteur XeTeX de Tectonic.

## Reproduction des sondes

Les scripts de `evidence/` se lancent depuis la copie du projet SmartRecruit dans laquelle ils ont été produits. Ils supposent la structure `output/latex/smartrecruit-memoire/evidence/` et les dépendances applicatives disponibles.

```text
python output/latex/smartrecruit-memoire/evidence/probes.py
```

Les sondes ne chargent pas le bootstrap Laravel, n'ouvrent pas MySQL et n'effectuent pas d'appel HTTP externe. Flask est interrogé en mémoire via son client de test. Les entrées sont synthétiques. Le script produit `results.json` avec les empreintes SHA-256 des fichiers source pertinents. Exécuter les sondes n'est pas nécessaire pour compiler le mémoire.

## Périmètre rédactionnel

Le nom **Rahma Amiri** a été confirmé. Aucune seconde étudiante n'est indiquée. Les informations non communiquées (jury, encadrant académique, historique et taille de Best Solutions) ne sont pas inventées. La mise en page est une proposition académique, sans affirmation de conformité à un gabarit officiel ISI non fourni.

Le rapport distingue code observé, essais exécutés en septembre 2026 et améliorations proposées. Il ne présente ni une recette MySQL/Docker complète, ni des performances de charge, ni une étude utilisateurs comme déjà réalisées. Les résultats sont liés à la version du code analysée ; après correction de l'application, les résultats et la discussion doivent être actualisés.
