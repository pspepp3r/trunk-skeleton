<?php

return [
    'default' => $_ENV['DB_CONNECTION'] ?? 'mysql',

    'connections' => [
        'mysql' => [
            'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
            'port' => $_ENV['DB_PORT'] ?? 3306,
            'database' => $_ENV['DB_DATABASE'] ?? 'forge',
            'username' => $_ENV['DB_USERNAME'] ?? 'forge',
            'password' => $_ENV['DB_PASSWORD'] ?? '',
        ],

        'pgsql' => [
            'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
            'port' => $_ENV['DB_PORT'] ?? 5432,
            'database' => $_ENV['DB_DATABASE'] ?? 'forge',
            'username' => $_ENV['DB_USERNAME'] ?? 'forge',
            'password' => $_ENV['DB_PASSWORD'] ?? '',
            'schema' => $_ENV['DB_SCHEMA'] ?? 'public',
            'sslmode' => $_ENV['DB_SSLMODE'] ?? 'prefer',
        ],

        'mongodb' => [
            'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
            'port' => $_ENV['DB_PORT'] ?? 27017,
            'database' => $_ENV['DB_DATABASE'] ?? 'forge',
            'username' => $_ENV['DB_USERNAME'] ?? '',
            'password' => $_ENV['DB_PASSWORD'] ?? '',
            'options' => [
                'database' => $_ENV['DB_AUTHENTICATION_DATABASE'] ?? 'admin',
            ],
        ],
    ],
];
