---
description: Load Markdown from content/, use components in it, and highlight code on the server.
---

# Content

Markdown in `content/` isn't routed on its own. Pages load it with `$site->find()`:

```php
$post = $site->find("hello");   // content/hello.md
```

That returns the post's `slug`, `meta` (its front matter), `title`, `html` and `toc`, or `null` when there's no such file. The title comes from the `title` front matter, then the first `#` heading, then the slug. `toc` lists the `##` and `###` headings, each with its `level`, `id` and `text`, for a table of contents.

`$site->slugs()` lists every file in `content/`, including subfolders, so `content/guides/setup.md` is `guides/setup`. Jumbo keeps each file once it has parsed it, so loading a post twice costs nothing extra.

```php
<?php foreach ($site->slugs() as $slug): ?>
    <a href="/blog/<?= e($slug) ?>"><?= e($site->find($slug)["title"]) ?></a>
<?php endforeach ?>
```

`$site->markdown($source)` renders a string of Markdown the same way.

## What the Markdown supports

Jumbo uses [league/commonmark](https://commonmark.thephpleague.com) with GitHub Flavored Markdown (tables, task lists, strikethrough and autolinks), front matter in YAML, and anchor links on `##` and `###` headings.

Local images in Markdown, like `![A beach](/photos/beach.jpg)`, become responsive images. [Images](/docs/images) explains what that means.

## Components

A fenced block that starts with `:::` and a name renders `components/{name}.php`:

```markdown
::: callout type="tip" title="Heads up"
Any **Markdown** goes in here.
:::
```

The component gets the attributes as `$attributes` and the rendered Markdown inside as `$slot`:

```php
<?php // components/callout.php ?>
<aside class="callout callout-<?= e($attributes["type"] ?? "note") ?>">
    <strong><?= e($attributes["title"] ?? "Note") ?></strong>
    <?= $slot ?>
</aside>
```

Nest components by giving the outer one more colons, like `::::: tabs` around `::: tab` blocks. An unknown name renders a warning box in the page, so typos are easy to spot.

## Code highlighting

Fenced code is highlighted on the server with [tempest/highlight](https://github.com/tempestphp/highlight), so pages ship no highlighting JavaScript. By default code gets `hl-*` classes, which a theme's CSS styles:

```php
<style><?= $site->highlightStyles("github-dark") ?></style>
```

Set `markdown.highlight` to a theme name to put the colours inline, or to `false` to leave code plain:

```php
"markdown" => ["highlight" => "github-dark"],
```

A path to a CSS file works as a theme too, or pass your own `Highlighter`.

## Markdown config

Add CommonMark extensions and config through `markdown`:

```php
"markdown" => [
    "extensions" => [new League\CommonMark\Extension\Footnote\FootnoteExtension()],
    "config" => ["heading_permalink" => ["symbol" => "¶"]],
],
```

To change the defaults themselves, extend `Site` and override `createMarkdown()`.
