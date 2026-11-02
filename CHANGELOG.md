# Historique des évolutions — CEREDIS WordPress

Ce document présente les principales évolutions de la distribution WordPress CEREDIS.

Il complète le journal détaillé de développement (`DEVLOG.md`) en conservant une synthèse des lots fonctionnels et techniques.

## 0.1.0 — Architecture initiale

- Définition de CEREDIS WordPress comme distribution WordPress Multisite contrôlée par Git.
- Adoption du mode multisite avec sous-domaines.
- Choix de l'image officielle WordPress comme base d'exécution.
- Définition du principe d'immuabilité du code applicatif.
- Mise en place du MU-plugin CEREDIS Core.
- Mise en place du thème FSE CEREDIS.
- Définition de la séparation entre les espaces éditoriaux WordPress et les applications spécialisées.
- Mise en place de l'environnement local Docker Compose.

## Lots 1 à 3 — Thème et identité visuelle

### Lot 1 — Modèles génériques

- Ajout des modèles génériques de pages et d'articles.
- Structuration des principaux gabarits du thème FSE.
- Mise en place des éléments nécessaires à la présentation des contenus éditoriaux.

### Lot 2 — Partenaires et contact

- Ajout de la page Partenaires.
- Intégration du formulaire de contact.
- Adaptation du thème aux besoins institutionnels du portail.

### Lot 3 — Système visuel CEREDIS

- Harmonisation du système visuel du thème.
- Ajustement des couleurs, de la typographie et des composants.
- Amélioration de la cohérence des interfaces et des gabarits.

## Lot 4 — Environnements Docker

- Séparation des valeurs par défaut de l'environnement général et des surcharges de développement.
- Désactivation par défaut des paramètres de débogage dans la configuration générale.
- Mise en place des surcharges spécifiques au développement.
- Clarification de la séparation entre configuration de production et configuration locale.

## Lot 5 — Documentation du projet

- Consolidation du README en français.
- Documentation de l'architecture technique.
- Formalisation des procédures de déploiement.
- Consolidation des règles de sécurité.
- Synthèse de l'historique des évolutions.
- Clarification du rôle des documents d'architecture et du journal de développement.

## Principes de suivi

Les évolutions significatives de l'architecture doivent être accompagnées de décisions documentées dans `docs/adr/`.

Le détail des travaux, validations, incidents et observations est conservé dans `DEVLOG.md`.

Les fonctionnalités en cours de développement ne doivent pas être présentées comme livrées ou validées.
