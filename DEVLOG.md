# Journal de développement — CEREDIS WordPress

Ce journal consigne les audits et changements réalisés pendant cette session.
Il distingue les modifications effectuées ici des changements locaux qui
étaient déjà présents dans le dépôt. Il ne remplace pas les rapports d’audit
ni les instructions propres à chaque lot.

## 2026-10-11 — Audit préalable, phase 0

### Périmètre

- Audit en lecture seule du thème et des diagnostics consignés dans
  `docs/rapport_audit.md`.
- Branche active vérifiée : `design/visual-system-v1`.
- État Git, différences non indexées et différences indexées examinés.
- Arborescence et sources du thème lues, puis page locale consultée à
  `http://ceredis.test/`.
- Aucun fichier ni aucune donnée n’a été modifié pendant cette phase.

### Constats principaux

- Le thème définit `contentSize` à 1200 px et `wideSize` à 1320 px. Les
  sections utilisent des fonds `alignfull` et des contenus `alignwide`.
- La page d’accueil a une disposition en flux et le style global définit un
  `blockGap` de 1,5 rem, susceptible de créer un espace entre sections.
- Le padding vertical du Hero était déclaré dans le pattern et son CSS
  déclarait séparément un padding supérieur différent.
- Les presets `hero`, `h2` et `h3` existaient dans `theme.json`; le H1 Hero
  porte `has-hero-font-size`, mais le CSS redéfinissait sa taille.
- Les cartes de l’écosystème contiennent des libellés décoratifs, non des
  liens.
- Le pattern Actualités contient une condition PHP qui peut interrompre son
  rendu sans article publié. La section était absente de la réponse HTTP
  inspectée. Le nombre d’articles n’a pas été confirmé par WP-CLI.
- Les images sous `assets/images/` existaient déjà comme fichiers non suivis.
  Pillow a confirmé leurs dimensions et la présence d’un canal alpha.
- La réponse HTML contenait une seule référence à `style.css` du thème.
- Plusieurs affirmations du rapport antérieur ne correspondaient pas au code
  présent ou ne pouvaient pas être confirmées par les vérifications réalisées.

## 2026-10-11 — Lot 1 : gouttières et rythme vertical

### Changements réalisés

Modifications apportées à des fichiers qui comportaient déjà des changements
locaux :

- `wp-content/themes/ceredis/style.css`
  - ajout d’une gouttière fluide `--ceredis-gutter`;
  - remplacement du padding horizontal mobile générique appliqué aux groupes
    et colonnes par des règles ciblées sur les conteneurs de contenu;
  - harmonisation de la largeur et des bords des conteneurs du header, du Hero,
    des sections et du footer;
  - suppression de la marge de début des sections CEREDIS directement placées
    dans `<main>`, sans supprimer le `blockGap` global ni les espacements
    internes;
  - harmonisation du padding vertical du Hero dans le CSS.
- `wp-content/themes/ceredis/patterns/hero-home.php`
  - retrait du padding vertical déclaré dans le commentaire du bloc afin que
    le CSS soit la source de vérité.

`wideSize` est resté à 1320 px. La largeur maximale des conteneurs visés est
gérée par les règles CSS ciblées; aucun changement global de largeur n’a été
introduit dans `theme.json`.

### Vérifications

- `git diff --check`, validation JSON et `php -l` du pattern Hero : succès.
- Une mesure exploratoire avec surcharge temporaire a d’abord montré les
  bords et espacements attendus; elle n’a pas été présentée comme mesure du
  CSS réellement servi.
- Après synchronisation locale par le porteur du projet, la feuille servie
  contenait `--ceredis-gutter`, la règle d’espacement des sections et le
  padding harmonisé du Hero.
- Mesures navigateur sans surcharge aux largeurs 1440, 1258, 768 et 390 px :
  les six repères (logo, texte Hero, titre Écosystème, première carte, texte
  Mission et texte Footer) partageaient le même bord gauche, respectivement
  112,5 px, 48 px, 30,72 px et 20 px.
- Les sections consécutives mesurées avaient un écart externe de 0 px. Le
  padding supérieur et inférieur calculé du Hero était égal à chaque largeur.
- `scrollWidth` était égal à `clientWidth` aux quatre largeurs. Aucun
  débordement horizontal n’a été détecté.

Ces mesures valident les critères géométriques testés dans le navigateur,
sans constituer une revue visuelle complète dans Firefox.

## 2026-10-11 — Lot 2 : typographie et finition du Hero

### Changements réalisés

Modifications apportées aux fichiers de thème déjà modifiés localement :

- `wp-content/themes/ceredis/theme.json`
  - ajustement du preset `hero` à
    `clamp(2.5rem, 4.5vw, 4.5rem)`;
  - raccordement des éléments `h2` et `h3` à leurs presets de taille existants
    (`var:preset|font-size|h2` et `var:preset|font-size|h3`).
- `wp-content/themes/ceredis/style.css`
  - suppression des déclarations de taille du H1 qui remplaçaient le preset
    `hero` dans la règle principale et les breakpoints;
  - conservation des autres propriétés du titre et de sa règle responsive de
    largeur.

Le padding du Hero, les gouttières, le rythme externe, les couleurs, les images,
les cartes, la navigation et le balisage de `hero-home.php` n’ont pas été
modifiés dans ce lot. `hero-home.php` a été examiné mais n’a pas nécessité de
changement.

### Validations et état restant

- `git diff --check` : succès.
- `python3 -m json.tool wp-content/themes/ceredis/theme.json` : JSON valide.
- `php -l wp-content/themes/ceredis/patterns/hero-home.php` : succès.
- Au dernier contrôle, le CSS local et celui servi par `ceredis.test` ne
  correspondaient pas : le fichier servi contenait encore les anciennes
  déclarations de taille du H1. Les mesures navigateur disponibles aux quatre
  largeurs décrivent donc l’ancienne feuille servie, et non le résultat final
  des changements du lot 2.
- Après synchronisation par le porteur du projet, le fichier CSS servi est
  identique au fichier local. Les anciennes tailles H1 dans les règles CSS
  sont absentes et le preset `hero` est rendu par WordPress.
- La vérification a révélé que WordPress normalisait les slugs numériques
  `h2` et `h3` en noms de variables CSS `h-2` et `h-3`, tandis que les
  références dans les styles des éléments demandaient `h2` et `h3`. Les
  valeurs calculées des titres retombaient donc à 16 px. Les presets ont été
  renommés `heading-2` et `heading-3` et les références correspondantes
  mises à jour dans `theme.json`.
- Le dernier CSS servi reste identique au CSS local, mais le HTML et les styles
  globaux WordPress du navigateur exposent encore les anciens presets `h-2`
  et `h-3`. La correction des slugs a été faite localement après la
  synchronisation/build communiqué par le porteur du projet; elle n’est donc
  pas encore visible dans l’environnement testé.
- Le H1 final utilise le preset `hero` et a été mesuré à trois lignes maximum
  aux quatre largeurs. Les mesures H2/H3 observées correspondent encore aux
  anciennes références non résolues et ne valident pas le correctif local.
- Les mesures typographiques finales H2/H3 restent à confirmer après
  synchronisation du `theme.json` corrigé; il faudra aussi refaire le contrôle
  visuel complet sur cette version.

### Complément — H1, alignement et composition desktop

- Comparaison navigateur des éléments Hero : le H1 était centré par la règle
  Gutenberg de conteneur contraint, combinée à `max-width: 14ch` dans le CSS.
  À 1258 px, son bord commençait 64,75 px après les autres éléments; à 1440 px,
  44,77 px après.
- Le texte descriptif était aussi centré dans les vues tablette, car sa limite
  de largeur `56ch` se combinait à la marge automatique Gutenberg. Une marge
  inline nulle a été ajoutée sur ce paragraphe pour aligner son bord avec le
  sur-titre et les boutons, sans retirer sa limite de longueur de ligne.
- Les réglages ont été appliqués à `patterns/hero-home.php` (colonnes 67/33)
  et à `style.css` (largeur de contenu du Hero, H1 non contraint et paragraphe
  descriptif aligné). Après synchronisation locale autorisée par le porteur
  du projet, le navigateur a chargé la règle H1 `max-width: none`; aucune
  surcharge CSS n’a été utilisée pour les mesures finales.
- Mesures finales à 1440, 1258, 768 et 390 px : le bord gauche du sur-titre,
  du H1, du descriptif et des boutons est identique, respectivement 112,5,
  48, 30,72 et 20 px. Le H1 est en deux lignes aux trois premières largeurs
  et en trois lignes à 390 px.
- Tailles calculées H1 : 64,8 / 56,61 / 40 / 40 px; H2 de l’écosystème :
  48 / 44,03 / 32 / 32 px; H3 représentatif : 24 / 24 / 20 / 20 px.
  Le descriptif du Hero mesure 20 / 19,63 / 16,69 / 14,42 px.
- `scrollWidth` et `clientWidth` sont égaux aux quatre largeurs (1425, 1243,
  753 et 375 px); aucun débordement horizontal n’a été détecté. Les paddings
  verticaux du Hero restent symétriques et les écarts externes entre les
  sections mesurées restent à 0 px.
- Les presets servis par WordPress sont `hero`,
  `heading-2` et `heading-3`, avec les références H2/H3 attendues.
- `git diff --check`, `python3 -m json.tool wp-content/themes/ceredis/theme.json`
  et `php -l wp-content/themes/ceredis/patterns/hero-home.php` passent.
- Aucune commande Docker n’a été lancée dans cette dernière vérification
  (la synchronisation locale a été réalisée précédemment après autorisation).
  Aucun commit ni déploiement n’a été effectué.

## 2026-10-20 — Incident 503 Service Unavailable : port 8080 non publié

### Symptôme constaté

Le 20 octobre 2026, le site local WordPress, normalement accessible à
`http://ceredis.test`, a soudainement renvoyé une erreur :

```
503 Service Unavailable
```

Apache fonctionnait correctement et recevait les requêtes adressées à
`ceredis.test`. Son virtual host continuait à transmettre les requêtes à
`http://127.0.0.1:8080/`. Cependant, aucune application n’était accessible sur
le port 8080 de l’hôte.

### Diagnostic technique

**État du conteneur WordPress :**
- Conteneur existant : `ceredis-wordpress-wordpress-1`
- Statut : démarré et déclaré healthy
- Inspection Docker : `Ports={}`
- Fichier Compose détecté dans les métadonnées : `docker-compose.yml` uniquement

**Configuration Compose fusionnée attendue :**
L’examen des deux fichiers de configuration (`docker-compose.yml` et
`docker-compose.dev.yml`) révèle que la publication du port est bien prévue :
```yaml
ports:
  - "8080:80"
```

**Décalage identifié :**
Les métadonnées du conteneur indiquaient le seul fichier `docker-compose.yml`
dans `com.docker.compose.project.config_files`, tandis que la configuration
Compose fusionnée des deux fichiers prévoyait bien la publication du port
8080. Le conteneur ne correspondait donc pas à la configuration de
développement attendue.

### Cause immédiate identifiée

La cause immédiate de l’erreur 503 est établie : **le conteneur WordPress
ne publiait pas le port 8080 attendu par la configuration Apache**, alors que le
fichier `docker-compose.dev.yml` prévoit cette publication.

Les métadonnées du conteneur suggèrent que celui-ci a pu être créé ou recréé
sans application du fichier de développement. Cette dernière explication reste
**une hypothèse technique** : les éléments disponibles ne permettent pas
d’affirmer avec certitude quelle commande a créé le conteneur incorrect, ni
quel outil ou intervenant a exécuté cette commande.

### Éléments restant incertains concernant l’origine historique

- La responsabilité éventuelle d’un outil ou d’un intervenant **n’a pas été
  établie**.
- Il n’a pas été déterminé si l’incident résultait d’une commande manuelle
  (ex. : `docker compose up` sans `-f docker-compose.dev.yml`), d’un outil
  d’orchestration automatique ou d’une autre intervention.
- Aucune preuve de corruption de la base de données MySQL ou de panne du
  service n’a été relevée.

### Correction appliquée

Le site a été réparé en exécutant la commande de référence pour recréer
l’application WordPress avec la configuration de développement attendue :

```bash
cd ~/CEREDIS/repos/ceredis-wordpress

docker compose \
  -f docker-compose.yml \
  -f docker-compose.dev.yml \
  up -d --no-deps wordpress
```

Cette opération a recréé le service WordPress **sans demander la suppression
des volumes persistants ni la recréation de la base MySQL**.

### Résultats des vérifications

**1. Vérification du port du conteneur :**
```bash
docker port ceredis-wordpress-wordpress-1
```
Résultat :
```
80/tcp -> 0.0.0.0:8080
80/tcp -> [::]:8080
```

**2. Vérification HTTP directe :**
```bash
curl -sS -o /dev/null \
  -w 'HTTP %{http_code}\n' \
  http://127.0.0.1:8080/
```
Résultat :
```
HTTP 302
```

**3. Vérification HTTP via Apache :**
```bash
curl -sS -o /dev/null \
  -w 'HTTP %{http_code}\n' \
  -H 'Host: ceredis.test' \
  http://127.0.0.1/
```
Résultat :
```
HTTP 200
```

**4. Vérification visuelle dans le navigateur :**
- La page d’accueil s’affiche.
- Les images sont chargées.
- La navigation fonctionne.
- Les liens et les boutons principaux sont visibles.
- L’interface d’administration WordPress est accessible.

L’incident est donc **résolu**.

### Règles de prévention

**Règle impérative pour toutes les opérations Docker locales :**
À partir de maintenant, l’environnement WordPress local CEREDIS doit être
piloté en utilisant **explicitement les deux fichiers Compose** :
- `docker-compose.yml`
- `docker-compose.dev.yml`

Ne lancer **pas** une commande de démarrage ou de recréation du projet en
supposant que Docker appliquera automatiquement les paramètres du fichier de
développement.

#### Commande de référence pour démarrer ou actualiser l’ensemble du projet :
```bash
cd ~/CEREDIS/repos/ceredis-wordpress

docker compose \
  -f docker-compose.yml \
  -f docker-compose.dev.yml \
  up -d
```

#### Commande de référence pour démarrer ou recréer uniquement WordPress :
```bash
cd ~/CEREDIS/repos/ceredis-wordpress

docker compose \
  -f docker-compose.yml \
  -f docker-compose.dev.yml \
  up -d --no-deps wordpress
```

Cette commande évite de demander à Compose de démarrer ou de recréer les
dépendances du service. Elle ne doit toutefois pas être interprétée comme une
garantie absolue d’absence d’interruption : si le conteneur WordPress doit
être recréé, une brève indisponibilité du site peut survenir.

### Commandes Docker de référence

Pour examiner l’état du projet :
```bash
docker compose \
  -f docker-compose.yml \
  -f docker-compose.dev.yml \
  ps
```

Pour vérifier la publication du port :
```bash
docker port ceredis-wordpress-wordpress-1
```

Pour vérifier la configuration effective des ports :
```bash
docker compose \
  -f docker-compose.yml \
  -f docker-compose.dev.yml \
  config --format json
```

**Précautions de sécurité :** la sortie complète de `docker compose config`
peut contenir des mots de passe, des clés ou d’autres variables sensibles.
Ne copier **pas** ces valeurs dans un rapport, un journal ou une conversation.

Pour vérifier la réponse HTTP directe :
```bash
curl -sS -o /dev/null \
  -w 'HTTP %{http_code}\n' \
  http://127.0.0.1:8080/
```

Pour vérifier la réponse HTTP via Apache :
```bash
curl -sS -o /dev/null \
  -w 'HTTP %{http_code}\n' \
  -H 'Host: ceredis.test' \
  http://127.0.0.1/
```

### Opérations interdites sans justification explicite

Ne lancer **jamais**, dans le seul but de résoudre un problème de port ou de
connectivité :

```bash
docker compose down -v
```

Ne supprimer **pas** les volumes persistants WordPress ou MySQL.
Ne supprimer **pas** la base de données et ne recréer **pas** les données du
projet. Ne réinitialiser **pas** le dépôt Git. Ne changer **pas** de branche
et ne supprimer **pas** les modifications locales pour tenter de réparer un
problème de connectivité. Ne modifier **pas** le virtual host Apache ni
les fichiers Compose sans avoir préalablement établi que cette modification
est nécessaire.

Si un incident se reproduit, commencer par un diagnostic en lecture seule,
comparer la configuration Compose fusionnée à la configuration effective du
conteneur et identifier l’écart avant d’appliquer une correction.

### Préservation du travail de conception du portail

Le dépôt contient des modifications locales du thème CEREDIS et de nombreux
documents de travail non encore suivis par Git. Par conséquent :
- ne supprimer aucun fichier local ;
- ne réinitialiser aucun fichier modifié ;
- ne supprimer aucun document non suivi ;
- ne réaliser aucun commit sans autorisation ;
- ne changer pas de branche ;
- ne lancer aucune opération Git destructive.

---

## État général de la session

- Branche de travail vérifiée : `design/visual-system-v1`.
- Les changements locaux et fichiers non suivis préexistants ont été préservés.
- Aucun commit n’a été créé et aucun déploiement n’a été effectué.
- Aucune base de données, configuration Docker, instance de staging ou
  production n’a été modifiée par l’assistant.
- Le lot 2 a été mesuré après synchronisation locale, sans surcharge CSS, aux
  largeurs 1440, 1258, 768 et 390 px. Le contrôle visuel manuel complémentaire
  dans Firefox n’est pas consigné comme effectué.

## 2026-10-11 — Correctif responsive navigation mobile

### Diagnostic et correction locale

- Branche vérifiée : `design/visual-system-v1`. Les changements locaux
  existants, y compris les fichiers non suivis, ont été conservés.
- `parts/header.html` utilise déjà le bloc Navigation Gutenberg avec
  `overlayMenu: "mobile"`. Le rendu natif fournit le bouton et le menu
  superposé sur téléphone; aucun JavaScript personnalisé n’est nécessaire.
- Le CSS sous 781 px faisait passer le header en retour à la ligne et
  attribuait `flex: 1 1 100%` à la navigation. Sous 480 px, la règle de
  `flex-basis: 100%` maintenait le bouton de menu sur une rangée distincte,
  sous le logo.
- Dans `wp-content/themes/ceredis/style.css`, le breakpoint existant sous
  480 px a été corrigé : le conteneur du header conserve une seule rangée et
  le bloc Navigation ne prend plus toute la largeur. La largeur de logo,
  les gouttières, le rythme vertical et les presets typographiques restent
  inchangés.
- À 768 px, WordPress n’affiche pas son bouton mobile; les six liens du menu
  restent visibles horizontalement sur la rangée sous le logo. À 390 et
  320 px, le bouton natif était visible mais se trouvait sous le logo avant
  correction. Cette correction de positionnement reste à confirmer après
  synchronisation de la feuille locale.

### Interactions et contrôle du Hero

- Avant la correction CSS locale, le menu natif a été testé dans le navigateur
  à 390 px : ouverture, fermeture par le bouton, accès aux six liens, puis
  sélection de « À propos » qui mène à `/#mission` et referme le menu.
- À 320 px, l’ouverture expose les six liens et la touche Échap referme le
  menu en rendant le focus au bouton d’ouverture.
- Le Hero à 320 px ne montre pas de défaut de débordement ou de contenu coupé :
  H1 à 40 px sur 3 lignes, boutons de 265 px de large, illustration chargée
  de 265 × 176,66 px, et 40 px entre les boutons et l’illustration. Les
  éléments examinés restent dans le conteneur Hero. `scrollWidth` et
  `clientWidth` valent tous deux 305 px.
- Mesures navigateur de la version alors servie : aux largeurs 1440, 768,
  390 et 320 px, `scrollWidth` égale `clientWidth` (1425, 753, 375 et 305 px).
  Le H1 est sur 2 lignes à 1440 et 768 px, 3 lignes à 390 et 320 px. Ces
  mesures décrivent l’état servi avant synchronisation du correctif CSS.
- Le navigateur retourne encore l’ancienne feuille `style.css?ver=0.2.0`
  (16 969 octets) et ne contient pas les deux règles du correctif. Les
  vérifications d’interaction et de géométrie post-correction sont donc en
  attente d’une synchronisation locale autorisée; aucune opération Docker
  n’a été lancée.
- `theme.json` et `patterns/hero-home.php` n’ont pas été modifiés pour ce
  correctif. Aucun commit ni déploiement n’a été effectué.

## 2026-10-11 — Lot 3A : logo, médias et cartes

### Audit

- Branche active vérifiée : `design/visual-system-v1`. `git status`,
  `git diff --check`, les statistiques de différences non indexées et
  indexées, ainsi que les différences existantes des fichiers ciblés ont été
  examinés. Les changements préexistants ont été préservés.
- Le logo du header est servi depuis
  `/wp-content/uploads/2026/10/CEREDIS.png`. Le fichier local de même nom est
  un PNG RGBA de 2100 × 1500 px (107 520 octets); le fichier servi a renvoyé
  HTTP 200 avec les mêmes dimensions et le même poids. Les pixels visibles
  occupent le rectangle (191, 576)–(1892, 977), laissant de grandes marges
  transparentes, surtout au-dessus et en dessous. Dans son cadre d’en-tête,
  l’image est affichée à 300 × 110 px sur ordinateur, 240 × 90 px à 768 px,
  et 210 × 80 px à 390 et 320 px.
- Le Hero charge `hero-illustration-1.webp`, WebP RGBA de 1536 × 1024 px
  (environ 148 Kio); ses attributs HTML précisent 1536 × 1024,
  `fetchpriority="high"` et `decoding="async"`. L’image est chargée et garde
  son ratio 3:2 aux quatre largeurs mesurées. Aucune image n’est utilisée dans
  les trois cartes.
- Les cartes Programmes, Ressources et Plateformes numériques ne comportent
  pas de liens. Leurs étiquettes sont des paragraphes décoratifs. Les hauteurs
  varient avec les retours à la ligne du contenu; les espacements internes,
  le padding, les bords et les étiquettes sont cohérents, sans défaut mesuré
  justifiant une modification.

### Changement et mesures

- Seule la règle existante `.ceredis-header .wp-block-image img` dans
  `style.css` a été modifiée : `object-fit` passe de `contain` à `cover`,
  conservant `object-position: left center`. Le cadrage remplit mieux le
  cadre sans modifier l’image source ni déformer son ratio; les dimensions
  visibles calculées du contenu du logo passent approximativement de
  125 × 30 px à 243 × 57 px sur ordinateur et de 91 × 21 px à 170 × 40 px sur
  mobile. Les marges internes transparentes du PNG motivent ce changement.
- Mesures navigateur réalisées avant cette modification locale, aux largeurs
  1440, 768, 390 et 320 px : `scrollWidth` égale `clientWidth` (1425, 753,
  375 et 305 px); le H1 compte respectivement 2, 2, 3 et 3 lignes. La navigation
  affiche les six liens à 1440 et 768 px et le bouton de menu à 390 et 320 px.
- Dimensions de l’illustration du Hero : 377 × 251,33 px à 1440,
  576 × 384 px à 768, 335 × 223,33 px à 390 et 265 × 176,66 px à 320.
  Elle reste dans le conteneur. Les trois cartes ont des largeurs communes
  (384 px en rangée à 1440, puis 691,55 / 335 / 265 px empilées) et un écart
  de grille de 24 px; les hauteurs s’adaptent au texte sans recouvrement.
- Le navigateur local sert encore la feuille antérieure au changement
  (`style.css?ver=0.2.0`, 17 005 octets, `object-fit: contain`). Les
  dimensions mesurées et les interactions visuelles après application du
  nouveau cadrage ne sont donc pas confirmées dans le navigateur. Aucune
  commande Docker n’a été lancée.
- Aucun changement n’a été apporté à `header.html`, `hero-home.php`,
  `ecosystem.php` ou `theme.json`; aucune carte n’a été rendue cliquable.
  Validations à exécuter après le changement : `git diff --check` et
  `python3 -m json.tool wp-content/themes/ceredis/theme.json`.

## 2026-10-11 — Lot 3B : navigation, blog et ressources

### Audit initial

- Branche active vérifiée : `design/visual-system-v1`. L’état Git complet,
  les différences non indexées et indexées et les différences des fichiers
  ciblés ont été examinés. Les changements suivis et non suivis antérieurs
  sont préservés.
- Les ancres `#mission` et `#ecosystem` existent dans les patterns actifs.
  L’ancre `#actualites` est portée par le pattern des derniers contenus,
  lequel n’était pas rendu quand aucun article publié n’était trouvé.
- Le menu principal utilise déjà le bloc Navigation Gutenberg
  (`overlayMenu: "mobile"`). Aucun JavaScript ni menu alternatif n’a été
  ajouté.
- L’API REST publique liste uniquement la page publiée « Accueil »; la
  route `/blog/` répond 404 et l’API des articles publiés retourne une liste
  vide. Le sitemap public ne liste que la racine. L’endpoint REST des réglages
  renvoie 401 : `show_on_front` et `page_for_posts` ne sont donc pas vérifiables
  sans authentification; l’existence d’une page privée ou brouillon n’est pas
  exclue. Aucun réglage WordPress ni contenu de base de données n’a été changé.
- Les liens `/#mission` et `/#ecosystem` correspondent aux ancres existantes.
  Les articles restent le type WordPress natif `post`; les documents et médias
  sont préparés dans un pattern de ressources distinct, sans type de contenu
  personnalisé. Les trois documents transmis n’ont pas été publiés.

### Modifications

- `parts/header.html` et `parts/footer.html` : remplacement du libellé
  « Actualités » par « Blog » et destination préparée `/blog/`.
- `patterns/hero-home.php` : répartition des colonnes modifiée de 67/33 à
  60/40 sur écran large.
- `style.css` : ratio 55/45 entre 782 et 1199 px; Hero empilé sous 960 px avec
  illustration sous le texte et les boutons. Les gouttières, le padding et
  les presets typographiques ne changent pas. Le footer utilise le preset
  d’arrière-plan `ceredis-ink` et une séparation cyan au-dessus, pour le
  distinguer de la Mission. Styles limités aux liens sociaux, à la catégorie
  d’article, à la section blog et aux iframes responsives.
- `patterns/latest-content.php` : introduction demandée, Query Loop natif de
  trois articles au maximum, catégorie, date, titre, extrait, lien de lecture
  et bouton « Tous les articles ». La requête préalable PHP `get_posts()` a
  été retirée pour éviter une requête supplémentaire. En l’absence de résultat,
  le style existant `:has()` masque la section; le bloc natif de non-résultats
  reste un repli lisible si le CSS n’est pas appliqué.
- `templates/home.html` : nouveau modèle FSE de page d’articles, avec Query
  Loop hérité, pagination, catégories, dates, extraits et message sans article.
- `patterns/institutional-resources.php` : nouveau pattern réutilisable, non
  inséré sur l’accueil, présentant les deux documents Canva et la vidéo avec
  iframe différée, étiquettes distinctes (« Rapport d’activités », « Document
  de référence », « Ressource multimédia »), titres accessibles et liens de
  consultation de repli. Aucune extension ni CPT n’est ajouté.
- Aucun autre fichier ciblé n’a été modifié par ce lot; `theme.json` est
  inchangé. Les réglages de lecture et la création/affectation d’une page Blog
  restent manuels : vérifier ou créer une seule page avec le slug `blog`,
  conserver « Accueil » comme page d’accueil statique et désigner « Blog »
  comme page des articles.

### Vérifications réalisées

- `git diff --check`, validation JSON de `theme.json`, `php -l` sur les trois
  patterns PHP concernés, validation de 80 objets JSON et équilibre des
  commentaires de blocs Gutenberg : succès. Les nouveaux fichiers vérifiés ne
  contiennent pas d’espaces finaux.
- Navigateur, version locale actuellement servie : le menu mobile natif s’est
  ouvert au clavier à 390 px; les six liens sont accessibles, Tab déplace le
  focus avec `:focus-visible`, Échap ferme le menu et rend le focus au bouton.
  Les six liens restent visibles à 1440 px. Ces tests portent sur le bloc natif
  actuellement servi, dont le libellé est encore « Actualités », pas sur la
  navigation locale « Blog » non synchronisée.
- La composition Hero finale a été évaluée dans le navigateur avec une règle
  exploratoire temporaire en mémoire représentant la nouvelle répartition
  locale; elle a été retirée ensuite. H1 sur 2 lignes à 1440 et 1024 px, 2 à
  768 px, 3 à 390 et 320 px. Image 457 × 304,6 px à 1440 (écart aux bords
  verticaux du texte : 12,4 px) et 398,8 × 265,8 px à 1024 (écart : 7 px).
  À 768, 390 et 320 px, elle reste empilée après les boutons avec 40 px d’écart.
  Les proportions sont 3:2; aucun débordement horizontal n’a été relevé :
  `scrollWidth/clientWidth` vaut 1425/1425, 1009/1009, 753/753, 375/375 et
  305/305 px.
- Le navigateur utilisé supporte `:has()` et la feuille actuellement servie
  contient la règle de masquage de la section des derniers articles sans
  publication. L’API confirme actuellement zéro article publié; le rendu du
  nouveau pattern sans article n’a pas pu être vérifié avant synchronisation.
- Ce test Hero est une simulation navigateur, pas une mesure du CSS réellement
  servi après déploiement/synchronisation. Aucun build ni commande Docker n’a
  été lancé.

### Points restant à vérifier

- Les liens `/blog/` répondent encore 404 tant que la page n’est pas créée ou
  désignée comme page des articles. L’API ne permet pas de déterminer si une
  page privée/brouillon Blog ou un réglage `page_for_posts` existe.
- Après la configuration manuelle de lecture, synchroniser selon le processus
  local autorisé, puis vérifier la page d’articles avec et sans publications,
  les Query Loops, les catégories et la pagination.
- Refaire dans le navigateur les contrôles du Hero, du nouveau footer et des
  libellés/liens après synchronisation; le rendu des icônes sociales, les
  iframes Canva, leurs liens de repli et leur accessibilité n’a pas été mesuré
  dans le navigateur.
- Aucun commit, push, déploiement, changement Docker, modification de base de
  données ou intervention sur staging/production n’a été effectué.

## 2026-10-23 — Audit et amélioration globale du Blog CEREDIS

### Modifications implémentées (session)

- `wp-content/themes/ceredis/style.css` :
  - `.ceredis-single-post-title` : ajout `font-size: var(--wp--preset--font-size--hero)` et `line-height: 1.15`.
  - `.ceredis-single-post-content` : passage de `max-width: 70ch` à `80ch` pour les paragraphes, citations, blocs préformatés, figures et colonnes.
  - Remplacement des sélecteurs `:where(...)` (spécificité nulle) par des sélecteurs directs pour que la largeur de 80ch s'applique effectivement face aux règles WordPress `is-layout-constrained`.
  - `ul`/`ol` du contenu d'article : ajout de `max-width: 80ch` et `margin-inline: auto` pour aligner la colonne des listes avec celle des paragraphes, tout en conservant `padding-inline-start: 1.5rem`.
  - Mise à jour des media queries mobiles pour inclure les listes dans le passage à `max-width: 100%`.
- Rapports de mise en page et d’audit du Blog : voir `docs/reports/blog/audits/` et `docs/reports/blog/mise-en-page/`. Les rapports d’intégration et de publication sont classés dans les sous-dossiers correspondants de `docs/reports/blog/`.

### Vérifications

- `python3 -c "json.load(theme.json)"` : OK.
- `git diff --check` : OK.
- Aucun navigateur disponible : pas de validation visuelle.

### Points restant à vérifier manuellement

- Rendu sur ordinateur/tablette/mobile d'un article représentatif.
- Alignement des listes, largeur de lecture, absence de débordement horizontal.
- Tableaux et images non déformés.
---
## 2026-10-24 - Intégration article 4 ENAA (blog)
- Ajout de l'article 'Chaque élève à son rythme...' (ID WP: 41) en brouillon, catégorie 'Transformation numérique de l’éducation' (ID 3)
- Extraction de l'illustration depuis article-enaa-blog-ceredis-v3.docx (image1.png) vers uploads/2026/10/ et média ID 40
- Conversion du contenu DOCX vers blocs WordPress natifs (titres, paragraphes, listes, tableaux, citations, liens)
- Extrait rédigé, image mise en avant, slug court explicite
- Conservation intégrale du contenu, structure sémantique respectée, aucun changement aux 3 articles existants
---
---
## 2026-10-24 - Correction article ENAA (ID 41)
- Reconstruction complète du contenu depuis docs/article-enaa-blog-ceredis-v3.docx (structure, ordre, hiérarchie, tableaux, listes, citations, références)
- Intégration de l'illustration boucle-enaa.svg dans le corps de l'article à l'emplacement de Figure 1 (copiée dans uploads/2026/10/, référence directe dans bloc image, texte alternatif descriptif)
- Reproduction fidèle des tableaux sous forme de blocs wp:table natifs (avec en-têtes, colonnes/lignes préservées)
- Article toujours en brouillon, catégorie Transformation numérique de l’éducation (ID 3), image mise en avant (média 40) conservée distincte de l'illustration du corps

---

## 2026-10-24 — Vérification et constat de publication de l'article 41

### Contexte

Intervention de reprise après un autre agent de développement. L'objectif était de publier l'article ID 41 et de vérifier son affichage public, après confirmation par l'utilisateur que les corrections visuelles étaient satisfaisantes.

### Vérifications effectuées

- **État du dépôt** : branche `design/visual-system-v1`, modifications staged (rapports, DEVLOG, SVG) et non staged (thème, docker, scripts) examinées. Aucune opération destructive.
- **Rapport annoncé** : confirmé présent dans `docs/reports/blog/correction-article-enaa-2026-10-24.md` et dans `DEVLOG.md` (ligne 655-660). L'absence signalée par l'utilisateur était une question de localisation, pas de création manquée.
- **Article 41 via WP-CLI** : déjà **publié** (`publish`). Catégorie ID 3 confirmée. Image mise en avant `_thumbnail_id` = 40. Illustration `boucle-enaa.svg` présente (1 occurrence). 4 blocs `wp:table` présents. Auteur ID 1.
- **Affichage public** (`http://ceredis.test/?p=41`) : accessible sans authentification. Titre, sections, illustration avec légende, tableau structuré, références et pied de page tous présents et corrects.

### Résultat

L'article 41 était déjà publié. Aucune modification de contenu ou de statut n'a été nécessaire. Aucun fichier modifié par cette intervention.

### Documentation ajoutée

- `docs/reports/blog/verification-publication-article-41-2026-10-24.md` : rapport rétrospectif de cette intervention.
---
