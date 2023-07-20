<?php

namespace App\Domain\Repositories;

use App\Domain\Services\CustomStoredEventsService;
use App\Http\Resources\CustomerResource;
use App\Models\Account;

class AccountRepository
{
    protected $customStoredEventsService;

    public function __construct(CustomStoredEventsService $customStoredEventsService)
    {
        $this->customStoredEventsService = $customStoredEventsService;
    }

    public function create(array $data)
    {
        return Account::create([
            'customer_id' => $data['customer_id'],
            'uuid' => $data['uuid'],
            'name' => $data['name'],
        ]);
    }

    public function getByUuid(string $uuid): ?Account
    {
        return Account::where('uuid', $uuid)->first();
    }

    public function save(Account $account): void
    {
        $account->save();
    }

    public function loadRelationships(Account $account)
    {
        return $account->load('customer');
    }

    public function getTransferHistoryByUuid(string $uuid)
    {
        $transferEvents = $this->customStoredEventsService->getTransferHistory($uuid);

        $transferHistory = [];

        foreach (collect($transferEvents) as $transfer) {

            $destinationAccount = $this->getByUuid($transfer['event_properties']['destinationUuid']);
            
            if (!$destinationAccount){
                continue;
            }

            $this->loadRelationships($destinationAccount);

            $transferHistory[] = [
                'origin_account_uuid' => $uuid,
                'destination_account_uuid' => $destinationAccount->uuid,
                'transfer_date' => $transfer['created_at'],
                'destination_account_customer' => new CustomerResource($destinationAccount->customer),
                'amount' => $transfer['event_properties']['amount'],
            ];
        }

        return $transferHistory;
    }
}