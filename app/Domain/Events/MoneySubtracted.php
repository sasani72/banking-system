<?php

namespace App\Domain\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class MoneySubtracted extends ShouldBeStored
{
    public function __construct(
        public int $amount
    ) {}
}