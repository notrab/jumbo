# bunny.net Magic Containers Example

This example shows how to run a Jumbo site on bunny.net Magic Containers, for sites that need a server: a guestbook whose form saves entries to a persistent volume. It runs on FrankenPHP in worker mode, so the site boots once and stays in memory.

## Running the Example

1. Install dependencies and start the dev server on [http://localhost:8000](http://localhost:8000):

   ```bash
   composer install
   composer dev
   ```

2. Or run it in Docker on [http://localhost:8080](http://localhost:8080), the way Magic Containers will, with a volume for the entries:

   ```bash
   docker build --platform linux/amd64 -t herd-guestbook .
   docker run -p 8080:8080 -v guestbook:/data herd-guestbook
   ```

Sign the guestbook, restart the container, and the entries are still there.

## Deploying

This folder is meant to be the root of its own repository. With the [bunny CLI](https://www.npmjs.com/package/@bunny.net/cli):

```bash
npm install -g @bunny.net/cli
bunny login
bunny apps deploy --dockerfile --port 8080
```

The first deploy asks for a name, region and container registry, and writes a `bunny.jsonc` to commit. After that, `bunny apps deploy --dockerfile` builds and ships each new version.

Then, in the app's settings in the bunny.net dashboard, add a persistent volume mounted at `/data`, so entries survive deploys and restarts.

### Deploy on every push

`.github/workflows/deploy.yml` builds the image on each push to `main`, pushes it to GitHub Container Registry and rolls it out with [`BunnyWay/actions/container-update-image`](https://github.com/BunnyWay/actions). Create the app from that image once, with a container named `app` and a CDN endpoint on port `8080`, then add a repository secret `BUNNYNET_API_KEY` with your bunny.net API key and a variable `APP_ID` with the app's ID.

## What it shows

- `index.php` sends every request to the site, and a `Caddyfile` runs it as a FrankenPHP worker on port 8080
- A `Dockerfile` with GD for resized images, OPcache with timestamp checks off, and `DUMBO_ENV=production`
- A page action that validates, saves and redirects with a 303, and refuses posts from other sites
- `prerender: false` on pages that change on every request
- `GUESTBOOK_DIR` pointing at `/data`, where a persistent volume keeps entries across deploys
- A `/health` page for uptime checks

The [deployment guide](https://notrab.github.io/jumbo/docs/deployment#magic-containers) covers Magic Containers in more detail.
