---
description: Serve files from public/ with cache-busting URLs, and load files from npm packages.
---

# Files and packages

Everything in `public/` is served at the same path, so `public/style.css` is `/style.css`. Builds copy the whole folder into `dist/`.

## Cache-busting URLs

`$site->file()` adds a hash of the file's contents to its URL:

```php
<link rel="stylesheet" href="<?= e($site->file("/style.css")) ?>">
```

```html
<link rel="stylesheet" href="/style.css?v=1a2b3c4d">
```

The URL changes whenever the file does, so Jumbo tells browsers and CDNs to cache versioned URLs for a year. A file that doesn't exist gives a warning and its plain path.

## Files from npm packages

`$site->asset()` loads a file from a package in `node_modules`:

```php
<link rel="stylesheet" href="<?= e($site->asset("@fontsource-variable/inter", "index.css")) ?>">
```

In development it points at a CDN, so the page loads without an install step. In production and in builds it points at `/vendor/...`, which Jumbo serves from `node_modules`. The version comes from `package-lock.json`, so both load the same file.

Builds copy each package file a page uses into `dist/vendor/`, along with the fonts and images its CSS points at, so the built site makes no requests to other hosts.

Set `assets` to `"cdn"` or `"local"` to pick one everywhere, and `cdn` to use a different CDN:

```php
"assets" => "local",
"cdn" => "https://cdn.jsdelivr.net/npm",
```

## CSS and JavaScript builds

Jumbo doesn't bundle CSS or JavaScript itself. Run your tool of choice into `public/` before each build with a build hook:

```php
$site->on("build:start", fn() => passthru("npx @tailwindcss/cli -i styles.css -o public/app.css --minify"));
```

These docs build their Tailwind CSS from `composer build` this way.
