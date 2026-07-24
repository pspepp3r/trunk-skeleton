<?php

use App\Providers\GraphQLSchemaServiceProvider;
use Trunk\Providers\AuthServiceProvider;
use Trunk\Providers\DatabaseServiceProvider;
use Trunk\Providers\EventServiceProvider;
use Trunk\Providers\LogServiceProvider;
use Trunk\Providers\SessionServiceProvider;

return [
    'name' => $_ENV['APP_NAME'] ?? 'TrunkApp',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'port' => $_ENV['APP_PORT'] ?? '8080',

    'providers' => [
        LogServiceProvider::class,
        DatabaseServiceProvider::class,
        SessionServiceProvider::class,
        EventServiceProvider::class,
        AuthServiceProvider::class,
        GraphQLSchemaServiceProvider::class,
    ],
];
