#!/bin/sh
set -eu

UPLOADS_DIR="/usr/src/wordpress/wp-content/uploads"

# The official WordPress entrypoint must run as root.
if [ "$(id -u)" -ne 0 ]; then
    echo >&2 "ERROR: CEREDIS entrypoint must run as root."
    exit 1
fi

# Ensure that the uploads directory exists before checking ownership.
mkdir -p "$UPLOADS_DIR"

# Repair ownership if any file or directory is not owned by www-data.
if find "$UPLOADS_DIR" ! -user www-data -print -quit | grep -q .; then
    echo "Correcting WordPress uploads directory ownership..."
    chown -R www-data:www-data "$UPLOADS_DIR"
    echo "WordPress uploads directory ownership corrected."
else
    echo "WordPress uploads directory ownership is correct."
fi

# Preserve the official WordPress initialization and startup behavior.
exec docker-entrypoint.sh "$@"
