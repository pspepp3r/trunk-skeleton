<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use Trunk\Log\Logger;

class LogUserRegistrationListener
{
    public function __construct(private readonly Logger $logger)
    {
    }

    public function __invoke(UserRegistered $event): void
    {
        $this->logger->info('User registered: {id} ({email})', [
            'id' => $event->user->getId(),
            'email' => $event->user->getEmail(),
        ]);
    }
}
