---
description: Install Jumbo, start a site from the starter and run it.
---

# Quickstart

Jumbo needs PHP 8.3 or later, Composer, and the GD extension if you want resized images.

## Make a site

Install Jumbo globally for the `jumbo` command, then copy the starter into a new folder:

```bash
composer global require notrab/jumbo
jumbo new my-site
cd my-site
composer require notrab/jumbo
```

To add Jumbo to a project you already have, run `composer require notrab/jumbo` in it and create an `app.php`.

## Run it

```bash
vendor/bin/jumbo dev
```

Open [http://localhost:8000](http://localhost:8000). The page reloads each time you save a file. The starter also has `composer dev`, `composer build` and `composer preview` scripts that run the same commands.

## The files

```
app.php       Returns the site
pages/        One file per route
views/        Layouts and templates
components/   Markdown components
content/      Markdown to load in pages
public/       Files served as-is
```

`app.php` returns a `Site`, with your config:

```php
<?php // app.php

require __DIR__ . "/vendor/autoload.php";

return new Dumbo\Jumbo\Site(__DIR__, [
    "name" => "Acme",
    "url" => "https://acme.dev",
]);
```

The Dumbo app is `$site->app`, so you can add middleware and API routes to it after creating the site.

## Add a page

Create `pages/hello.md`:

```markdown
---
layout: page
---

# Hello

This page is at /hello.
```

Visit `/hello` and it renders inside `views/page.php`. [Pages](/docs/pages) covers PHP templates and routes with params.

## Build it

```bash
vendor/bin/jumbo build     # Write every page to dist/
vendor/bin/jumbo preview   # Serve dist/ like a static host, on localhost:4000
vendor/bin/jumbo routes    # List the pages and how each is rendered
```

Upload `dist/` to any static host. [Deployment](/docs/deployment) has the details, including GitHub Pages and running the site on a PHP server.
