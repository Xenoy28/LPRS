/* ---------- Variables ---------- */
:root {
    --fond: #000000; --texte: #e7e9ea; --texte-doux: #71767b; --bordure: #2f3336;
    --survol: rgba(231,233,234,.1); --surface: #16181c;
    --bleu: #1d9bf0; --bleu-fonce: #1a8cd8; --rouge: #f4212e; --vert: #00ba7c; --orange: #ffad1f;
    --police: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    --largeur-nav: 275px; --largeur-colonne: 600px; --largeur-droite: 350px;
}

/* ---------- Base ---------- */
*, *::before, *::after { box-sizing: border-box; }
html { color-scheme: dark; }
body { margin: 0; background: var(--fond); color: var(--texte); font-family: var(--police); font-size: 15px; line-height: 1.4; -webkit-font-smoothing: antialiased; }
a { color: var(--bleu); text-decoration: none; }
a:hover { text-decoration: underline; }
:focus-visible { outline: 2px solid var(--bleu); outline-offset: 2px; }
h1, h2, h3 { color: var(--texte); line-height: 1.25; }
h1 { font-size: 20px; font-weight: 800; margin: 0; }
h2 { font-size: 20px; font-weight: 800; margin: 28px 0 12px; }
h3 { font-size: 17px; font-weight: 700; margin: 0 0 6px; }
p { margin: 0 0 14px; }
ul, ol { padding-left: 22px; line-height: 1.7; }
hr { border: 0; border-top: 1px solid var(--bordure); margin: 20px 0; }
.maj, .meta, .petit { color: var(--texte-doux); font-size: 13px; }
.piege { position: absolute; left: -9999px; }

/* ---------- Mise en page 3 colonnes (site) ---------- */
.x-layout { display: flex; justify-content: center; min-height: 100vh; }

.x-nav { position: sticky; top: 0; align-self: flex-start; width: var(--largeur-nav); height: 100vh; display: flex; flex-direction: column; padding: 4px 12px 12px; overflow-y: auto; }
.x-logo { display: inline-flex; align-items: center; gap: 10px; align-self: flex-start; padding: 8px 12px; border-radius: 9999px; color: var(--texte); font-size: 22px; font-weight: 800; }
.x-logo:hover { background: var(--survol); text-decoration: none; }
.x-logo-pastille { display: grid; place-items: center; width: 32px; height: 32px; border-radius: 9px; background: var(--bleu); color: #fff; font-size: 18px; }
.x-menu { display: flex; flex-direction: column; margin-top: 4px; }
.x-lien { display: inline-flex; align-items: center; gap: 18px; align-self: flex-start; padding: 12px; border-radius: 9999px; color: var(--texte); font-size: 20px; line-height: 24px; position: relative; }
.x-lien:hover { background: var(--survol); text-decoration: none; }
.x-lien.actif { font-weight: 800; }
.x-lien .ico { flex: none; width: 26px; height: 26px; }
.x-lien.actif .ico { stroke-width: 2.6; }
.ico { fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

.x-compte { display: flex; align-items: center; gap: 12px; margin-top: auto; padding: 12px; border-radius: 9999px; color: var(--texte); }
.x-compte:hover { background: var(--survol); text-decoration: none; }
.x-avatar { flex: none; display: grid; place-items: center; width: 40px; height: 40px; border-radius: 50%; background: var(--surface); border: 1px solid var(--bordure); font-weight: 700; text-transform: uppercase; }
.x-compte-texte { min-width: 0; }
.x-compte-nom { display: block; font-weight: 700; line-height: 1.2; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.x-compte-role { display: block; color: var(--texte-doux); font-size: 13px; }

.x-col { flex: 0 1 var(--largeur-colonne); width: 100%; min-width: 0; min-height: 100vh; display: flex; flex-direction: column; border-left: 1px solid var(--bordure); border-right: 1px solid var(--bordure); }
.x-tete { position: sticky; top: 0; z-index: 5; padding: 14px 16px; background: rgba(0,0,0,.65); -webkit-backdrop-filter: blur(12px); backdrop-filter: blur(12px); border-bottom: 1px solid var(--bordure); }
.x-corps { flex: 1; padding: 16px; }
.fil { font-size: 13px; color: var(--texte-doux); margin-bottom: 16px; }
.fil a { color: var(--texte-doux); }

.x-droite { width: var(--largeur-droite); padding: 12px 0 12px 24px; position: sticky; top: 0; align-self: flex-start; height: 100vh; overflow-y: auto; }
.x-widget { background: var(--surface); border-radius: 16px; padding: 16px; margin-bottom: 16px; }
.x-widget h2 { margin: 0 0 8px; }
.x-widget p { color: var(--texte-doux); }
.x-widget ul { list-style: none; padding: 0; margin: 0; }
.x-widget li a { display: block; padding: 8px 0; color: var(--texte); font-weight: 700; }
.x-widget .btn { display: block; width: 100%; margin-top: 8px; }

.footer { padding: 16px; border-top: 1px solid var(--bordure); }
.x-droite .footer { padding: 0 16px; border: 0; }
.footer-liens { list-style: none; margin: 0; padding: 0; display: flex; flex-wrap: wrap; gap: 4px 12px; }
.footer-liens a { color: var(--texte-doux); font-size: 13px; }
.footer-copy { margin: 8px 0 0; font-size: 13px; color: var(--texte-doux); }
.x-pied-colonne { display: none; }

/* ---------- Cartes, blocs ---------- */
.grille { display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 12px; }
.bloc, .carte, .publication { background: var(--fond); border: 1px solid var(--bordure); border-radius: 16px; padding: 16px; margin-bottom: 12px; }
.bloc h2 { margin-top: 0; font-size: 17px; }
.bloc ul { margin: 0; padding-left: 18px; }
.carte h3 { margin: 0 0 4px; }
.carte h3 a { color: var(--texte); }
.carte:hover { background: rgba(255,255,255,.03); }
.publication .meta { margin-bottom: 8px; }
.hero { padding: 8px 0 20px; border-bottom: 1px solid var(--bordure); margin-bottom: 8px; }
.hero h2 { font-size: 31px; line-height: 1.2; margin: 0 0 12px; }
.hero p { font-size: 17px; }

/* ---------- Tableaux ---------- */
table { width: 100%; border-collapse: collapse; font-size: 15px; background: transparent; }
.x-corps table { display: block; max-width: 100%; overflow-x: auto; margin-bottom: 16px; }
table[border] { border: 0; }
th, td { text-align: left; padding: 12px 10px; border: 0; border-bottom: 1px solid var(--bordure); vertical-align: top; }
th { color: var(--texte-doux); font-size: 13px; font-weight: 700; white-space: nowrap; }
tr:hover td { background: rgba(255,255,255,.03); }
.defilement { overflow-x: auto; }

/* ---------- Formulaires ---------- */
label { font-weight: 400; }
label[for] { display: block; font-weight: 700; margin: 14px 0 6px; }
label > input[type=checkbox], label > input[type=radio] { margin-right: 8px; }
input:not([type=checkbox]):not([type=radio]):not([type=hidden]):not([type=submit]), select, textarea {
    display: block; width: 100%; margin-top: 6px; padding: 12px; background: var(--fond); color: var(--texte);
    border: 1px solid var(--bordure); border-radius: 6px; font: inherit;
}
textarea { min-height: 110px; resize: vertical; }
input::placeholder, textarea::placeholder { color: var(--texte-doux); }
input:not([type=checkbox]):not([type=radio]):focus, select:focus, textarea:focus { outline: none; border-color: var(--bleu); box-shadow: 0 0 0 1px var(--bleu); }
input[type=checkbox], input[type=radio] { accent-color: var(--bleu); width: 16px; height: 16px; vertical-align: middle; }
select option { background: var(--fond); color: var(--texte); }
fieldset { border: 1px solid var(--bordure); border-radius: 12px; padding: 12px 16px; margin: 0 0 16px; min-width: 0; }
legend { padding: 0 6px; color: var(--texte-doux); }
form[method=get], .filtres { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
form[method=get] > input:not([type=hidden]), form[method=get] > select, .filtres > input, .filtres > select { flex: 1 1 150px; width: auto; margin: 0; }
form.en-ligne { display: inline; }
.grille-form { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0 16px; }

/* ---------- Boutons ---------- */
button, .btn, .bouton, input[type=submit] { display: inline-block; min-height: 36px; padding: 8px 20px; border: 1px solid transparent; border-radius: 9999px; background: var(--bleu); color: #fff; font: inherit; font-weight: 700; line-height: 20px; text-align: center; cursor: pointer; transition: background .15s; }
button:hover, .btn:hover, .bouton:hover, input[type=submit]:hover { background: var(--bleu-fonce); text-decoration: none; }
button.gris, .bouton.gris, .btn.gris { background: transparent; border-color: #536471; color: #eff3f4; }
button.gris:hover, .bouton.gris:hover, .btn.gris:hover { background: var(--survol); }
button.vert { background: var(--vert); }
button.rouge { background: var(--rouge); }
button.rouge:hover { background: #d81c28; }

/* ---------- Messages, badges, pagination ---------- */
.erreur, .succes, .message { padding: 12px 16px; border-radius: 12px; border: 1px solid; margin-bottom: 16px; }
.erreur, .message.erreur { background: rgba(244,33,46,.1); border-color: rgba(244,33,46,.45); color: #ff7a83; }
.succes, .message.ok { background: rgba(0,186,124,.1); border-color: rgba(0,186,124,.45); color: #3ddba1; }
.badge { display: inline-block; padding: 2px 10px; border-radius: 9999px; background: rgba(29,155,240,.15); color: var(--bleu); font-size: 12px; font-weight: 700; white-space: nowrap; }
.badge.attente { background: rgba(255,173,31,.15); color: var(--orange); }
.badge.valide { background: rgba(0,186,124,.15); color: #3ddba1; }
.badge.refuse { background: rgba(244,33,46,.15); color: #ff7a83; }
.badge.neutre { background: rgba(113,118,123,.25); color: #a3a8ad; }
.pastille { display: inline-block; min-width: 20px; padding: 1px 6px; border-radius: 9999px; background: var(--bleu); color: #fff; font-size: 12px; font-weight: 700; line-height: 18px; text-align: center; }
.pagination { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 16px; }
.pagination a, .pagination strong { display: inline-block; padding: 6px 14px; border: 1px solid var(--bordure); border-radius: 9999px; color: var(--texte); }
.pagination strong { background: var(--bleu); border-color: var(--bleu); color: #fff; }

/* ---------- Administration ---------- */
.admin-entete { position: sticky; top: 0; z-index: 10; display: flex; flex-wrap: wrap; align-items: center; gap: 6px 16px; padding: 8px 20px; background: rgba(0,0,0,.75); -webkit-backdrop-filter: blur(12px); backdrop-filter: blur(12px); border-bottom: 1px solid var(--bordure); }
.admin-entete .logo { font-size: 18px; font-weight: 800; }
.admin-entete nav { display: flex; flex-wrap: wrap; gap: 2px; flex: 1; }
.admin-entete nav a, .admin-entete .quitter { padding: 8px 14px; border-radius: 9999px; color: var(--texte); }
.admin-entete nav a:hover, .admin-entete .quitter:hover { background: var(--survol); text-decoration: none; }
.admin-entete nav a.actif { font-weight: 800; background: var(--survol); }
.admin-entete .quitter, .admin-entete .utilisateur { color: var(--texte-doux); }
.admin-contenu { max-width: 1200px; margin: 0 auto; padding: 24px 16px 40px; }
.admin-contenu h1 { margin-bottom: 20px; font-size: 24px; }
.stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 20px; }
.stats .carte { margin: 0; text-align: center; }
.stats .nombre { display: block; font-size: 32px; font-weight: 800; }
.page-centree { display: flex; justify-content: center; padding: 40px 16px; }
.page-centree form { width: 100%; max-width: 400px; padding: 24px; border: 1px solid var(--bordure); border-radius: 16px; }
.page-centree form button { width: 100%; margin-top: 18px; }

/* ---------- Responsive ---------- */
@media (max-width: 1279px) {
    .x-nav { width: 88px; align-items: center; padding-left: 0; padding-right: 0; }
    .x-logo-texte, .x-lien-texte, .x-compte-texte { display: none; }
}
@media (max-width: 999px) {
    .x-droite { display: none; }
    .x-pied-colonne { display: block; }
}
@media (max-width: 699px) {
    body { padding-bottom: calc(54px + env(safe-area-inset-bottom, 0px)); }
    .x-layout { display: block; }
    .x-nav { position: fixed; top: auto; bottom: 0; left: 0; right: 0; z-index: 20; width: 100%; height: auto; flex-direction: row; padding: 0; background: rgba(0,0,0,.92); border-top: 1px solid var(--bordure); overflow-x: auto; overflow-y: hidden; }
    .x-logo, .x-compte { display: none; }
    .x-menu { flex-direction: row; justify-content: space-around; width: 100%; margin: 0; min-width: max-content; }
    .x-lien { align-self: center; padding: 14px 16px; }
    .x-col { min-height: 0; border-left: 0; border-right: 0; }
    .x-corps { padding: 12px; }
}