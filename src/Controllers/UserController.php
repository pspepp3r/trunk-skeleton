<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Trunk\Http\Response;
use React\Http\Message\Response as ReactResponse;
use React\Promise\PromiseInterface;
use React\EventLoop\Loop;
use Trunk\Event\Dispatcher;
use Trunk\Log\Logger;
use Trunk\ORM\EntityManager;
use App\Entities\User;
use App\Events\UserRegistered;
use App\Requests\CreateUserRequest;

class UserController
{
    private Logger $logger;
    private EntityManager $em;
    private Dispatcher $events;

    // Autowired via PSR-11 container on route dispatch
    public function __construct(Logger $logger, EntityManager $em, Dispatcher $events)
    {
        $this->logger = $logger;
        $this->em = $em;
        $this->events = $events;
    }

    public function index(ServerRequestInterface $request): ReactResponse
    {
        $this->logger->info("Fetched users listing using ORM Repository");

        /** @var \Trunk\Session\Session $session */
        $session = $request->getAttribute('session');
        $visits = $session->get('visits', 0) + 1;
        $session->set('visits', $visits);

        // Fetch using the EntityManager's Repository
        $repository = $this->em->getRepository(User::class);

        return Response::json([
            'message' => 'Please query /users/{id}/async to test the ORM query loader.',
            'session_visits' => $visits
        ]);
    }

    public function create(CreateUserRequest $request): PromiseInterface
    {
        $data = $request->validated();

        $user = new User();
        $user->setName($data['name']);
        $user->setEmail($data['email']);

        $repository = $this->em->getRepository(User::class);

        return $repository->persist($user)->then(
            function (User $user) {
                $this->logger->info("Created user {id}", ['id' => $user->getId()]);
                $this->events->dispatchAsync(new UserRegistered($user));
                return Response::json([
                    'id' => $user->getId(),
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                ], 201);
            },
            function (\Throwable $error) {
                $this->logger->error("Failed to create user: {error}", ['error' => $error->getMessage()]);
                return Response::json([
                    'error' => 'ORM Persist Failed',
                    'details' => $error->getMessage(),
                ], 500);
            }
        );
    }

    public function show(ServerRequestInterface $request, string $id): ReactResponse
    {
        $this->logger->info("Viewing user {id}", ['id' => $id]);
        return Response::json(['id' => (int)$id, 'name' => 'Alice']);
    }

    /**
     * Demo of running an async database query using the ORM repository
     */
    public function showAsync(ServerRequestInterface $request, string $id): PromiseInterface
    {
        $this->logger->info("Async DB query started via ORM for user {id}", ['id' => $id]);

        $repository = $this->em->getRepository(User::class);

        // Returns Promise resolving to User entity or null
        return $repository->find((int)$id)
            ->then(
                function (?User $user) {
                    if ($user === null) {
                        return Response::json(['error' => 'User not found in ORM'], 404);
                    }

                    $this->logger->info("Async ORM query resolved successfully");
                    return Response::json([
                        'id' => $user->getId(),
                        'name' => $user->getName(),
                        'email' => $user->getEmail(),
                        'source' => 'async_orm_data_mapper'
                    ]);
                },
                function (\Throwable $error) {
                    $this->logger->error("Async ORM query failed: {error}", ['error' => $error->getMessage()]);
                    return Response::json([
                        'error' => 'ORM Query Failed',
                        'details' => $error->getMessage()
                    ], 500);
                }
            );
    }

    /**
     * Demo of route model binding: the {user} route segment is resolved into a
     * User entity by the Router before this method is even called (a 404 is
     * returned automatically if no matching row exists) - no manual find() needed.
     */
    public function showBound(ServerRequestInterface $request, User $user): ReactResponse
    {
        $this->logger->info("Viewing bound user {id}", ['id' => $user->getId()]);

        return Response::json([
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'source' => 'route_model_binding',
        ]);
    }
}
