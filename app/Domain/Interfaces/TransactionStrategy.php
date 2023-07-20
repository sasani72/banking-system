<?php

namespace App\Domain\Interfaces;

use App\Domain\AccountAggregate;

interface TransactionStrategy
{
    /**
     * @param AccountAggregate $accountAggregate
     * @param $amount
     */
    public function handle(AccountAggregate $accountAggregate, $amount): void;
}
