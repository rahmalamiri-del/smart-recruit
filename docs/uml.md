# UML Smart-Recruit

Diagrammes mis à jour pour refléter le contrôle d'accès par rôle, l'authentification réelle (email + mot de passe), l'extraction de CV robuste (IA + repli local), la candidature unifiée en un clic, et le tri/filtrage côté recruteur.

## Cas d'utilisation

```mermaid
flowchart LR
    subgraph Etudiant
        S_Register[S'inscrire]
        S_Login[Se connecter]
        S_Profile[Gerer mon profil + CV]
        S_Offers[Consulter offres recommandees]
        S_Apply[Postuler en 1 clic]
        S_Track[Suivre mes candidatures]
    end

    subgraph Recruteur
        R_Login[Se connecter]
        R_Offer[Creer / modifier une offre]
        R_Status[Definir le statut de l'offre]
        R_Filter[Trier et filtrer mes offres]
        R_Students[Consulter etudiants recommandes]
        R_Decision[Accepter ou refuser]
    end

    subgraph Admin
        A_Login[Se connecter]
        A_All[Voir toute la plateforme]
        A_Accounts[Garantir comptes + mot de passe par defaut]
        A_AddCv[Ajouter un candidat avec CV]
    end

    S_Register --> S_Login
    S_Profile --> Parser[Parseur CV + IA]
    S_Offers --> Matching[Moteur de matching]
    S_Apply --> Matching
    S_Apply --> S_Track
    R_Offer --> R_Status
    R_Filter --> Matching
    R_Students --> Matching
    Matching --> R_Filter
    Matching --> S_Offers
    A_AddCv --> Parser
```

**Changements par rapport à l'ancienne version** : l'étudiant s'inscrit et se connecte réellement (fini le sélecteur de rôle démo) ; il n'existe plus qu'un seul chemin de candidature (`Postuler en 1 clic`, qui réutilise le CV du profil) — l'ancien formulaire "postuler avec CV" par offre a été supprimé. Le recruteur gagne le tri/filtrage de ses offres et la mise en avant des étudiants compatibles.

## Contrôle d'accès par rôle

```mermaid
flowchart TB
    Req[Requete HTTP] --> MW{"Middleware role:admin,recruiter,student"}
    MW -->|role absent - guest| Guest[Redirection vers /login]
    MW -->|role present mais non autorise| Forbidden[403]
    MW -->|role autorise| Ownership{Verification de propriete}
    Ownership -->|recruteur, offre d'un autre| Forbidden
    Ownership -->|etudiant, profil d'un autre| Forbidden
    Ownership -->|OK| Handler[Execution de la route]
```

`EnsureRole` (middleware) gère le filtrage large par rôle ; les vérifications fines (un recruteur ne modifie que ses propres offres, un étudiant ne voit que son propre profil) restent faites en ligne dans chaque route via `sr_offer_owner_matches()`.

## Classes principales

```mermaid
classDiagram
    User "1" --> "0..1" StudentProfile
    User "1" --> "0..1" RecruiterProfile
    RecruiterProfile "1" --> "*" Offer : proprietaire
    StudentProfile "1" --> "*" CvDocument
    StudentProfile "1" --> "*" Application
    Offer "1" --> "*" Application
    Application "1" --> "0..1" MatchScore

    class User {
        id
        public_id
        name
        email
        role : admin|recruiter|student
        password : hash bcrypt
    }

    class StudentProfile {
        public_id
        headline
        education
        experience_years
        skills JSON
        cv_text : texte normalise
        cv_metadata JSON : emails, telephones, competences detectees
    }

    class RecruiterProfile {
        public_id
        company_name
        position
        website
    }

    class Offer {
        public_id
        title
        company
        description
        required_skills JSON
        status : draft|published|closed
    }

    class Application {
        public_id
        status : submitted|shortlisted|interview|rejected
        applied_at
    }

    class MatchScore {
        score
        text_similarity
        semantic_coverage
        matched_skills JSON
        missing_skills JSON
    }
```

Les champs `password`, `experience_years` et `status` (offre) sont désormais réellement exploités : mot de passe vérifié à la connexion, expérience saisissable ou déduite du CV, statut d'offre pilotable par le recruteur.

## Séquence : inscription

```mermaid
sequenceDiagram
    participant U as Visiteur
    participant L as Laravel
    participant DB as MySQL

    U->>L: GET /register?role=etudiant|recruteur
    L-->>U: Formulaire (nom, email, mot de passe, champ metier)
    U->>L: POST /register
    L->>L: Valide (email unique, mot de passe >= 8 + confirmation)
    L->>DB: Cree User (mot de passe hache bcrypt)
    alt role = etudiant
        L->>DB: Cree StudentProfile (headline)
    else role = recruteur
        L->>DB: Cree RecruiterProfile (company_name)
    end
    L->>L: Ouvre la session (connexion automatique)
    L-->>U: Redirection vers le dashboard du role
```

## Séquence : connexion

```mermaid
sequenceDiagram
    participant U as Utilisateur
    participant L as Laravel
    participant DB as MySQL

    U->>L: POST /login (email, mot de passe)
    L->>DB: findUserByEmail(email)
    alt utilisateur introuvable ou mot de passe incorrect
        L-->>U: "Identifiants invalides" (throttle 10/min)
    else
        L->>L: password_verify(mot de passe, hash)
        L->>L: Construit la session (id, role, student_id)
        L-->>U: Redirection dashboard selon le role
    end
```

## Séquence : upload CV avec repli IA

```mermaid
sequenceDiagram
    participant E as Etudiant
    participant L as Laravel
    participant P as Microservice Python
    participant DB as MySQL

    E->>L: Envoie un fichier CV (PDF/TXT/MD)
    L->>P: GET /health
    alt service IA en ligne et fichier PDF
        L->>P: POST /parse-cv (multipart)
        P->>P: pdfminer extrait le texte reel (flux compresses geres)
        P-->>L: texte, emails, competences
    else IA hors-ligne ou texte insuffisant
        L->>L: Extraction locale par regex (repli)
        L->>L: extraction_unreliable = true si texte < 40 caracteres
    end
    L->>L: Detecte competences, formation, emails, telephones, annees d'experience
    L->>DB: Met a jour StudentProfile + CvDocument
    alt extraction non fiable
        L-->>E: Avertissement : completer le CV manuellement
    else
        L-->>E: Profil mis a jour
    end
```

Le microservice Python bascule lui-même sur un serveur de secours si Flask est absent ; ce mode dégradé refuse `/parse-cv` proprement (401/501) au lieu de planter sur un contenu binaire.

## Séquence : candidature en un clic

```mermaid
sequenceDiagram
    participant E as Etudiant
    participant L as Laravel
    participant P as Microservice Python
    participant DB as MySQL

    Note over E,L: Le profil (CV, competences) existe deja - plus de formulaire par offre
    E->>L: POST /offers/{offre}/apply
    L->>DB: Verifie student_id de la session
    L->>DB: Cree Application (statut submitted)
    L->>P: POST /match (texte offre, texte profil)
    P-->>L: score, competences trouvees / manquantes
    L->>DB: Sauvegarde MatchScore
    L-->>E: Candidature envoyee, statut visible sur la liste des offres
```

## Séquence : décision recruteur

```mermaid
sequenceDiagram
    participant R as Recruteur
    participant L as Laravel
    participant DB as MySQL

    R->>L: Consulte le classement d'une offre
    L->>L: Verifie que R est bien le proprietaire de l'offre
    L->>DB: Charge candidatures + scores
    L-->>R: Candidats classes par score
    R->>L: Accepter ou refuser
    L->>L: Verifie a nouveau la propriete de l'offre liee
    L->>DB: Met a jour Application.status
    L-->>R: Statut visible dans le classement
```

## Séquence : tri et filtres côté recruteur

```mermaid
sequenceDiagram
    participant R as Recruteur
    participant L as Laravel
    participant P as Microservice Python
    participant DB as MySQL

    R->>L: GET /offers?status=...&with_applications=...
    L->>DB: Charge les offres du recruteur
    L->>L: Filtre par statut et/ou presence de candidatures
    loop chaque offre filtree
        L->>P: POST /match (meilleur candidat pour cette offre)
        P-->>L: score du meilleur profil
    end
    L->>L: Trie les offres par score decroissant
    L-->>R: Liste triee, badges de statut, compteurs de filtre

    Note over R,L: La meme logique inversee (meilleure offre par etudiant)<br/>alimente la liste "etudiants recommandes"
```

## Déploiement

```mermaid
flowchart TB
    Browser[Navigateur] -->|HTTP| Laravel[Laravel via server.php ou public/]
    Laravel -->|SQL 3306 - requis pour l'authentification| MySQL[(MySQL)]
    Laravel -->|HTTP /match /parse-cv /health| AIBox

    subgraph AIBox[Microservice Python]
        direction TB
        Flask[Flask + waitress] -->|si dependances absentes| Fallback[Serveur stdlib de secours]
        Fallback -.->|parse-cv impossible en mode degrade| Info["Retourne 501 + message d'installation"]
    end

    Laravel -. env .-> EnvVar[AI_SERVICE_URL]
    Laravel -. lang/fr .-> I18n[Messages de validation en francais]
```

`pip install -r ai-service/requirements.txt` installe Flask, pdfminer.six et waitress ; sans ces dépendances, le service reste fonctionnel pour `/health` et `/match` mais dégrade proprement `/parse-cv` plutôt que de planter.
