<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key'    => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'stripe' => [
        'model'  => \App\Models\User\User::class,
        'key'    => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'github' => [
        'client_id'     => env('OAUTH_GITHUB_CLIENT_ID'),
        'client_secret' => env('OAUTH_GITHUB_SECRET_ID'),
        'redirect'      => env('OAUTH_GITHUB_CALLBACK'),
    ],

    'bitbucket' => [
        'client_id'     => env('OAUTH_BITBUCKET_CLIENT_ID'),
        'client_secret' => env('OAUTH_BITBUCKET_SECRET_ID'),
        'redirect'      => env('OAUTH_BITBUCKET_CALLBACK'),
    ],

    'digitalocean' => [
        'client_id'     => env('OAUTH_DIGITALOCEAN_CLIENT_ID'),
        'client_secret' => env('OAUTH_DIGITALOCEAN_SECRET_ID'),
        'redirect'      => env('OAUTH_DIGITALOCEAN_CALLBACK'),
    ],

    'slack' => [
        'client_id'     => env('SLACK_KEY'),
        'client_secret' => env('SLACK_SECRET'),
        'redirect'      => env('SLACK_REDIRECT_URI'),
    ],

    'gitlab' => [
        'client_id'     => env('GITLAB_KEY'),
        'client_secret' => env('GITLAB_SECRET'),
        'redirect'      => env('GITLAB_REDIRECT_URI'),
    ],
];
