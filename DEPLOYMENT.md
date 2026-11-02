# Déploiement — Portail WordPress CEREDIS

## 1. Objet et principes

Ce document décrit la procédure de déploiement du portail institutionnel WordPress CEREDIS.

Le projet utilise une distribution WordPress personnalisée, construite à partir du dépôt Git et exécutée dans un environnement Docker orchestré par Coolify.

Le déploiement respecte trois principes :

- le code applicatif est versionné dans Git ;
- l'image Docker constitue l'unité de livraison ;
- les données persistantes sont séparées du code applicatif.

Le déploiement ne doit pas reposer sur des modifications manuelles du code dans le conteneur ou dans l'interface d'administration WordPress.

## 2. Environnements et progression

Le déploiement suit trois étapes successives.

### 2.1. Développement local

Le développement du thème, du MU-plugin et des composants de la distribution est réalisé localement.

L'environnement local repose sur Docker Compose et les fichiers de configuration du dépôt.

Avant de passer au staging, il convient notamment de vérifier :

- la construction de l'image ;
- le démarrage des services ;
- le fonctionnement de WordPress ;
- le chargement du thème CEREDIS ;
- le fonctionnement du MU-plugin ;
- les modèles et compositions du thème FSE ;
- les principales pages institutionnelles ;
- les fonctionnalités multisites concernées.

Les modifications doivent être enregistrées dans Git avant leur livraison.

### 2.2. Staging

Le premier déploiement distant doit être effectué sur un environnement indépendant de la production :

`https://wp-staging.ceredis.net/`

Cet environnement est géré séparément dans Coolify. Il ne doit pas remplacer ni modifier directement l'installation historique de production.

Le staging sert à vérifier le comportement de la distribution dans des conditions proches de la production.

Les contrôles portent notamment sur :

- la construction et le démarrage de l'image ;
- la connexion à la base de données ;
- l'initialisation du multisite ;
- le routage des sous-domaines ;
- le certificat HTTPS ;
- le fonctionnement du thème et du MU-plugin ;
- la persistance des données ;
- les journaux et les erreurs applicatives.

Le passage en production n'est autorisé qu'après validation de cette étape.

### 2.3. Production

Le déploiement de production est prévu sur :

`https://ceredis.net/`

Il constitue une opération distincte du déploiement de staging.

La mise en production ne doit intervenir qu'après validation du staging, préparation des données persistantes, vérification des sauvegardes et validation du plan de bascule.

L'ancienne installation doit être préservée jusqu'à ce que la nouvelle installation ait été validée.

## 3. Architecture de déploiement

La chaîne de livraison est la suivante :

```text
Dépôt GitHub
     |
     v
Construction de l'image Docker
     |
     v
Image CEREDIS WordPress
     |
     v
Coolify
     |
     v
Conteneur WordPress / Apache
```

Le trafic utilisateur suit le chemin suivant :

```text
Navigateur
    |
    v
Cloudflare
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
MySQL
```

Coolify assure l'orchestration des services et des déploiements. Traefik assure le routage et la terminaison HTTPS selon la configuration de l'infrastructure. Cloudflare assure la gestion DNS et les fonctions de proxy et de protection activées.

## 4. Configuration et variables d'environnement

Les paramètres propres à chaque environnement doivent être définis dans la configuration du service Coolify ou dans son mécanisme de gestion des variables et des secrets.

Les valeurs sensibles ne doivent jamais être inscrites dans le dépôt Git.

### 4.1. Variables de base de données

La configuration doit notamment prévoir :

- `WORDPRESS_DB_NAME`
- `WORDPRESS_DB_USER`
- `WORDPRESS_DB_PASSWORD`
- `MYSQL_ROOT_PASSWORD`

Les valeurs doivent être cohérentes entre le service WordPress et le service MySQL.

### 4.2. Configuration WordPress

Les paramètres suivants doivent être adaptés à l'environnement :

- `WORDPRESS_DOMAIN`
- `WORDPRESS_SCHEME`
- `WP_ENVIRONMENT_TYPE`
- `WP_DEBUG`
- `SCRIPT_DEBUG`
- `WP_MULTISITE`

Pour la production, le domaine attendu est `ceredis.net`, le schéma est HTTPS et le débogage doit être désactivé.

Le multisite est configuré en mode sous-domaines.

### 4.3. Clés et sels cryptographiques

Les clés et sels WordPress doivent être générés pour l'environnement concerné et conservés dans le gestionnaire de secrets de Coolify.

Ils ne doivent pas être réutilisés depuis l'ancienne installation.

Les variables concernées sont :

- `WORDPRESS_AUTH_KEY`
- `WORDPRESS_SECURE_AUTH_KEY`
- `WORDPRESS_LOGGED_IN_KEY`
- `WORDPRESS_NONCE_KEY`
- `WORDPRESS_AUTH_SALT`
- `WORDPRESS_SECURE_AUTH_SALT`
- `WORDPRESS_LOGGED_IN_SALT`
- `WORDPRESS_NONCE_SALT`

### 4.4. Paramètres d'administration initiale

L'initialisation du réseau nécessite également les paramètres d'administration prévus par le script de bootstrap :

- `CEREDIS_ADMIN_USER`
- `CEREDIS_ADMIN_EMAIL`
- `CEREDIS_ADMIN_PASSWORD`, lorsque l'exécution est non interactive.

Le mot de passe initial ne doit pas être enregistré dans Git ni dans un fichier documentaire versionné.

La méthode de transmission de ce secret doit être définie au moment de l'exécution du bootstrap.

## 5. Initialisation du multisite

Le réseau WordPress est destiné à fonctionner en mode multisite avec des sous-domaines.

Le script d'initialisation est :

```text
scripts/bootstrap-multisite.sh
```

Il doit être exécuté dans le conteneur WordPress, dans les conditions prévues par le projet.

L'initialisation nécessite notamment :

- un nom de domaine valide ;
- un schéma HTTP ou HTTPS cohérent avec l'environnement ;
- un compte administrateur initial ;
- une base de données accessible ;
- une configuration WordPress compatible avec le multisite.

Le script de bootstrap n'a pas encore été exécuté sur le serveur de staging ou de production. Cette opération reste donc à réaliser et à valider.

Après initialisation, il faut vérifier l'état réel de l'installation avec WP-CLI et contrôler que le réseau est effectivement configuré en multisite.

Ne pas considérer le simple fait que WordPress soit installé comme une preuve que le multisite est initialisé.

## 6. Création et gouvernance des sites

La création de sites dans le réseau doit répondre à une mission éditoriale ou institutionnelle distincte.

Les sites déjà identifiés dans le réseau CEREDIS comprennent notamment :

- le portail institutionnel `ceredis.net` ;
- le réseau `reseau.ceredis.net` ;
- l'espace `association.ceredis.net` ;
- l'espace `agritech.ceredis.net`.

La création de nouveaux sites doit être contrôlée et documentée.

Les applications spécialisées ne doivent pas être intégrées au réseau WordPress lorsqu'elles disposent de leur propre architecture applicative.

C'est notamment le cas de la plateforme Rallye Scolaire, développée avec Next.js, de la plateforme de formation Moodle et de la plateforme de publications Lodel.

## 7. Données persistantes et volumes

Les données doivent être conservées indépendamment des reconstructions de l'image.

Les volumes persistants prévus comprennent :

| Volume | Destination | Fonction |
|---|---|---|
| `wordpress_db` | `/var/lib/mysql` | Données MySQL |
| `wordpress_uploads` | `/usr/src/wordpress/wp-content/uploads` | Médias téléversés |

Les données persistantes doivent être sauvegardées avant toute opération susceptible de les affecter.

Le code source, le cœur WordPress, le thème et le MU-plugin ne doivent pas être utilisés comme substituts aux volumes de données.

## 8. Contrôles après déploiement

Après chaque déploiement, effectuer les vérifications suivantes :

1. Le conteneur WordPress démarre correctement.
2. La connexion à MySQL fonctionne.
3. Le site répond sur son domaine.
4. HTTPS fonctionne sans erreur de certificat.
5. Le routage vers les sous-domaines attendus est opérationnel.
6. Le multisite est effectivement actif.
7. Le thème CEREDIS est chargé.
8. Le MU-plugin CEREDIS Core est chargé.
9. Les médias existants sont accessibles.
10. Les journaux ne révèlent pas d'erreurs critiques.
11. Les fonctions administratives nécessaires sont accessibles.
12. Les sauvegardes et la procédure de restauration sont opérationnelles.

Les résultats doivent être consignés dans le journal de développement ou dans un rapport de validation.

## 9. Mise en production et bascule

La bascule de production constitue une opération distincte, à planifier.

Elle comprend les étapes suivantes :

1. Valider le déploiement de staging.
2. Vérifier la disponibilité des sauvegardes.
3. Préparer les volumes et la base de données de production.
4. Configurer les secrets et les variables de production dans Coolify.
5. Construire et déployer l'image validée.
6. Initialiser ou restaurer le réseau selon le plan de migration retenu.
7. Configurer les sites nécessaires.
8. Vérifier HTTPS, les domaines et le routage multisite.
9. Contrôler les contenus, les médias et les fonctions éditoriales.
10. Valider la procédure de retour arrière.
11. Effectuer la bascule du domaine principal vers la nouvelle installation.

L'ancienne installation ne doit pas être supprimée avant validation de la nouvelle infrastructure.

## 10. Retour arrière

Toute opération de mise à jour ou de migration doit prévoir une possibilité de retour à un état fonctionnel.

Le retour arrière doit prendre en compte :

- l'image Docker précédemment validée ;
- les données de la base MySQL ;
- les médias téléversés ;
- la configuration des domaines et du routage ;
- les secrets et variables propres à l'environnement.

Une restauration de la base et des médias doit être cohérente. La seule remise en service d'une ancienne image ne suffit pas si les données ont été modifiées entre-temps.

## 11. Documents associés

- `README.md` : présentation et démarrage du projet.
- `ARCHITECTURE.md` : architecture technique.
- `SECURITY.md` : règles de sécurité.
- `CHANGELOG.md` : historique des évolutions.
- `DEVLOG.md` : journal de développement.
- `docs/adr/` : décisions architecturales acceptées.
