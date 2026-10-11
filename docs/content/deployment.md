---
description: Put a built site on any static host or GitHub Pages, or run it on a PHP server.
---

# Deployment

A built site is plain files, so any static host can serve it: Netlify, Cloudflare Pages, GitHub Pages, S3, bunny.net or a folder on a web server.

```bash
vendor/bin/jumbo build --url=https://acme.dev
```

Upload `dist/`. Set `url` (in config or with `--url`) so canonical links, the sitemap and feeds have absolute URLs.

## GitHub Pages

GitHub Pages serves project sites from a sub-path, like `https://you.github.io/acme`. Put the path in the URL and Jumbo adds it to every link:

```yaml
# .github/workflows/pages.yml
name: Pages

on:
  push:
    branches: ["main"]

permissions:
  contents: read
  pages: write
  id-token: write

jobs:
  deploy:
    runs-on: ubuntu-latest
    environment:
      name: github-pages
      url: ${{ steps.deployment.outputs.page_url }}
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: "8.3"
      - id: pages
        uses: actions/configure-pages@v5
      - run: composer install --no-progress
      - run: vendor/bin/jumbo build --url=${{ steps.pages.outputs.base_url }}
      - uses: actions/upload-pages-artifact@v3
        with:
          path: dist
      - id: deployment
        uses: actions/deploy-pages@v4
```

Set the repo's Pages source to GitHub Actions in its settings. These docs deploy the same way.

## A PHP server

Pages with actions, or with `prerender: false`, need PHP to run. Add a front controller next to `app.php`:

```php
<?php // index.php

(require __DIR__ . "/app.php")->run();
```

Then send every request to `index.php` with `DUMBO_ENV=production` set. Jumbo serves `public/` itself, so the web server doesn't need to serve any files. With Nginx and PHP-FPM:

```nginx
location / {
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root/index.php;
    fastcgi_param DUMBO_ENV production;
    fastcgi_pass unix:/run/php/php8.3-fpm.sock;
}
```

Turn on OPcache, and give `images.cache` a folder that survives deploys so resized images aren't made twice. Dumbo's [deployment guide](https://notrab.github.io/dumbo/docs/deployment) covers Apache, FrankenPHP and Docker, and all of it applies to a Jumbo site.

You can also mix the two: build the static pages for a CDN, and run the server for the pages that need it.
