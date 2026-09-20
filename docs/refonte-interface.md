# Refonte de l’interface SmartRecruit

L’interface reprend le vert profond et la menthe du logo. Les espaces administrateur, recruteur et étudiant partagent désormais une navigation latérale sur ordinateur et un menu dépliable sur mobile. Les destinations et les droits des rôles sont conservés.

Les tableaux de bord, l’accueil, l’authentification, les offres, les profils et les pages d’administration utilisent une même hiérarchie de titres, de boutons, de cartes et de formulaires. Les champs des formulaires sont regroupés par sujet, sans changement de nom, de méthode ou de destination. Les tableaux restent défilables horizontalement sur petit écran.

## Fichiers

- Styles communs : `assets/app.css`, recopié à l’identique dans `public/assets/app.css`.
- Structure : `resources/views/layout.blade.php` et composants `partials/navigation`, `partials/icon`, `partials/main`.
- Pages : les 19 modèles d’écran présents dans `resources/views/`.
- Aperçus : `output/restyle/tableau-de-bord.png` et `output/restyle/connexion.png` (données fictives).

## Vérification

- Les 19 modèles Blade ont été rendus dans 32 variantes, sans erreur, avec les vrais composants partagés.
- Les 32 variantes ont été contrôlées dans un navigateur à 375 px et sur ordinateur : aucun débordement de page ni image manquante détecté.
- Six écrans représentatifs ont également été vérifiés à 320 px : tableau de bord, création d’offre, modification de profil et de compte, inscription recruteur et liste d’offres.
- L’ouverture du menu mobile, ses six liens administrateur et le raccourci clavier « Aller au contenu » ont été vérifiés.
- Les noms et l’ordre des champs, les routes, les méthodes et les marqueurs CSRF des cinq formulaires métier restructurés ont été comparés avant et après modification ; les deux vues d’authentification ont également conservé leurs champs et routes.

Le générateur `tmp/restyle-preview/render.php` utilise uniquement des fixtures fictives, sans charger `.env`, contacter de base de données ou exécuter une route métier. Ces contrôles portent sur la présentation et le rendu ; ils ne constituent pas des essais des opérations en base ni du moteur de recommandation. Les détails figurent dans `tmp/restyle-preview/report.json` et `browser-checks.json`.
