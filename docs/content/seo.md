---
description: Page titles, descriptions, canonical URLs and social cards from config and front matter.
---

# SEO

`$site->seo()` works out a page's meta tags from the `seo` config and the page's own front matter. Markdown pages get the result as `$seo`, and templates can call it themselves:

```php
<?php $seo = $site->seo(["description" => $description ?? null], $context->req->path(), $title ?? null) ?>
<title><?= e($seo["title"]) ?></title>
<meta name="description" content="<?= e($seo["description"]) ?>">
<?php if ($seo["canonical"]): ?><link rel="canonical" href="<?= e($seo["canonical"]) ?>"><?php endif ?>
<?php if ($seo["image"]): ?><meta property="og:image" content="<?= e($seo["image"]) ?>"><?php endif ?>
<?php if ($seo["noindex"]): ?><meta name="robots" content="noindex"><?php endif ?>
```

It returns `title`, `description`, `canonical`, `image`, `noindex`, `type` (`website` for the home page and `article` for the rest) and `twitter`.

## Defaults

```php
"seo" => [
    "title" => "Acme - Websites for elephants",   // The home page, and pages without a title
    "titleTemplate" => "%s - Acme",               // Every other page's title
    "description" => "We make websites.",
    "image" => "/social.png",                     // The default social card
    "twitter" => "@acme",
],
```

## Front matter

| Key | Does |
| --- | --- |
| `title` | The page's title, put through `titleTemplate` |
| `seo_title` | The whole `<title>`, skipping the template |
| `description` | The meta and social description |
| `image` | The social card image |
| `canonical` | A canonical URL of its own |
| `noindex` | Hide the page from search engines and the sitemap |

Canonical URLs and image paths become absolute when `url` is set in config, as social networks need full URLs.

## Faster navigation

Two config options make moving between pages feel quicker, without any JavaScript of your own:

```php
"prefetch" => true,          // Or "conservative", "moderate" or "eager"
"viewTransitions" => true,
```

`prefetch` adds speculation rules, so browsers that support them fetch a page before someone clicks the link to it. `viewTransitions` animates between pages with cross-document view transitions.
