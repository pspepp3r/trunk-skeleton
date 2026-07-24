<?php

namespace App\Tests\Controllers;

use App\Controllers\UserController;
use App\Entities\User;
use App\Events\UserRegistered;
use App\Requests\CreateUserRequest;
use PHPUnit\Framework\TestCase;
use React\Http\Message\ServerRequest;
use ReflectionProperty;
use Trunk\Event\Dispatcher;
use Trunk\Log\Logger;
use Trunk\ORM\EntityManager;
use Trunk\ORM\Repository;

use function React\Async\await;
use function React\Promise\resolve;

class UserControllerTest extends TestCase
{
    public function testCreateReturns201AndDispatchesUserRegistered(): void
    {
        $persistedUser = new User();
        $persistedUser->setName('Alice');
        $persistedUser->setEmail('alice@example.com');
        // Mirrors what Repository::persist() does after a real INSERT: it back-fills the
        // id via reflection (there's deliberately no public setId() on the entity).
        (new ReflectionProperty(User::class, 'id'))->setValue($persistedUser, 1);

        $repository = $this->createMock(Repository::class);
        $repository->method('persist')->willReturn(resolve($persistedUser));

        $entityManager = $this->createMock(EntityManager::class);
        $entityManager->method('getRepository')->with(User::class)->willReturn($repository);

        $logger = $this->createMock(Logger::class);

        $dispatcher = $this->createMock(Dispatcher::class);
        $dispatcher->expects($this->once())
            ->method('dispatchAsync')
            ->with($this->isInstanceOf(UserRegistered::class));

        $controller = new UserController($logger, $entityManager, $dispatcher);

        $httpRequest = (new ServerRequest('POST', '/users'))->withParsedBody([
            'name' => 'Alice',
            'email' => 'alice@example.com',
        ]);
        $formRequest = new CreateUserRequest($httpRequest);
        $formRequest->validate();

        $response = await($controller->create($formRequest));

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame(
            ['id' => 1, 'name' => 'Alice', 'email' => 'alice@example.com'],
            json_decode((string) $response->getBody(), true)
        );
    }

    public function testCreateReturns500WhenPersistRejects(): void
    {
        $repository = $this->createMock(Repository::class);
        $repository->method('persist')->willReturn(\React\Promise\reject(new \RuntimeException('db is down')));

        $entityManager = $this->createMock(EntityManager::class);
        $entityManager->method('getRepository')->willReturn($repository);

        $logger = $this->createMock(Logger::class);

        $dispatcher = $this->createMock(Dispatcher::class);
        $dispatcher->expects($this->never())->method('dispatchAsync');

        $controller = new UserController($logger, $entityManager, $dispatcher);

        $httpRequest = (new ServerRequest('POST', '/users'))->withParsedBody([
            'name' => 'Alice',
            'email' => 'alice@example.com',
        ]);
        $formRequest = new CreateUserRequest($httpRequest);
        $formRequest->validate();

        $response = await($controller->create($formRequest));

        $this->assertSame(500, $response->getStatusCode());
    }
}
