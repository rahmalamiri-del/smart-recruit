"""Rejoue les sondes du memoire sur le code local, sans reseau ni base de donnees.
Usage depuis le projet : python output/latex/smartrecruit-memoire/evidence/probes.py
Le JSON contient des exemples synthetiques et des empreintes du code analyse.
"""
from pathlib import Path
import datetime
import hashlib
import io
import json
import logging
import platform
import runpy
import subprocess
import sys

sys.dont_write_bytecode = True
HERE = Path(__file__).resolve().parent
ROOT = HERE.parents[3]
php = subprocess.run(['php', str(HERE/'probes.php')], cwd=ROOT, capture_output=True, check=True)
results = json.loads(php.stdout.decode('utf-8-sig'))
module = runpy.run_path(str(ROOT/'ai-service'/'app.py'), run_name='smartrecruit_probe')
for case in results['cases']:
    offer = (case['offer_text']+' '+' '.join(case['required'])).strip()
    cv = (case['cv_text']+' '+' '.join(case['candidate'])).strip()
    case['python'] = module['compute_match'](offer,cv,case['required'],case['candidate'])
results['python_version'] = platform.python_version()
results['date_utc'] = datetime.datetime.now(datetime.timezone.utc).isoformat()
results['scope'] = 'Synthetic local probes, no database, no external HTTP calls'
results['python_punctuation_probe'] = module['extract_skills']('Python.')
results['python_token_probe'] = module['tokens']('React.js')
if module['Flask'] is not None:
    logging.disable(logging.CRITICAL)
    client = module['app'].test_client()
    fake_pdf = (b'%PDF-1.4\n'+b'garbage unparseable object stream '*8)[:173]
    response = client.post('/parse-cv', data={'file':(io.BytesIO(fake_pdf),'synthetic-invalid.pdf')}, content_type='multipart/form-data')
    results['invalid_pdf'] = {'input_bytes':len(fake_pdf),'http_status':response.status_code,
                              'text_length':len((response.get_json() or {}).get('text',''))}
    results['invalid_payloads'] = []
    for payload in [{'offer_text':5},{'required_skills':'java'},['java']]:
        response = client.post('/match',json=payload)
        results['invalid_payloads'].append({'input':payload,'http_status':response.status_code})
else:
    results['flask_checks'] = 'not executed: Flask unavailable'
paths = ['app/Support/SemanticMatcher.php','app/Support/CvParser.php','app/Support/AiClient.php',
         'config/smart_recruit.php','ai-service/app.py','routes/web.php','app/Support/SqlStore.php',
         'app/Support/DemoStore.php','server.php']
results['sha256'] = {p:hashlib.sha256((ROOT/p).read_bytes()).hexdigest() for p in paths}
(HERE/'results.json').write_text(json.dumps(results,ensure_ascii=False,indent=2)+'\n',encoding='utf-8')
for case in results['cases']:
    print(case['id'], 'PHP',case['php']['score'],'Python',case['python']['score'])
print('Age probe:', results['age_probe']['experience_years'])
print('Invalid PDF:', results.get('invalid_pdf'))
print('Saved:',HERE/'results.json')
