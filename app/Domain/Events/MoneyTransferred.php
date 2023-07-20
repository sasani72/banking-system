<?php

namespace App\Domain\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class MoneyTransferred extends ShouldBeStored
{
    public function __construct(
        public string $destinationUuid,
        public int $amount
    ) {}
}