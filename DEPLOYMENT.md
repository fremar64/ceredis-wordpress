# Deployment — CEREDIS WordPress

## Staging first

Do not attach the new image to `ceredis.net` during the initial deployment.

Create an independent Coolify resource and use a staging hostname.

## Coolify

The application should be deployed from the Git repository using the Dockerfile.

Required production secrets:

- WORDPRESS_DB_PASSWORD
- MYSQL_ROOT_PASSWORD
- WORDPRESS_AUTH_KEY
- WORDPRESS_SECURE_AUTH_KEY
- WORDPRESS_LOGGED_IN_KEY
- WORDPRESS_NONCE_KEY
- WORDPRESS_AUTH_SALT
- WORDPRESS_SECURE_AUTH_SALT
- WORDPRESS_LOGGED_IN_SALT
- WORDPRESS_NONCE_SALT

Required production variables:

- WORDPRESS_DOMAIN=ceredis.net
- WORDPRESS_DB_NAME=wordpress
- WORDPRESS_DB_USER=wordpress
- WP_ENVIRONMENT_TYPE=production
- WP_DEBUG=false
- SCRIPT_DEBUG=false
- WP_MULTISITE=true
- WORDPRESS_SCHEME=https

Do not copy secrets from the legacy WordPress deployment.

## DNS

The production network requires:

- `ceredis.net`
- wildcard subdomain routing for future WordPress sites

External applications may use the same namespace but must be routed to their own services.

## Volumes

Persist:

- MySQL data
- WordPress uploads
- WordPress languages, if used

Do not persist the entire application source tree as a replacement for Git-controlled code.

## Cut-over

The final production cut-over is a separate change window:

1. validate staging;
2. create production database;
3. deploy the image;
4. bootstrap the network;
5. create the main site;
6. configure the CEREDIS theme;
7. create only required programme sites;
8. validate HTTPS and multisite routing;
9. only then change `ceredis.net` routing.

The legacy installation remains available until the new production deployment has passed validation.
