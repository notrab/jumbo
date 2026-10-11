<?php

namespace Dumbo\Jumbo\Images;

/**
 * Resizes images with Bunny Optimizer's dynamic images, on a bunny.net pull
 * zone whose origin is the site:
 *
 *   https://acme.b-cdn.net/photos/beach.jpg?width=640&format=webp&quality=80
 *
 * Turn on Bunny Optimizer and its dynamic images in the pull zone's
 * settings. When the site itself is served through the pull zone, leave the
 * URL out and the image URLs stay on the site's own host.
 *
 * @see https://docs.bunny.net/optimizer/dynamic-images
 */
final class Bunny implements Loader
{
    /**
     * @param string $url The pull zone's URL, like "https://acme.b-cdn.net", or "" for the site's own host
     * @param array<string, string|int|bool> $params Other options for every image, like ["sharpen" => true]
     */
    public function __construct(private string $url = "", private array $params = [])
    {
    }

    public function formats(): array
    {
        // GIFs are left alone so they keep moving
        return ["jpg", "jpeg", "png", "webp", "avif"];
    }

    public function url(string $path, int $width, string $format, ?int $quality, string $version): string
    {
        $params = array_map(fn($value) => is_bool($value) ? var_export($value, true) : $value, [
            "width" => $width,
            "format" => $format === strtolower(pathinfo($path, PATHINFO_EXTENSION)) ? null : ($format === "jpg" ? "jpeg" : $format),
            "quality" => $quality,
            ...$this->params,
            "v" => $version,
        ]);

        return rtrim($this->url, "/") . "$path?" . http_build_query($params, "", "&", PHP_QUERY_RFC3986);
    }
}
