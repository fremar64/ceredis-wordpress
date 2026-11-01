#!/usr/bin/env bash
set -euo pipefail

usage() {
  echo "Usage: $0 --url URL" >&2
}

SITE_URL=""
while (($#)); do
  case "$1" in
    --url)
      if (($# < 2)) || [[ "$2" == --* ]]; then
        echo "Error: --url requires a URL value." >&2
        usage
        exit 1
      fi
      if [[ -n "$SITE_URL" ]]; then
        echo "Error: --url may only be specified once." >&2
        usage
        exit 1
      fi
      SITE_URL="$2"
      shift 2
      ;;
    *)
      echo "Error: unknown option or argument: $1" >&2
      usage
      exit 1
      ;;
  esac
done

if [[ -z "$SITE_URL" ]]; then
  echo "Error: --url is required." >&2
  usage
  exit 1
fi

if [[ ! "$SITE_URL" =~ ^https?://([[:alnum:]]([[:alnum:]-]*[[:alnum:]])?\.)*[[:alnum:]]([[:alnum:]-]*[[:alnum:]])?(:[0-9]{1,5})?([/?#][^[:space:]]*)?$ ]]; then
  echo "Error: invalid URL: $SITE_URL" >&2
  usage
  exit 1
fi

URL_AUTHORITY="${SITE_URL#*://}"
URL_AUTHORITY="${URL_AUTHORITY%%[/?#]*}"
if [[ "$URL_AUTHORITY" =~ :([0-9]+)$ ]] && (( 10#${BASH_REMATCH[1]} == 0 || 10#${BASH_REMATCH[1]} > 65535 )); then
  echo "Error: invalid URL port: ${BASH_REMATCH[1]}" >&2
  usage
  exit 1
fi

EXISTING_PAGE_ID=$(
  wp post list \
    --post_type=page \
    --name=partenaires \
    --field=ID \
    --url="$SITE_URL" \
    --allow-root 2>/dev/null
)

if [[ -n "$EXISTING_PAGE_ID" ]]; then
  echo "Page Partenaires already exists."
  exit 0
fi

wp post create \
  --post_type=page \
  --post_title="Partenaires" \
  --post_name=partenaires \
  --post_status=publish \
  --page_template=page-partenaires \
  --url="$SITE_URL" \
  --allow-root

echo "Page Partenaires created."
