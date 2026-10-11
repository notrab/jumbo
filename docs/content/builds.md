---
description: Render every page to static files, check links, and run your own steps around a build.
---

# Builds

```bash
vendor/bin/jumbo build
```

A build requests every page through the app and writes each response to `dist/`, so the files match what the server sends. Pages with params are built for each entry in their `paths`, in every locale, and pages with `prerender: false` are skipped.

Each build also:

- copies `public/` into `dist/`
- writes `404.html` from `pages/404.php`
- writes `sitemap.xml` when `url` is set
- writes the resized images and package files the pages use
- turns redirects into pages that send the browser on

The output folder is emptied first. Jumbo refuses to empty a folder that holds the site itself.

## Options

```bash
vendor/bin/jumbo build --url=https://acme.dev/docs --out=public_html
```

| Option | Does |
| --- | --- |
| `--url` | The site's URL, for canonical links, the sitemap and feeds. A path in it, like `/docs`, becomes the base path. |
| `--base` | A base path on its own, added to every root-relative link |
| `--out` | Where to write the files, `dist` by default |

Builds run in production mode, so `pages/500.php` and local assets are used.

## File names

Pages are written as `about/index.html` by default, which every static host serves at `/about`. For hosts that prefer files, set:

```php
"build" => ["format" => "file"],
```

and `/about` is written as `about.html`.

## Broken links

After writing the pages, the build lists links to pages and files it didn't write and that the app doesn't serve either:

```
Broken links:
  /blog/hello links to /blog/helo
```

Links to routes the app serves on demand, like API routes, aren't counted as broken.

## Build hooks

Run your own steps before and after a build:

```php
$site->on("build:start", fn(string $out) => passthru("npm run build"));
$site->on("build:done", fn(string $out, array $files) => print(count($files) . " files\n"));
```

`build:start` gets the output folder. `build:done` gets the folder and every file written, relative to it, for things like writing host config or uploading the site.

## Previewing

```bash
vendor/bin/jumbo preview
```

That serves `dist/` on [http://localhost:4000](http://localhost:4000) the way a static host would, with `404.html` for missing pages. For a build with a base path, pass the same `--base`, or let it read the path from `url`.
