# Nazora Tech — Brand Search Visibility Audit & Changes

**Site:** https://nazora.tech/  
**Brand:** Nazora Tech  
**Location:** Kestopur, Kolkata, West Bengal, India  
**Scope:** Local code changes only — not deployed. No ranking guarantees.

## Context

Google frequently autocorrects “Nazora Tech” / “Nazora Tech Kolkata” toward **Nazara Technologies** (a large, well-indexed Indian brand). That is largely an entity/authority problem outside on-page SEO alone. This work strengthens **exact brand + Kolkata** signals so Google can distinguish the correct entity when the site is re-crawled.

## Before (audit snapshot)

| Signal | Before |
|--------|--------|
| Title | `Nazora Tech \| Website, App & Software Development Company in Kolkata` |
| Meta description | Brand + Kolkata present; weaker “independent / Kestopur” entity wording |
| H1 | `Website, App & Software Development Company in Kolkata` (**no brand name**) |
| Canonical | `https://nazora.tech/` |
| Robots | `index, follow` |
| Organization `alternateName` | `Nazora TECH`, bare **`Nazora`** (ambiguous) |
| ProfessionalService | Brand/location OK; no `Nazora Tech Kolkata` alternate |
| Logo URL | `https://nazora.tech/assets/images/logo/nazora-logo.png` (200) |
| Header email label | Showed company name instead of email |
| Homepage shorthand | Several “Nazora” / “Nazora's” phrases without “Tech” |
| Incorrect spelling `Nazara` | **Not present** in site source |
| Social `sameAs` | No verified Facebook/Instagram/LinkedIn company URLs on site — **not invented** |
| Sitemap | 22 absolute `https://nazora.tech/...` URLs |
| Verified contacts | Phone `+91 9332016918`, WhatsApp `wa.me`, email `skonlineitsolution@gmail.com`, location Kestopur / Kolkata |

## After (local)

| Signal | After |
|--------|--------|
| Title | `Nazora Tech Kolkata \| Website, App & Software Development Company` |
| Meta description | Explicit **independent** studio in **Kestopur, Kolkata, West Bengal** |
| H1 | `Nazora Tech — Website, App & Software Development Company in Kolkata` |
| Hero lead | Natural brand + Kolkata sentence |
| FAQ | Added “Who is Nazora Tech?” |
| Schema | `alternateName`: `Nazora TECH`, `Nazora Tech Kolkata`; E.164 phone; Brand object; clearer PostalAddress; logo `contentUrl`/`caption` |
| Header/sidemenu email | Shows real email address |
| Homepage copy | Short brand forms expanded to **Nazora Tech** |
| Design / colors / layout | Unchanged (content/SEO only) |

## Changed files

1. `header.php` — homepage SEO map, Organization/ProfessionalService/WebSite schema, contact email labels  
2. `index.php` — H1, lead, brand wording, FAQ  
3. `tools/seo_phase3_audit.php` — brand/H1 checks  
4. `docs/BRAND_SEARCH_VISIBILITY_REPORT.md` — this report  

## Validation run

- `php -l header.php` / `index.php` — OK  
- `php tools/seo_phase3_audit.php` — sitemap 22 canonical URLs; brand/H1 checks updated  
- Local homepage sample: title/H1/canonical/robots OK; no `Nazara`; logo absolute URL in JSON-LD  

## What will **not** fix autocorrect by itself

- Google’s preference for the larger “Nazara” entity  
- Lack of Google Business Profile / Knowledge Panel (off-site)  
- Missing verified social profiles (do not invent them)  

**Recommended off-site next steps (manual, owner-owned):** create/claim Google Business Profile as “Nazora Tech” in Kolkata; publish verified LinkedIn/Facebook pages and then add them to `sameAs`; request indexing in Search Console after deploy; use exact brand quotes in citations.

## Deploy note

Do not push or deploy without approval. After deploy, re-check live title, H1, JSON-LD, and logo URL, then use Search Console URL inspection.
