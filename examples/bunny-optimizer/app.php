<?php

require __DIR__ . "/vendor/autoload.php";

use Dumbo\Jumbo\Images\Bunny;

return new Dumbo\Jumbo\Site(__DIR__, [
    "name" => "Watering Hole",
    "url" => getenv("SITE_URL") ?: "https://watering-hole.example",
    "images" => [
        // Bunny Optimizer resizes images on the CDN, so builds don't. With no
        // URL, image URLs stay on the site's own host, which suits a site
        // served through its pull zone, like one deployed with bunny sites.
        // Pass a pull zone's URL, like "https://acme.b-cdn.net", otherwise.
        "loader" => new Bunny(getenv("BUNNY_PULL_ZONE") ?: ""),
        "widths" => [480, 960, 1440, 1920],
        "format" => "webp",
        "quality" => 80,
    ],
]);
