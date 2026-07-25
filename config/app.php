<?php

return [
    'name' => $_ENV['APP_NAME'] ?? 'TrunkApp',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'port' => $_ENV['APP_PORT'] ?? '8080',

    'providers' => [] // Add service provider classes within this array.
];
