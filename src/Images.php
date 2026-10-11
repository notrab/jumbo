<?php

namespace Dumbo\Jumbo;

use Dumbo\Context;
use Dumbo\Jumbo\Images\Loader;
use Dumbo\Jumbo\Images\Local;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\ResponseInterface;

/**
 * Resized and converted copies of the images in public/. The images.loader
 * config makes their URLs, and resizes them with GD on this server by
 * default (see Images\Local), or with an image CDN like bunny.net.
 *
 * Only the configured widths and each image's own width are used, so a
 * client can't ask for an endless number of sizes, and CDNs cache fewer.
 */
final class Images
{
    private const MIME_TYPES = [
        "jpg" => "image/jpeg",
        "jpeg" => "image/jpeg",
        "png" => "image/png",
        "webp" => "image/webp",
        "avif" => "image/avif",
    ];

    /** @var array<string, array{int, int}|false> */
    private array $sizes = [];

    private readonly Loader $loader;

    public function __construct(private Site $site)
    {
        $this->loader = $site->config["images"]["loader"]
            ?? new Local($site->config["images"]["cache"] ?? sys_get_temp_dir() . "/jumbo-images-" . substr(md5($site->root), 0, 8));
    }

    /**
     * The URL of an image in public/ at a width and format. The width is
     * rounded up to the next configured width, and images that can't be
     * resized, like SVGs, keep their own URL.
     */
    public function url(string $src, ?int $width = null, ?string $format = null): string
    {
        $file = $this->source($src);
        $size = $file ? $this->size($file) : null;

        if ($size === null) {
            return $src;
        }

        $path = "/" . implode("/", array_map(rawurlencode(...), explode("/", ltrim(rawurldecode(self::path($src)), "/"))));

        return $this->loader->url(
            $path,
            $this->snap($width ?? $size[0], $size[0]),
            $this->format($format, self::extension($file)),
            $this->quality(),
            substr(md5(filemtime($file) . ":" . filesize($file)), 0, 8),
        );
    }

    /**
     * An <img> tag with a srcset of widths up to twice the given width, plus
     * its width and height so the page doesn't shift as it loads
     *
     * @param array<string, string|bool|null> $attributes Extra attributes, or overrides like "loading" => "eager"
     */
    public function tag(string $src, string $alt = "", ?int $width = null, ?string $sizes = null, ?string $format = null, array $attributes = []): string
    {
        $file = $this->source($src);
        $size = $file ? $this->size($file) : null;

        if ($size === null) {
            return self::element(["src" => $src, "alt" => $alt, ...$attributes]);
        }

        [$naturalWidth, $naturalHeight] = $size;
        $display = min($width ?? $naturalWidth, $naturalWidth);
        $widths = array_filter($this->widths($naturalWidth), fn(int $w) => $w <= $display * 2);
        $widths = array_unique([...$widths, $this->snap($display, $naturalWidth)]);
        sort($widths);

        return self::element([
            "src" => $this->url($src, $display, $format),
            "srcset" => implode(", ", array_map(fn(int $w) => $this->url($src, $w, $format) . " {$w}w", $widths)),
            "sizes" => $sizes ?? "(min-width: {$display}px) {$display}px, 100vw",
            "width" => (string) $display,
            "height" => (string) max(1, (int) round($naturalHeight * $display / $naturalWidth)),
            "alt" => $alt,
            "loading" => "lazy",
            "decoding" => "async",
            ...$attributes,
        ]);
    }

    /**
     * Whether an image in public/ can be resized
     */
    public function resizable(string $src): bool
    {
        $file = $this->source($src);

        return $file !== null && $this->size($file) !== null;
    }

    /**
     * The route handler for /_image/{width}/{file}, when images are resized
     * on this server
     */
    public function serve(Context $context): ResponseInterface
    {
        if (!$this->loader instanceof Local) {
            return $context->notFound();
        }

        $width = (int) $context->req->param("width");
        $path = "/" . rawurldecode($context->req->param("file"));
        $file = $this->source($path);
        $format = null;

        // A second extension is the format to convert to
        if ($file === null || $this->size($file) === null) {
            $format = self::extension($path);
            $file = $this->source(substr($path, 0, -strlen($format) - 1));
        }

        $size = $file ? $this->size($file) : null;
        $format ??= $file ? self::extension($file) : null;

        if ($size === null || !in_array($width, $this->widths($size[0]), true) || !in_array($format, $this->formats(), true)) {
            return $context->notFound();
        }

        $cache = $this->loader->make($file, $width, $format, $this->quality());

        return $context->send(Stream::create(fopen($cache, "r")), self::MIME_TYPES[$format], 200, [
            // Versioned URLs change when the image does
            "Cache-Control" => $context->req->query("v") !== null ? "public, max-age=31536000, immutable" : "public, max-age=3600",
        ]);
    }

    /**
     * The widths an image can be made at: the configured ones smaller than
     * it, and its own
     *
     * @return array<int>
     */
    private function widths(int $natural): array
    {
        $widths = array_filter(array_map(intval(...), $this->site->config["images"]["widths"]), fn(int $w) => $w > 0 && $w < $natural);
        $widths[] = $natural;
        sort($widths);

        return array_values(array_unique($widths));
    }

    private function snap(int $width, int $natural): int
    {
        foreach ($this->widths($natural) as $allowed) {
            if ($allowed >= $width) {
                return $allowed;
            }
        }

        return $natural;
    }

    private function format(?string $format, string $extension): string
    {
        $format = strtolower($format ?? $this->site->config["images"]["format"] ?? $extension);

        return in_array($format, $this->formats(), true) ? $format : $extension;
    }

    /**
     * @return array<string> The formats the loader can resize and convert to
     */
    private function formats(): array
    {
        return array_map(strtolower(...), $this->loader->formats());
    }

    private function quality(): ?int
    {
        $quality = $this->site->config["images"]["quality"];

        return $quality === null ? null : (int) $quality;
    }

    /**
     * The file in public/ for a URL path, or null if there's none
     */
    private function source(string $src): ?string
    {
        if (!str_starts_with($src, "/") || str_starts_with($src, "//")) {
            return null;
        }

        $root = realpath($this->site->path("public"));
        $file = $root ? realpath($root . "/" . ltrim(rawurldecode(self::path($src)), "/")) : false;

        return $file && is_file($file) && str_starts_with($file, $root . DIRECTORY_SEPARATOR) ? $file : null;
    }

    /**
     * The width and height of an image as it's shown, or null for anything
     * the loader can't resize
     *
     * @return array{int, int}|null
     */
    private function size(string $file): ?array
    {
        if (!array_key_exists($file, $this->sizes)) {
            $size = in_array(self::extension($file), $this->formats(), true) ? @getimagesize($file) : false;

            // Photos turned on their side are shown upright
            if ($size && in_array(self::orientation($file), [5, 6, 7, 8], true)) {
                [$size[0], $size[1]] = [$size[1], $size[0]];
            }

            $this->sizes[$file] = $size && $size[0] > 0 ? [$size[0], $size[1]] : false;
        }

        return $this->sizes[$file] ?: null;
    }

    /**
     * The EXIF orientation of a JPEG, 1 when it's upright
     *
     * @internal
     */
    public static function orientation(string $file): int
    {
        if (!in_array(self::extension($file), ["jpg", "jpeg"], true) || !function_exists("exif_read_data")) {
            return 1;
        }

        return (int) (@exif_read_data($file)["Orientation"] ?? 1);
    }

    private static function element(array $attributes): string
    {
        $html = "<img";

        foreach ($attributes as $name => $value) {
            if ($value === null || $value === false) {
                continue;
            }

            $html .= $value === true ? " $name" : " $name=\"" . e((string) $value) . "\"";
        }

        return "$html>";
    }

    private static function path(string $src): string
    {
        return strtok($src, "?#") ?: "";
    }

    private static function extension(string $path): string
    {
        return strtolower(pathinfo(self::path($path), PATHINFO_EXTENSION));
    }
}
