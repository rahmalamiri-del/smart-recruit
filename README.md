# Smart-Recruit

Plateforme de mise en relation étudiants-entreprises avec moteur de recommandation sémantique CV-offre.

## Démarrage

Le dossier est prêt pour le sous-domaine:

```text
https://smart-recruit.smartbs.tn
```

Les dépendances Composer doivent être installées dans ce dossier, avec un `vendor/` propre au projet.

```bash
cd smart-recruit
composer install --no-dev --optimize-autoloader
```

En local serveur:

```bash
php -S 127.0.0.1:8088 -t smart-recruit smart-recruit/server.php
```

## Docker et microservices

Pour démontrer l'architecture distribuée Laravel + Python + MySQL:

```bash
cd smart-recruit
docker compose up -d --build
docker compose exec laravel-app php artisan migrate --force
```

Points de vérification:

```text
http://localhost:8080
http://localhost:8080/test-connection
http://localhost:8010/health
```

Dans Docker, Laravel contacte Python avec `AI_SERVICE_URL=http://ai-service:8010`.

## Modules livrés

- Session de démonstration multi-rôles: étudiant, recruteur, admin.
- Vue admin de synthèse utilisateurs, offres et candidatures.
- Gestion de profils étudiants avec upload CV PDF/TXT.
- Parser CV PHP léger avec extraction e-mail, expérience, diplôme, compétences.
- Création d'offres avec description libre.
- Candidature en un clic.
- Candidature avec CV directement depuis une offre.
- Score de matching et ranking automatique.
- Actions recruteur: accepter ou refuser une candidature.
- Schéma MySQL et migration Laravel.
- Microservice IA Python séparé.
- Docker Compose pour démontrer l'architecture microservices.
- Route `/test-connection` pour tester la communication Laravel vers Python.
- Documentation d'architecture, UML et roadmap.

## Donnees de test

Le projet contient un jeu de donnees de demonstration etendu dans:

```text
database/demo_dataset.php
```

Il ajoute plusieurs domaines de stage: cybersecurite, DevOps cloud, mobile Flutter, BI Power BI, UX/UI, IoT embarque, QA automation, SAP ABAP, marketing digital, finance data, Laravel API et data engineering.

Volume actuel apres synchronisation MySQL:

```text
25 utilisateurs
23 profils etudiants
14 offres
56 candidatures
56 scores de matching
```

## Microservice IA

```bash
cd smart-recruit/ai-service
python3 -m venv venv
source venv/bin/activate
pip install -r requirements.txt
python app.py
```

Laravel lit l'URL depuis `AI_SERVICE_URL` dans `.env`.
Si Flask n'est pas encore installé, le service Python fournit quand même `/health` et `/match` via un fallback standard-library.

## Base de données

Le projet est branché sur MySQL via `.env`.

```bash
php artisan migrate --force
```

Tables principales:

```text
sr_users
sr_student_profiles
sr_recruiter_profiles
sr_cv_documents
sr_offers
sr_applications
sr_match_scores
```

Le fichier JSON reste seulement un fallback de développement si les tables SQL ne sont pas disponibles:

```text
storage/app/data/smart-recruit.json
```

Le schéma MySQL est documenté dans:

```text
database/migrations/2026_05_27_000001_create_smart_recruit_tables.php
database/schema/mysql.sql
```

## Documents mémoire

```text
docs/architecture.md
docs/algorithme_matching.md
docs/uml.md
docs/comparatif_cahier_charges.md
docs/tests_validation.md
docs/bibliographie.md
docs/sequence_submission_analysis.puml
docs/memoire_plan.md
docs/soutenance_questions.md
```

## Comptes démo

- Admin: `admin@smart-recruit.test`
- Recruteur: `recruteur@smart-recruit.test`
- Étudiant: `amira.bensalem@example.com`

La page `/login` permet de changer de rôle sans mot de passe pour la démo.
