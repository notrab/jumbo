---
description: Responsive, resized images from public/, made with GD or an image CDN like bunny.net.
---

# Images

`$site->img()` turns an image in `public/` into a responsive `<img>`:

```php
<?= $site->img("/photos/beach.jpg", "The beach", width: 800) ?>
```

```html
<img src="/_image/960/photos/beach.jpg.webp?v=1a2b3c4d"
     srcset="/_image/320/photos/beach.jpg.webp?v=1a2b3c4d 320w, ... /_image/1280/photos/beach.jpg.webp?v=1a2b3c4d 1280w"
     sizes="(min-width: 800px) 800px, 100vw"
     width="800" height="533" alt="The beach" loading="lazy" decoding="async">
```

The `srcset` covers screens up to twice the width, the `width` and `height` stop the page jumping as the image loads, and the copies are WebP. An image is never made wider than the original.

Pass `sizes` for images that aren't full width, `format` for a different format, and `attributes` for anything else. Attributes also override the defaults, so a hero image at the top of the page can load straight away:

```php
<?= $site->img("/hero.jpg", "", width: 1200, attributes: ["loading" => "eager", "class" => "hero"]) ?>
```

`$site->image("/photos/beach.jpg", 400)` is the URL alone, for CSS backgrounds or social cards.

## In Markdown

Local images in Markdown get the same treatment:

```markdown
![The beach](/photos/beach.jpg)
```

Remote images and SVGs keep their own URLs. Set `markdown.images` to `false` to leave Markdown images alone.

## Sizes and formats

```php
"images" => [
    "loader" => null,     // An image CDN, below, or null to resize with GD
    "widths" => [320, 640, 960, 1280, 1920, 2560],
    "format" => "webp",   // "avif", "png", "jpg", or null to keep each image's own
    "quality" => 80,      // Or null for the loader's default
    "cache" => null,      // A directory for resized copies, the temp directory by default
],
```

Jumbo resizes with the GD extension unless you give it an [image CDN](#image-cdns). It only makes the widths in `images.widths` plus each image's own width, so visitors can't ask the server for any size they like. Requested widths round up to the next one in the list.

Copies are made the first time they're asked for and kept in the cache. Their URLs carry a version that changes with the file, so browsers cache them for a year.

## In builds

`jumbo build` writes every size the built pages use into `dist/_image/`, so a static host serves them as plain files.

## Image CDNs

An image CDN can resize images instead of your server. Jumbo still reads each image's size from `public/` and picks the widths and format, so `img()` writes the same `srcset`, `width` and `height`, and a loader turns each size into a URL for the CDN.

### bunny.net

[Bunny Optimizer](https://docs.bunny.net/optimizer/dynamic-images) resizes images on a pull zone with query strings. Turn on Optimizer and its dynamic images in the pull zone's settings, then pass the pull zone's URL:

```php
use Dumbo\Jumbo\Images\Bunny;

return new Dumbo\Jumbo\Site(__DIR__, [
    "images" => [
        "loader" => new Bunny("https://acme.b-cdn.net"),
    ],
]);
```

```html
<img src="https://acme.b-cdn.net/photos/beach.jpg?width=960&format=webp&quality=80&v=1a2b3c4d"
     srcset="https://acme.b-cdn.net/photos/beach.jpg?width=320&format=webp&quality=80&v=1a2b3c4d 320w, ..."
     width="800" height="533" alt="The beach" loading="lazy" decoding="async">
```

The pull zone fetches originals from your site, so its origin is the site's URL. When the site itself is served through the pull zone, like a site deployed with [`bunny sites deploy`](/docs/deployment#bunnynet), leave the URL out and images stay on the site's own host:

```php
"loader" => new Bunny(),
```

The development server ignores the options and sends the original image, so pages still work locally.

The second argument adds [Optimizer options](https://docs.bunny.net/optimizer/dynamic-images) to every image:

```php
"loader" => new Bunny("https://acme.b-cdn.net", ["sharpen" => true]),
```

Set `quality` to `null` to use the quality in the pull zone's Optimizer settings instead.

### Your own

A loader is a class with two methods: the formats the CDN can convert to, and the URL for an image at a width, format and quality. Here's one for [imgix](https://docs.imgix.com/apis/rendering), with a web folder source pointing at your site:

```php
use Dumbo\Jumbo\Images\Loader;

class Imgix implements Loader
{
    public function __construct(private string $url)
    {
    }

    public function formats(): array
    {
        return ["jpg", "jpeg", "png", "webp", "avif"];
    }

    public function url(string $path, int $width, string $format, ?int $quality, string $version): string
    {
        return $this->url . $path . "?" . http_build_query(["w" => $width, "fm" => $format, "q" => $quality, "v" => $version]);
    }
}
```

`$path` is the image's URL-encoded path in `public/`, like `/photos/beach.jpg`, and `$version` changes when the file does, to bust the CDN's cache. Cloudinary, ImageKit and the rest work the same way.

With a CDN loader, builds don't write `dist/_image/`, and the server doesn't need GD.

In development, images on a separate pull zone come from your live site, so new ones don't show until they're deployed. Use GD locally if that gets in the way:

```php
"loader" => getenv("DUMBO_ENV") === "production" ? new Bunny("https://acme.b-cdn.net") : null,
```
