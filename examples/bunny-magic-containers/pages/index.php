<?php

use Dumbo\Context;
use Dumbo\Jumbo\Action;
use Dumbo\Jumbo\Page;
use Dumbo\Jumbo\Site;

/**
 * The guestbook: the page lists the entries, and its form adds one
 */
return new Page(
    render: fn(Context $context, Site $site) => $site->view("guestbook", [
        "entries" => $site->entries(),
        "result" => $site->action($context),
    ]),
    // Render on each request, as the entries change
    prerender: false,
    actions: [
        "default" => function (array $input, Context $context, Site $site) {
            $name = trim((string) ($input["name"] ?? ""));
            $message = trim((string) ($input["message"] ?? ""));
            $errors = [];

            if ($name === "" || mb_strlen($name) > 40) {
                $errors["name"] = "Sign with a name of up to 40 characters";
            }

            if ($message === "" || mb_strlen($message) > 280) {
                $errors["message"] = "Leave a message of up to 280 characters";
            }

            if ($errors) {
                return Action::fail($errors);
            }

            $site->sign($name, $message);

            // Redirect after a post, so refreshing doesn't sign twice
            return $context->redirect("/", 303);
        },
    ],
);
