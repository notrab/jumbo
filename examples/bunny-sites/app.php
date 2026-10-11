<?php

require __DIR__ . "/vendor/autoload.php";

return new Dumbo\Jumbo\Site(__DIR__, [
    "name" => "Trunk Show",
    // Builds use this for canonical URLs and the sitemap. bunny.jsonc sets it
    // for deploys, and your custom domain goes here once it's added.
    "url" => getenv("SITE_URL") ?: "https://trunk-show.example",
    "seo" => [
        "titleTemplate" => "%s - Trunk Show",
        "description" => "A week of elephant talks, hosted on bunny.net.",
    ],
]);
