---
description: Wrap templates in layouts, fill named slots, and let components bring their own CSS and JS.
---

# Layouts

Templates pick a layout from `views/` with `$this->layout()`:

```php
<?php // pages/pricing.php ?>
<?php $this->layout("base", ["title" => "Pricing"]) ?>

<?php $this->start("head") ?>
    <link rel="stylesheet" href="/pricing.css">
<?php $this->end() ?>

<h1>Pricing</h1>
```

```php
<?php // views/base.php ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= e($title ?? $site->config["name"]) ?></title>
    <?= $this->slot("head") ?>
</head>
<body>
    <?= $slot ?>
</body>
</html>
```

The layout gets the template's output as `$slot`, the data passed to `layout()`, and the template's own variables. `$this->slot("head")` is a named slot, with an optional default for templates that don't fill it.

Layouts can have layouts, so `views/post.php` can wrap a post and then pick `base` for the page around it. When two templates fill the same slot, the innermost one wins.

## Escaping

Templates are plain PHP, so nothing is escaped for you. Print values with `e()`, which escapes HTML and accepts `null`:

```php
<h1><?= e($post["title"]) ?></h1>
```

Rendered Markdown, like `$page["html"]`, is HTML already and goes out as it is.

## Views and components

`$site->view("card", ["post" => $post])` renders `views/card.php` with those variables and returns the HTML, for partials and for pages that build their HTML in `render`.

`$site->component("note", ["type" => "tip"], $html)` renders `components/note.php`, the same file Markdown uses for `::: note`. Components get `$attributes` and `$slot`.

## Hoisted styles and scripts

`<style hoist>` and `<script hoist>` move into the `<head>`, and each one appears once per page. A component can carry its own CSS:

```php
<?php // components/note.php ?>
<aside class="note"><?= $slot ?></aside>

<style hoist>
    .note { padding: 1rem; border-left: 4px solid hotpink; }
</style>
```

Use the note ten times on a page and the page gets one copy of its styles.
