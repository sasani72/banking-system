<?php

namespace App\Domain\TransactionStrategies\Deposit;

use App\Domain\AccountAggregate;
use App\Domain\Interfaces\TransactionStrategy;

class DepositTransaction implements TransactionStrategy
{
    public function handle(AccountAggregate $accountAggregate, $amount): void
    {
        $accountAggregate->addMoney($amount);
    }
}