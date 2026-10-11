---
description: Handle forms in the page that shows them, with validation, errors and redirects.
---

# Actions

Pages handle their own forms with actions. A form posts back to its page, the action runs, and the page renders again with what happened.

```php
<?php // pages/contact.php

use Dumbo\Jumbo\Action;
use Dumbo\Jumbo\Page;

return new Page(
    render: fn($context, $site) => $site->view("contact", ["action" => $site->action($context)]),
    actions: [
        "default" => function (array $input, $context, $site) {
            if (!filter_var($input["email"] ?? "", FILTER_VALIDATE_EMAIL)) {
                return Action::fail(["email" => "Enter an email address"]);
            }

            // ...send the message
            return $context->redirect("/contact/thanks", 303);
        },
    ],
);
```

```php
<?php // views/contact.php ?>
<form method="post">
    <input name="email" value="<?= e($action?->old("email")) ?>">
    <?php if ($error = $action?->error("email")): ?>
        <p><?= e($error) ?></p>
    <?php endif ?>
    <button>Send</button>
</form>
```

An action gets the posted fields, the context and the site.

## What an action returns

- A response, like a redirect, goes straight to the browser. Redirect with a 303 after a successful post so a refresh doesn't send the form twice.
- Data shows the page again, with the data as `$action->data`.
- `Action::fail($errors)` shows the page again with a 422 status, the errors, and what was sent, so the form keeps its values.

`$site->action($context)` is the result, or `null` on a normal visit. It has `ok()`, `error($field)`, `old($field)`, `data`, `errors` and `status`.

## Several forms on one page

Give each action a name and say which one a form uses with a hidden `_action` field, or post to `?_action=subscribe`:

```html
<form method="post">
    <input type="hidden" name="_action" value="subscribe">
    <input name="email">
    <button>Subscribe</button>
</form>
```

`$site->action($context, "subscribe")` is only that action's result, so each form shows its own errors.

## Without reloading

A `fetch()` that sends `Accept: application/json` gets the result as JSON, with `ok`, `data` and `errors`:

```js
const response = await fetch("/contact", {
    method: "POST",
    headers: { Accept: "application/json" },
    body: new FormData(form),
});
const { ok, errors } = await response.json();
```

## Security

Jumbo refuses forms posted from other sites with a 403, using the `Sec-Fetch-Site` and `Origin` headers that browsers send. Allow other origins in config, or turn the check off:

```php
"actions" => [
    "origins" => ["https://admin.example.com"],
    "checkOrigin" => true,
],
```

## Hosting

Actions need the PHP server, since a static host can't take a POST. The page itself still builds for GET, and `jumbo build` lists the pages with actions so you know which ones need a server. For endpoints that aren't pages, add routes to `$site->app` as usual.
