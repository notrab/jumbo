---
description: Complete Jumbo sites to read, run and copy.
---

# Examples

Each example is a small, complete site in the [`examples`](https://github.com/notrab/jumbo/tree/main/examples) folder. Run one with:

```bash
cd examples/blog
composer install
vendor/bin/jumbo dev
```

:::: cards
::: card title="Blog" href="https://github.com/notrab/jumbo/tree/main/examples/blog"
Posts from Markdown, pagination, tag pages, an RSS feed, a sitemap, highlighted code, responsive images and a component with its own CSS.
:::
::: card title="Contact form" href="https://github.com/notrab/jumbo/tree/main/examples/contact-form"
Page actions with validation, errors, a redirect after posting, two forms on one page and a `fetch()` that stays on the page.
:::
::: card title="Multilingual" href="https://github.com/notrab/jumbo/tree/main/examples/multilingual"
Three locales, translated content with fallbacks, a language switcher and hreflang links.
:::
::: card title="Static hosting" href="https://github.com/notrab/jumbo/tree/main/examples/static-hosting"
Redirects, `.html` file builds, build hooks that write `_redirects` and `_headers`, and previewing a build.
:::
::: card title="bunny.net sites" href="https://github.com/notrab/jumbo/tree/main/examples/bunny-sites"
A built site deployed with `bunny sites`, with a `bunny.jsonc` and a GitHub Actions workflow.
:::
::: card title="Bunny Optimizer" href="https://github.com/notrab/jumbo/tree/main/examples/bunny-optimizer"
The Bunny images loader, so Bunny Optimizer resizes, converts and compresses images on the CDN instead of the build.
:::
::: card title="Magic Containers" href="https://github.com/notrab/jumbo/tree/main/examples/bunny-magic-containers"
A guestbook with page actions, running on bunny.net Magic Containers with FrankenPHP's worker mode and a persistent volume.
:::
::::

## The starter

`jumbo new` copies the starter: a home page, an about page in Markdown, a blog with an RSS feed, a layout and a component. It's in [`stubs/starter`](https://github.com/notrab/jumbo/tree/main/stubs/starter) if you'd like to read it first.

## These docs

This site is built with Jumbo too. The [`docs`](https://github.com/notrab/jumbo/tree/main/docs) folder has a themed home page, a sidebar from config, components for callouts, tabs, steps and cards, and Tailwind CSS built before each build. Copy it to start docs for your own project.
