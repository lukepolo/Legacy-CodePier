<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Connection
    |--------------------------------------------------------------------------
    |
    | This is the name of the Redis connection where Horizon will store the
    | meta information required for it to function. It includes the list
    | of supervisors, failed jobs, job metrics, and other information.
    |
    */

    'use' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Queue Wait Time Thresholds
    |--------------------------------------------------------------------------
    |
    | This option allows you to configure when the LongWaitDetected event
    | will be fired. Every connection / queue combination may have its
    | own, unique threshold (in seconds) before this event is fired.
    |
    */

    'waits' => [
        'redis:default' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Worker Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may define the queue worker settings used by your application
    | in all environments. These supervisors and settings handle all your
    | queued jobs and will be provisioned by Horizon during deployment.
    |
    */

    'environments' => [
        'production' => [
            'default-1' => [
                'connection' => 'redis',
                'queue' => ['default'],
                'balance' => 'auto',
                'processes' => 5,
                'tries' => 3,
            ],
            'server_features-1' => [
                'connection' => 'redis',
                'queue' => ['server_features'],
                'balance' => 'auto',
                'processes' => 10,
                'tries' => 3,
            ],
            'server_commands-1' => [
                'connection' => 'redis',
                'queue' => ['server_commands'],
                'balance' => 'auto',
                'processes' => 10,
                'tries' => 3,
            ],
            'server_provisioning-1' => [
                'connection' => 'redis',
                'queue' => ['server_provisioning'],
                'balance' => 'auto',
                'processes' => 10,
                'tries' => 3,
            ],
        ],

        'local' => [
            'default-1' => [
                'connection' => 'redis',
                'queue' => ['default'],
                'balance' => 'auto',
                'processes' => 5,
                'tries' => 3,
            ],
            'server_features-1' => [
                'connection' => 'redis',
                'queue' => ['server_features'],
                'balance' => 'auto',
                'processes' => 3,
                'tries' => 3,
            ],
            'server_commands-1' => [
                'connection' => 'redis',
                'queue' => ['server_commands'],
                'balance' => 'auto',
                'processes' => 3,
                'tries' => 3,
            ],
            'server_provisioning-1' => [
                'connection' => 'redis',
                'queue' => ['server_provisioning'],
                'balance' => 'auto',
                'processes' => 3,
                'tries' => 3,
            ],
        ],
    ],

];
