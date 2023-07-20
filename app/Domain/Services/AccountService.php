<?php

namespace App\Domain\Services;

use App\Domain\Repositories\AccountRepository;

class AccountService
{
    protected $accountRepository;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function createAccount(array $data)
    {
        return $this->accountRepository->create($data);
    }

    public function getAccountwithUuid(string $uuid)
    {
        return $this->accountRepository->getByUuid($uuid);
    }

    public function addMoneyToAccount(string $uuid, int $amount): void
    {
        $account = $this->accountRepository->getByUuid($uuid);

        if ($account) {

            $account->balance += $amount;

            $this->accountRepository->save($account);
        }
    }

    public function subtractMoneyFromAccount(string $uuid, int $amount): void
    {
        $account = $this->accountRepository->getByUuid($uuid);

        if ($account) {

            $account->balance -= $amount;

            $this->accountRepository->save($account);
        }
    }

    public function getTransferHistoryByUuid(string $uuid)
    {
        $account = $this->accountRepository->getByUuid($uuid);

        if (!$account) {
            return null;
        }

        $transferHistory = $this->accountRepository->getTransferHistoryByUuid($uuid);

        return $transferHistory;
    }
}