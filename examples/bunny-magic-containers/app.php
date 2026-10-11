<?php

require __DIR__ . "/vendor/autoload.php";

/**
 * A guestbook. Entries are saved in GUESTBOOK_DIR, which the Dockerfile
 * points at /data, where Magic Containers can mount a persistent volume.
 */
class Guestbook extends Dumbo\Jumbo\Site
{
    /**
     * The latest entries, newest first
     *
     * @return array<array{name: string, message: string, at: int}>
     */
    public function entries(int $limit = 50): array
    {
        $file = $this->entriesFile();
        $lines = is_file($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

        return array_reverse(array_map(fn(string $line) => json_decode($line, true), array_slice($lines, -$limit)));
    }

    public function sign(string $name, string $message): void
    {
        $file = $this->entriesFile();

        if (!is_dir(dirname($file))) {
            mkdir(dirname($file), 0700, true);
        }

        file_put_contents($file, json_encode(["name" => $name, "message" => $message, "at" => time()]) . "\n", FILE_APPEND | LOCK_EX);
    }

    private function entriesFile(): string
    {
        return (getenv("GUESTBOOK_DIR") ?: __DIR__ . "/storage") . "/entries.jsonl";
    }
}

return new Guestbook(__DIR__, [
    "name" => "Herd Guestbook",
    "seo" => ["titleTemplate" => "%s - Herd Guestbook"],
]);
