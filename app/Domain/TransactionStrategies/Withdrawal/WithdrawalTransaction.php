<?php

namespace App\Domain\TransactionStrategies\Withdrawal;

use App\Domain\AccountAggregate;
use App\Domain\Interfaces\TransactionStrategy;

class WithdrawalTransaction implements TransactionStrategy
{
    public function handle(AccountAggregate $accountAggregate, $amount): void
    {
        $accountAggregate->subtractMoney($amount);
    }
}