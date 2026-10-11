# Jumbo examples

Small sites that each show a few of Jumbo's features. Each one installs Jumbo from this repo, so run `composer install` inside it first.

| Example | Shows |
| --- | --- |
| [blog](blog) | Posts, pagination, tags, RSS, sitemaps, highlighted code, responsive images and hoisted component CSS |
| [contact-form](contact-form) | Page actions: validation, redirects, two forms on a page and `fetch()` |
| [multilingual](multilingual) | Locales, translated content, a language switcher and hreflang links |
| [static-hosting](static-hosting) | Redirects, `.html` builds, build hooks and previewing a build |
| [bunny-sites](bunny-sites) | Deploying a build to bunny.net with `bunny sites`, from the terminal or GitHub Actions |
| [bunny-optimizer](bunny-optimizer) | The Bunny images loader, so Bunny Optimizer resizes images on the CDN |
| [bunny-magic-containers](bunny-magic-containers) | Running a site with page actions on bunny.net Magic Containers, with FrankenPHP and a persistent volume |

To start a site of your own, run `jumbo new my-site`.
