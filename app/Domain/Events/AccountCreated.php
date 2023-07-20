<?php

namespace App\Domain\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class AccountCreated extends ShouldBeStored
{
    public function __construct(
        public string $name,
        public int $customerId,
    ) {}
}
