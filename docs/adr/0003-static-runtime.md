# ADR 0003 — Static Runtime

## Status

Accepted

## Decision

Serve WordPress from an immutable image and persist only explicitly writable runtime data.

## Rationale

This prevents production drift caused by dashboard-installed or automatically updated code.

The official WordPress image documents this static/read-only deployment pattern.
