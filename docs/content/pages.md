---
description: How files in pages/ become routes, and what templates and Markdown pages get.
---

# Pages

Every `.php` and `.md` file in `pages/` is a route.

| File | Route |
| --- | --- |
| `pages/index.php` | `/` |
| `pages/about.md` | `/about` |
| `pages/blog/index.php` | `/blog` |
| `pages/blog/[slug].php` | `/blog/:slug` |
| `pages/docs/[...path].php` | `/docs/*`, at any depth |
| `pages/rss.xml.php` | `/rss.xml`, sent as XML |
| `pages/404.php` | Pages that don't exist, and `404.html` in builds |
| `pages/500.php` | Errors in production, with `$error` |

Files and folders starting with `_` are skipped, so helpers can live next to the pages that use them. When two routes could match, the one with fewer params wins, so `pages/blog/archive.php` beats `pages/blog/[slug].php`.

## Templates

A PHP page is a template. It gets the site as `$site`, the request as `$context` and the route's params as `$params`:

```php
<?php // pages/blog/[slug].php ?>
<?php $this->layout("layout", ["title" => $params["slug"]]) ?>

<h1><?= e($params["slug"]) ?></h1>
```

`$context` is Dumbo's context, so `$context->req->query("page")` and the rest of the request are there. Escape anything you print with `e()`.

A file extension in the name sets the content type. `pages/feed.json.php` is sent as JSON, and `pages/rss.xml.php` as XML.

## Markdown pages

A Markdown page renders in the view named by its `layout` front matter:

```markdown
---
layout: page
description: Who we are.
---

# About

We make websites.
```

The view gets the rendered page as `$page`, with its `html`, `title`, `meta` (the front matter) and `toc`, and the page's meta tags as `$seo`. Without a `layout`, the page is the bare HTML. [Content](/docs/content) covers Markdown in more depth.

## Pages with data

A PHP page that starts with `return` defines its route with a `Page`. That's how a page lists the params to build, handles forms, or renders on every request. [Page data](/docs/page-data) covers each one.

## Error pages

`pages/404.php` renders for any route that doesn't exist, and builds write it to `404.html`, which most static hosts serve for missing pages.

`pages/500.php` renders when a page throws in production, with the exception as `$error`. In development you get the error and the code around it.
