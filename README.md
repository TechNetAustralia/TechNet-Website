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

## Adding content

If you're writing posts, editing pages, or changing images — not touching
code — see **[`CONTENT-GUIDE.md`](CONTENT-GUIDE.md)** instead of this file.
It covers logging into wp-admin, the Pages-vs-Posts distinction, setting
featured images, changing the homepage banner, and what not to touch.
Everything below here is for people working on the code/infrastructure.

## Branches

Day-to-day work happens on **`develop`** — that's what your local checkout
tracks and what `Update Site.command` pulls. Routine changes (design tweaks,
content templates, small fixes) get pushed straight to `develop`, no
PR/merge step needed each time. **`main`** is a slower-moving, reviewed
snapshot — `develop` gets merged into it periodically (a stable checkpoint,
or before ever considering a production cutover), not on every change.

## Daily workflow (start here)

Once you've done the one-time [local setup](#local-setup) below, this is all
you need day to day.

**No Terminal, please:** double-click **`Start Site.command`** and
**`Stop Site.command`** in the repo folder (Finder may warn about running a
downloaded script the very first time — click *Open* to allow it, it only
needs approving once). `Start Site.command` also checks Docker Desktop is
running and opens `http://localhost:8888` for you automatically. There's
also **`Update Site.command`**, a double-click alternative to "Fetch
origin" / "Pull origin" in GitHub Desktop.

The Terminal versions of the same steps, if you'd rather:

**Starting work:**

```bash
cd ~/Sites/TechNet-Website   # or wherever you cloned it
open -a Docker                # make sure Docker Desktop is running first
wp-env start
```

Wait for `WordPress development site started at http://localhost:8888`, then
open that URL. Nothing else needs re-running — theme/plugin activation and
demo content persist in the Docker volume between sessions.

**Finishing for the day:**

```bash
wp-env stop
```

This pauses the containers without losing any data (posts, settings,
uploads) — `wp-env start` next time picks up right where you left off.
Quitting Docker Desktop also stops it, but running `wp-env stop` first
is cleaner.

**If something feels broken** (weird errors, stale data you don't
recognise), the reset button is:

```bash
wp-env destroy   # wipes the database and uploads — you'll need to
                  # re-activate the theme/plugins and re-run seed-demo after
```

## Local setup

One-time setup on a new machine. Requires PHP 8.2+, [Composer](https://getcomposer.org),
[Node.js](https://nodejs.org) 20+, and Docker (for `@wordpress/env`).

```bash
git clone https://github.com/TechNetAustralia/TechNet-Website.git
cd TechNet-Website
git checkout develop              # this is the branch to work from — see "Branches" above
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
wp-env run cli -- wp technet seed-demo   # sample conference, 3 forums, speakers, sessions, members, 4 example posts
```

`wp technet seed-demo` already creates the Conference/Forums/NEATTS/NEATTS
Nominate/Member Directory/Documents/About pages with the right template
assigned to each — no manual page setup needed. Just, in wp-admin:

1. **Settings → Permalinks** — click Save once to flush rewrite rules for the
   custom post types (`tn_speaker`, `tn_session`, events, documents).
2. *(Optional)* **Appearance → Menus** — add the seeded pages to the primary
   nav menu. The theme falls back to a hardcoded Conference/NEATTS/Forums/About
   nav if you skip this, so it's not required to see the site working.
3. *(Optional)* **Memberships → Membership Levels** (Paid Memberships Pro) —
   `seed-demo` already creates a free "Member" level and puts the 5 sample
   users on it; only revisit this if you want to change what that level
   gates or grants.

## CI

`.github/workflows/ci.yml` runs on every push:

- PHP syntax check + `phpcs` (WordPress Coding Standards) across the theme
  and `technet-core`
- A full `wp-env` boot in a clean container: activates the theme and all
  plugins, runs `wp technet seed-demo`, and checks the homepage returns
  `200` with no PHP fatals in the debug log

This is the sandbox's pass/fail signal — treat a red CI run the same as a
broken staging site.

## Syncing changes from the design system

The theme is a hand-port of the [Claude Design System project](https://claude.ai/design)
— there's no build step or automated sync between the two, so this is a
manual (but quick) loop each time the design system changes:

> **Currently owed the other direction:** the site's move to an image-rich
> design (post/hero/speaker photos, a Card media variant) hasn't been
> reflected back into the design system project yet — writing to it needs
> a `/design-login` step that isn't available from every session. See
> [`docs/design-changelog.md`](docs/design-changelog.md) for the exact
> changes to apply there; delete that file once it's done.

1. **Make the change in the design system first** — at claude.ai/design,
   in the TechNet Australia Design System project (tokens, a component, or
   a UI kit page). Treat that project as the source of truth; don't
   hand-tweak a colour or spacing value directly in this repo without also
   updating it there, or the next sync will silently overwrite your change.
2. **Ask Claude Code to re-port it** — open a Claude Code session on this
   repo and describe what changed, e.g. *"I changed the accent green in the
   design system, pull it into the theme"* or *"the Card component in the
   design system now has a colored left border, update `inc/components.php`
   and `components.css` to match."* Claude reads the updated files from the
   design system project (via the `claude_design` MCP connection) and edits
   the matching files here:
   - `tokens/*.css` in the design system → `assets/css/tokens.css`
   - `components/**/*.jsx` → `inc/components.php` (PHP helper) +
     `assets/css/components.css` (styling)
   - `ui_kits/**/*.jsx` (page layouts) → the matching `page-*.php` /
     `front-page.php` / `single-tribe_events.php` template
3. **Preview locally** — same loop as any other change: save, hard-refresh
   `http://localhost:8888` (see [Daily workflow](#daily-workflow-start-here)
   above; `wp-env start` first if it's not already running).
4. **Commit** — routine changes go straight to `develop` (see
   [Branches](#branches)), so once it's pushed, `Update Site.command` is all
   you need to get it locally.

For anything more involved than a token tweak — a whole new component, a
new page in a UI kit — it's worth asking Claude to summarize the diff
between what's in the design system and what's currently ported before
making changes, so nothing gets missed.

## Explicitly out of scope

- No real TechNet Australia logo/brand assets — the design system was built
  without one supplied, so the header still uses the placeholder wordmark.
  Swap `template-parts/header.php` once real brand assets exist.
- No production cutover. Promoting this sandbox to replace the live WP 7.02
  site is a separate, manual decision — not automated by anything in this
  repo.
