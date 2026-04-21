# rgeometry-wp

Custom WordPress theme ported 1:1 from the Lovable reference at
[rgeometry.lovable.app](https://rgeometry.lovable.app/). Built as the first
proof of concept for GruffyGoat's Lovable-to-WordPress workflow.

## Stack

- Custom theme, no page builder, no Tailwind build step.
- Hand-written CSS mirroring the original Tailwind output so the theme stays
  editable without a Node toolchain.
- Advanced Custom Fields Pro for all editable content. Field groups live in
  `acf-json/` and auto-sync on admin load.
- Vanilla IntersectionObserver replaces Framer Motion's `ScrollReveal`. Same
  behavior, zero framework weight.
- Google Fonts: DM Sans (body) and Playfair Display (display).

## Requirements

- WordPress 6.2+
- PHP 7.4+
- Advanced Custom Fields Pro (active)

## Layout

```
rgeometry-wp/
  style.css            # theme header only
  functions.php        # bootstrap
  front-page.php       # one-pager entry
  header.php / footer.php
  index.php            # fallback
  inc/
    setup.php          # supports, menus, ACF guard
    enqueue.php        # CSS + JS + Google Fonts
    acf.php            # local JSON sync
    helpers.php        # rgeometry_field, placeholder URLs
  template-parts/
    section-hero.php
    (more sections land after Hero review)
  assets/
    css/theme.css      # tokens, base, components, sections, animations
    js/reveal.js       # ScrollReveal + smooth-scroll
  acf-json/
    group_hero.json    # Hero field group
```

## Status

**v0.1.0** — Hero section only. Remaining sections (Services, Projects, About,
Process, Testimonials, Contact, Footer, Navbar) queued pending fidelity review
on Hero.

## Deploy

Theme is deployed to a Cloudways sandbox for visual QA. Production deployment
flow is SFTP-based; see `DEPLOY.md` once that's written.
