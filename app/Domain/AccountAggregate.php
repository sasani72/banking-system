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

    /**
     * Add AccountCreated event to create a new account
     * 
     * @param int $customerId
     * @param string $name
     * @return $this
     */
    public function createAccount(int $customerId, string $name)
    {
        $this->recordThat(new AccountCreated($name, $customerId));

        return $this;
    }

    /**
     * Add MoneyAdded event to add deposit to an account
     * 
     * @param int $amount
     * @return $this
     */
    public function addMoney(int $amount)
    {
        $this->recordThat(new MoneyAdded($amount));

        return $this;
    }

    /**
     * calculate added balance when aggregate replayed
     * 
     * @param MoneyAdded $event
     */
    public function applyMoneyAdded(MoneyAdded $event)
    {
        $this->balance += $event->amount;
    }

    /**
     * Add MoneySubtracted event to withdraw from an account
     * 
     * @param int $amount
     * @return $this
     * @throws ExceptionCouldNotSubtractMoney
     */
    public function subtractMoney(int $amount)
    {
        if (!$this->hasEnoughBalanceToSubtractMoney($amount)) {
            throw new ExceptionCouldNotSubtractMoney($amount);
        }

        $this->recordThat(new MoneySubtracted($amount));

        return $this;
    }

    /**
     * calculate subtracted balance when aggregate replayed
     * 
     * @param MoneySubtracted $event
     */
    public function applyMoneySubtracted(MoneySubtracted $event)
    {
        $this->balance -= $event->amount;
    }

    /**
     * Add MoneyTransferred event to withdraw money from an account 
     * in order to transfer money to another account
     * 
     * @param string $destinationUuid
     * @param int $amount
     * @return $this
     * @throws ExceptionCouldNotSubtractMoney
     */
    public function transferMoney(string $destinationUuid, int $amount)
    {
        if (!$this->hasEnoughBalanceToSubtractMoney($amount)) {
            throw new ExceptionCouldNotSubtractMoney($amount);
        }

        $this->recordThat(new MoneyTransferred($destinationUuid, $amount));

        return $this;
    }

    /**
     * calculate subtracted balance due to transfer when aggregate replayed
     * 
     * @param MoneyTransferred $event
     */
    public function applyMoneyTransferred(MoneyTransferred $event)
    {
        $this->balance -= $event->amount;
    }

    /**
     * get current balance calculated
     * 
     * @return int
     */
    public function getCurrentBalance()
    {
        return $this->balance;
    }

    /**
     * check if current balance is sufficient for subtract money
     * 
     * @param int $amount
     * @return bool
     */
    private function hasEnoughBalanceToSubtractMoney(int $amount): bool
    {
        return $this->balance - $amount >= $this->accountLimit;
    }
}
