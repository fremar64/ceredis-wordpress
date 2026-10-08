#!/usr/bin/env bash
set -euo pipefail

cd /usr/src/wordpress

: "${WORDPRESS_DOMAIN:?WORDPRESS_DOMAIN must be set}"
: "${CEREDIS_ADMIN_USER:?CEREDIS_ADMIN_USER must be set}"
: "${CEREDIS_ADMIN_EMAIL:?CEREDIS_ADMIN_EMAIL must be set}"
: "${WORDPRESS_SCHEME:?WORDPRESS_SCHEME must be set}"

if [[ "${WORDPRESS_DOMAIN}" == localhost || "${WORDPRESS_DOMAIN}" =~ ^[0-9.]+$ ]]; then
  echo "ERROR: subdomain multisite requires a real development/staging hostname; localhost and IP addresses are not supported." >&2
  exit 1
fi

if wp core is-installed --allow-root >/dev/null 2>&1; then
  echo "WordPress database is already installed."
else
  echo "Installing WordPress Multisite database..."
  if [[ -z "${CEREDIS_ADMIN_PASSWORD:-}" ]]; then
    read -r -s -p "CEREDIS admin password: " CEREDIS_ADMIN_PASSWORD
    echo
  fi

  wp core multisite-install \
    --url="${WORDPRESS_SCHEME}://${WORDPRESS_DOMAIN}" \
    --base=/ \
    --subdomains \
    --title="CEREDIS" \
    --admin_user="${CEREDIS_ADMIN_USER}" \
    --admin_password="${CEREDIS_ADMIN_PASSWORD}" \
    --admin_email="${CEREDIS_ADMIN_EMAIL}" \
    --skip-email \
    --skip-config \
    --allow-root
fi

echo "Multisite database tables created."
echo "Multisite bootstrap complete."
echo "Next step: set WP_MULTISITE=true and restart the WordPress service."
