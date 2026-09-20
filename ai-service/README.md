# Microservice IA Smart-Recruit

Service Python séparé du backend Laravel. Il expose:

- `GET /health`
- `POST /match`
- `POST /parse-cv`

Installation sur Python 3.6:

```bash
cd smart-recruit/ai-service
python3 -m venv venv
source venv/bin/activate
pip install -r requirements.txt
python app.py
```

Sans Flask installé, `app.py` démarre aussi un serveur HTTP minimal compatible avec `/health` et `/match`. Flask reste recommandé pour `/parse-cv`, car le parsing PDF multipart nécessite la gestion de fichiers uploadés.

Exemple:

```bash
curl -X POST http://127.0.0.1:8010/match \
  -H "Content-Type: application/json" \
  -d '{"offer_text":"Java Enterprise React SQL","cv_text":"J2EE Spring Boot ReactJS MySQL"}'
```

Le MVP utilise un score hybride: couverture des compétences, similarité cosinus et taxonomie sémantique. Pour le mémoire, l'évolution naturelle est d'ajouter une expérimentation comparative TF-IDF, Word2Vec, CamemBERT/BERT.
