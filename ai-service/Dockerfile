FROM python:3.9-slim

WORKDIR /app

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY app.py .

ENV AI_HOST=0.0.0.0
ENV AI_PORT=8010

EXPOSE 8010

CMD ["python", "app.py"]
