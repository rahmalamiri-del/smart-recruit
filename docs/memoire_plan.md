# Plan Type: Mémoire de Master

## Introduction générale

- Contexte: difficulté du recrutement moderne, volume de CV, limites des plateformes classiques.
- Problématique: comment automatiser et fiabiliser la présélection des candidats avec le NLP ?
- Objectifs: parser les CV, calculer un score sémantique, afficher un ranking recruteur.
- Annonce du plan.

## Chapitre 1: État de l'art

- Systèmes de recrutement actuels: LinkedIn, Indeed, plateformes universitaires.
- Recherche par mots-clés vs recherche sémantique.
- TF-IDF, similarité cosinus, Word2Vec, BERT/CamemBERT.
- Architectures monolithiques vs microservices.
- Justification du choix Laravel + microservice Python.

## Chapitre 2: Analyse et conception

- Besoins fonctionnels: authentification, profils, offres, candidatures, upload CV, ranking.
- Besoins non fonctionnels: sécurité des fichiers, performance, maintenabilité, extensibilité.
- Diagrammes UML: cas d'utilisation, classes, séquence, déploiement.
- Modèle de données MySQL.

## Chapitre 3: Réalisation

- Environnement de développement: PHP/Laravel, Python, MySQL, Docker.
- Backend Laravel: routes, stockage SQL, vues Blade.
- Microservice IA: parsing PDF, extraction de compétences, TF-IDF, API REST.
- Intégration Laravel -> Python avec fallback local.
- Interface recruteur: scores, ranking, compétences manquantes.

## Chapitre 4: Tests et validation

- Tests de pertinence: Java Enterprise vs J2EE, ReactJS vs Front-end.
- Tests de performance: temps de réponse du matching.
- Tests de résilience: microservice IA indisponible, fallback PHP.
- Limites: CV scannés, candidats qui copient l'offre dans leur CV, besoin futur d'OCR.

## Conclusion et perspectives

- Bilan technique et scientifique.
- Apport de l'architecture distribuée.
- Perspectives: OCR, CamemBERT/BERT, files de jobs Laravel, dashboard analytique avancé.
