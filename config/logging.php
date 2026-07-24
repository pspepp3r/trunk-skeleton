<?php

return [
    'default' => $_ENV['LOG_CHANNEL'] ?? 'stack',

    'channels' => [
        'stack' => [
            'driver' => 'single',
            'path' => 'php://stdout',
            'level' => 'debug',
        ],
        'file' => [
            'driver' => 'single',
            'path' => __DIR__ . '/../storage/logs/trunk.log',
            'level' => 'debug',
        ]
    ]
];
