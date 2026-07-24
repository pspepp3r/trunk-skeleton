<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Trunk\App;

// 1. Create Trunk Application
$app = new App();

// 2. Load Environment Variables & Configurations
$app->configure(dirname(__DIR__));

// 4. Load routes configuration
$routes = require __DIR__ . '/../config/routes.php';
$routes($app);

return $app;
