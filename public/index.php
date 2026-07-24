<?php

// 1. Bootstrap the Application
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 2. Start the HTTP loop server
$port = config('app.port', '8080');
$app->run("127.0.0.1:{$port}");
