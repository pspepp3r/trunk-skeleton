<?php

namespace App\Providers;

use App\GraphQL\QueryType;
use GraphQL\Type\Schema;
use Trunk\ORM\EntityManager;
use Trunk\Providers\ServiceProvider;

class GraphQLSchemaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(Schema::class, function ($c) {
            return new Schema([
                'query' => new QueryType($c->get(EntityManager::class)),
            ]);
        });
    }
}
