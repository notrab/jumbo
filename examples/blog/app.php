<?php

require __DIR__ . "/vendor/autoload.php";

/**
 * A blog. Extending the site gives every page and template a posts()
 * helper, as $site->posts().
 */
class Blog extends Dumbo\Jumbo\Site
{
    /**
     * Every post in content/, newest first, with its date as a timestamp
     *
     * @return array<array>
     */
    public function posts(?string $tag = null): array
    {
        $posts = array_map(function (string $slug): array {
            $post = $this->find($slug);
            // YAML can give dates as timestamps or strings, depending on the parser
            $date = $post["meta"]["date"] ?? 0;
            $post["meta"]["date"] = match (true) {
                is_int($date) => $date,
                $date instanceof DateTimeInterface => $date->getTimestamp(),
                default => (int) strtotime((string) $date),
            };

            return $post;
        }, $this->slugs());
        $posts = array_filter($posts, fn(array $post) => $tag === null || in_array($tag, $post["meta"]["tags"] ?? [], true));
        usort($posts, fn(array $a, array $b) => $b["meta"]["date"] <=> $a["meta"]["date"]);

        return $posts;
    }

    /**
     * @return array<string> Every tag used by a post
     */
    public function tags(): array
    {
        $tags = array_unique(array_merge(...array_map(fn(array $post) => $post["meta"]["tags"] ?? [], $this->posts())));
        sort($tags);

        return $tags;
    }
}

return new Blog(__DIR__, [
    "name" => "Trunk Notes",
    // Builds use this for canonical URLs, the sitemap and the feed
    "url" => getenv("SITE_URL") ?: "https://trunk-notes.example",
    "seo" => [
        "titleTemplate" => "%s - Trunk Notes",
        "description" => "Notes from an elephant who builds websites in PHP.",
    ],
    "images" => ["widths" => [480, 960, 1440]],
    "prefetch" => true,
    "viewTransitions" => true,
]);
