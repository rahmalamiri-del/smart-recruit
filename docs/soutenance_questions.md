# Questions de Soutenance

## Pourquoi utiliser TF-IDF au lieu d'un LLM ?

TF-IDF est explicable, rapide, économique et respecte mieux la confidentialité des CV. Un LLM peut être une évolution, mais le mémoire doit montrer un algorithme maîtrisé et justifiable.

## Pourquoi séparer Laravel et Python ?

Laravel gère très bien l'application web, l'authentification et MySQL. Python est l'écosystème naturel pour le NLP et les bibliothèques de data science. La séparation permet aussi de scaler le service IA indépendamment.

## Que se passe-t-il si le microservice IA tombe ?

La candidature reste sauvegardée et l'application utilise un fallback PHP. Le recruteur conserve un ranking calculé localement. C'est un choix de résilience.

## Un candidat peut-il tromper le système en copiant l'offre dans son CV ?

Oui, c'est une limite classique des systèmes basés sur le texte. Le prototype agit comme aide à la décision, pas comme décision automatique finale. Les futures améliorations peuvent ajouter détection d'anomalies, comparaison d'expérience réelle et validation humaine.

## Pourquoi MySQL avec JSON ?

Les données relationnelles restent normalisées: utilisateurs, offres, candidatures. Les JSON servent aux métadonnées variables extraites des CV: compétences détectées, e-mails, téléphones, réponse brute IA.

## Quelle est la différence avec une requête SQL `LIKE` ?

`LIKE` cherche une correspondance exacte. Le moteur TF-IDF transforme les textes en vecteurs et mesure leur proximité globale. La taxonomie ajoute les synonymes et rapprochements métiers, par exemple `ReactJS` et `front-end`.
