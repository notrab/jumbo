<?php

namespace Dumbo\Jumbo\Images;

use Dumbo\Jumbo\Images;

/**
 * Resizes images on this server with GD, the default loader:
 *
 *   /_image/640/photos/beach.jpg.webp    beach.jpg, 640px wide, as WebP
 *   /_image/1280/photos/beach.jpg        beach.jpg, 1280px wide, as JPEG
 *
 * Copies are cached on disk, and static builds write each one the pages use.
 */
final class Local implements Loader
{
    /**
     * @param string|null $cache Where resized copies are kept, the temp directory by default
     */
    public function __construct(public readonly ?string $cache = null)
    {
    }

    public function formats(): array
    {
        if (!function_exists("imagecreatefromstring")) {
            return [];
        }

        return array_keys(array_filter([
            "jpg" => true,
            "jpeg" => true,
            "png" => true,
            "webp" => function_exists("imagewebp") && function_exists("imagecreatefromwebp"),
            "avif" => function_exists("imageavif") && function_exists("imagecreatefromavif") && !empty(gd_info()["AVIF Support"]),
        ]));
    }

    public function url(string $path, int $width, string $format, ?int $quality, string $version): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return "/_image/$width$path" . ($format === $extension ? "" : ".$format") . "?v=$version";
    }

    /**
     * A resized copy of a file, made the first time it's asked for
     *
     * @return string The copy's path
     */
    public function make(string $file, int $width, string $format, ?int $quality): string
    {
        $cache = ($this->cache ?? sys_get_temp_dir() . "/jumbo-images") . "/" . sha1("$file:" . filemtime($file) . ":$width:$quality") . ".$format";

        if (!is_file($cache)) {
            $this->resize($file, $cache, $width, $format, $quality ?? -1);
        }

        return $cache;
    }

    private function resize(string $file, string $cache, int $width, string $format, int $quality): void
    {
        $image = @imagecreatefromstring(file_get_contents($file)) ?: throw new \RuntimeException("GD couldn't read $file");
        $image = self::orient($image, Images::orientation($file));

        $naturalWidth = imagesx($image);
        $naturalHeight = imagesy($image);
        $height = max(1, (int) round($naturalHeight * $width / $naturalWidth));
        $resized = imagecreatetruecolor($width, $height);

        if (in_array($format, ["jpg", "jpeg"], true)) {
            // JPEGs have no transparency, so it becomes white
            imagefill($resized, 0, 0, imagecolorallocate($resized, 255, 255, 255));
        } else {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            imagefill($resized, 0, 0, imagecolorallocatealpha($resized, 0, 0, 0, 127));
        }

        imagecopyresampled($resized, $image, 0, 0, 0, 0, $width, $height, $naturalWidth, $naturalHeight);

        if (!is_dir(dirname($cache))) {
            mkdir(dirname($cache), 0777, true);
        }

        // Written then moved, so a request never reads a half-written file
        $temporary = "$cache." . getmypid() . ".tmp";

        match ($format) {
            "jpg", "jpeg" => imagejpeg($resized, $temporary, $quality),
            "png" => imagepng($resized, $temporary),
            "webp" => imagewebp($resized, $temporary, $quality),
            "avif" => imageavif($resized, $temporary, $quality),
        };

        rename($temporary, $cache);
    }

    private static function orient(\GdImage $image, int $orientation): \GdImage
    {
        if (in_array($orientation, [2, 4, 5, 7], true)) {
            imageflip($image, $orientation === 4 ? IMG_FLIP_VERTICAL : IMG_FLIP_HORIZONTAL);
        }

        // GD turns images anticlockwise
        $angle = match ($orientation) {
            3 => 180,
            5, 8 => 90,
            6, 7 => -90,
            default => 0,
        };

        return $angle === 0 ? $image : imagerotate($image, $angle, 0);
    }
}
