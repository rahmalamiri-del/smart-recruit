# Comparatif cahier des charges / projet

| Element demande | Etat dans le projet | Emplacement |
| --- | --- | --- |
| Plateforme etudiants-entreprises | Inclus | Dashboard, offres, profils etudiants |
| Roles etudiant, recruteur, admin | Inclus en mode demonstration | `/login`, session role demo |
| Vue admin | Inclus | `/admin` pour synthese utilisateurs/offres/candidatures |
| Authentification production | Perspective | A remplacer par Laravel Breeze/Fortify/Sanctum |
| Upload CV PDF/TXT | Inclus | `/students/create`, `/offers/{id}` |
| Parsing CV | Inclus | `CvParser.php`, `ai-service/app.py` |
| Extraction competences | Inclus | `SemanticMatcher.php`, microservice Python |
| Extraction email, telephone, diplome, experience | Inclus partiellement | Parseur PHP/Python selon qualite du CV |
| Creation d'offres | Inclus | `/offers/create` |
| Candidature 1 clic | Inclus | `/offers/{id}` |
| Candidature avec CV depuis une offre | Inclus | Formulaire `Postuler avec CV` |
| Matching semantique CV-offre | Inclus | Score TF-IDF + cosine + couverture competences |
| Score 0-100 | Inclus | Ranking offre et API JSON |
| Classement recruteur | Inclus | `/offers/{id}` |
| Actions accepter/refuser | Inclus | Boutons recruteur dans le ranking |
| Persistance MySQL | Inclus | Tables `sr_*`, migration Laravel |
| Fallback JSON | Inclus | `storage/app/data/smart-recruit.json` |
| Microservice Python IA | Inclus | `ai-service/app.py` |
| Test ping Laravel/Python | Inclus | `/test-connection`, `/health` |
| Docker Compose Laravel + Python + MySQL | Inclus | `docker-compose.yml`, `Dockerfile`, `ai-service/Dockerfile` |
| Diagrammes UML | Inclus | `docs/uml.md` |
| Diagramme de deploiement | Inclus | `docs/uml.md` |
| Tests et validation | Inclus documente | `docs/tests_validation.md` |
| Bibliographie | Inclus documente | `docs/bibliographie.md` |
| OCR CV scannes | Perspective | Mentionne dans tests/validation et roadmap |
| BERT/CamemBERT | Perspective | Mentionne dans memoire, roadmap, bibliographie |
| Jobs/queues asynchrones | Perspective | Mentionne dans tests/validation et roadmap |

## Points a presenter comme limites assumees

- Le login actuel sert a la demonstration rapide des roles; pour une vraie mise en production, il faut activer une authentification complete avec mots de passe, reinitialisation et protections de session.
- L'OCR n'est pas integre: les CV scannes doivent etre convertis en texte ou colles dans le champ resume.
- Le moteur actuel privilegie l'explicabilite: chaque score affiche les competences trouvees et manquantes. Un modele plus lourd peut etre ajoute plus tard pour ameliorer les nuances semantiques.
