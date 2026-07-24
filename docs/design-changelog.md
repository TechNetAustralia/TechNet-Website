# Design-system changelog — image-rich pivot

The website (this repo) now includes changes that aren't reflected back in
the source [Claude Design System project](https://claude.ai/design/p/63fe9c91-9d03-4716-b628-c400d0c6b028)
yet, because writing to that project needs a `/design-login` step that
isn't available from every session. This document is the precise list of
what changed, so it can be applied there either by you directly, or by
pasting the relevant section into a Claude Design chat and asking it to
make that change to the project.

Once applied there, this file can be deleted — it's a to-do list, not
permanent documentation. (See README's "Syncing changes from the design
system" for the normal direction of travel: design system → website. This
document is for the reverse, one-time catch-up.)

## Why this changed

The original design system was built with **no photography or imagery
supplied**, and documented that as a deliberate choice ("flat colour only
... the 'clean, simple, timeless' brief argues against inventing imagery" —
`readme.md`, Visual foundations). That's no longer the direction: TechNet
wants an image-rich site — banner images on posts, a photo-backed homepage
hero, speaker photos — with images easy for non-technical staff to manage
through the CMS. This is a deliberate, requested reversal of that original
constraint, not a mistake to revert.

## 1. `readme.md` — Visual foundations

Replace the "Backgrounds: flat colour only..." line with something like:

> **Backgrounds:** photography is now used deliberately in specific,
> bounded slots — the homepage hero banner, post/news featured images, and
> speaker photos — always with the same treatment (a dark navy overlay
> gradient at ~62% opacity when text sits on top, so legibility stays
> consistent regardless of the photo). Flat colour remains the default for
> everything else (cards, sections without a natural photo, forms) — this
> is additive, not a wholesale move to a photography-driven brand.

Also update the "Imagery" line ("none supplied... do not invent
photography") — imagery is now supplied per-instance by editors through the
CMS, so this should note that images are user-supplied content, not
part of the fixed design system (same category as page copy), and that any
*empty* image slot should still degrade to the original flat-colour
treatment (this behavior is already implemented site-side — every image
slot has a graceful no-image fallback, never a broken image).

## 2. Card component — new media variant

`components/core/card/Card.jsx` currently only supports flat content
children. Add an optional image slot:

```jsx
export function Card({ children, padded = true, image, imageAlt, style }) {
  return (
    <div style={{ background: 'var(--surface-card)', border: '1px solid var(--border-subtle)', borderRadius: 'var(--radius-md)', boxShadow: 'var(--shadow-sm)', overflow: 'hidden', ...style }}>
      {image && <img src={image} alt={imageAlt || ''} style={{ display: 'block', width: '100%', aspectRatio: '16/9', objectFit: 'cover' }} />}
      <div style={{ padding: padded ? 'var(--space-5)' : 0 }}>{children}</div>
    </div>
  );
}
```

(The website's PHP port of this is `technet_media_card()` in
`wp-content/themes/technet-australia/inc/components.php` plus the
`.tn-card--media` / `.tn-card__image` classes in `components.css` — port
back from those if it's easier than reading the description above.)

Add a `card.card.html` preview variant showing the image-topped card, and
update `Card.prompt.md` to document the new `image`/`imageAlt` props.

## 3. New pattern — post/news card grid

Not a new *component*, but a new *composition* worth adding to the
`marketing-site` UI kit: a 3-up grid of media cards (image, date badge,
title, excerpt) for a "Latest updates" section — this is now live on the
homepage, sourced from a blog/posts pattern the original UI kit didn't
have (the design system predates any News/Updates content type). See
`front-page.php`'s "Latest updates" section for the exact composition.

## 4. Speaker photos

`SpeakersPage.jsx`'s speaker cards used a fixed neutral placeholder circle
(`background: var(--surface-sunken)`) for every speaker, since no photos
were available. That's now a real photo when one's supplied, falling back
to the same placeholder circle when not — i.e. purely additive, the
placeholder behavior is unchanged as a fallback. No component code change
needed here, just worth reflecting in the UI kit's own copy/notes so it
doesn't read as "no photography, ever."

## 5. Hero banner treatment (reference, already page-specific)

`Home.jsx`'s hero section was flat `var(--surface-sunken)`. The website
adds an optional photo-backed variant: a dark navy gradient overlay
(`linear-gradient(rgba(11,31,48,0.62), rgba(11,31,48,0.62))`) layered over
the photo, with hero text flipping to white/light-neutral for contrast.
Worth adding as a documented hero variant in the `marketing-site` UI kit
rather than leaving it website-only, since it's a real, reusable pattern
now.
