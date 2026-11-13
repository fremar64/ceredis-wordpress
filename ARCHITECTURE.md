# CEREDIS WordPress — Architecture technique

## 1. Rôle et périmètre

Le portail CEREDIS constitue la couche institutionnelle et éditoriale de l'écosystème numérique du Centre de Ressources Didactiques et Scolaires Numériques (CEREDIS).

Il assure notamment :

- la présentation institutionnelle du Centre ;
- la publication des actualités et des ressources ;
- la valorisation des programmes et projets ;
- la communication avec les établissements scolaires et les partenaires ;
- la gestion de plusieurs espaces éditoriaux au sein d'un même réseau.

WordPress n'a pas vocation à devenir la plateforme applicative universelle du CEREDIS.

Les applications qui comportent une logique métier, pédagogique ou transactionnelle spécifique conservent leur propre architecture et leur propre cycle de développement.

Le présent document décrit exclusivement l'architecture du portail WordPress et ses interactions avec l'infrastructure d'hébergement.

## 2. Modèle de réseau

### 2.1. WordPress Multisite

Le portail repose sur une installation WordPress Multisite configurée en mode sous-domaines.

Le domaine principal est :

```text
ceredis.net
```

Chaque site du réseau dispose de son propre espace éditorial et de son propre sous-domaine.

Exemples de sites du réseau :

```text
ceredis.net
reseau.ceredis.net
association.ceredis.net
agritech.ceredis.net
```

Le réseau peut accueillir de nouveaux sites lorsque les programmes ou les structures du CEREDIS justifient un espace éditorial distinct.

La création d'un site ne nécessite pas une nouvelle installation de WordPress. Elle s'effectue dans le réseau existant, sous le contrôle de l'administrateur du réseau.

### 2.2. Gouvernance du réseau

La création d'un site doit correspondre à une finalité éditoriale identifiable.

Le réseau distingue :

- l'administration générale de WordPress ;
- l'administration du réseau multisite ;
- l'administration éditoriale de chaque site.

L'administrateur du réseau contrôle les sites, les thèmes, les extensions, les utilisateurs et les paramètres communs.

Les administrateurs de sites interviennent dans le périmètre éditorial qui leur est attribué.

Un nouveau site ne doit pas être créé uniquement pour répondre à un besoin ponctuel qui pourrait être traité dans un site existant.

### 2.3. Applications externes

Les applications spécialisées ne sont pas automatiquement intégrées au réseau WordPress.

L'écosystème CEREDIS prévoit notamment :

| Application | Technologie | Responsabilité |
|---|---|---|
| Rallye Scolaire CEREDIS | Next.js | Gestion des activités du Rallye scolaire |
| CEREDIS Académie | Moodle | Formation et accompagnement des enseignants |
| Publications CEREDIS | Lodel | Gestion et diffusion des publications |
| ENAA | Application dédiée | Apprentissage adaptatif |

Ces applications peuvent partager le même espace de nommage DNS que le portail, sans pour autant appartenir à son réseau WordPress.

Elles disposent de leurs propres données, dépendances, mécanismes d'authentification et procédures de déploiement.

## 3. Architecture de déploiement

### 3.1. Chaîne de déploiement

Le code source est versionné dans GitHub. La construction et le déploiement de l'application sont orchestrés par Coolify.

L'architecture générale est la suivante :

```text
                  CODE SOURCE
                   GitHub
                     |
                     v
              Construction Docker
                     |
                     v
            Image CEREDIS WordPress
                     |
                     v
                  Coolify
                     |
                     v
             Conteneur WordPress
                     |
                     v
                  Apache
```

Le chemin d'accès des utilisateurs est distinct :

```text
              Utilisateur / Navigateur
                       |
                       v
                    Cloudflare
                 DNS et protection
                       |
                       v
                Traefik / HTTPS
                       |
                       v
                Service WordPress
                       |
                       v
                    Apache
                       |
                       v
                  WordPress
                       |
                       v
                 Base MySQL
```

Coolify assure l'orchestration du déploiement et des services. Traefik assure le routage des requêtes vers les applications et la gestion du trafic HTTPS selon la configuration de l'infrastructure.

Cloudflare assure notamment la gestion DNS du domaine et les fonctions de protection et de proxy activées.

### 3.2. Image Docker

Le Dockerfile utilise l'image officielle :

```dockerfile
FROM wordpress:7.1.4-php8.5-apache
```

Cette image fournit le socle d'exécution :

- WordPress ;
- PHP 8.5 ;
- Apache ;
- les dépendances nécessaires au fonctionnement de WordPress.

WP-CLI est intégré à partir de l'image officielle :

```text
wordpress:cli-2.12.0-php8.5
```

Le cœur WordPress n'est pas forké. Les personnalisations CEREDIS sont ajoutées au-dessus de la distribution officielle.

La configuration de référence a évolué depuis la première génération fondée sur PHP 8.3. Cette évolution s'inscrit dans la mise à jour de la base d'exécution et dans la prise en compte des vulnérabilités de sécurité PHP rendues publiques en octobre 2026, qui ont conduit CEREDIS à faire évoluer son image de référence vers PHP 8.5. Cette décision ne doit pas être présentée comme une version unique ou universelle de correction ; elle correspond à la politique de mise à jour du projet et à la compatibilité de l'infrastructure exécutée.

Une évolution de WordPress passe donc par le changement de version de l'image de base, la reconstruction et la validation de la nouvelle image CEREDIS.

### 3.3. Principe d'immuabilité

Le code applicatif livré en production est intégré à l'image Docker.

Les modifications du cœur, du thème et du MU-plugin doivent passer par le dépôt Git et le processus de construction de l'image.

Les modifications directes du code dans le conteneur ne constituent pas une méthode de déploiement.

Cette approche limite les dérives entre environnements et permet de reconstruire une version cohérente de l'application.

## 4. Configuration de WordPress

### 4.1. Configuration externalisée

La configuration est définie dans :

```text
docker/wp-config-docker.php
```

Ce fichier est intégré à l'image et lié à l'emplacement attendu par WordPress :

```text
/usr/src/wordpress/wp-config.php
```

Les paramètres sensibles et variables d'environnement sont fournis à l'exécution.

Le mécanisme `getenv_docker` permet notamment de prendre en charge les variables d'environnement et leurs variantes en fichiers, lorsque celles-ci sont disponibles.

La configuration couvre notamment :

- les paramètres de connexion à MySQL ;
- les clés et sels cryptographiques ;
- les paramètres de débogage ;
- le type d'environnement ;
- les paramètres de multisite ;
- la prise en charge du HTTPS derrière un proxy inverse.

Aucune valeur secrète de production ne doit être inscrite dans le code source.

### 4.2. Configuration multisite

L'activation du réseau est pilotée par la variable :

```dotenv
WP_MULTISITE=true
```

Lorsque cette variable est activée, la configuration définit notamment :

```php
MULTISITE
SUBDOMAIN_INSTALL
DOMAIN_CURRENT_SITE
PATH_CURRENT_SITE
SITE_ID_CURRENT_SITE
BLOG_ID_CURRENT_SITE
```

Le réseau est configuré en sous-domaines, avec le domaine principal fourni par l'environnement.

L'initialisation du réseau et la configuration qui permet à WordPress de fonctionner en mode multisite sont deux opérations distinctes.

Le script `scripts/bootstrap-multisite.sh` réalise l'initialisation du réseau. La configuration Docker prend ensuite en charge son fonctionnement au démarrage de WordPress.

### 4.3. Environnements

Le projet distingue les paramètres de développement et ceux de production.

Le fichier `docker-compose.yml` définit les valeurs par défaut destinées à l'exécution générale et à la production.

Le fichier `docker-compose.dev.yml` apporte les surcharges spécifiques au développement, notamment pour le débogage.

Le type d'environnement est défini par :

```dotenv
WP_ENVIRONMENT_TYPE
```

Les paramètres de débogage sont contrôlés par des variables distinctes.

Les valeurs effectives doivent être vérifiées dans chaque environnement afin d'éviter l'activation involontaire de fonctions de débogage en production.

## 5. Couche applicative CEREDIS

### 5.1. MU-plugin CEREDIS Core

Le MU-plugin CEREDIS Core se trouve dans :

```text
wp-content/mu-plugins/
```

Son organisation distingue le fichier chargeur :

```text
wp-content/mu-plugins/ceredis-core.php
```

de l'implémentation :

```text
wp-content/mu-plugins/ceredis-core/ceredis-core.php
```

Le fichier chargeur inclut l'implémentation du composant.

Le MU-plugin est chargé automatiquement par WordPress et ne dépend pas d'une activation ordinaire depuis l'administration.

Son périmètre actuel reste volontairement limité.

Il fournit notamment :

- les classes CSS d'identification du réseau ;
- l'identification du contexte multisite ;
- une indication de version dans la balise generator hors production.

Le MU-plugin doit rester stable, léger et indépendant des fonctionnalités propres aux programmes.

Il n'a pas vocation à accueillir la logique métier des applications spécialisées.

### 5.2. Thème CEREDIS

Le thème personnalisé est situé dans :

```text
wp-content/themes/ceredis/
```

Il constitue la couche de présentation commune du réseau.

Il repose sur l'architecture WordPress Full Site Editing (FSE).

Il regroupe notamment :

- les modèles de pages et d'articles ;
- les parties de modèles ;
- les compositions de blocs ;
- les motifs réutilisables ;
- les styles globaux ;
- les ressources graphiques ;
- les composants de navigation ;
- les éléments de présentation institutionnelle.

Le thème doit permettre aux différents sites du réseau de partager une identité visuelle et des composants communs tout en conservant des contenus et des finalités éditoriales distincts.

Les évolutions de présentation doivent être réalisées dans le thème et livrées par le processus de construction de l'image.

### 5.3. Séparation des responsabilités

La séparation entre le thème et le MU-plugin répond à une règle architecturale :

| Composant | Responsabilité |
|---|---|
| Thème CEREDIS | Présentation, modèles, styles et composants éditoriaux |
| MU-plugin CEREDIS Core | Fonctions transversales du réseau |
| Extensions WordPress | Fonctionnalités complémentaires adoptées et gouvernées |
| Applications externes | Logique métier et fonctionnelle spécialisée |

Cette séparation limite les dépendances et facilite la maintenance.

## 6. Serveur web et routage

### 6.1. Apache


Le serveur web utilisé est Apache, fourni par l'image officielle WordPress.

La configuration spécifique CEREDIS se trouve dans :

docker/apache-ceredis.conf

Elle autorise les directives nécessaires dans le répertoire /usr/src/wordpress, notamment celles qui sont définies dans le fichier .htaccess.

La configuration Apache et les règles de réécriture WordPress sont volontairement séparées.


### 6.2. Réécriture des URL multisite

Les règles de réécriture du réseau multisite sont définies dans :

docker/apache-multisite.htaccess

Ce fichier est intégré à l'image sous le nom .htaccess.

Il contient les règles nécessaires au fonctionnement de WordPress Multisite en mode sous-domaines.

La directive AllowOverride FileInfo, définie dans docker/apache-ceredis.conf, permet à Apache de prendre en compte les directives autorisées dans ce fichier.

### 6.3. HTTPS derrière le proxy inverse

L'infrastructure utilise Traefik comme proxy inverse.

La configuration WordPress tient compte de l'en-tête :

```text
HTTP_X_FORWARDED_PROTO
```

Lorsque le proxy indique que la requête d'origine utilise HTTPS, la configuration adapte la variable serveur PHP :

```php
$_SERVER['HTTPS'] = 'on';
```

Cette prise en charge permet à WordPress de reconnaître le protocole HTTPS lorsque la terminaison TLS est assurée en amont.

La configuration du proxy et celle de WordPress doivent être cohérentes afin d'éviter les problèmes de redirection, de contenu mixte et de cookies sécurisés.

## 7. Frontière entre code et données

### 7.1. Code versionné

Le dépôt contient notamment :

- le Dockerfile ;
- les fichiers Docker Compose ;
- la configuration Apache ;
- la configuration WordPress ;
- le script d'entrée du conteneur ;
- le script de bootstrap multisite ;
- le MU-plugin CEREDIS Core ;
- le thème CEREDIS ;
- les scripts d'administration retenus ;
- la documentation et les décisions architecturales.

Les composants explicitement copiés dans l'image sont déterminés par le Dockerfile.

La présence d'un fichier dans le dépôt ne signifie donc pas nécessairement qu'il est disponible dans le conteneur.

Par exemple, `scripts/create-partners-page.sh` est exécuté depuis le dépôt et transmis au shell du conteneur par l'entrée standard. Il n'est pas intégré à l'image.


### 7.2. Données persistantes

Le fichier `docker-compose.yml` définit deux volumes Docker nommés destinés à préserver les données lors du remplacement ou de la reconstruction des conteneurs.

| Volume Docker | Chemin dans le conteneur | Contenu |
|---|---|---|
| `wordpress_db` | `/var/lib/mysql` | Données de la base MySQL |
| `wordpress_uploads` | `/usr/src/wordpress/wp-content/uploads` | Fichiers téléversés dans WordPress |

Ces volumes constituent les principaux espaces de persistance de l'installation.

Le code applicatif, la configuration et les composants CEREDIS intégrés à l'image ne doivent pas être confondus avec ces données persistantes.

Les répertoires `/tmp` et `/run` sont montés en `tmpfs`. Ils constituent des espaces temporaires, dont le contenu n'est pas destiné à être conservé lors du remplacement du conteneur.

Les traductions françaises du cœur WordPress sont intégrées à l'image et ne constituent pas un volume de données utilisateur.

Les sauvegardes de la base et des téléversements doivent être organisées de manière à permettre leur restauration indépendamment de la reconstruction de l'image applicative.

### 7.3. Éléments exclus du dépôt

Les éléments suivants ne doivent pas être versionnés comme partie du code applicatif :

- secrets et clés privées ;
- fichiers `.env` contenant des valeurs réelles ;
- dumps de bases de données ;
- caches ;
- journaux d'exécution ;
- fichiers temporaires ;
- données téléversées par les utilisateurs.

Les éventuelles exceptions doivent être justifiées et documentées.

## 8. Gouvernance des extensions

Les extensions WordPress ne sont pas ajoutées automatiquement à l'image.

Toute extension retenue doit faire l'objet d'une décision explicite prenant en compte :

- son utilité fonctionnelle ;
- sa compatibilité avec WordPress ;
- sa compatibilité avec le multisite ;
- sa sécurité ;
- sa politique de mise à jour ;
- ses dépendances ;
- sa maintenance à long terme.

Une extension installée dans un environnement ne doit pas être considérée comme intégrée à la distribution CEREDIS tant que son mode de livraison et sa gouvernance ne sont pas définis.

## 9. Modèle de sécurité

L'architecture privilégie la maîtrise du code et des accès.

Les paramètres de production interdisent notamment les modifications du code par l'interface d'administration.

Les constantes suivantes sont pilotées par la configuration :

```text
DISALLOW_FILE_EDIT
DISALLOW_FILE_MODS
AUTOMATIC_UPDATER_DISABLED
WP_AUTO_UPDATE_CORE
```

Les modifications du cœur, du thème et des extensions doivent passer par le processus de développement et de déploiement.

La gestion des secrets, des accès, des sauvegardes et des mises à jour est détaillée dans `SECURITY.md` et `DEPLOYMENT.md`.

## 10. Migration et évolution

Le nouveau portail constitue une distribution CEREDIS maîtrisée.

La migration de l'ancien environnement WordPress n'est pas une copie intégrale de l'installation historique.

Les contenus et fonctionnalités sont réévalués avant leur intégration dans le nouveau réseau.

L'ancienne installation doit rester préservée tant que la nouvelle infrastructure n'a pas été suffisamment validée.

Les évolutions de l'architecture doivent être accompagnées de décisions documentées lorsque leur portée est significative.

Les décisions déjà acceptées sont conservées dans :

```text
docs/adr/
```

Elles constituent la référence pour les choix structurants du projet.

## 11. Règle architecturale fondamentale

> WordPress constitue le tissu éditorial du CEREDIS ; il ne constitue pas le tissu applicatif de l'ensemble de son écosystème numérique.

Cette distinction guide les choix de développement, d'intégration, de déploiement et de maintenance du portail.
