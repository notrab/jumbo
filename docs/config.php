<?php

/**
 * Everything specific to these docs. Pages live in content/, and the home
 * page is content/index.md.
 */
return [
    "name" => "Jumbo",

    // An emoji, or a path to an image in public/
    "logo" => "🐘",

    // A little sticker next to the logo, or null
    "badge" => "dumbo's mum",

    // Where the site is hosted, for canonical URLs, social cards and the
    // sitemap. Include any sub-path, e.g. https://notrab.github.io/jumbo
    "url" => getenv("DOCS_URL") ?: null,

    "locale" => "en",

    // Defaults for every page. Override them in a page's front matter with
    // title, seo_title, description, image, canonical or noindex.
    "seo" => [
        "title" => "Jumbo - Content sites in plain PHP",
        "titleTemplate" => "%s - Jumbo",
        "description" => "Content sites in plain PHP on the Dumbo framework: Markdown pages, PHP templates and static builds.",
        "image" => null,
        "twitter" => null,
    ],

    // Any CSS colours. The accent should be light, as text on it is dark.
    "theme" => [
        "primary" => "#d6336c",
        "accent" => "#ffd166",
    ],

    "links" => [
        ["text" => "Docs", "href" => "/docs"],
        ["text" => "Examples", "href" => "/docs/examples"],
        ["text" => "Dumbo", "href" => "https://notrab.github.io/dumbo"],
    ],

    // Icons in the header: github, discord, x or bluesky
    "social" => [
        "discord" => "https://discord.gg/nAq2h9BfsU",
        "github" => "https://github.com/notrab/jumbo",
    ],

    // Sidebar groups, in reading order. Each slug is a file in content/.
    "sidebar" => [
        "🚀 Getting started" => ["introduction", "quickstart"],
        "📄 Pages" => ["pages", "page-data", "layouts", "content", "actions"],
        "🖼️ Assets" => ["images", "files"],
        "🌍 Site" => ["redirects", "locales", "feeds-and-sitemaps", "seo"],
        "🚚 Shipping" => ["developing", "builds", "deployment", "config"],
        "🧪 Examples" => ["examples"],
    ],

    // {path} is the page's file in content/. Set to null to hide edit links.
    "editUrl" => "https://github.com/notrab/jumbo/edit/main/docs/content/{path}",

    "footer" => [
        "title" => "Made with 🥜 and PHP",
        "text" => "Released under the MIT License. Dumbo's mum keeps the content in order.",
    ],

    // Sprinkled on copy buttons and thrown by the home page mascot
    "confetti" => "🎪",

    // "cdn" loads fonts and scripts from hop.js, "local" serves them from
    // node_modules. Defaults to the CDN in development and local in
    // production, and static builds are always local.
    "assets" => null,
];
