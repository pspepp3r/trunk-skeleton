<?php

return [
    'secret' => $_ENV['JWT_SECRET'] ?? 'trunk-insecure-default-secret',
    'algo' => $_ENV['JWT_ALGO'] ?? 'HS256',
    'ttl' => (int) ($_ENV['JWT_TTL'] ?? 3600),
];
