import math
import os
import re
import tempfile

try:
    from flask import Flask, jsonify, request
except ImportError:
    Flask = None
    request = None

    def jsonify(payload):
        return payload


class FallbackApp(object):
    def route(self, *args, **kwargs):
        def decorator(func):
            return func

        return decorator

    def run(self, host="127.0.0.1", port=8010):
        run_builtin_server(host, port)


app = Flask(__name__) if Flask else FallbackApp()


class LoggingMiddleware(object):
    """Trace chaque requête sur le terminal quand l'app tourne sous waitress
    (qui, contrairement au serveur de dev Flask, ne logge rien par défaut)."""

    def __init__(self, wsgi_app):
        self.wsgi_app = wsgi_app

    def __call__(self, environ, start_response):
        method = environ.get("REQUEST_METHOD", "?")
        path = environ.get("PATH_INFO", "?")
        client = environ.get("REMOTE_ADDR", "?")

        def logging_start_response(status, headers, exc_info=None):
            print('[ai-service] %s - "%s %s" %s' % (client, method, path, status), flush=True)
            return start_response(status, headers, exc_info)

        return self.wsgi_app(environ, logging_start_response)

SKILL_ALIASES = {
    "java": ["java", "j2ee", "jee", "java enterprise", "spring", "spring boot"],
    "php": ["php", "laravel", "symfony"],
    "python": ["python", "fastapi", "flask", "django"],
    "javascript": ["javascript", "js", "ecmascript"],
    "typescript": ["typescript", "ts"],
    "react": ["react", "reactjs", "react.js", "front-end react", "frontend react"],
    "vue": ["vue", "vuejs", "vue.js"],
    "frontend": ["frontend", "front end", "front-end", "ui", "interface utilisateur"],
    "backend": ["backend", "back end", "back-end", "api rest", "rest api"],
    "sql": ["sql", "mysql", "postgresql", "postgres", "oracle", "mariadb"],
    "devops": ["devops", "docker", "ci cd", "ci/cd", "gitlab ci", "github actions"],
    "machine learning": ["machine learning", "ml", "apprentissage automatique", "scikit-learn", "sklearn"],
    "nlp": ["nlp", "traitement du langage naturel", "spacy", "bert", "tf-idf", "tfidf"],
    "data analysis": ["data analysis", "analyse de données", "pandas", "numpy", "power bi"],
    "git": ["git", "github", "gitlab"],
    "agile": ["agile", "scrum", "kanban"],
}

SKILL_TAXONOMY = {
    "java": "backend",
    "php": "backend",
    "python": "backend",
    "javascript": "frontend",
    "typescript": "frontend",
    "react": "frontend",
    "vue": "frontend",
    "frontend": "frontend",
    "backend": "backend",
    "sql": "data",
    "devops": "infrastructure",
    "machine learning": "ai",
    "nlp": "ai",
    "data analysis": "data",
    "git": "tools",
    "agile": "methods",
}

STOP_WORDS = set(
    "a au aux avec ce ces dans de des du elle en et eux il je la le les leur lui ma "
    "mais me meme mes moi mon ne nos notre nous on ou par pas pour qu que qui sa se "
    "ses son sur ta te tes toi ton tu un une vos votre vous the and or of to in for with is are an".split()
)


def normalize(text):
    text = (text or "").lower()
    text = re.sub(r"[^a-z0-9+#.]+", " ", text)
    return re.sub(r"\s+", " ", text).strip()


def extract_skills(text):
    normalized = " " + normalize(text) + " "
    skills = []

    for canonical, aliases in SKILL_ALIASES.items():
        for alias in aliases:
            if " " + normalize(alias) + " " in normalized:
                skills.append(canonical)
                break

    return sorted(set(skills))


def tokens(text):
    text = normalize(text)

    for canonical, aliases in SKILL_ALIASES.items():
        for alias in aliases:
            text = text.replace(normalize(alias), canonical.replace(" ", "_"))

    return [token for token in text.split() if len(token) > 2 and token not in STOP_WORDS]


def cosine(a_tokens, b_tokens):
    a = tfidf_vector(a_tokens, [a_tokens, b_tokens])
    b = tfidf_vector(b_tokens, [a_tokens, b_tokens])
    terms = set(a.keys()) | set(b.keys())
    dot = sum(a.get(term, 0.0) * b.get(term, 0.0) for term in terms)
    norm_a = math.sqrt(sum(value * value for value in a.values()))
    norm_b = math.sqrt(sum(value * value for value in b.values()))

    if norm_a == 0 or norm_b == 0:
        return 0.0

    return dot / (norm_a * norm_b)


def tfidf_vector(doc_tokens, corpus):
    counts = {}

    for token in doc_tokens:
        counts[token] = counts.get(token, 0) + 1

    total = float(len(doc_tokens) or 1)
    documents = float(len(corpus) or 1)
    vector = {}

    for term, count in counts.items():
        document_frequency = sum(1 for document in corpus if term in document)
        term_frequency = float(count) / total
        inverse_document_frequency = math.log((1.0 + documents) / (1.0 + document_frequency)) + 1.0
        vector[term] = term_frequency * inverse_document_frequency

    return vector


def category_coverage(required, candidate):
    required_categories = set(SKILL_TAXONOMY.get(skill) for skill in required if SKILL_TAXONOMY.get(skill))
    candidate_categories = set(SKILL_TAXONOMY.get(skill) for skill in candidate if SKILL_TAXONOMY.get(skill))

    if not required_categories:
        return 0.0

    return float(len(required_categories & candidate_categories)) / float(len(required_categories))


def compute_match(offer_text, cv_text, required_skills=None, candidate_skills=None):
    required = sorted(set((required_skills or []) + extract_skills(offer_text)))
    candidate = sorted(set((candidate_skills or []) + extract_skills(cv_text)))
    matched = sorted(set(required) & set(candidate))
    missing = sorted(set(required) - set(candidate))
    skill_coverage = float(len(matched)) / float(len(required)) if required else 0.0
    text_similarity = cosine(tokens(offer_text), tokens(cv_text))
    semantic = category_coverage(required, candidate)
    score = int(round(100 * ((0.58 * skill_coverage) + (0.32 * text_similarity) + (0.10 * semantic))))
    score = max(0, min(100, score))

    return {
        "score": score,
        "matched_skills": matched,
        "missing_skills": missing,
        "text_similarity": int(round(text_similarity * 100)),
        "semantic_coverage": int(round(semantic * 100)),
        "algorithm": "tf-idf-cosine+semantic-skill-taxonomy",
        "source": "python",
    }


def extract_pdf_text(path):
    try:
        from pdfminer.high_level import extract_text

        return extract_text(path) or ""
    except Exception:
        pass

    try:
        import PyPDF2

        with open(path, "rb") as handle:
            reader = PyPDF2.PdfFileReader(handle)
            pages = []
            for index in range(reader.getNumPages()):
                pages.append(reader.getPage(index).extractText())
            return "\n".join(pages)
    except Exception:
        pass

    with open(path, "rb") as handle:
        return handle.read().decode("latin-1", errors="ignore")


@app.route("/health", methods=["GET"])
def health():
    return jsonify({
        "status": "ok",
        "message": "Le service d'analyse Python répond.",
        "service": "Python/Flask",
        "algorithm": "tf-idf-cosine+semantic-skill-taxonomy",
    })


@app.route("/match", methods=["POST"])
def match():
    payload = request.get_json(force=True) or {}
    result = compute_match(
        payload.get("offer_text", ""),
        payload.get("cv_text", ""),
        payload.get("required_skills") or [],
        payload.get("candidate_skills") or [],
    )

    return jsonify(result)


@app.route("/parse-cv", methods=["POST"])
def parse_cv():
    if "file" not in request.files:
        return jsonify({"error": "file field is required"}), 422

    uploaded = request.files["file"]
    suffix = os.path.splitext(uploaded.filename or "cv.pdf")[1] or ".pdf"
    fd, path = tempfile.mkstemp(suffix=suffix)
    os.close(fd)

    try:
        uploaded.save(path)
        text = extract_pdf_text(path)
        emails = sorted(set(re.findall(r"[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}", text, flags=re.I)))

        return jsonify({
            "text": text,
            "emails": emails,
            "skills": extract_skills(text),
        })
    finally:
        try:
            os.remove(path)
        except OSError:
            pass


def run_builtin_server(host, port):
    import json
    from http.server import BaseHTTPRequestHandler, HTTPServer

    class Handler(BaseHTTPRequestHandler):
        def send_json(self, payload, status=200):
            encoded = json.dumps(payload).encode("utf-8")
            self.send_response(status)
            self.send_header("Content-Type", "application/json; charset=utf-8")
            self.send_header("Content-Length", str(len(encoded)))
            self.end_headers()
            self.wfile.write(encoded)

        def do_GET(self):
            if self.path == "/health":
                self.send_json({
                    "status": "ok",
                    "message": "Le service d'analyse Python répond.",
                    "service": "Python/standard-library",
                    "algorithm": "tf-idf-cosine+semantic-skill-taxonomy",
                })
                return

            self.send_json({"error": "not found"}, 404)

        def do_POST(self):
            content_length = int(self.headers.get("Content-Length", "0"))

            if self.path == "/parse-cv":
                # Upload multipart binaire (PDF) : le serveur de secours ne sait pas le
                # parser (seul Flask le peut) — on vide le flux sans le décoder en UTF-8,
                # sinon un PDF/octet binaire fait planter le serveur (UnicodeDecodeError).
                if content_length:
                    self.rfile.read(content_length)
                self.send_json({
                    "error": "Flask est requis pour le parsing multipart PDF. "
                             "Installez les dépendances : pip install -r ai-service/requirements.txt",
                }, 501)
                return

            raw = self.rfile.read(content_length).decode("utf-8", errors="replace") if content_length else "{}"

            try:
                payload = json.loads(raw or "{}")
            except ValueError:
                payload = {}

            if self.path == "/match":
                self.send_json(compute_match(
                    payload.get("offer_text", ""),
                    payload.get("cv_text", ""),
                    payload.get("required_skills") or [],
                    payload.get("candidate_skills") or [],
                ))
                return

            self.send_json({"error": "not found"}, 404)

        def log_message(self, format, *args):
            print("[ai-service] %s - %s" % (self.address_string(), format % args))

    HTTPServer((host, port), Handler).serve_forever()


if __name__ == "__main__":
    host = os.environ.get("AI_HOST", "127.0.0.1")
    port = int(os.environ.get("AI_PORT", "8010"))

    if Flask:
        try:
            from waitress import serve

            print(" * Serving Flask app 'app' with waitress (production WSGI server)", flush=True)
            print(" * Running on http://%s:%s" % (host, port), flush=True)
            serve(LoggingMiddleware(app), host=host, port=port, ident="ai-service")
        except ImportError:
            app.run(host=host, port=port)
    else:
        app.run(host=host, port=port)
