# Nazora Tech — Official Corporate Website

Nazora Tech is an IT services company specializing in web development, application development, custom software solutions, AI automation, and digital marketing.

This repository contains the company's PHP-based corporate website, designed to showcase its services, portfolio, pricing, blog, career opportunities, and business information.

## Website Features

- **Home Page:** Company introduction and service highlights.
- **About Us:** Company information and background.
- **Services:** Overview of the company's IT and digital services.
- **Service Details:** Dedicated pages for application development, software development, and digital marketing.
- **Portfolio:** Project showcase and individual project details.
- **Pricing:** Information about available service packages.
- **Blog:** Blog listing and individual article pages.
- **Careers:** Career opportunities and related information.
- **Contact:** Business contact page and email-handling functionality.
- **Admin Panel:** Dedicated administrative section.
- **Legal Pages:** Privacy policy, terms and conditions, cancellation policy, and refund policy.
- **Sitemap:** Website sitemap functionality.

## Technology Stack

- **Backend:** Core PHP
- **Frontend:** HTML, CSS, JavaScript
- **Web Server:** Apache-compatible configuration
- **Deployment:** Docker and Render configuration

## Project Structure

Website source lives at the repository root (no `in_php/` wrapper):

```text
Nazora-Tech/
├── admin/
├── assets/
├── includes/
├── storage/          # runtime data; mostly gitignored
├── tools/            # local QA helpers (optional)
├── index.php
├── about_us.php
├── service.php
├── portfolio.php
├── pricing.php
├── contact.php
├── sitemap.php
├── robots.php
├── Dockerfile
├── .htaccess
└── README.md
```

## Local development (XAMPP)

1. Clone or open this repository under `htdocs` (for example `C:\xampp\htdocs\SNF_TEAM`).
2. Run `composer install` in the repo root if `vendor/` is missing.
3. Open `http://localhost/SNF_TEAM/` (path matches the folder name under `htdocs`).
4. Optional secrets: copy `includes/config.local.example.php` to `storage/config.local.php` and fill real values only on your machine.

URL helpers (`site_url`, `site_base_path`) detect the subdirectory automatically, so local subfolder installs keep working without hardcoded `/in_php/` paths.

## Deployment

- Point the web document root at this repository root (`index.php`, `.htaccess`, `assets/`, etc.).
- On Hostinger, upload the site files into `public_html` (or the domain root), not into an `in_php/` subfolder.
- Prefer secrets in `nazora-config.php` one level above the web root, or in gitignored `storage/config.local.php`.
- The repository includes a Dockerfile for container-based deployment (domain-root document root).

Deployment settings, environment variables, storage permissions, and server requirements should be verified before publishing. Do not commit real SMTP passwords, API keys, or database files.

## Security

Keep database credentials, API keys, email credentials, customer information, and administrative secrets out of version control.

Review the administrative section, file uploads, contact forms, and access controls before production deployment.

Ignored by default: `vendor/`, `.env`, `storage/config.local.php`, `nazora-config.php`, SQLite databases, submission logs, and uploaded media under `storage/uploads/`.

## Project Status

Company website with ongoing development and maintenance.

## License / Ownership

Private company website for Nazora Tech / SK NADIM.
