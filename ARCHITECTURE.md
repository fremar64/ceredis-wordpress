# CEREDIS WordPress — Architecture

## 1. Role

CEREDIS WordPress is the editorial and institutional layer of the CEREDIS ecosystem.

It is deliberately not the universal application platform.

## 2. Network model

The installation is WordPress Multisite in domain/subdomain mode:

```text
ceredis.net
├── agriculture.ceredis.net
├── partenaires.ceredis.net
├── recherche.ceredis.net
├── association.ceredis.net
└── future-programme.ceredis.net
```

A site is created only when a CEREDIS programme has a sufficiently distinct editorial purpose.

Applications with substantial transactional or pedagogical logic remain outside WordPress:

```text
rallye.ceredis.net       → Next.js
academie.ceredis.net     → Moodle
publications.ceredis.net → Lodel
```

## 3. Deployment model

```text
GitHub
  ↓
Docker build
  ↓
CEREDIS WordPress image
  ↓
Coolify
  ↓
Traefik
  ↓
Cloudflare
  ↓
Users
```

The WordPress image is based on the official `wordpress:7.1.2-php8.3-apache` image.

The upstream image supports a static/read-only deployment pattern in which WordPress is served from `/usr/src/wordpress` and only explicit writable paths, such as uploads, are mounted at runtime.

## 4. Code/data boundary

### Version-controlled

- Dockerfile
- Docker/Apache configuration
- CEREDIS Core MU-plugin
- CEREDIS theme
- selected plugins, when formally adopted
- CI configuration
- deployment documentation
- architecture decisions

### Persistent runtime data

- MySQL database
- uploads
- WordPress language files when needed

### Never committed

- secrets
- production `.env`
- database dumps
- uploads
- caches
- logs
- legacy WordPress content

## 5. CEREDIS Core

`wp-content/mu-plugins/ceredis-core/` contains network-wide functionality that must not depend on normal plugin activation.

It must remain small, stable and dependency-light.

It is not a place for programme-specific business logic.

## 6. Theme

`wp-content/themes/ceredis/` is the canonical CEREDIS design layer.

It provides:

- design tokens;
- global typography;
- header/footer;
- navigation;
- reusable patterns;
- programme cards;
- calls to action;
- institutional components;
- responsive behavior;
- accessibility foundations.

Programme sites may vary their content and limited presentation settings without forking the theme.

## 7. Plugin governance

Plugins are admitted to the distribution deliberately.

Every adopted plugin must have:

- an identified business/editorial purpose;
- a compatibility assessment;
- a multisite compatibility assessment;
- an update strategy;
- an owner or maintenance decision.

No plugin is installed merely because a theme/demo recommends it.

## 8. Security model

Production administrators must not modify PHP/theme/plugin code through wp-admin.

The following are disabled:

- file editor;
- plugin/theme installation from the dashboard;
- automatic core updates;
- automatic plugin/theme updates.

Changes flow through Git and deployment.

## 9. Multisite governance

The Network Administrator controls:

- sites;
- themes;
- plugins;
- network-wide settings;
- users and roles at network level.

Site administrators control editorial content within their own site.

The creation of a new site is an architectural decision, not an ad-hoc administrative action.

## 10. Domain strategy

The primary network domain is:

`ceredis.net`

Programme sites use subdomains.

External applications are allowed to use the same domain namespace but are not automatically WordPress sites.

## 11. Migration strategy

The legacy WordPress installation is not migrated wholesale.

The audit established that the current network contains little or no meaningful CEREDIS editorial content outside the main site's initial structure. The new distribution therefore starts clean.

The legacy installation remains untouched until the new staging deployment has been validated.

## 12. Architectural rule

> WordPress provides the editorial fabric of CEREDIS; it does not become the application fabric of CEREDIS.
