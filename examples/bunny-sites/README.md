# Bunny Sites Example

This example shows how to host a built Jumbo site on bunny.net with `bunny sites`, which uploads the build to a storage zone with a pull zone in front, and makes every deploy live straight away with instant rollbacks.

## Running the Example

1. Install dependencies:

   ```bash
   composer install
   ```

2. Build it and serve the build the way a static host would:

   ```bash
   composer build
   composer preview
   ```

3. Open [http://localhost:4000](http://localhost:4000).

## Deploying

Install the [bunny CLI](https://www.npmjs.com/package/@bunny.net/cli) and log in:

```bash
npm install -g @bunny.net/cli
bunny login
```

Then build and deploy in one go. `bunny.jsonc` tells the CLI the site's name, how to build it and which folder to upload, so install dependencies first:

```bash
composer install
bunny sites deploy --build
```

The first deploy creates the site and links this folder to it. Each deploy after that goes live straight away:

```bash
bunny sites deployments list                # every deploy, and which one is live
bunny sites deployments publish --previous  # roll back
bunny sites domains add trunkshow.dev       # use your own domain
```

Once the domain is added, set `url` in `app.php` to it, so canonical links and the sitemap point there.

### Deploy on every push

`.github/workflows/deploy.yml` builds the site on each push to `main` and deploys it with [`BunnyWay/actions/deploy-site`](https://github.com/BunnyWay/actions). Add a repository secret `BUNNYNET_API_KEY` with your bunny.net API key.

## What it shows

- A `bunny.jsonc` with the site's name, build command and output folder, so `bunny sites deploy --build` needs no arguments
- `pages/404.md` becomes `404.html`, which bunny sites serves for missing pages
- A GitHub Actions workflow that builds with PHP and deploys with bunny.net's action

This example installs Jumbo from this repo. In your own project, run `composer require notrab/jumbo` instead.
