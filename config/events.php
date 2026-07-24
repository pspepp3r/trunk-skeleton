<?php

use App\Events\UserRegistered;
use App\Listeners\LogUserRegistrationListener;

return [
    UserRegistered::class => [
        LogUserRegistrationListener::class,
    ],
];
