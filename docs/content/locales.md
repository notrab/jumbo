---
description: Serve every page in several languages, with translated content and hreflang links.
---

# Locales

List the locales in config, and every page is also served under each locale's prefix:

```php
"i18n" => ["locales" => ["en", "fr"]],
```

`/about` is English, `/fr/about` is French, and builds write both. The first locale is the default, which has no prefix. Set `default` to choose another one, and `prefixDefault` to serve the default under its own prefix too, with `/` redirecting to it.

## In templates

```php
<html lang="<?= e($site->locale($context)) ?>">
```

- `$site->locale($context)` is the locale of the page being rendered.
- `$site->localize("/about", "fr")` is `/fr/about`.
- `$site->alternates($context)` is the current page's path in each locale, keyed by locale.

`alternates()` covers language switchers and hreflang links:

```php
<?php foreach ($site->alternates($context) as $locale => $path): ?>
    <link rel="alternate" hreflang="<?= e($locale) ?>" href="<?= e($site->url($path) ?? $path) ?>">
<?php endforeach ?>
```

## Translated content

`$site->find("hello", "fr")` loads `content/fr/hello.md` when there is one, and falls back to `content/hello.md`. Pages without a translation render in the default language.

For short strings like button labels, a PHP array per locale in `views/` goes a long way. The [multilingual example](https://github.com/notrab/jumbo/tree/main/examples/multilingual) has one, with a language switcher.
