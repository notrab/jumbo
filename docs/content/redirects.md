---
description: Redirect old URLs to new ones, with params, on the server and in static builds.
---

# Redirects

List redirects in config. Each one is a path and where it goes, or the destination with a status:

```php
"redirects" => [
    "/old" => "/new",
    "/posts/[slug]" => ["to" => "/blog/[slug]", "status" => 302],
],
```

Redirects are 301s unless you say otherwise. Params use the same `[name]` and `[...name]` syntax as page files, and fill in the destination.

The PHP server sends real redirects. Builds write a page for each redirect without params, with a meta refresh and a canonical link, since a static host has no way to send a redirect status from a file. Redirects with params only work on the server.

`jumbo routes` lists the redirects with the pages.

## Real redirects on static hosts

Netlify and Cloudflare Pages read redirects from a `_redirects` file. Write one from a build hook:

```php
$site->on("build:done", function (string $out) use ($site) {
    $lines = "";

    foreach ($site->redirects() as $from => $redirect) {
        $lines .= "$from {$redirect["to"]} {$redirect["status"]}\n";
    }

    file_put_contents("$out/_redirects", $lines);
});
```

The [static hosting example](https://github.com/notrab/jumbo/tree/main/examples/static-hosting) does this, and writes a `_headers` file too.
