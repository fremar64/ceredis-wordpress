# Sécurité — Portail WordPress CEREDIS

## 1. Objet

Ce document définit les principes de sécurité applicables à la distribution WordPress du portail CEREDIS.

Il complète les dispositions techniques décrites dans `ARCHITECTURE.md` et les procédures opérationnelles de `DEPLOYMENT.md`.

La sécurité repose sur la maîtrise du code, la protection des secrets, la limitation des accès, la séparation des données et la maintenance contrôlée des composants.

## 2. Protection du code applicatif

Le code livré dans les environnements distants doit provenir du dépôt Git et du processus de construction de l'image Docker.

Les modifications directes du cœur WordPress, du thème ou du MU-plugin dans un conteneur en production ne constituent pas une méthode de maintenance acceptable.

Les règles suivantes s'appliquent :

- ne pas modifier manuellement le code applicatif en production ;
- ne pas utiliser l'éditeur de fichiers de l'administration WordPress ;
- ne pas installer de composants arbitraires depuis le tableau de bord ;
- ne pas activer les mises à jour automatiques non contrôlées du code ;
- faire passer les modifications par Git, la construction de l'image et la validation de l'environnement.

Cette stratégie vise à préserver la reproductibilité des déploiements et à limiter les dérives entre environnements.

## 3. Gestion des secrets

Les secrets de production doivent être conservés dans le mécanisme de gestion des secrets de l'hébergeur, notamment Coolify.

Ils ne doivent jamais être :

- inscrits dans le code source ;
- ajoutés aux fichiers versionnés ;
- publiés dans les journaux ;
- transmis dans des rapports accessibles publiquement ;
- réutilisés d'un environnement à l'autre sans justification et contrôle.

Les mots de passe MySQL, les clés et sels WordPress ainsi que les identifiants administratifs doivent être propres à chaque environnement.

Les secrets doivent être renouvelés lorsqu'une exposition est suspectée ou confirmée.

## 4. Comptes et droits d'accès

Les comptes administratifs doivent être limités aux personnes qui en ont besoin.

Les principes suivants doivent être appliqués :

- utiliser des mots de passe robustes et uniques ;
- limiter le nombre de comptes Super Admin ;
- attribuer les rôles selon les responsabilités réelles ;
- supprimer ou désactiver les comptes qui ne sont plus nécessaires ;
- éviter le partage de comptes administratifs ;
- protéger les accès à Coolify, au serveur et à la base de données.

Les accès à l'administration WordPress ne doivent pas donner automatiquement accès à l'infrastructure d'hébergement.

## 5. Extensions et composants tiers

Toute extension ou dépendance supplémentaire doit faire l'objet d'une évaluation préalable.

Cette évaluation prend en compte :

- la nécessité fonctionnelle ;
- la compatibilité avec la version de WordPress ;
- la compatibilité avec le multisite ;
- la qualité de la maintenance ;
- les dépendances ;
- les risques de sécurité ;
- les modalités de mise à jour.

Une extension installée ponctuellement dans un environnement ne doit pas être considérée comme intégrée à la distribution CEREDIS tant que son mode de livraison n'est pas défini.

## 6. Mises à jour et maintenance

Les mises à jour du cœur WordPress, de PHP, de l'image Docker et des composants tiers doivent être maîtrisées.

La procédure de maintenance comprend :

1. l'identification de la version concernée ;
2. l'examen des changements et des avis de sécurité applicables ;
3. la mise à jour du dépôt ou de la configuration ;
4. la construction d'une nouvelle image ;
5. les tests dans l'environnement de développement ;
6. la validation sur staging ;
7. le déploiement en production après validation.

La version de l'image de base doit rester explicitement définie.

Une mise à jour ne doit pas être considérée comme sûre du seul fait qu'elle est disponible.

## 7. Données et sauvegardes

Les données MySQL et les médias téléversés constituent des éléments essentiels du portail.

Ils doivent être sauvegardés indépendamment du cycle de construction des images.

Une politique de sauvegarde doit préciser :

- les données concernées ;
- la fréquence des sauvegardes ;
- leur destination ;
- les personnes responsables ;
- la durée de conservation ;
- les modalités de restauration.

La présence d'une sauvegarde ne suffit pas : sa restauration doit être testée périodiquement.

Les sauvegardes doivent être protégées contre les accès non autorisés.

## 8. Réseau et exposition des services

Les services internes ne doivent pas être exposés publiquement sans nécessité.

En particulier :

- MySQL ne doit pas être accessible directement depuis Internet ;
- les interfaces d'administration doivent être protégées ;
- les domaines doivent être routés vers les services auxquels ils sont destinés ;
- HTTPS doit être utilisé pour les accès distants ;
- les paramètres de proxy et de schéma doivent être cohérents avec la configuration WordPress.

Les applications spécialisées doivent conserver leurs propres frontières de sécurité.

## 9. Mesure temporaire de précaution concernant les services PHP

Les services WordPress, Moodle et les autres services reposant sur PHP ont été provisoirement mis hors ligne par mesure de précaution, dans l'attente d'une vérification de la situation de sécurité des composants concernés.

Cette mesure est temporaire et ne constitue pas une preuve que les vulnérabilités éventuelles ont été identifiées, ni que les correctifs ont été appliqués.

Avant toute remise en ligne, il convient de :

1. identifier les versions réellement utilisées ;
2. examiner les avis de sécurité applicables ;
3. vérifier les correctifs disponibles ;
4. mettre à jour les composants concernés si nécessaire ;
5. reconstruire les images ;
6. effectuer les contrôles de sécurité et de fonctionnement ;
7. valider la remise en service.

La remise en ligne doit être une décision explicite, fondée sur les résultats de ces vérifications.

## 10. Gestion des incidents

En cas de suspicion d'incident de sécurité :

1. préserver les éléments disponibles ;
2. identifier les services et données potentiellement concernés ;
3. limiter l'exposition si nécessaire ;
4. protéger ou renouveler les secrets susceptibles d'avoir été compromis ;
5. examiner les journaux et les accès ;
6. déterminer les mesures correctives ;
7. vérifier le retour à un état maîtrisé ;
8. consigner l'incident et les actions entreprises.

Les opérations correctives doivent éviter de détruire les éléments utiles à l'analyse.

## 11. Responsabilités

La sécurité du portail repose sur plusieurs niveaux de responsabilité :

- le dépôt Git et les processus de développement pour l'intégrité du code ;
- Coolify et l'administration du serveur pour la configuration des services ;
- WordPress pour la gestion des comptes et des rôles éditoriaux ;
- les responsables du projet pour la gouvernance des extensions, des accès et des mises à jour.

La sécurité ne peut pas être garantie par le seul choix d'une image Docker ou d'un système de gestion de contenu.

## 12. Documents associés

- `README.md`
- `ARCHITECTURE.md`
- `DEPLOYMENT.md`
- `CHANGELOG.md`
- `DEVLOG.md`
- `docs/adr/`
