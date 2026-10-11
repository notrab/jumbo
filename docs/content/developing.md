---
description: Run the dev server with live reload, read errors, and list your routes.
---

# Developing

```bash
vendor/bin/jumbo dev
```

That serves the site on [http://localhost:8000](http://localhost:8000) with PHP's built-in server, rendering each page as it's asked for. Pick another address with `--host` and `--port`:

```bash
vendor/bin/jumbo dev --host=0.0.0.0 --port=3000
```

## Live reload

Open pages reload when any file in the project changes, skipping `vendor/`, `node_modules/` and `dist/`. Turn it off with `--no-reload`.

The dev server runs four PHP workers, so the reload checks never hold up a page. Set `PHP_CLI_SERVER_WORKERS` to change that.

## Errors

An uncaught exception in development shows the error, the stack trace and the code around it, and the page still reloads once you fix it.

In production, Jumbo logs the error and renders `pages/500.php` with the exception as `$error`, or a plain "Internal Server Error" without one. Set `DUMBO_ENV=production` to see what visitors get. To handle errors yourself, call `$site->app->onError()` after creating the site.

## Listing routes

```bash
vendor/bin/jumbo routes
```

```
  /                            prerendered            pages/index.php
  /blog/{slug}                 prerendered            pages/blog/[slug].php
  /search                      on demand              pages/search.php
  /contact                     prerendered + actions  pages/contact.php
  /old                         redirect (301)         /new
```

Each page shows how it's rendered: built ahead of time, on demand by the server, or with actions that need the server for forms.
