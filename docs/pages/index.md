---
layout: home

hero:
  name: Jumbo
  text: Content sites in plain PHP.
  tagline:
    - Dumbo's mum. Bigger ears.
    - Files in, pages out.
    - Markdown, PHP and a trunk full of HTML.
    - Serve it live, or build it static.
    - Never forgets a page.
  badges:
    - PHP 8.3+
    - Built on Dumbo 🐘
    - MIT licensed
  actions:
    - text: Get started →
      href: /docs/introduction
    - text: Browse examples
      href: /docs/examples
  install: composer global require notrab/jumbo
  mascot: 🐘
  code:
    file: pages/blog/[slug].php
    language: php
    source: |
      <?php

      use Dumbo\Jumbo\Page;

      return new Page(
          paths: fn($site) => array_map(
              fn($slug) => ["slug" => $slug],
              $site->slugs(),
          ),
          render: fn($context, $site) => $site->view("post", [
              "post" => $site->find($context->req->param("slug")),
          ]),
      );

features_title: "Pick something to *build*"
features_text: Each of these is a few files in a Jumbo site. Run jumbo new, add one, and you're away.
features:
  - title: A blog with a feed
    details: Posts in Markdown, a page for each one, and an RSS feed from the same list. Code is highlighted on the server.
    href: /docs/feeds-and-sitemaps
    link: Feeds and sitemaps
    code: |
      // pages/rss.xml.php
      return new Page(render: fn($c, $site) => $site->rss(
          array_map(fn($post) => [
              'title' => $post['title'],
              'link' => "/blog/{$post['slug']}",
              'date' => $post['meta']['date'],
          ], $site->posts())
      ));
  - title: A docs site, like this one
    details: Markdown pages with a sidebar, components for callouts and tabs, and a table of contents from the headings.
    href: /docs/content
    link: Content
    language: markdown
    code: |
      # Routing

      ::: callout type="tip"
      Files in pages/ are **routes**.
      :::

      ```php
      $site->find('routing')['toc'];
      ```
  - title: A landing page with a contact form
    details: The page handles its own form. Validation errors show next to the fields, and a good post redirects.
    href: /docs/actions
    link: Actions
    code: |
      actions: [
          'default' => function ($input, $c) {
              if (empty($input['email'])) {
                  return Action::fail(['email' => 'Required']);
              }

              return $c->redirect('/thanks', 303);
          },
      ],
  - title: A site in three languages
    details: Every page is served under each locale, with translated Markdown and hreflang links.
    href: /docs/locales
    link: Locales
    code: |
      'i18n' => ['locales' => ['en', 'fr', 'es']],

      $site->find('home', $site->locale($c));
      $site->alternates($c);
      // ['en' => '/', 'fr' => '/fr', 'es' => '/es']
  - title: A portfolio full of photos
    details: Responsive images resized with GD, in WebP, with their sizes set so nothing jumps as they load.
    href: /docs/images
    link: Images
    code: |
      <?= $site->img('/work/poster.jpg', 'Gig poster',
          width: 800,
      ) ?>
  - title: A static site with a live corner
    details: Build most pages for a CDN, and render search or a dashboard on each request with Dumbo middleware in front.
    href: /docs/page-data
    link: Page data
    code: |
      return new Page(
          prerender: false,
          render: fn($c, $site) => $site->view('search', [
              'q' => $c->req->query('q'),
          ]),
      );

cta:
  icon: 🎪
  title: Put on your first show in about a minute
  text: Install Jumbo, run jumbo new, and open your browser.
  action:
    text: Quickstart →
    href: /docs/quickstart
---
