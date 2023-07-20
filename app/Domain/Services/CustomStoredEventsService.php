<?php

namespace App\Domain\Services;

use App\Domain\Events\MoneyTransferred;
use App\Domain\Repositories\CustomStoredEventRepository;

class CustomStoredEventsService
{
    protected $storedEventRepository;

    public function __construct(CustomStoredEventRepository $storedEventRepository)
    {
        $this->storedEventRepository = $storedEventRepository;
    }

    public function getTransferHistory(string $uuid)
    {
        return $this->storedEventRepository->retrieveAllWhereEvent(MoneyTransferred::class, $uuid);
    }
}