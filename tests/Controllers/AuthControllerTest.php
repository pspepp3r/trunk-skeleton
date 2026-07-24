<?php

namespace App\Tests\Controllers;

use App\Controllers\AuthController;
use PHPUnit\Framework\TestCase;
use React\Http\Message\ServerRequest;
use Trunk\Auth\JwtTokenService;

class AuthControllerTest extends TestCase
{
    private AuthController $controller;
    private JwtTokenService $tokens;

    protected function setUp(): void
    {
        // A real (not mocked) token service - it's pure/deterministic, so exercising the
        // actual issue-then-verify round trip is more useful here than mocking it away.
        $this->tokens = new JwtTokenService(secret: str_repeat('a', 32));
        $this->controller = new AuthController($this->tokens);
    }

    public function testValidCredentialsReturnAVerifiableToken(): void
    {
        $request = (new ServerRequest('POST', '/login'))->withParsedBody([
            'email' => 'demo@trunk.dev',
            'password' => 'password',
        ]);

        $response = $this->controller->login($request);

        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertArrayHasKey('token', $body);

        $claims = $this->tokens->verify($body['token']);
        $this->assertSame('demo@trunk.dev', $claims['sub']);
    }

    public function testInvalidPasswordReturns401(): void
    {
        $request = (new ServerRequest('POST', '/login'))->withParsedBody([
            'email' => 'demo@trunk.dev',
            'password' => 'wrong-password',
        ]);

        $response = $this->controller->login($request);

        $this->assertSame(401, $response->getStatusCode());
    }

    public function testUnknownEmailReturns401(): void
    {
        $request = (new ServerRequest('POST', '/login'))->withParsedBody([
            'email' => 'nobody@example.com',
            'password' => 'password',
        ]);

        $response = $this->controller->login($request);

        $this->assertSame(401, $response->getStatusCode());
    }
}
