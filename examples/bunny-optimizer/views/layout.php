<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($site->seo($page["meta"] ?? [], $context->req->path(), $title ?? $page["title"] ?? null)["title"]) ?></title>
    <link rel="stylesheet" href="<?= e($site->file("/style.css")) ?>">
</head>
<body>
    <nav><a href="/">Watering Hole</a> <a href="/gallery">Gallery</a></nav>
    <main><?= $page["html"] ?? $slot ?></main>
</body>
</html>
