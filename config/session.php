<?php

return [
    'driver' => $_ENV['SESSION_DRIVER'] ?? 'memory',
    'lifetime' => $_ENV['SESSION_LIFETIME'] ?? 3600,
    'cookie' => $_ENV['SESSION_COOKIE'] ?? 'trunk_session',
];
