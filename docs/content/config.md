---
description: Every config option, and how to extend Site with your own defaults and helpers.
---

# Config

Pass config as the second argument to `Site`. Anything you leave out keeps its default, and nested options merge with the defaults.

```php
return new Dumbo\Jumbo\Site(__DIR__, [
    "name" => "Acme",
    "url" => "https://acme.dev",
]);
```

| Option | Default | Does |
| --- | --- | --- |
| `name` | `"Site"` | The site's name, for titles and feeds |
| `url` | `null` | Where the site is hosted, for canonical URLs, the sitemap, feeds and the build's base path |
| `locale` | `"en"` | The site's language, without `i18n` |
| `seo` | | Default titles, description, image and Twitter handle. See [SEO](/docs/seo). |
| `assets` | `null` | `"cdn"` or `"local"` for `asset()`. The CDN in development and local in production by default. |
| `cdn` | hop.js | The CDN for `asset()` |
| `markdown` | | `extensions`, `config`, `highlight` and `images`. See [Content](/docs/content). |
| `images` | | `widths`, `format`, `quality` and `cache`. See [Images](/docs/images). |
| `redirects` | `[]` | See [Redirects](/docs/redirects) |
| `i18n` | | `locales`, `default` and `prefixDefault`. See [Locales](/docs/locales). |
| `build` | | `format`: `"directory"` or `"file"`. See [Builds](/docs/builds). |
| `sitemap` | `true` | `false`, or a function that's given each URL and returns whether to list it |
| `prefetch` | `false` | Fetch pages before they're clicked: `true`, `"conservative"`, `"moderate"` or `"eager"` |
| `viewTransitions` | `false` | Animate between pages |
| `actions` | | `checkOrigin` and `origins`. See [Actions](/docs/actions). |
| `dirs` | | Rename `pages`, `views`, `components`, `content` or `public` |

`Site::DEFAULTS` in the source has every option with a comment.

Read config anywhere as `$site->config`, like `$site->config["name"]` in a template.

## Your own site class

Extend `Site` to give your templates helpers of their own. They're on `$site` in every page and view:

```php
<?php // app.php

require __DIR__ . "/vendor/autoload.php";

class Blog extends Dumbo\Jumbo\Site
{
    /** Every post in content/, newest first */
    public function posts(): array
    {
        $posts = array_map($this->find(...), $this->slugs());
        usort($posts, fn($a, $b) => $b["meta"]["date"] <=> $a["meta"]["date"]);

        return $posts;
    }
}

return new Blog(__DIR__, ["name" => "Trunk Notes"]);
```

A subclass can set its own defaults too, by overriding `DEFAULTS`:

```php
class Docs extends Dumbo\Jumbo\Site
{
    public const DEFAULTS = [
        ...parent::DEFAULTS,
        "sidebar" => [],
        "dirs" => [...parent::DEFAULTS["dirs"], "views" => "theme"],
    ];
}
```

These docs do both, in [`docs/src/Site.php`](https://github.com/notrab/jumbo/blob/main/docs/src/Site.php).
