<?php

namespace App\Events;

use App\Entities\User;

class UserRegistered
{
    public function __construct(public readonly User $user)
    {
    }
}
