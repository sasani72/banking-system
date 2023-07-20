<?php

namespace App\Domain\Projectors;

use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use App\Domain\Events\AccountCreated;
use App\Domain\Events\MoneyAdded;
use App\Domain\Events\MoneySubtracted;
use App\Domain\Events\MoneyTransferred;
use App\Domain\Services\AccountService;
use Illuminate\Contracts\Queue\ShouldQueue;

class AccountProjector extends Projector implements ShouldQueue
{
    protected $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function onAccountCreated(AccountCreated $event)
    {
        $this->accountService->createAccount([
            'uuid' => $event->aggregateRootUuid(),
            'name' => $event->name,
            'customer_id' => $event->customerId,
        ]);
    }

    public function onMoneyAdded(MoneyAdded $event)
    {
        $this->accountService->addMoneyToAccount($event->aggregateRootUuid(), $event->amount);
    }

    public function onMoneySubtracted(MoneySubtracted $event)
    {
        $this->accountService->subtractMoneyFromAccount($event->aggregateRootUuid(), $event->amount);
    }

    public function onMoneyTransferred(MoneyTransferred $event)
    {
        $this->accountService->subtractMoneyFromAccount($event->aggregateRootUuid(), $event->amount);
    }
}