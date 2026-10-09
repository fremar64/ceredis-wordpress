FROM wordpress:7.1.2-php8.3-apache

# curl is required by Coolify's HTTP healthcheck.
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends curl unzip; \
    rm -rf /var/lib/apt/lists/*

# French translation files are part of the immutable Docker image.
RUN set -eux; \
    mkdir -p /usr/src/wordpress/wp-content/languages; \
    curl --fail --silent --show-error --location \
      https://downloads.wordpress.org/translation/core/7.1.2/fr_FR.zip \
      --output /tmp/wordpress-fr_FR.zip; \
    unzip -o /tmp/wordpress-fr_FR.zip \
      -d /usr/src/wordpress/wp-content/languages; \
    rm -f /tmp/wordpress-fr_FR.zip; \
    chown -R www-data:www-data \
      /usr/src/wordpress/wp-content/languages

# WP-CLI is included for controlled, explicit bootstrap/diagnostic operations.
COPY --from=wordpress:cli-2.12.0-php8.3 /usr/local/bin/wp /usr/local/bin/wp

WORKDIR /usr/src/wordpress
# CEREDIS-controlled WordPress configuration.
# This replaces the official config so that no PHP code is evaluated
# from the WORDPRESS_CONFIG_EXTRA environment variable.
COPY docker/wp-config-docker.php ./wp-config-docker.php

# Apache configuration for WordPress Multisite.
RUN set -eux; \
    find /etc/apache2 -name '*.conf' -type f \
      -exec sed -ri \
        -e 's!/var/www/html!/usr/src/wordpress!g' \
        -e 's!Directory /var/www/!Directory /usr/src/wordpress!g' \
        -e 's!DocumentRoot \$PWD!DocumentRoot /usr/src/wordpress!g' \
        -e 's!<Directory \$PWD>!<Directory /usr/src/wordpress>!g' \
        '{}' +; \
    cp -s wp-config-docker.php wp-config.php

COPY docker/apache-ceredis.conf /etc/apache2/conf-available/ceredis-wordpress.conf

RUN set -eux; \
    a2enconf ceredis-wordpress; \
    apache2ctl configtest

# CEREDIS-owned code and Apache routing are part of the image.
COPY docker/apache-multisite.htaccess ./.htaccess
COPY wp-content/mu-plugins/ ./wp-content/mu-plugins/
COPY wp-content/themes/ceredis/ ./wp-content/themes/ceredis/
COPY scripts/bootstrap-multisite.sh /usr/local/bin/ceredis-bootstrap-multisite

RUN set -eux; \
    chmod 0755 /usr/local/bin/ceredis-bootstrap-multisite; \
    chown -R www-data:www-data \
      ./wp-content/mu-plugins/ceredis-core \
      ./wp-content/themes/ceredis \
      ./.htaccess

# Docker-native healthcheck for Coolify.
# Coolify will use this instead of generating a wget-based check.
HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=5 \
    CMD curl --fail --silent --show-error \
    http://127.0.0.1/wp-login.php >/dev/null || exit 1

# Runtime writes are limited to explicitly mounted writable paths.
