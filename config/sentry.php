<?php

return [
    'dsn' => env('SENTRY_DSN'),

    'js_dsn' => env('SENTRY_JS'),

    // capture release as git sha
     'release' => trim(exec('git log --pretty="%h" -n1 HEAD')),

    // Capture bindings on SQL queries
    'breadcrumbs.sql_bindings' => true,

    // Capture default user context
    'user_context' => true,
];
