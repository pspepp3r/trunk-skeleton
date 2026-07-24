<?php

namespace App\GraphQL;

use App\Entities\User;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Trunk\ORM\EntityManager;

class QueryType extends ObjectType
{
    public function __construct(EntityManager $entityManager)
    {
        $userType = new UserType();

        parent::__construct([
            'name' => 'Query',
            'fields' => [
                'user' => [
                    'type' => $userType,
                    'args' => ['id' => Type::nonNull(Type::id())],
                    // Returns a React\Promise\PromiseInterface directly - graphql-php's
                    // ReactPromiseAdapter resolves it as part of the async execution.
                    'resolve' => function ($root, array $args) use ($entityManager) {
                        return $entityManager->getRepository(User::class)->find((int) $args['id']);
                    },
                ],
                'users' => [
                    'type' => Type::listOf($userType),
                    'resolve' => function () use ($entityManager) {
                        return $entityManager->getRepository(User::class)->findAll();
                    },
                ],
            ],
        ]);
    }
}
