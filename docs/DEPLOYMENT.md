# Deployment notes

## Repository layout

All website files live at the **Git repository root** (`index.php`, `.htaccess`, `assets/`, `admin/`, `includes/`, etc.). There is no `in_php/` wrapper folder.

## Hostinger

1. Point the domain document root at the folder that contains `index.php` and `.htaccess` (typically `public_html`).
2. Upload/sync the repository root contents into that folder. Do **not** nest them under `public_html/in_php/`.
3. Store secrets outside the web root as `nazora-config.php`, or as gitignored `storage/config.local.php`.
4. Ensure `storage/` is writable by PHP for SQLite, uploads, and submissions.
5. After upload, verify:
   - `https://nazora.tech/`
   - `https://nazora.tech/sitemap.xml`
   - `https://nazora.tech/robots.txt`
   - Canonical tags use `https://nazora.tech/...` (non-www)

This document does not perform remote deploys. Live Hostinger changes are a separate, explicit step.

## Local XAMPP

Serve the clone from a folder under `htdocs`, e.g. `http://localhost/SNF_TEAM/`. Path detection in `includes/site_config.php` preserves subdirectory installs without hardcoded `/in_php/` URLs.

## Docker / Render

Build from the repository root. The Dockerfile copies the project into Apache’s document root.
