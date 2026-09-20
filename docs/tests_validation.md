# Tests et validation

Ce document sert de base pour le chapitre "Tests et validation" du memoire et pour la demonstration.

## Tests fonctionnels

| Cas | Etapes | Resultat attendu |
| --- | --- | --- |
| Connexion technique | Ouvrir `/test-connection` | JSON avec `storage=mysql` et reponse IA si le service Python est lance |
| Creation profil etudiant | Aller sur `/students/create`, saisir les infos, deposer un PDF/TXT | Profil cree, texte CV extrait, competences detectees |
| Creation offre | Aller sur `/offers/create`, saisir description et competences | Offre publiee, ranking calcule |
| Candidature 1 clic | Ouvrir une offre, cliquer sur `Candidature 1 clic` | Candidature ajoutee pour l'etudiant demo |
| Candidature avec CV | Ouvrir une offre, remplir `Postuler avec CV` | Profil candidat cree, candidature enregistree, score IA sauvegarde |
| Decision recruteur | Dans le ranking, cliquer `Accepter` ou `Refuser` | Statut de candidature mis a jour dans MySQL |
| Vue admin | Se connecter en admin puis ouvrir `/admin` | Synthese utilisateurs, offres et candidatures visible |

## Tests de pertinence IA

| Scenario | Offre | CV attendu en tete | Justification |
| --- | --- | --- | --- |
| Full-stack Java/React | Java, Spring Boot, React, SQL, Git | Profil Java/React | Correspondance forte sur competences techniques |
| NLP/Data Science | Python, NLP, machine learning | Profil Data Scientist NLP | Correspondance sur Python, NLP, machine learning |
| Front-end UI | JavaScript, TypeScript, Vue | Profil Front-End | Correspondance sur competences front-end |

## Tests de resilience

| Situation | Comportement attendu |
| --- | --- |
| Microservice Python arrete | Laravel utilise le moteur local PHP comme fallback |
| MySQL indisponible | Le projet bascule sur le fichier JSON de demonstration |
| CV PDF difficile a lire | Le texte manuel colle dans le formulaire reste utilisable |

## Tests de performance proposes

| Mesure | Methode | Objectif |
| --- | --- | --- |
| Temps `/test-connection` | Chronometrer l'appel HTTP | Confirmer que Laravel contacte Python rapidement |
| Temps ranking offre | Ouvrir une offre avec plusieurs candidats | Verifier que le classement reste fluide |
| Taille CV | Tester TXT/PDF de 1 a 4 Mo | Respecter la limite d'upload configuree |

## Limites identifiees

- Les CV scannes comme images necessitent une future couche OCR.
- Le projet utilise une authentification de demonstration; une version production devra activer Laravel Breeze/Fortify/Sanctum ou une solution equivalente.
- Le matching actuel est explicable et leger; un modele BERT/CamemBERT peut etre ajoute pour ameliorer la similarite semantique.
- Les calculs sont synchrones; des jobs Laravel Queue seraient preferables pour de gros volumes.
