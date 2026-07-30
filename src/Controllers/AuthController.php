<?php

namespace App\Controllers;

use App\Entities\User;
use App\Requests\RegisterRequest;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;
use Trunk\Auth\Interface\TokenServiceInterface;
use Trunk\Http\Response;
use Trunk\ORM\EntityManager;

class AuthController
{
    public function __construct(
        private readonly TokenServiceInterface $tokens,
        private readonly EntityManager $em,
    ) {
    }

    public function register(RegisterRequest $request): PromiseInterface
    {
        $data = $request->validated();
        $repository = $this->em->getRepository(User::class);

        return $repository->findOneBy('email', $data['email'])->then(
            function (?User $existing) use ($data, $repository) {
                if ($existing !== null) {
                    return Response::json(['error' => 'A user with that email already exists'], 409);
                }

                $user = new User();
                $user->setName($data['name']);
                $user->setEmail($data['email']);
                $user->setPassword(password_hash($data['password'], PASSWORD_DEFAULT));

                return $repository->persist($user)->then(function (User $user) {
                    $token = $this->tokens->issue(['sub' => (string) $user->getId()]);

                    return Response::json([
                        'token' => $token,
                        'user' => [
                            'id' => $user->getId(),
                            'name' => $user->getName(),
                            'email' => $user->getEmail(),
                        ],
                    ], 201);
                });
            }
        );
    }

    public function login(ServerRequestInterface $request): PromiseInterface
    {
        $body = $request->getParsedBody() ?? [];
        $email = $body['email'] ?? '';
        $password = $body['password'] ?? '';

        return $this->em->getRepository(User::class)->findOneBy('email', $email)->then(
            function (?User $user) use ($password) {
                if ($user === null || !password_verify($password, $user->getPassword())) {
                    return Response::json(['error' => 'Invalid credentials'], 401);
                }

                $token = $this->tokens->issue(['sub' => (string) $user->getId()]);

                return Response::json(['token' => $token]);
            }
        );
    }

    public function me(ServerRequestInterface $request): PromiseInterface
    {
        $claims = $request->getAttribute('auth');

        return $this->em->getRepository(User::class)->find((int) $claims['sub'])->then(
            function (?User $user) {
                if ($user === null) {
                    return Response::json(['error' => 'User not found'], 404);
                }

                return Response::json([
                    'id' => $user->getId(),
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                ]);
            }
        );
    }
}
