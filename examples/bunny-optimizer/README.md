# Bunny Optimizer Example

This example shows how to have Bunny Optimizer resize a Jumbo site's images on bunny.net's CDN, with the Bunny images loader. Builds skip resizing, and each image stays a single original that the CDN resizes, converts to WebP and compresses on request.

## Running the Example

1. Install dependencies:

   ```bash
   composer install
   ```

2. Start the dev server, which reloads as you save:

   ```bash
   composer dev
   ```

3. Open [http://localhost:8000](http://localhost:8000) and look at the image URLs.

The dev server ignores the Optimizer options and sends each original, so pages still work locally.

## Deploying

This site deploys with [bunny sites](https://www.npmjs.com/package/@bunny.net/cli), which serves it through a pull zone:

```bash
npm install -g @bunny.net/cli
bunny login
composer install
bunny sites deploy --build
```

Then, in the bunny.net dashboard, turn on **Bunny Optimizer** for the site's pull zone, and turn on its **Dynamic Image API** in the Optimizer settings. Images are resized from the next request on.

Hosting the site somewhere else? Create a pull zone with your site's URL as its origin, turn on Optimizer for it, and set `BUNNY_PULL_ZONE` to the pull zone's URL when you build:

```bash
BUNNY_PULL_ZONE=https://your-zone.b-cdn.net vendor/bin/jumbo build
```

## What it shows

- `"loader" => new Bunny()` in `app.php`, which turns every responsive image into Optimizer URLs like `/photos/meadow.jpg?width=960&format=webp&quality=80`
- Markdown images in `pages/index.md` becoming responsive images
- `$site->img()` in `pages/gallery.php` with `sizes` for a grid
- Builds that copy the originals instead of writing resized copies
- A `bunny.jsonc` and GitHub Actions workflow for deploying with bunny sites

The [images guide](https://notrab.github.io/jumbo/docs/images#bunnynet) covers the loader's options. This example installs Jumbo from this repo. In your own project, run `composer require notrab/jumbo` instead.
