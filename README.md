<h1 align="center">Jumbo</h1>

<p align="center">
  Content sites in plain PHP, on <a href="https://github.com/notrab/dumbo">Dumbo</a>. Named after his mum.
</p>

<p align="center">
  <a href="https://notrab.github.io/jumbo"><img src="https://img.shields.io/badge/read-docs-d6336c" alt="Docs" /></a>
  <a href="https://packagist.org/packages/notrab/jumbo"><img src="https://img.shields.io/packagist/v/notrab/jumbo?color=d6336c" alt="Version" /></a>
  <a href="https://discord.gg/nAq2h9BfsU"><img src="https://img.shields.io/discord/1278637768918171709?color=d6336c" alt="Discord" /></a>
</p>

Pages are files, templates are plain PHP, Markdown renders inside them, and the same app serves pages on request or builds them to static HTML.

## Quickstart

```bash
composer global require notrab/jumbo
jumbo new my-site
cd my-site
composer require notrab/jumbo
vendor/bin/jumbo dev
```

Open [localhost:8000](http://localhost:8000), and the page reloads as you save.

```
app.php       Returns the site
pages/        One file per route
views/        Layouts and templates
components/   Markdown components
content/      Markdown to load in pages
public/       Files served as-is
```

```bash
vendor/bin/jumbo build    # Build to dist/
vendor/bin/jumbo preview  # Serve dist/ like a static host
vendor/bin/jumbo routes   # List the pages and redirects
```

Routes, page data, layouts, Markdown components, forms, images, redirects, locales, feeds, builds and deployment are all in the [docs](https://notrab.github.io/jumbo). There are runnable sites in [`examples/`](examples), and the docs themselves are built with Jumbo in [`docs/`](docs).

## Contributing

```bash
composer install
vendor/bin/phpunit
```

To work on the docs, run `composer install`, `npm install` and `composer start` in `docs/`.

## License

Jumbo is open-sourced software licensed under the [MIT license](LICENSE).
