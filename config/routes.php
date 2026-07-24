<?php

use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Middleware\AuthMiddleware;
use Trunk\App;
use Trunk\GraphQL\GraphQLHandler;

return function (App $app) {
    // Public auth endpoint - issues a bearer token for demo credentials
    $app->post('/login', [AuthController::class, 'login']);

    // User endpoints
    $app->get('/users', [UserController::class, 'index']);
    $app->post('/users', [UserController::class, 'create'], [AuthMiddleware::class]);

    // Path parameter pattern match demo
    $app->get('/users/{id}', [UserController::class, 'show']);
    $app->get('/users/{id}/async', [UserController::class, 'showAsync']);

    // Route model binding demo - {user} is resolved into a User entity before the handler runs
    $app->get('/users/{user}/bound', [UserController::class, 'showBound']);

    // GraphQL - resolvers query the ORM asynchronously via the same Repository promises
    $app->post('/graphql', [GraphQLHandler::class, 'handle']);
};
