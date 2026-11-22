# Nazora Tech — Header/Layout Repair Report

## 1. Exact root cause

### A. Broken logo (production Network evidence)
- `GET https://nazora.tech/assets/images/logo/nazora-logo.webp` → **404** (`Content-Type: text/html`)
- `GET https://nazora.tech/assets/images/logo/nazora-logo.png` → **200**
- Header used `<picture><source srcset="…nazora-logo.webp" type="image/webp">` so browsers selected WebP and rendered a broken image (`naturalWidth/Height = 0`, `currentSrc` = webp).
- Files: `header.php` (preloader ~L345, brand logo ~L408–411, mobile logo ~L423–426, sidemenu ~L505), `footer.php` (~L57).

### B. Floating buttons overlapping hero (CSS cascade)
- Load order: `main.css` → `nazora-home.css` → `nazora-theme.css`
- `nazora-home.css` correctly set floats to the **right** (`left:auto; right:18px`)
- `nazora-theme.css` then overrode with `left: 18px !important; right: auto !important` (lines ~661–665) — confirmed via `document.styleSheets` on production.
- Result: WhatsApp / Call Now stuck on the **left**, covering hero copy.

### C. Hero heading clipped / header too tall
- Broken logo + HTML `width="434" height="198"` inflated header/layout before/without reliable CSS sizing.
- Fixed header (`position:fixed`) + `--nz-header-h` padding is correct when logo is sized; left floats + oversized intrinsic logo made the hero look clipped.
- SEO Phase 3 `header.php` metadata/schema edits did **not** cause the visual break (diff was only blog seoMap + WebSite `about`).

### D. Production deploy gap (not fixed by local-only CSS)
- Production `index.php` still serves old H1: “Fueled by Data, Driven by Nazora…”
- Production CSS smaller/outdated vs local; `assets/vendor/remixicon/remixicon.css` → **404** on production.

## 2. Why the logo was broken
Missing WebP asset on Hostinger + `<picture>` preferring WebP over the working PNG.

## 3. Why the header layout changed
Cascade conflict (theme float left) + broken oversized logo + missing remixicon on production; contact row wrapping on mid widths.

## 4. Why the hero was clipped
Fixed header expected; broken/oversized logo + left-side floats covering copy made content unreadable / appear under the header.

## 5. Files modified (local)
- `header.php` — PNG-only logos; display attrs 140×64
- `footer.php` — PNG logo
- `assets/css/nazora-theme.css` — floats right; nowrap navbar/contacts; mobile icon floats; header height token; menu margin
- `assets/css/nazora-home.css` — aligned float/header tokens; hero padding-right clearance

## 6. Screenshots
- Before (production): `tools/layout_qa/before-production-desktop.png`
- After (local desktop capture): `tools/layout_qa/after-local-desktop-1440.png`

## 7. Local test results (CDP metrics)
| Width | Logo | Header≈ | H1 clear | Float side | Overlap H1 | Overflow-X |
|------|------|---------|----------|------------|------------|------------|
| 390 | OK 140×56 | 83 / token 84 | yes | right (icon 48) | no | no |
| 1440 | OK | 129 | yes | right | no | no |

Desktop nav: Home / Pages / Portfolio / Contact + Get a Quote present. Single floating-contact block. Mobile hamburger present (`Open navigation`).

## 8. SEO regression (local) — preserved
- Title: Nazora Tech | Website, App & Software Development Company in Kolkata
- Canonical: `https://nazora.tech/`
- H1: Website, App & Software Development Company in Kolkata
- robots: index, follow
- JSON-LD: Organization, ProfessionalService, WebSite
- robots.txt Sitemap → https://nazora.tech
- sitemap.xml: 22 absolute https://nazora.tech URLs; no localhost
- Phase 3 blog seoMap + WebSite `about` retained

## 9. Production deployment instructions (DO NOT auto-deploy)
Upload/replace these paths on Hostinger document root (keep backup first):

1. `header.php`, `footer.php`, `index.php` (index still outdated on prod — required for SEO H1)
2. `assets/css/nazora-theme.css`, `assets/css/nazora-home.css`
3. `assets/images/logo/nazora-logo.png` (confirm present; WebP optional now)
4. Entire `assets/vendor/remixicon/` (css + woff/woff2) — currently 404 on live
5. Purge Hostinger/CDN/browser cache
6. Verify live:
   - Logo URL 200 (PNG)
   - Remixicon CSS 200
   - Floats on right
   - H1 matches SEO string
   - Canonical still https://nazora.tech/
   - robots.txt + sitemap.xml unchanged intent

**Stop condition:** Local layout verified. Production not claimed fixed until the deploy above is completed and re-checked on https://nazora.tech.
