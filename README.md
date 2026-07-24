# TechNet Australia — website (sandbox rebuild)

A from-scratch WordPress rebuild of the TechNet Australia site, built from the
[TechNet Australia Design System](https://claude.ai/design) (tokens, components,
and marketing/conference/member-directory UI kits). This is a **sandbox** —
it is not connected to the live TechNet Australia WordPress site in any way.
Nothing here touches production hosting or DNS.

## What's here

- `wp-content/themes/technet-australia` — custom classic PHP theme implementing
  the design system (navy/green palette, IBM Plex Sans + Mono, 4px spacing
  scale) as plain PHP templates + CSS custom properties. No build step, no
  React at runtime — the design system's JSX components are ported 1:1 to PHP
  helper functions in `inc/components.php`.
- `wp-content/plugins/technet-core` — small first-party plugin holding data
  that shouldn't live in the theme: speaker/session custom post types,
  conference registration + NEATTS nomination form handling, member profile
  fields, and a `wp technet seed-demo` WP-CLI command for demo content.
- Third-party plugins (not committed, pulled in by Composer):
  - **Paid Memberships Pro** — membership levels, gates the member directory
    and document library. Not on wpackagist, so this comes from its own
    Packagist package (`strangerstudios/paid-memberships-pro`) instead.
  - **The Events Calendar** — the annual conference + 3 regional forums
    (via wpackagist)
  - **WP Document Revisions** — institutional document library

## Local setup

Requires PHP 8.2+, [Composer](https://getcomposer.org), [Node.js](https://nodejs.org)
20+, and Docker (for `@wordpress/env`).

```bash
composer install                 # pulls PMP / The Events Calendar / WP Document Revisions
npm install -g @wordpress/env    # one-off, or use `npx @wordpress/env` below
wp-env start                     # boots WordPress + MySQL in Docker, mounts theme + plugins
```

Site is then at **http://localhost:8888** (admin: http://localhost:8888/wp-admin,
user `admin` / pass `password` — wp-env defaults).

First-time activation (wp-env's CLI container has WP-CLI preinstalled):

```bash
wp-env run cli -- wp theme activate technet-australia
wp-env run cli -- wp plugin activate technet-core paid-memberships-pro the-events-calendar wp-document-revisions
wp-env run cli -- wp technet seed-demo   # sample conference, 3 forums, speakers, sessions, members
```

Then, in wp-admin:

1. **Memberships → Membership Levels** (Paid Memberships Pro) — create one
   free "Member" level if `seed-demo` didn't already. This is what gates the
   member directory and document library.
2. **Settings → Permalinks** — click Save once to flush rewrite rules for the
   custom post types (`tn_speaker`, `tn_session`, events, documents).
3. **Pages** — create pages titled *Conference*, *Forums*, *NEATTS*, *NEATTS
   Nominate*, *Member Directory*, *Documents*, *About* and set each one's
   **Template** (Page Attributes panel) to the matching template — the theme
   ships `page-conference.php`, `page-forums.php`, `page-neatts.php`,
   `page-neatts-nominate.php`, `page-member-directory.php`,
   `page-documents.php`. Add them to the primary nav menu (**Appearance →
   Menus**).

To stop the sandbox: `wp-env stop`. To reset it entirely: `wp-env destroy`.

## CI

`.github/workflows/ci.yml` runs on every push:

- PHP syntax check + `phpcs` (WordPress Coding Standards) across the theme
  and `technet-core`
- A full `wp-env` boot in a clean container: activates the theme and all
  plugins, runs `wp technet seed-demo`, and checks the homepage returns
  `200` with no PHP fatals in the debug log

This is the sandbox's pass/fail signal — treat a red CI run the same as a
broken staging site.

## Design system fidelity

The theme intentionally reuses the design system's exact CSS custom
properties and component prop shapes (see `inc/components.php` docblocks vs.
the original `components/*.jsx`), so visual changes should generally be made
by updating the Claude Design System project first and re-porting the
relevant token/component files here, rather than hand-tweaking colours or
spacing directly in the theme.

## Explicitly out of scope

- No real TechNet Australia logo/brand assets — the design system was built
  without one supplied, so the header still uses the placeholder wordmark.
  Swap `template-parts/header.php` once real brand assets exist.
- No production cutover. Promoting this sandbox to replace the live WP 7.02
  site is a separate, manual decision — not automated by anything in this
  repo.
