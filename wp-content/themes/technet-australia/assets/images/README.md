# Theme images

Drop a homepage hero banner image here named exactly:

```
hero-banner.jpg   (or .jpeg / .png / .webp)
```

`technet_hero_banner_url()` in `functions.php` picks it up automatically —
no code change needed, just add the file, commit, and push. If no file is
present, the hero renders as a plain flat-colour section (no broken image,
no missing-banner gap).

A wide image (at least ~1600px) that reads reasonably well with a dark navy
overlay across it works best, since the hero text renders in white over a
semi-transparent navy wash for legibility.
