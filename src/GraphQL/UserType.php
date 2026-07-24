<?php

namespace App\GraphQL;

use App\Entities\User;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

class UserType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'User',
            'fields' => [
                'id' => Type::nonNull(Type::id()),
                'name' => Type::nonNull(Type::string()),
                'email' => Type::nonNull(Type::string()),
            ],
            'resolveField' => function (User $user, array $args, mixed $context, ResolveInfo $info) {
                return match ($info->fieldName) {
                    'id' => $user->getId(),
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    default => null,
                };
            },
        ]);
    }
}
