# CEREDIS WordPress

CEREDIS WordPress is the Git-controlled editorial distribution for the CEREDIS digital ecosystem.

It provides a WordPress Multisite network in subdomain mode. WordPress is the editorial/institutional layer; specialised applications such as Rallye Next.js, Académie Moodle and Publications Lodel remain independent services.

## Principles

- Official WordPress Docker image as the upstream runtime.
- CEREDIS code is versioned in Git.
- WordPress core, themes and plugins are deployed by rebuilding/redeploying the image.
- Runtime state is limited to the database and explicitly writable uploads/language volumes.
- Multisite uses subdomains under `ceredis.net`.
- Production secrets are supplied by Coolify and never committed.
- No arbitrary plugin/theme installation from wp-admin.
- A new programme site is created only when its editorial scope justifies it.

## Repository layout

```text
.
├── .github/workflows/
├── config/
├── docker/
├── scripts/
├── wp-content/
│   ├── mu-plugins/ceredis-core/
│   └── themes/ceredis/
├── .dockerignore
├── .env.example
├── ARCHITECTURE.md
├── DEPLOYMENT.md
├── Dockerfile
└── docker-compose.yml
```

## Local development

1. Copy `.env.example` to `.env`.
2. Generate unique local salts and replace every `change-me` value.
3. Keep `WORDPRESS_SCHEME=http` for local development.
4. Use a development hostname such as `ceredis.test` and map it to `127.0.0.1` locally.
5. Build and start:

```bash
docker compose -f docker-compose.yml -f docker-compose.dev.yml build
docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d
```

6. Bootstrap the network explicitly:

```bash
docker compose exec wordpress ceredis-bootstrap-multisite
```

7. Set `WP_MULTISITE=true` in `.env`, then restart the stack.

The bootstrap uses WP-CLI's official Multisite installation command with `--skip-config`; the repository-owned immutable configuration supplies the network constants. WordPress documents that subdomain Multisite cannot use `localhost`, so use a real development hostname such as `ceredis.test`. Do not create production sites from this local environment.

## Production

Production is deployed from GitHub through Coolify. The production domain is `ceredis.net`.

The production deployment must use:

- the repository's Dockerfile;
- persistent MySQL storage;
- persistent uploads storage;
- Coolify-managed secrets;
- Cloudflare DNS;
- Traefik HTTPS termination.

The production switch-over is a separate runbook and must never be performed by changing the current legacy WordPress container in place.

## Update policy

WordPress core, PHP base image, CEREDIS Core and the CEREDIS theme are updated through Git commits and image rebuilds.

Automatic WordPress/plugin/theme updates are disabled deliberately. This makes production changes auditable and reproducible.

## Scope

This repository is not intended to contain:

- WordPress database dumps;
- media uploads;
- secrets;
- legacy demo content;
- unrelated themes;
- arbitrary third-party plugins;
- specialised CEREDIS applications.
