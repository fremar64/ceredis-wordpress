# ADR 0002 — Official WordPress Image

## Status

Accepted

## Decision

Use the official Docker WordPress image as the upstream runtime and maintain CEREDIS customisations above it.

## Consequences

WordPress core is not forked.

Core upgrades are handled by changing the pinned image version and rebuilding the CEREDIS image.
