---
description: Define a page with Page to build routes with params, pass props, paginate, and render on each request.
---

# Page data

A PHP page that starts with `return` hands Jumbo a `Page`. The `render` function returns the HTML, a response, or `null` to send the context's response.

```php
<?php // pages/blog/[slug].php

use Dumbo\Jumbo\Page;

return new Page(
    paths: fn($site) => array_map(fn($slug) => [
        "params" => ["slug" => $slug],
        "props" => ["post" => $site->find($slug)],
    ], $site->slugs()),
    render: fn($context, $site, $props) => $site->view("post", ["post" => $props["post"]]),
);
```

## Paths

A page with params in its file name needs `paths` to be built, so Jumbo knows which URLs exist. Each entry is the params on their own, like `["slug" => "hello"]`, or the params with props for `render`, like the example above.

Jumbo works out the paths and props once per page, and reuses them for every URL. A `render` function that takes a third argument gets the props for the URL being rendered, and gets an empty array for a URL that `paths` didn't list.

`jumbo build` stops with an error for a page that has params and no `paths`, as it can't guess the URLs.

## Rendering on each request

Set `prerender: false` to skip a page in builds and render it each time it's asked for, for search results or anything that reads the request:

```php
return new Page(
    prerender: false,
    render: fn($context, $site) => $site->view("search", [
        "results" => $search->find($context->req->query("q") ?? ""),
    ]),
);
```

`prerender` also takes a function of the site, to decide from config. Pages that render on request need the PHP server, as a static host has nothing to run them.

## Middleware

`middleware` adds Dumbo middleware to the page's route, which runs before `render`:

```php
use Dumbo\Middleware\BasicAuth;

return new Page(
    prerender: false,
    middleware: [BasicAuth::basicAuth("admin:" . getenv("ADMIN_PASSWORD"))],
    render: fn($context, $site) => $site->view("admin"),
);
```

## Pagination

`$site->paginate($items, 10)` makes the paths for a page like `pages/blog/[page].php`, numbered from 1. Each page gets a `Pagination` as its `page` prop:

```php
<?php // pages/blog/[page].php

use Dumbo\Jumbo\Page;

return new Page(
    paths: fn($site) => $site->paginate($site->posts(), 10),
    render: fn($context, $site, $props) => $site->view("archive", ["page" => $props["page"]]),
);
```

```php
<?php // views/archive.php ?>
<?php foreach ($page->items as $post): ?>
    <a href="/posts/<?= e($post["slug"]) ?>"><?= e($post["title"]) ?></a>
<?php endforeach ?>

<p>Posts <?= $page->start() ?> to <?= $page->end() ?> of <?= $page->total ?></p>

<?php if ($page->previous()): ?><a href="<?= e($page->previous()) ?>">Newer</a><?php endif ?>
<?php if ($page->next()): ?><a href="<?= e($page->next()) ?>">Older</a><?php endif ?>
```

`$page->current` and `$page->last` are page numbers, and `$page->url(3)` links to any page. A route with other params, like `pages/tags/[tag]/[page].php`, passes them along with a different param name if it needs one:

```php
paths: fn($site) => array_merge(...array_map(
    fn($tag) => $site->paginate($site->posts($tag), 10, "page", ["tag" => $tag]),
    $site->tags(),
)),
```

`posts()` and `tags()` here are helpers of your own. [Config](/docs/config#your-own-site-class) shows how to add them to the site.
