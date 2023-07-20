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

    /**
     * @param array $data
     * @return mixed
     */
    public function createAccount(array $data)
    {
        return $this->accountRepository->create($data);
    }

    /**
     * @param string $uuid
     * @return \App\Models\Account|null
     */
    public function getAccountwithUuid(string $uuid)
    {
        return $this->accountRepository->getByUuid($uuid);
    }

    /**
     * @param string $uuid
     * @param int $amount
     */
    public function addMoneyToAccount(string $uuid, int $amount): void
    {
        $account = $this->accountRepository->getByUuid($uuid);

        if ($account) {

            $account->balance += $amount;

            $this->accountRepository->save($account);
        }
    }

    /**
     * @param string $uuid
     * @param int $amount
     */
    public function subtractMoneyFromAccount(string $uuid, int $amount): void
    {
        $account = $this->accountRepository->getByUuid($uuid);

        if ($account) {

            $account->balance -= $amount;

            $this->accountRepository->save($account);
        }
    }

    /**
     * @param string $uuid
     * @return array|null
     */
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
