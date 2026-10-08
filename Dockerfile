FROM wordpress:7.1.2-php8.3-apache

# WP-CLI is included for controlled, explicit bootstrap/diagnostic operations.
COPY --from=wordpress:cli-2.12.0-php8.3 /usr/local/bin/wp /usr/local/bin/wp

WORKDIR /usr/src/wordpress

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

# Runtime writes are limited to explicitly mounted writable paths.
