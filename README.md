# CEREDIS — Portail institutionnel WordPress

**CEREDIS — Centre de Ressources Didactiques et d'Innovation Scolaire**

*La transformation numérique de l’éducation.*

Ce dépôt contient le code source du portail institutionnel du CEREDIS, construit avec WordPress Multisite et un thème personnalisé fondé sur l’architecture Full Site Editing (FSE).

Le portail a pour vocation de présenter les missions, les activités, les ressources et les programmes du CEREDIS, de faciliter les échanges avec les établissements scolaires et les partenaires institutionnels, et de constituer une porte d’entrée vers son écosystème numérique.

---

## 1. Positionnement du portail

Le portail WordPress constitue la couche institutionnelle et éditoriale de l’écosystème numérique CEREDIS.

Il ne doit pas être confondu avec les plateformes spécialisées qui répondent à des besoins fonctionnels distincts.

| Composant | Fonction principale | Technologie |
|---|---|---|
| Portail CEREDIS | Présentation institutionnelle, actualités, ressources et communication | WordPress Multisite |
| Rallye Scolaire CEREDIS | Organisation et suivi des activités du Rallye scolaire | Next.js |
| CEREDIS Académie | Formation et accompagnement des enseignants | Moodle |
| Publications CEREDIS | Gestion et diffusion des publications scientifiques et professionnelles | Lodel |
| ENAA | Environnement Numérique d’Apprentissage Adaptatif | Application dédiée |

Ces applications peuvent être reliées éditorialement au portail, mais conservent leur propre architecture, leurs données et leurs responsabilités opérationnelles.

Le présent dépôt concerne exclusivement le portail WordPress.

## 2. Principes architecturaux

Le projet repose sur les principes suivants :

- **WordPress Multisite** : gestion de plusieurs sites au sein d’une même installation.
- **Thème FSE personnalisé** : conception des interfaces à partir des fonctionnalités natives de blocs et de modèles WordPress.
- **Développement maîtrisé** : pas de dépendance à un constructeur de pages propriétaire.
- **Séparation des responsabilités** : le thème gère la présentation ; le MU-plugin CEREDIS Core porte les fonctions transversales du réseau.
- **Image Docker maîtrisée** : le cœur WordPress provient de l’image officielle et n’est pas forké.
- **Configuration externalisée** : les paramètres d’environnement et les secrets ne sont pas intégrés au code source.
- **Applications spécialisées indépendantes** : les plateformes applicatives de l’écosystème ne sont pas hébergées dans ce dépôt.

## 3. Technologies

| Composant | Technologie |
|---|---|
| CMS | WordPress |
| Mode de gestion | Multisite en sous-domaines |
| Thème | Thème FSE CEREDIS |
| Fonctions réseau | MU-plugin CEREDIS Core |
| Serveur web | Apache |
| Environnement d’exécution | PHP 8.5 |
| Base de données | MySQL 8.4 |
| Conteneurisation | Docker Compose |
| Administration en ligne de commande | WP-CLI |
| Déploiement | Coolify |
| Proxy inverse et HTTPS | Traefik |

L’image de base utilisée par le Dockerfile est :

```dockerfile
FROM wordpress:7.1.4-php8.5-apache
```

WP-CLI est intégré à l’image à partir de :

```text
wordpress:cli-2.12.0-php8.5
```

Le cœur WordPress reste celui de la distribution officielle. La version PHP indiquée ici correspond à l’environnement d’exécution de l’image Docker. Les en-têtes `Requires PHP: 8.3` observés dans certains composants du thème et du MU-plugin expriment un prérequis minimal de compatibilité, et non la version PHP effectivement utilisée par l’image de production.

## 4. Structure du dépôt

La structure principale du projet est organisée autour des éléments suivants :

```text
ceredis-wordpress/
├── Dockerfile
├── docker-compose.yml
├── docker-compose.dev.yml
├── .env.example
├── README.md
├── ARCHITECTURE.md
├── DEPLOYMENT.md
├── SECURITY.md
├── CHANGELOG.md
├── DEVLOG.md
├── docker/
│   ├── wp-config-docker.php
│   ├── entrypoint-ceredis.sh
│   ├── apache-ceredis.conf
│   └── apache-multisite.htaccess
├── scripts/
│   ├── bootstrap-multisite.sh
│   └── create-partners-page.sh
├── wp-content/
│   ├── mu-plugins/
│   │   └── ceredis-core/
│   └── themes/
│       └── ceredis/
└── docs/
    └── adr/
        ├── 0001-wordpress-multisite.md
        ├── 0002-official-wordpress-image.md
        └── 0003-static-runtime.md
```

Cette représentation est indicative : le dépôt comporte également des ressources graphiques, des modèles, des compositions, des motifs de blocs et d’autres documents.

## 5. Prérequis pour le développement local

L’environnement local nécessite :

- Git ;
- Docker Engine ;
- Docker Compose (plugin `docker compose`) ;
- un navigateur web ;
- un éditeur de code.

Le fonctionnement du multisite en sous-domaines suppose également une configuration DNS locale permettant de résoudre le domaine de développement et les sous-domaines utilisés.

Le fichier `.env.example` fournit les variables de configuration nécessaires au démarrage.

## 6. Démarrage local

### 6.1. Récupérer le dépôt

```bash
git clone <URL_DU_DEPOT>
cd ceredis-wordpress
```

### 6.2. Préparer les variables d’environnement

```bash
cp .env.example .env
vim .env
```

Adapter notamment les paramètres suivants :

```dotenv
WORDPRESS_DOMAIN=ceredis.test
WORDPRESS_SCHEME=http

WORDPRESS_DB_NAME=wordpress
WORDPRESS_DB_USER=wordpress
WORDPRESS_DB_PASSWORD=change-me
MYSQL_ROOT_PASSWORD=change-me-root

WP_ENVIRONMENT_TYPE=development
WP_DEBUG=true
SCRIPT_DEBUG=true

WP_MULTISITE=false

CEREDIS_ADMIN_USER=ceredis-admin
CEREDIS_ADMIN_EMAIL=admin@ceredis.test
```

Les valeurs de mots de passe et de clés cryptographiques fournies dans `.env.example` sont des valeurs de démonstration. Elles doivent être remplacées par des valeurs propres à chaque environnement.

### 6.3. Démarrer les services

Le fichier `docker-compose.yml` définit les services WordPress et MySQL. Le fichier `docker-compose.dev.yml` apporte les paramètres spécifiques au développement local.

Démarrer l’environnement :

```bash
docker compose \
  -f docker-compose.yml \
  -f docker-compose.dev.yml \
  up -d --build
```

Vérifier les services :

```bash
docker compose \
  -f docker-compose.yml \
  -f docker-compose.dev.yml \
  ps
```

Les services attendus sont :

```text
db
wordpress
```

En développement local, le portail est accessible à l’adresse :

```text
http://ceredis.test:8080
```

Cette adresse suppose que le domaine `ceredis.test` est correctement résolu vers l’environnement local.

### 6.4. Consulter les journaux

```bash
docker compose logs -f wordpress
```

Pour la base de données :

```bash
docker compose logs -f db
```

### 6.5. Utiliser WP-CLI

WP-CLI est intégré au conteneur WordPress.

Exemple :

```bash
docker compose exec -T wordpress wp core version --allow-root
```

Pour consulter les pages :

```bash
docker compose exec -T wordpress wp post list \
  --post_type=page \
  --fields=ID,post_title,post_name,post_status \
  --allow-root
```

Les commandes WP-CLI doivent être exécutées dans le contexte du conteneur afin d’utiliser la configuration et les accès à la base de données de l’installation.

## 7. Initialisation du multisite

L’installation initiale de WordPress et l’activation du réseau multisite sont deux opérations distinctes.

Le script `scripts/bootstrap-multisite.sh` permet d’initialiser le réseau en sous-domaines.

Il nécessite les variables suivantes :

```text
WORDPRESS_DOMAIN
WORDPRESS_SCHEME
CEREDIS_ADMIN_USER
CEREDIS_ADMIN_EMAIL
```

Le mot de passe administrateur peut être fourni par `CEREDIS_ADMIN_PASSWORD`. En environnement interactif, le script peut également le demander.

Le script refuse les domaines `localhost` et les adresses IP, car le mode multisite retenu repose sur des sous-domaines.

Après l’installation initiale de WordPress, exécuter :

```bash
docker compose exec wordpress ceredis-bootstrap-multisite
```

Le script lance notamment :

```bash
wp core multisite-install \
  --url="${WORDPRESS_SCHEME}://${WORDPRESS_DOMAIN}" \
  --base=/ \
  --subdomains \
  --title=CEREDIS \
  --skip-email \
  --skip-config \
  --allow-root
```

Après l’initialisation, activer le paramètre suivant dans `.env` :

```dotenv
WP_MULTISITE=true
```

Puis recréer le conteneur WordPress afin que la nouvelle variable d’environnement soit effectivement prise en compte :

```bash
docker compose up -d --force-recreate wordpress
```

Le réseau multisite doit ensuite être contrôlé depuis l’administration WordPress et avec WP-CLI.

### 8. Création de nouveaux sites dans le réseau

Une fois le réseau multisite initialisé, de nouveaux sites peuvent être créés sans réinstaller WordPress ni réinitialiser le réseau.

CEREDIS utilise le mode multisite en sous-domaines. Chaque nouveau site est donc associé à un sous-domaine de la forme :

```text
https://nom-du-site.ceredis.net
```

#### Création avec WP-CLI

La commande `wp site create` permet de créer un site dans le réseau.

Exemple :

```bash
docker compose exec wordpress wp site create \
  --slug=reseau \
  --title="Réseau CEREDIS" \
  --email="ADRESSE_ADMINISTRATEUR" \
  --allow-root
```

Pour créer d'autres sites, adapter les paramètres :

```bash
docker compose exec wordpress wp site create \
  --slug=association \
  --title="Association CEREDIS" \
  --email="ADRESSE_ADMINISTRATEUR" \
  --allow-root
```

```bash
docker compose exec wordpress wp site create \
  --slug=agritech \
  --title="CEREDIS Agritech" \
  --email="ADRESSE_ADMINISTRATEUR" \
  --allow-root
```

Les valeurs des paramètres `--title` et `--email` doivent être adaptées à chaque site et à son responsable.

#### Conditions d'accessibilité

La création d'un site dans le réseau WordPress ne configure pas automatiquement son accessibilité sur Internet.

Pour chaque nouveau sous-domaine, vérifier :

1. La résolution DNS du sous-domaine.
2. Le routage vers l'infrastructure CEREDIS.
3. La configuration du proxy inverse et du service WordPress.
4. La disponibilité du certificat HTTPS.
5. L'accès au site et à son administration.

Ces opérations relèvent de la configuration de l'infrastructure et sont détaillées dans `DEPLOYMENT.md`.

#### Vérification du réseau

Pour afficher les sites du réseau :

```bash
docker compose exec -T wordpress wp site list \
  --fields=blog_id,url,domain,path \
  --allow-root
```

Cette commande permet de vérifier que le nouveau site est bien enregistré dans le réseau multisite.

La création d'un site ne déploie pas une nouvelle application : elle ajoute un espace éditorial WordPress au réseau existant.


## 9. Création de la page Partenaires

Le dépôt contient le script :

```text
scripts/create-partners-page.sh
```

Il permet de créer la page publiée « Partenaires », avec le slug `partenaires` et le modèle `page-partenaires`.

Le script vérifie d’abord si une page portant ce slug existe déjà sur le site ciblé. Si elle existe, il s’arrête sans créer de doublon.

**Important :** ce script est présent dans le dépôt, mais il n’est pas copié dans l’image Docker. Il doit donc être transmis au shell du conteneur depuis le poste hôte.

Pour l’environnement local :

```bash
docker compose exec -T wordpress bash -s -- \
  --url http://ceredis.test \
  < scripts/create-partners-page.sh
```

Pour un autre site du réseau, adapter l’URL en fonction du domaine du site ciblé.

La méthode de transmission par l’entrée standard a été testée localement. Le test a confirmé l’exécution du script, l’accès à WP-CLI et la détection correcte d’une page existante.

Vérifier le résultat avec :

```bash
docker compose exec -T wordpress wp post list \
  --post_type=page \
  --name=partenaires \
  --fields=ID,post_title,post_name,post_status \
  --allow-root
```

## 10. Déploiement

Le déploiement du portail repose sur une image Docker construite à partir du dépôt et exécutée dans l’environnement Coolify.

L’architecture de production utilise :

- Coolify pour l’orchestration du déploiement ;
- Traefik pour le routage HTTP et HTTPS ;
- Cloudflare pour la gestion DNS du domaine ;
- MySQL pour la persistance des données ;
- WordPress pour la gestion éditoriale et institutionnelle.

Les paramètres de production doivent être fournis par l’environnement de déploiement et non intégrés au code source.

Les opérations de déploiement, d’initialisation, de configuration du réseau et de contrôle doivent suivre les procédures décrites dans `DEPLOYMENT.md`.

Le déploiement d’une nouvelle version du thème ou du MU-plugin doit être réalisé à travers le processus de construction de l’image prévu par le projet.

## 11. Sécurité et persistance

Les principes de sécurité du projet sont les suivants :

- ne jamais versionner les secrets de production ;
- utiliser des mots de passe et des clés cryptographiques propres à chaque environnement ;
- limiter les accès administratifs ;
- conserver les paramètres sensibles dans les variables d’environnement ou les mécanismes de secrets prévus par l’hébergement ;
- éviter les modifications directes du cœur WordPress ;
- maîtriser les extensions et les composants ajoutés au réseau ;
- préserver les données persistantes lors des reconstructions et des mises à jour.

Le conteneur applicatif ne doit pas être considéré comme l’espace de stockage permanent des données du site.

Les fichiers téléversés et les données de la base doivent être conservés dans les volumes persistants prévus par l’environnement de déploiement.

Pour les règles détaillées, consulter `SECURITY.md` et `ARCHITECTURE.md`.

## 12. Thème CEREDIS et MU-plugin

### Thème CEREDIS

Le thème personnalisé se trouve dans :

```text
wp-content/themes/ceredis/
```

Il repose sur les fonctionnalités natives de WordPress Full Site Editing.

Il regroupe notamment :

- les modèles de pages et d’articles ;
- les parties de modèles ;
- les compositions de blocs ;
- les styles globaux ;
- les ressources graphiques ;
- les composants de navigation et de présentation.

Le thème doit rester autonome et ne pas introduire de dépendance obligatoire à un constructeur de pages tiers.

### MU-plugin CEREDIS Core

Le MU-plugin se trouve dans :

```text
wp-content/mu-plugins/
```

Le fichier `ceredis-core.php` sert de chargeur vers l’implémentation située dans :

```text
wp-content/mu-plugins/ceredis-core/ceredis-core.php
```

Cette extension obligatoire porte les fonctions transversales du réseau CEREDIS.

Son périmètre actuel reste volontairement minimal.

## 13. Extensions WordPress

Le projet privilégie les fonctionnalités natives de WordPress et les développements spécifiques maîtrisés.

L’ajout d’une extension doit répondre à un besoin fonctionnel identifié et faire l’objet d’une évaluation portant notamment sur :

- la sécurité ;
- la maintenance ;
- la compatibilité multisite ;
- les performances ;
- les dépendances ;
- la pérennité ;
- les possibilités de remplacement.

La présence d’une extension dans un environnement WordPress ne signifie pas nécessairement que son code est construit ou distribué par ce dépôt.

Les composants effectivement embarqués dans l’image sont ceux explicitement intégrés par le Dockerfile.

## 14. Fonctionnalités à finaliser

Certaines fonctionnalités éditoriales ou opérationnelles nécessitent encore des développements ou des vérifications avant leur mise en production.

En particulier, le formulaire de contact et les mécanismes associés doivent faire l’objet d’une finalisation comprenant notamment :

- le traitement effectif des soumissions ;
- la confirmation destinée à l’utilisateur ;
- les protections contre le spam ;
- les informations relatives au traitement des données personnelles ;
- les liens vers les informations de confidentialité.

Le thème prévoit actuellement un mécanisme de repli par courrier électronique. Il ne doit pas être confondu avec un traitement serveur complet des formulaires.

## 15. Documentation du projet

La documentation est organisée autour de plusieurs fichiers de référence.

| Document | Contenu |
|---|---|
| `README.md` | Présentation générale, prérequis et démarrage local |
| `ARCHITECTURE.md` | Architecture technique et responsabilités des composants |
| `DEPLOYMENT.md` | Configuration, initialisation et déploiement |
| `SECURITY.md` | Principes et règles de sécurité |
| `CHANGELOG.md` | Historique synthétique des évolutions |
| `DEVLOG.md` | Journal de développement |
| `docs/adr/` | Décisions architecturales formalisées |

Les décisions architecturales de référence sont notamment :

- `0001-wordpress-multisite.md` : choix de WordPress Multisite ;
- `0002-official-wordpress-image.md` : utilisation de l’image officielle WordPress ;
- `0003-static-runtime.md` : principes de construction et de fonctionnement de l’image.

Les documents complémentaires et rapports de travail présents dans `docs/` peuvent apporter des précisions sur les évolutions du portail.

## 16. Contribuer au projet

Toute contribution doit respecter les principes suivants :

1. Identifier précisément le périmètre de la modification.
2. Vérifier l’état du dépôt avant toute intervention.
3. Ne modifier que les fichiers nécessaires.
4. Tester les changements dans l’environnement local.
5. Documenter les décisions techniques significatives.
6. Vérifier le contenu exact des modifications avant leur intégration dans Git.

Les modifications du thème, du MU-plugin, des scripts et de l’infrastructure doivent rester cohérentes avec les documents de référence.

## 17. Vision du projet

Le portail CEREDIS est conçu comme une infrastructure institutionnelle durable, évolutive et maîtrisée.

Il doit permettre de rendre visibles les activités du Centre, de valoriser les ressources didactiques et scolaires, de faciliter les partenariats et de relier les différents services de son écosystème numérique.

L’objectif n’est pas de concentrer toutes les fonctionnalités dans WordPress, mais de construire un ensemble cohérent de plateformes spécialisées, interconnectées lorsque cela est pertinent et maintenues selon leurs propres exigences techniques et fonctionnelles.

**CEREDIS — La transformation numérique de l’éducation.**
