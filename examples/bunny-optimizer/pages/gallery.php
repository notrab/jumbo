<?php $this->layout("layout", ["title" => "Gallery"]) ?>

<h1>Gallery</h1>
<p>Made with <code>$site->img()</code>, which sizes each image for a third of the page on wide screens.</p>

<div class="grid">
    <?php foreach (["sunrise" => "Sunrise", "meadow" => "The meadow", "dusk" => "Dusk"] as $photo => $caption): ?>
        <figure>
            <?= $site->img("/photos/$photo.jpg", $caption, 480, sizes: "(min-width: 60rem) 20rem, 100vw") ?>
            <figcaption><?= e($caption) ?></figcaption>
        </figure>
    <?php endforeach ?>
</div>
