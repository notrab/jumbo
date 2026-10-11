---
description: Responsive, resized images from public/, made with GD and written into builds.
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
    "widths" => [320, 640, 960, 1280, 1920, 2560],
    "format" => "webp",   // "avif", "png", "jpg", or null to keep each image's own
    "quality" => 80,
    "cache" => null,      // A directory for resized copies, the temp directory by default
],
```

Jumbo resizes with the GD extension. It only makes the widths in `images.widths` plus each image's own width, so visitors can't ask the server for any size they like. Requested widths round up to the next one in the list.

Copies are made the first time they're asked for and kept in the cache. Their URLs carry a version that changes with the file, so browsers cache them for a year.

## In builds

`jumbo build` writes every size the built pages use into `dist/_image/`, so a static host serves them as plain files.
