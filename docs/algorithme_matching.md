# Algorithme de matching

## Objectif

Le moteur calcule un score de compatibilité entre une offre et un profil candidat. Le score n'est pas une simple recherche `LIKE`; il combine trois signaux:

- similarité TF-IDF + cosinus entre le texte de l'offre et le texte du CV;
- couverture des compétences requises;
- proximité sémantique via une taxonomie de compétences.

## Étapes

1. Normalisation du texte: minuscules, suppression des accents et caractères parasites.
2. Remplacement des synonymes par une compétence canonique: `J2EE`, `Java Enterprise`, `Spring Boot` -> `java`.
3. Suppression des stop words français/anglais.
4. Vectorisation TF-IDF sur deux documents: offre et CV.
5. Calcul de similarité cosinus.
6. Calcul du score final pondéré.

## Formule MVP

```text
score = 55% couverture_competences
      + 30% similarite_tfidf_cosinus
      + 10% couverture_semantique
      +  5% bonus_experience
```

Le microservice Python renvoie le même type de score que le fallback PHP. Cela permet de démontrer l'architecture distribuée sans rendre l'application inutilisable si Python est indisponible.

## Pourquoi TF-IDF plutôt qu'un LLM ?

- Confidentialité: les CV restent dans l'infrastructure du projet.
- Coût: aucun appel externe payant.
- Explicabilité: le jury peut comprendre la pondération et la similarité cosinus.
- Évolutivité: l'architecture permet de remplacer TF-IDF par BERT ou CamemBERT plus tard.
