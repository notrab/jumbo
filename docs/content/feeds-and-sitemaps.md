---
description: Publish an RSS feed from a page, and get a sitemap written with every build.
---

# Feeds and sitemaps

## RSS

`$site->rss($items)` returns an RSS feed, with the channel's title, link, description and language from your config. Return it from a page named for the feed, and the `.xml` in the name sends it as XML:

```php
<?php // pages/rss.xml.php

use Dumbo\Jumbo\Page;

return new Page(render: fn($context, $site) => $site->rss(array_map(fn($post) => [
    "title" => $post["title"],
    "link" => "/posts/{$post["slug"]}",
    "description" => $post["meta"]["description"] ?? "",
    "content" => $post["html"],
    "date" => $post["meta"]["date"],
    "categories" => $post["meta"]["tags"] ?? [],
], $site->posts())));
```

Each item needs a `title` and a `link`, and can have a `description`, `content` (the full HTML), a `date`, an `author`, `categories` and a `guid`. Links that are paths become absolute when `url` is set. Dates can be anything PHP's `DateTimeImmutable` reads, including YAML dates from front matter.

Pass a second argument to change the channel, like `$site->rss($items, ["title" => "Release notes"])`.

Point feed readers at it from your layout:

```html
<link rel="alternate" type="application/rss+xml" title="Acme" href="/rss.xml">
```

## Sitemaps

Builds write `sitemap.xml` with every HTML page that doesn't have a `noindex` robots meta tag, when `url` is set in config.

Set `sitemap` to a function to choose the URLs, or to `false` to skip it:

```php
"sitemap" => fn(string $url) => !str_contains($url, "/drafts/"),
```

A page at `pages/sitemap.xml.php` replaces the generated one, for sitemaps with dates or images.
