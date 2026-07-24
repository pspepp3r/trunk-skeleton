<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response as ReactResponse;
use Trunk\Auth\Interface\TokenServiceInterface;
use Trunk\Http\Response;

class AuthController
{
    // Demo credentials only — replace with a real user lookup (e.g. via the ORM) in a real app.
    private const DEMO_EMAIL = 'demo@trunk.dev';
    private const DEMO_PASSWORD = 'password';

    public function __construct(private readonly TokenServiceInterface $tokens)
    {
    }

    public function login(ServerRequestInterface $request): ReactResponse
    {
        $body = $request->getParsedBody() ?? [];
        $email = $body['email'] ?? '';
        $password = $body['password'] ?? '';

        if ($email !== self::DEMO_EMAIL || $password !== self::DEMO_PASSWORD) {
            return Response::json(['error' => 'Invalid credentials'], 401);
        }

        $token = $this->tokens->issue(['sub' => $email]);

        return Response::json(['token' => $token]);
    }
}
