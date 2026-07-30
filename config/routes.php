<?php

use App\Controllers\AuthController;
use App\Middleware\AuthMiddleware;
use Trunk\App;

return function (App $app) {
    $app->post('/register', [AuthController::class, 'register']);
    $app->post('/login', [AuthController::class, 'login']);

    $app->get('/me', [AuthController::class, 'me'], [AuthMiddleware::class]);
};
