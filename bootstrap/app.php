<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Trunk\App;

// Create Trunk Application
$app = new App();

// Load Environment Variables & Configurations
$app->configure(dirname(__DIR__));

// Load middlewares
$middlewares = require __DIR__ . '/../config/middleware.php';
$middlewares($app);

// Load routes configuration
$routes = require __DIR__ . '/../config/routes.php';
$routes($app);

return $app;
