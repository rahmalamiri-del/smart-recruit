# Aperçu statique SmartRecruit

Depuis la racine du projet :

```powershell
php -d allow_url_fopen=0 tmp/restyle-preview/render.php
```

Le générateur utilise les vrais templates Blade, le layout et les partials avec des données entièrement fictives. Il ne charge pas `.env`, ne démarre aucun kernel Laravel, ne charge aucun provider applicatif ou de base de données, ne dispatch aucune route et n'appelle aucun service. Toutes les écritures restent dans `tmp/restyle-preview`.

Les routes sont enregistrées uniquement pour générer leurs noms et URL. Les URL métier des résultats sont ensuite remplacées par des pages statiques ou des liens inactifs. Les formulaires sont neutralisés. Les noms de CV ne correspondent à aucun document existant.

Le dossier à servir est exclusivement `tmp/restyle-preview/site`, jamais la racine applicative :

```powershell
php -S 127.0.0.1:18996 -t tmp/restyle-preview/site
```

Ouvrir ensuite `/index.html`. Le rapport de couverture et les éventuelles erreurs se trouvent dans `tmp/restyle-preview/report.json`.

Relancer la génération après toute modification des vues ou des assets : elle recopie CSS et images dans le site d'aperçu. Cette vérification porte sur le rendu HTML, pas sur les autorisations, les contrôleurs, les transactions ou les calculs métier. Les compteurs, noms, comptes et scores sont des fixtures synthétiques.
