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
    | Horizon Redis Prefix
    |--------------------------------------------------------------------------
    |
    | This prefix will be used when storing all Horizon data in Redis. You
    | may modify the prefix when you are running multiple installations
    | of Horizon on the same server so that they don't have problems.
    |
    */

    'prefix' => env('HORIZON_PREFIX', 'codepier'),

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
    | Job Trimming Times
    |--------------------------------------------------------------------------
    |
    | Here you can configure for how long (in minutes) you desire Horizon to
    | persist the recent and failed jobs. Typically, recent jobs are kept
    | for one hour while all failed jobs are stored for an entire week.
    |
    */

    'trim' => [
        'recent' => 60,
        'failed' => 10080,
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
            'bitts' => [
                'connection' => 'redis-bitts',
                'queue' => ['bitts'],
                'balance' => 'auto',
                'processes' => 2,
                'tries' => 1,
            ],
            'server_features' => [
                'connection' => 'redis',
                'queue' => ['server_features'],
                'balance' => 'auto',
                'processes' => 3,
                'tries' => 1,
            ],
            'server_commands' => [
                'connection' => 'redis',
                'queue' => ['server_commands'],
                'balance' => 'auto',
                'processes' => 5,
                'tries' => 3,
            ],
            'site_deployments' => [
                'connection' => 'redis-deploying',
                'queue' => ['site_deployments'],
                'balance' => 'auto',
                'processes' => 5,
                'tries' => 1,
            ],
            'server_provisioning' => [
                'connection' => 'redis-provisioning',
                'queue' => ['server_provisioning'],
                'balance' => 'auto',
                'processes' => 5,
                'tries' => 1,
            ],
            'check_ssh_connection' => [
                'connection' => 'check-ssh-connections',
                'queue' => ['check_ssh_connection'],
                'balance' => 'auto',
                'processes' => 3,
                'tries' => 1,
            ],
        ],

        'local' => [
            'default-1' => [
                'connection' => 'redis',
                'queue' => ['default'],
                'balance' => 'auto',
                'processes' => 3,
                'tries' => 3,
            ],
            'bitts' => [
                'connection' => 'redis-bitts',
                'queue' => ['bitts'],
                'balance' => 'auto',
                'processes' => 30,
                'tries' => 1,
            ],
            'server_features' => [
                'connection' => 'redis',
                'queue' => ['server_features'],
                'balance' => 'auto',
                'processes' => 3,
                'tries' => 1,
            ],
            'server_commands' => [
                'connection' => 'redis',
                'queue' => ['server_commands'],
                'balance' => 'auto',
                'processes' => 3,
                'tries' => 3,
            ],
            'site_deployments' => [
                'connection' => 'redis-deploying',
                'queue' => ['site_deployments'],
                'balance' => 'auto',
                'processes' => 3,
                'tries' => 1,
            ],
            'server_provisioning' => [
                'connection' => 'redis-provisioning',
                'queue' => ['server_provisioning'],
                'balance' => 'auto',
                'processes' => 3,
                'tries' => 1,
            ],
            'check_ssh_connection' => [
                'connection' => 'check-ssh-connections',
                'queue' => ['check_ssh_connection'],
                'balance' => 'auto',
                'processes' => 1,
                'tries' => 1,
            ],
        ],
    ],

];
