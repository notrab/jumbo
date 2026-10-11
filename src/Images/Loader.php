<?php

namespace Dumbo\Jumbo\Images;

/**
 * Makes the URLs of resized images. Jumbo reads each image's size and picks
 * the widths and format, and a loader turns them into a URL for whatever
 * does the resizing: Jumbo itself, or an image CDN like bunny.net.
 *
 * Set one as the images.loader config:
 *
 *   "images" => ["loader" => new Dumbo\Jumbo\Images\Bunny("https://acme.b-cdn.net")]
 */
interface Loader
{
    /**
     * The formats it can resize and convert to, like ["jpg", "png", "webp"].
     * Images in other formats keep their own URL.
     *
     * @return array<string>
     */
    public function formats(): array;

    /**
     * The URL of an image in public/ at a width and format
     *
     * @param string $path The image's URL path, encoded, like /photos/beach.jpg
     * @param int $width A width from images.widths, or the image's own
     * @param string $format One of formats(), the image's own extension when it's kept
     * @param int|null $quality From images.quality, or null for the loader's default
     * @param string $version A short hash that changes when the file does
     */
    public function url(string $path, int $width, string $format, ?int $quality, string $version): string;
}
