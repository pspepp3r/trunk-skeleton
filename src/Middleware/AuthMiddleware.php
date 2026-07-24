<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;
use Trunk\Auth\Exception\InvalidTokenException;
use Trunk\Auth\Interface\TokenServiceInterface;
use Trunk\Http\Response;
use Trunk\Middleware\Interface\MiddlewareInterface;

use function React\Promise\resolve;

class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly TokenServiceInterface $tokens)
    {
    }

    public function process(ServerRequestInterface $request, callable $next): PromiseInterface
    {
        $header = $request->getHeaderLine('Authorization');

        if (!str_starts_with($header, 'Bearer ')) {
            return resolve(Response::json(['error' => 'Unauthorized', 'message' => 'Missing bearer token'], 401));
        }

        $token = substr($header, 7);

        try {
            $claims = $this->tokens->verify($token);
        } catch (InvalidTokenException $e) {
            return resolve(Response::json(['error' => 'Unauthorized', 'message' => $e->getMessage()], 401));
        }

        $request = $request->withAttribute('auth', $claims);

        return $next($request);
    }
}
