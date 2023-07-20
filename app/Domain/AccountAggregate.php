<?php

namespace App\Domain;

use App\Domain\Events\AccountCreated;
use App\Domain\Events\MoneyAdded;
use App\Domain\Events\MoneySubtracted;
use App\Domain\Events\MoneyTransferred;
use App\Domain\Exceptions\ExceptionCouldNotSubtractMoney;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class AccountAggregate extends AggregateRoot
{
    protected int $balance = 0;

    protected int $accountLimit = -10;

    public function createAccount(int $customerId,string $name)
    {
        $this->recordThat(new AccountCreated($name, $customerId));

        return $this;
    }

    public function addMoney(int $amount)
    {
        $this->recordThat(new MoneyAdded($amount));

        return $this;
    }

    public function applyMoneyAdded(MoneyAdded $event)
    {
        $this->balance += $event->amount;
    }

    public function subtractMoney(int $amount)
    {
        if (!$this->hasEnoughBalanceToSubtractMoney($amount)) {
            throw new ExceptionCouldNotSubtractMoney($amount);
        }

        $this->recordThat(new MoneySubtracted($amount));

        return $this;
    }

    public function applyMoneySubtracted(MoneySubtracted $event)
    {
        $this->balance -= $event->amount;
    }

    public function transferMoney(string $destinationUuid, int $amount)
    {
        if (!$this->hasEnoughBalanceToSubtractMoney($amount)) {
            throw new ExceptionCouldNotSubtractMoney($amount);
        }

        $this->recordThat(new MoneyTransferred($destinationUuid, $amount));

        return $this;
    }

    public function applyMoneyTransferred(MoneyTransferred $event)
    {
        $this->balance -= $event->amount;
    }

    public function getCurrentBalance()
    {
        return $this->balance;
    }

    private function hasEnoughBalanceToSubtractMoney(int $amount): bool
    {
        return $this->balance - $amount >= $this->accountLimit;
    }
}