# Logo SmartRecruit

Deux maillons entrelacés représentent la mise en relation des étudiants et des entreprises. Le vert profond reprend la couleur principale de l’application ; le vert menthe distingue les deux parties du symbole.

- `smartrecruit-mark.svg` : symbole compact, utilisé par l’application et comme favicon.
- `smartrecruit-logo.svg` : signature complète sur fond clair.
- `smartrecruit-logo-light.svg` : signature complète sur fond sombre.

La signature est « Étudiants & entreprises ». Les fichiers SVG ont un fond transparent en dehors du carré du symbole et peuvent être agrandis sans perte de netteté. Les versions complètes utilisent Segoe UI, puis Inter ou Arial si nécessaire.

Couleurs : vert `#126B5B`, menthe `#A8E6CE`, encre `#17201B`, blanc `#FFFFFF`.

Conserver les proportions et une marge libre d’au moins un quart de la hauteur du symbole. Pour les petites tailles, utiliser le symbole seul ; dans la navigation, il mesure 40 px. Le nom accessible du lien est fourni par le composant Blade ; son image est donc décorative (`alt=""`).

Le projet expose actuellement les ressources dans `assets/` et `public/assets/`. Lors d’une modification, recopier ces trois SVG dans `public/assets/brand/` et garder les deux feuilles `app.css` identiques.
