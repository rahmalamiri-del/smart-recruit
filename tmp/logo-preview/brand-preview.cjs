const fs = require('node:fs');
const path = require('node:path');
const sharp = require('C:/Users/hamma/.cache/codex-runtimes/codex-primary-runtime/dependencies/node/node_modules/sharp');
const root = path.resolve(__dirname, '../..');
const brand = path.join(root, 'assets/brand');
const inline = name => 'data:image/svg+xml;base64,' + fs.readFileSync(path.join(brand, name)).toString('base64');
const mark = inline('smartrecruit-mark.svg');
const regular = inline('smartrecruit-logo.svg');
const light = inline('smartrecruit-logo-light.svg');
const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="1440" height="850" viewBox="0 0 1440 850">
<rect width="1440" height="850" fill="#F6F7F4"/>
<g font-family="Segoe UI, Arial, sans-serif">
<text x="72" y="78" fill="#126B5B" font-size="16" font-weight="600" letter-spacing="2">SMARTRECRUIT</text>
<text x="72" y="139" fill="#17201B" font-size="42" font-weight="600">Le lien vers votre avenir.</text>
<text x="72" y="182" fill="#68736F" font-size="21">Deux maillons, une rencontre entre étudiants et entreprises.</text>
<rect x="72" y="230" width="636" height="326" rx="24" fill="white"/>
<rect x="732" y="230" width="636" height="326" rx="24" fill="#102E27"/>
<text x="104" y="279" fill="#68736F" font-size="14" letter-spacing="1.4">SUR FOND CLAIR</text>
<text x="764" y="279" fill="#B9D2C9" font-size="14" letter-spacing="1.4">SUR FOND SOMBRE</text>
<image x="132" y="352" width="516" height="121" href="${regular}"/>
<image x="792" y="352" width="516" height="121" href="${light}"/>
<text x="72" y="617" fill="#68736F" font-size="14" letter-spacing="1.4">SYMBOLE &amp; ICÔNE D’ONGLET</text>
<image x="72" y="652" width="80" height="80" href="${mark}"/>
<image x="184" y="674" width="40" height="40" href="${mark}"/>
<image x="256" y="682" width="24" height="24" href="${mark}"/>
<image x="312" y="686" width="16" height="16" href="${mark}"/>
<text x="732" y="617" fill="#68736F" font-size="14" letter-spacing="1.4">COULEURS DE L’APPLICATION</text>
<circle cx="750" cy="686" r="18" fill="#126B5B"/>
<text x="784" y="692" fill="#17201B" font-size="18">Vert profond</text>
<circle cx="1030" cy="686" r="18" fill="#A8E6CE"/>
<text x="1064" y="692" fill="#17201B" font-size="18">Vert menthe</text>
<path d="M72 774H1368" stroke="#DFE6DF"/>
<text x="72" y="813" fill="#68736F" font-size="16">SmartRecruit · Étudiants &amp; entreprises</text>
</g></svg>`;
(async () => {
  await sharp(Buffer.from(svg)).png().toFile(path.join(root, 'output/branding/smartrecruit-logo-preview.png'));
  await sharp(path.join(brand, 'smartrecruit-mark.svg')).resize(256, 256).png().toFile(path.join(root, 'output/branding/smartrecruit-icon.png'));
  console.log('Brand preview and reusable PNG exported.');
})();
