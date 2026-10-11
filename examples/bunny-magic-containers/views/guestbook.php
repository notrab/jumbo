<?php $this->layout("layout", ["title" => "Guestbook"]) ?>

<h1>🐘 Herd Guestbook</h1>
<p>A Jumbo site running on bunny.net Magic Containers. Sign it, and your entry is saved on the container's volume.</p>

<form method="post" class="card" novalidate>
    <label>
        Name
        <input name="name" value="<?= e($result?->old("name")) ?>" autocomplete="name">
        <?php if ($error = $result?->error("name")): ?><span class="error"><?= e($error) ?></span><?php endif ?>
    </label>

    <label>
        Message
        <textarea name="message" rows="3"><?= e($result?->old("message")) ?></textarea>
        <?php if ($error = $result?->error("message")): ?><span class="error"><?= e($error) ?></span><?php endif ?>
    </label>

    <button>Sign the guestbook</button>
</form>

<h2>Signed by</h2>

<?php if ($entries): ?>
    <ul class="entries">
        <?php foreach ($entries as $entry): ?>
            <li>
                <strong><?= e($entry["name"]) ?></strong>
                <small><?= e(date("j M Y", $entry["at"])) ?></small>
                <p><?= e($entry["message"]) ?></p>
            </li>
        <?php endforeach ?>
    </ul>
<?php else: ?>
    <p>Nobody yet. Be the first.</p>
<?php endif ?>
