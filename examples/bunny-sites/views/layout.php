<?php $seo = $site->seo($page["meta"], $context->req->path(), $context->req->path() === "/" ? null : $page["title"]) ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($seo["title"]) ?></title>
    <meta name="description" content="<?= e($seo["description"]) ?>">
    <?php if ($seo["canonical"]): ?>
        <link rel="canonical" href="<?= e($seo["canonical"]) ?>">
    <?php endif ?>
    <?php if ($seo["noindex"]): ?>
        <meta name="robots" content="noindex">
    <?php endif ?>
    <link rel="stylesheet" href="<?= e($site->file("/style.css")) ?>">
</head>
<body>
    <nav><a href="/">Trunk Show</a> <a href="/schedule">Schedule</a> <a href="/venue">Venue</a></nav>
    <main><?= $page["html"] ?></main>
</body>
</html>
