# Editing the TechNet Australia site — a guide for content editors

This guide is for anyone adding or updating **content** (text, images, events)
through wp-admin — no code, git, or Terminal involved. If you're setting up
the development sandbox itself, see `README.md` instead; this document
assumes someone already has the site running and you're logging into
wp-admin.

Everything below applies the same way whether you're editing the local
sandbox (`http://localhost:8888/wp-admin`) or, eventually, the live site —
WordPress editing works identically either way. Only the web address and
login details change.

## Pages vs. Posts — the one thing worth understanding first

WordPress has two different kinds of content, and they behave differently:

- **Pages** are the site's fixed structure: Home, Conference, Forums, NEATTS,
  Member Directory, Documents, About. Each one has a specific job and (for
  most of them) a specific look built into the theme — you're editing the
  *content* of a page, not its layout.
- **Posts** are timestamped news/updates — announcements, event recaps,
  "nominations are open," that kind of thing. Posts are what shows up in the
  **"Latest updates"** section on the homepage, newest first. If you want
  something to appear there, write a **Post**, not a Page.

If you're ever unsure which one you need: "does this need a fixed spot in
the site's navigation?" → Page. "Is this a dated announcement?" → Post.

## Logging in

Go to `/wp-admin` on whichever URL you're working on (e.g.
`http://localhost:8888/wp-admin` for the local sandbox) and log in with your
account. You'll land on the **Dashboard**.

## Writing a News & Update post

This is the most common thing you'll do. Four sample posts are already in
the site (from `wp technet seed-demo`) — open one of those first to see a
worked example of the pattern before writing your own.

1. **Posts → Add New** (left sidebar).
2. Type a title. Keep it plain and specific — "Registrations open for
   TechNet 2027", not "Big news!!!" (see [Voice](#voice-a-quick-note), below).
3. Write the body using the block editor — type normally for paragraphs,
   or type `/` to insert a heading, image, list, etc.
4. **Set a featured image — every post should have one.** This is what
   shows on the homepage card and at the top of the post itself:
   - In the right-hand sidebar, find **Featured Image** (if you don't see
     the sidebar, click the gear icon ⚙️ top-right).
   - Click **Set featured image**.
   - **Upload files** (drag a photo in, or browse) or pick something
     already in **Media Library**.
   - See [Image guidelines](#image-guidelines) below for sizing.
5. *(Optional but recommended)* Add an **Excerpt** in the sidebar — a
   one-sentence summary. This is what shows under the title on the
   homepage card. If you skip it, WordPress auto-generates one from your
   first paragraph, which is usually fine too.
6. Click **Publish** (top-right), then **Publish** again to confirm.

That's it — it'll appear on the homepage automatically, newest post first,
no other steps needed.

## Editing an existing page

1. **Pages** (left sidebar) → find the page (e.g. "Conference," "About") →
   click its title.
2. Edit the content the same way as a post — click into text and type, or
   `/` for blocks.
3. Click **Update** (top-right).

A few pages (Conference, Forums, NEATTS, Member Directory, Documents) pull
some of their content automatically from other places (events, speakers,
member data) rather than from what you type in the page editor — for those,
the page editor content is mainly used for the intro text. If you're not
sure whether a page is "fully editable" or "partly automatic," ask before
assuming a text change will show up somewhere it isn't.

## Changing the homepage banner image

**Appearance → Customize → Homepage** → **Hero banner image** → upload or
select an image → you'll see a live preview → click **Publish** (top-left)
to make it live.

If you leave this empty, the hero just shows as a plain flat-colour section
— no broken image, nothing looks wrong either way.

## Adding conference speakers and schedule sessions

These live under their own admin menu items, not as a page you edit:

- **Speakers** (left sidebar) → **Add speaker** → name as the title, fill in
  role/institution/which conference event they're speaking at, and set a
  **Featured Image** (their photo) the same way as a post.
- **Sessions** (left sidebar) → **Add session** → fill in day label, time
  (24-hour, like `09:00`, so sessions sort in order), track/tag, and which
  event.

Both automatically show up on that conference's page once saved — no
further action needed.

## Image guidelines

| Where | Recommended size | Notes |
|---|---|---|
| Homepage hero banner | at least 1600×700px, wide | Dark overlay is applied automatically so text stays readable — busy photos work fine |
| Post featured image | around 1200×675px (16:9) | Shows on homepage cards and at the top of the post |
| Speaker photo | roughly square, 400×400px+ | Displayed in a circle — center the face |

General rules of thumb:
- Keep files under ~1MB where you can (right-click → check file size, or
  resize/export at a lower quality before uploading) — large images slow
  the site down for visitors.
- Only use images you actually have the right to use — your own photos, or
  stock images you've licensed. Not a random Google Images result, and not
  an unpurchased stock preview (those usually have a watermark or ID number
  visible if you look closely).
- No image is always fine — every image slot on this site degrades
  gracefully to a plain flat-colour block if left empty. Better to skip an
  image than use one you don't have rights to.

## Voice — a quick note

TechNet's writing style, in short: plain and institutional, like a
professional association writing to peers — not a startup selling
something. No hype adjectives ("game-changing," "revolutionary"), no emoji,
sentence case in headings ("Registrations open," not "Registrations Open"
or "REGISTRATIONS OPEN"). State numbers and facts plainly — "540+ members,"
not "an incredible 540+ members!"

## What not to touch

If you're a content editor rather than a developer, steer clear of:

- **Plugins** (Paid Memberships Pro, The Events Calendar, WP Document
  Revisions settings) — these control membership gating, event structure,
  and document access. Changing settings here can lock people out of
  content or break pages.
- **Appearance → Theme File Editor** / any "Editor" that shows code — this
  site's theme is managed through git (see `README.md`), not edited live.
  Changes made this way wouldn't survive the next update anyway, since the
  whole theme gets overwritten by the next `git pull`.
- **Settings → Permalinks** — already configured correctly; changing it can
  break every link on the site until it's fixed.

If something needs a structural change (a new page template, a new kind of
content, a layout tweak) rather than just new text/images, that's a
developer task — ask in the Claude Code session working on this repo rather
than trying to work around it in wp-admin.
