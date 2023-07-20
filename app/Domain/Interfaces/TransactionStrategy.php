<?php

namespace App\Domain\Interfaces;

use App\Domain\AccountAggregate;

interface TransactionStrategy
{
    public function handle(AccountAggregate $accountAggregate, $amount): void;
}