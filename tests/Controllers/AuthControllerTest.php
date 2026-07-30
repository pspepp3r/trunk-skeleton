<?php

namespace App\Tests\Controllers;

use App\Controllers\AuthController;
use App\Entities\User;
use App\Requests\RegisterRequest;
use PHPUnit\Framework\TestCase;
use React\Http\Message\ServerRequest;
use Trunk\Auth\JwtTokenService;
use Trunk\ORM\EntityManager;
use Trunk\ORM\Repository;

use function React\Async\await;
use function React\Promise\resolve;

class AuthControllerTest extends TestCase
{
    private JwtTokenService $tokens;
    private Repository $repository;
    private AuthController $controller;

    protected function setUp(): void
    {
        // A real (not mocked) token service - it's pure/deterministic, so exercising the
        // actual issue-then-verify round trip is more useful here than mocking it away.
        $this->tokens = new JwtTokenService(secret: str_repeat('a', 32));

        $this->repository = $this->createMock(Repository::class);

        $entityManager = $this->createMock(EntityManager::class);
        $entityManager->method('getRepository')->willReturn($this->repository);

        $this->controller = new AuthController($this->tokens, $entityManager);
    }

    private function existingUser(): User
    {
        $user = new User();
        $user->setName('Ada');
        $user->setEmail('ada@example.com');
        $user->setPassword(password_hash('secret123', PASSWORD_DEFAULT));
        (new \ReflectionProperty($user, 'id'))->setValue($user, 1);

        return $user;
    }

    public function testRegisterCreatesAUserAndReturnsAToken(): void
    {
        $this->repository->method('findOneBy')->willReturn(resolve(null));
        $this->repository->method('persist')->willReturnCallback(function (User $user) {
            (new \ReflectionProperty($user, 'id'))->setValue($user, 1);
            return resolve($user);
        });

        $request = (new ServerRequest('POST', '/register'))->withParsedBody([
            'name' => 'Ada',
            'email' => 'ada@example.com',
            'password' => 'secret123',
        ]);
        $formRequest = new RegisterRequest($request);
        $formRequest->validate();

        $response = await($this->controller->register($formRequest));

        $this->assertSame(201, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertSame('ada@example.com', $body['user']['email']);

        $claims = $this->tokens->verify($body['token']);
        $this->assertSame('1', $claims['sub']);
    }

    public function testRegisterRejectsADuplicateEmail(): void
    {
        $this->repository->method('findOneBy')->willReturn(resolve($this->existingUser()));

        $request = (new ServerRequest('POST', '/register'))->withParsedBody([
            'name' => 'Ada',
            'email' => 'ada@example.com',
            'password' => 'secret123',
        ]);
        $formRequest = new RegisterRequest($request);
        $formRequest->validate();

        $response = await($this->controller->register($formRequest));

        $this->assertSame(409, $response->getStatusCode());
    }

    public function testValidCredentialsReturnAVerifiableToken(): void
    {
        $this->repository->method('findOneBy')->willReturn(resolve($this->existingUser()));

        $request = (new ServerRequest('POST', '/login'))->withParsedBody([
            'email' => 'ada@example.com',
            'password' => 'secret123',
        ]);

        $response = await($this->controller->login($request));

        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertArrayHasKey('token', $body);

        $claims = $this->tokens->verify($body['token']);
        $this->assertSame('1', $claims['sub']);
    }

    public function testInvalidPasswordReturns401(): void
    {
        $this->repository->method('findOneBy')->willReturn(resolve($this->existingUser()));

        $request = (new ServerRequest('POST', '/login'))->withParsedBody([
            'email' => 'ada@example.com',
            'password' => 'wrong-password',
        ]);

        $response = await($this->controller->login($request));

        $this->assertSame(401, $response->getStatusCode());
    }

    public function testUnknownEmailReturns401(): void
    {
        $this->repository->method('findOneBy')->willReturn(resolve(null));

        $request = (new ServerRequest('POST', '/login'))->withParsedBody([
            'email' => 'nobody@example.com',
            'password' => 'secret123',
        ]);

        $response = await($this->controller->login($request));

        $this->assertSame(401, $response->getStatusCode());
    }
}
