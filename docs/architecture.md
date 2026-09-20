# Architecture Smart-Recruit

## Vue d'ensemble

Smart-Recruit suit une architecture orientée services:

- Backend principal Laravel: authentification, profils, offres, candidatures, vues Blade, stockage relationnel.
- Microservice IA Python: parsing CV, extraction de compétences, TF-IDF, similarité cosinus, score de matching.
- MySQL: utilisateurs, profils, offres, candidatures, scores et métadonnées JSON.
- Docker Compose: environnement reproductible pour démontrer Laravel, Python et MySQL comme services séparés.

## Flux de matching

1. L'étudiant charge son CV.
2. Laravel stocke le document et envoie le texte ou le fichier au microservice IA.
3. Python extrait les compétences et retourne les métadonnées.
4. Le recruteur publie une offre en description libre.
5. Laravel demande le score de compatibilité pour chaque candidature.
6. Laravel persiste le score dans `sr_match_scores`.
7. Le tableau de bord trie les profils du plus pertinent au moins pertinent.

## Choix algorithmique initial

Le MVP utilise un score hybride:

- Similarité TF-IDF + cosinus sur tokens normalisés.
- Couverture des compétences requises.
- Taxonomie sémantique pour rapprocher des termes liés, par exemple `ReactJS` et `front-end`.

Cette base est volontairement explicable pour la soutenance. Elle peut ensuite être comparée à Word2Vec, Sentence-BERT ou CamemBERT.

## Vérification microservice

La route Laravel `/test-connection` appelle le endpoint Python `/health`. Elle sert de preuve simple que les deux services communiquent correctement, notamment en Docker avec `AI_SERVICE_URL=http://ai-service:8010`.
