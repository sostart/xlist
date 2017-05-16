<?php

return [

    'default' => 'memcache',

    'stores' => [

        'file' => [
            'driver' => 'file',
            'path' => __DIR__.'/../storage/framework/cache',
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'cache',
        ],
        
        'memcache' => [
            'driver' => 'memcache',
            'servers' => [
                ['127.0.0.1', '11211', 100],
            ],
        ],

        'memcached' => [
            'driver' => 'memcached',
            'persistent_id' => 'heresy',
            'servers' => [
                ['127.0.0.1', '11211', 100],
            ],
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],
    ],

    'prefix' => 'heresy',
];
