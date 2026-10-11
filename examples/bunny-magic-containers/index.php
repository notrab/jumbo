<?php

// Every request comes here. In FrankenPHP's worker mode, run() keeps the
// site in memory and handles each request the worker receives.
(require __DIR__ . "/app.php")->run();
