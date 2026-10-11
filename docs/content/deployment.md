---
description: Put a built site on any static host, GitHub Pages or bunny.net, or run it on a PHP server.
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

## bunny.net

[bunny.net](https://bunny.net) can host a built site on its CDN, or run the server in a container with [Magic Containers](https://bunny.net/magic-containers/). Both use the [bunny CLI](https://github.com/BunnyWay/cli):

```bash
npm install -g @bunny.net/cli
bunny login
```

### Static sites

Build the site and deploy `dist/`:

```bash
vendor/bin/jumbo build --url=https://acme.dev
bunny sites deploy dist
```

The first deploy creates the site, a storage zone with a pull zone in front, and links the folder to it. Every deploy after that goes live straight away, and `bunny sites deployments publish --previous` rolls back to the one before. Jumbo writes `404.html`, which the site serves for missing pages.

To build and deploy with one command, put both in a `bunny.jsonc` next to `app.php`:

```jsonc
{
  "sites": {
    "name": "acme",
    "dir": "./dist",
    "build": "vendor/bin/jumbo build --url=https://acme.dev"
  }
}
```

```bash
bunny sites deploy --build
bunny sites domains add acme.dev
```

To deploy on every push, add an API key from your bunny.net account as the `BUNNYNET_API_KEY` secret in the repo, and a workflow:

```yaml
# .github/workflows/bunny.yml
name: Deploy

on:
  push:
    branches: ["main"]
  workflow_dispatch:

concurrency:
  group: bunny-sites
  cancel-in-progress: false

jobs:
  deploy:
    runs-on: ubuntu-latest
    permissions:
      contents: read
      deployments: write
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: "8.3"
      - run: composer install --no-progress
      - run: vendor/bin/jumbo build --url=https://acme.dev
      - uses: BunnyWay/actions/deploy-site@deploy-site_0.1.0
        with:
          site: acme
          directory: dist
          api_key: ${{ secrets.BUNNYNET_API_KEY }}
```

Turn on Bunny Optimizer for the site's pull zone and set the images loader to `new Bunny()` to have the CDN resize images instead of the build. See [Images](/docs/images#bunnynet).

### Magic Containers

Sites with actions, or pages with `prerender: false`, can run in a container on Magic Containers, with bunny.net's CDN in front. Add the `index.php` front controller [above](#a-php-server), then a `Caddyfile` for [FrankenPHP](https://frankenphp.dev) that sends every request to it and keeps the site in memory with worker mode:

```caddyfile
{
	frankenphp {
		worker /app/index.php
	}
}

:8080 {
	root * /app
	rewrite * /index.php
	php
}
```

And a `Dockerfile`:

```dockerfile
FROM dunglas/frankenphp:php8.3-alpine

RUN install-php-extensions gd exif
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Swap the image's welcome page for the site
WORKDIR /app
RUN rm -rf /app/*
COPY . .
RUN composer install --no-dev --optimize-autoloader

COPY Caddyfile /etc/frankenphp/Caddyfile
ENV DUMBO_ENV=production
```

Leave `vendor/`, `dist/` and `node_modules/` out of the image with a `.dockerignore`, then deploy:

```bash
bunny apps deploy --dockerfile --port 8080
```

The first deploy asks for a name, region and container registry, and writes a `bunny.jsonc` to commit. After that, `bunny apps deploy --dockerfile` builds and ships each new version.

Containers start with an empty disk, so resized images are made again after each deploy. The CDN keeps the copies it has already served, or turn on Bunny Optimizer for the app's pull zone and use the [Bunny images loader](/docs/images#bunnynet) so the container never resizes them at all.
