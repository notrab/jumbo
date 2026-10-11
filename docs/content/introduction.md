---
description: What Jumbo is, and how a Jumbo site fits together.
---

# Introduction

Jumbo builds content sites in plain PHP. Each file in `pages/` is a route, layouts are PHP templates, Markdown renders inside them, and the same app serves pages on request or writes them to static HTML with `jumbo build`.

It runs on [Dumbo](https://notrab.github.io/dumbo), a small PHP framework for HTTP. Every Jumbo site is a Dumbo app, so middleware, API routes and the rest of Dumbo's docs apply to your site too.

## What's in the box

- Routes from files, with params like `[slug]` and `[...path]`
- Markdown with front matter, a table of contents, components and code highlighted on the server
- Layouts with named slots, written in PHP
- Forms handled by the page that shows them
- Responsive images, resized with GD
- Pagination, redirects, locales, RSS and a sitemap
- Live reload in development, and static builds that match what the server sends

## How a page gets rendered

`jumbo dev` runs PHP's built-in server. A request for `/blog/hello` matches `pages/blog/[slug].php`, Jumbo renders that file, and the response goes back out through Dumbo's middleware.

`jumbo build` makes the same requests through the app for every page it can list, and writes each response to `dist/`. You write a page once, and static hosting and a PHP server get the same HTML.

## Where to go next

The [quickstart](/docs/quickstart) makes a site from the starter in a couple of minutes. The [examples](/docs/examples) are complete sites you can read or copy.
