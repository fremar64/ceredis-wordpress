#!/bin/sh
set -eu

UPLOADS_DIR="/usr/src/wordpress/wp-content/uploads"

# The official WordPress entrypoint must run as root.
if [ "$(id -u)" -ne 0 ]; then
    echo >&2 "ERROR: CEREDIS entrypoint must run as root."
    exit 1
fi

if [ ! -d "$UPLOADS_DIR" ]; then
    echo >&2 "ERROR: WordPress uploads directory does not exist: $UPLOADS_DIR"
    exit 1
fi

# Repair ownership if any file or directory is not owned by www-data.
# This also detects permission regressions after the initial deployment.
if find "$UPLOADS_DIR" ! -user www-data -print -quit | grep -q .; then
    echo "Correcting WordPress uploads directory ownership..."
    chown -R www-data:www-data "$UPLOADS_DIR"
    echo "WordPress uploads directory ownership corrected."
else
    echo "WordPress uploads directory ownership is correct."
fi

# Preserve the official WordPress initialization and startup behavior.
exec docker-entrypoint.sh "$@"
