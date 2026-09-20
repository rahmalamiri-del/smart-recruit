# Traçabilité de la révision

- `results.json` : résultats historiques du 11 septembre 2026, inchangés.
- `probes.php` / `probes.py` : sondes synthétiques associées à ces résultats ; les rejouer remplace le fichier de résultats.
- `source_snapshot_2026-09-13.json` : empreintes des fichiers pertinents relus pour cette révision ; ne constitue pas une campagne d'exécution.
- `interfaces/render-results-2026-09-13.json` : bilan de génération des vues avec fixtures fictives (19 gabarits, 32 variantes).
- `interfaces/render.php` : reproduction du rendu isolé depuis le projet, sans base ni dispatch des routes.
- `../figures/screenshots/manifest.json` : correspondance entre chaque capture et sa page d'aperçu.
- `original_report_validation_2026-09-11.json` : contrôle du précédent document de 84 pages, conservé pour éviter de l'attribuer au nouveau PDF.
- `report_validation.json` : contrôle du PDF révisé et son empreinte, généré à la livraison.

Les données des captures et les scores sont illustratifs. Les résultats du 11 septembre ne sont pas présentés comme réexécutés le 13 septembre. Les limites figurent également dans le mémoire.
