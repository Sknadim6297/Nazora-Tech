# Nazora Tech — SEO Phase 3 Report

**Date:** local audit (no production deploy)  
**Scope:** Homepage H1, robots, sitemap, metadata, JSON-LD, crawl hygiene  
**Constraint:** Theme, branding, SMTP, admin auth, payments, and production data unchanged

---

## 1. Issues found (severity)

| Severity | Area | Issue | Status |
|----------|------|-------|--------|
| High | Blog crawl | `blog.php` / `blog_details.php` redirected to `/index` (extra hop) and lacked `noindex` on redirect | **Fixed** — 301 to `/` + `X-Robots-Tag: noindex, follow` |
| Medium | robots.txt | Private paths (`includes`, `tools`, unpublished blog) not listed; upload allow-rule needed clearer comments | **Fixed** |
| Medium | storage | No directory-level `.htaccess` under `storage/` / `storage/uploads/` (relied only on root rewrite) | **Fixed** |
| Medium | meta | `blog.php` absent from `header.php` SEO map (blog unpublished, but map incomplete) | **Fixed** (titles ready for future) |
| Low | JSON-LD | WebSite node lacked `about` → Organization link | **Fixed** |
| Info | H1 | Homepage primary H1 already correct | **Verified — no change** |
| Info | Sitemap | 22 absolute `https://nazora.tech/...` URLs; no `/index` | **Verified** |
| Info | Blog | Public blog is intentionally unpublished and not in sitemap (matches Phase 2 count of 22) | **Documented** |

No invented reviews, ratings, or social `sameAs` profiles were added.

---

## 2. Files changed

| File | Change |
|------|--------|
| `robots.php` | Clearer Allow/Disallow for admin, includes, submissions, vendor, tools, blog, storage + uploads |
| `storage/.htaccess` | Deny all private storage by default |
| `storage/uploads/.htaccess` | Allow public media; block script extensions |
| `blog.php` | 301 to site root + `noindex` (was redirecting via `/index`) |
| `blog_details.php` | Same as blog listing |
| `header.php` | Blog SEO map entry; WebSite JSON-LD `about` |
| `tools/seo_phase3_audit.php` | Local CLI audit helper |
| `docs/SEO_PHASE3_REPORT.md` | This report |

---

## 3. Before / after SEO improvements

### Homepage H1
| | Before | After |
|--|--------|-------|
| Primary H1 | `Website, App & Software Development Company in Kolkata` | **Unchanged** (already correct) |
| Brand line | `<p class="nz-hero-brand">Nazora Tech</p>` | **Preserved** |

### robots.txt
| Before | After |
|--------|-------|
| Allow `/`, Allow uploads, Disallow admin/storage/send_email | Same core rules + Disallow `includes`, `submissions`, `vendor`, `tools`, `blog`; clearer comments |

### Blog redirects
| Before | After |
|--------|-------|
| Redirect to `…/index` (then another hop to `/`) | Single **301** to `/` + `X-Robots-Tag: noindex, follow` |

### JSON-LD (homepage)
| Type | Status |
|------|--------|
| Organization | Present — name, logo, email, phone, address (no fake social/ratings) |
| ProfessionalService | Present — services, areaServed, priceRange ₹₹ |
| WebSite | Present — now includes `about` → Organization |

### Sitemap (22 URLs)
All locs are absolute HTTPS on `https://nazora.tech` (non-www):

1. `/`  
2. `/service`  
3. `/pricing`  
4. `/portfolio`  
5. `/about_us`  
6. `/team`  
7. `/careers`  
8. `/contact`  
9. `/app_development_details`  
10. `/software_development_details`  
11. `/service_details`  
12. `/digital_marketing_details`  
13. `/privacy_policy`  
14. `/terms_and_conditions`  
15. `/refund_policy`  
16. `/cancellation_policy`  
17–22. Portfolio detail slugs (`rc-logistics`, `clinic-connect`, `ledger-desk`, `restaurant-food-drinks-logo`, `minimal-fashion-mark`, `esports-eagle`)

---

## 4. PHP lint & local test results

```text
php -l robots.php          → No syntax errors
php -l blog.php            → No syntax errors
php -l blog_details.php    → No syntax errors
php -l header.php          → No syntax errors
php tools/seo_phase3_audit.php    → Sitemap count 22; remaining notes are informational
```

Local HTTP probes (XAMPP) may vary by vhost; Phase 2 production checks already passed for 200 / sitemap / robots / redirects.

---

## 5. Production deployment checklist (manual — do not auto-deploy)

1. Upload only the changed files listed in section 2 (exclude `tools/` from public crawl if present on host).
2. Confirm `storage/.htaccess` and `storage/uploads/.htaccess` deploy with correct permissions.
3. Verify production:
   - `https://nazora.tech/` → 200, one H1, brand tagline visible  
   - `https://nazora.tech/robots.txt` → new Disallow/Allow rules  
   - `https://nazora.tech/sitemap.xml` → still 22 absolute URLs  
   - `https://nazora.tech/blog` → **301** to `https://nazora.tech/` with noindex header  
   - `https://nazora.tech/storage/` → **403**  
   - `https://nazora.tech/storage/uploads/` media → **200** for real image files  
4. Re-check homepage View Source for Organization / ProfessionalService / WebSite JSON-LD.
5. Do **not** change SMTP, admin passwords, payment settings, or live customer data.
6. Submit sitemap refresh in Google Search Console after deploy (optional).
7. Monitor Coverage / Page indexing for soft-404s on former blog URLs.

---

## 6. One H1 per page (verified in source)

Public pages use a single breadcrumb/page H1. Homepage uses one hero H1. Admin pages are noindex via `/admin/` robots Disallow. Email templates contain H1 only in outbound mail HTML (not site pages).
