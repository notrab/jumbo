<?php

use Dumbo\Context;
use Dumbo\Jumbo\Page;

// A cheap page for health checks and uptime monitors
return new Page(render: fn(Context $context) => $context->text("ok"), prerender: false);
