<?php

namespace App\Http\Controllers;

use App\Domain\AccountAggregate;
use App\Domain\Exceptions\ExceptionCouldNotSubtractMoney;
use App\Domain\Interfaces\TransactionStrategy;
use App\Domain\TransactionStrategies\Deposit\DepositTransaction;
use App\Domain\TransactionStrategies\Withdrawal\WithdrawalTransaction;
use App\Http\Requests\AddAccountRequest;
use App\Http\Requests\TransferMoneyRequest;
use App\Models\Account;
use App\Domain\Services\AccountService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    protected $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    /**
     * Add new account for given customer
     * 
     * @param AddAccountRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AddAccountRequest $request)
    {
        $uuid = Str::uuid()->toString();

        AccountAggregate::retrieve($uuid)
            ->createAccount($request->customer_id, $request->name)
            ->addMoney($request->deposit)
            ->persist();

        return response()->json([
            'message' => 'Account creation event raised successfully',
        ]);
    }

    /**
     * operate given transaction for the given account
     * 
     * @param Account $account
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function transaction(Account $account, Request $request)
    {
        $aggregateRoot = AccountAggregate::retrieve($account->uuid);

        $transactionStrategy = $this->getTransactionStrategy($request->transaction_type);

        if (!$transactionStrategy) {
            return response()->json([
                'error' => 'Invalid transaction type.',
            ], 400);
        }

        $transactionStrategy->handle($aggregateRoot, $request->amount);

        $aggregateRoot->persist();

        return response()->json([
            'message' => 'Transaction completed successfully',
        ]);
    }

    /**
     * transfer money to given account
     * 
     * @param TransferMoneyRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function transferTo(TransferMoneyRequest $request)
    {
        DB::beginTransaction();

        try {
            $aggregateRootOrigin = AccountAggregate::retrieve($request->origin_account);

            $aggregateRootDestination = AccountAggregate::retrieve($request->destination_account);

            $aggregateRootOrigin->transferMoney($request->destination_account, $request->amount)->persist();
            $aggregateRootDestination->addMoney($request->amount)->persist();

            DB::commit();

            return response()->json([
                'message' => 'Money transfer to destination account done successfully',
            ]);
        } catch (ExceptionCouldNotSubtractMoney $e) {

            DB::rollback();

            return response()->json([
                'error' => 'Insufficient funds in the origin account.',
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * retrieve the Transaction class according to transaction Type
     * 
     * @param string $transactionType
     * @return TransactionStrategy|null
     */
    private function getTransactionStrategy(string $transactionType): ?TransactionStrategy
    {
        return match ($transactionType) {
            'deposit' => new DepositTransaction(),
            'withdrawal' => new WithdrawalTransaction(),
            default => null,
        };
    }

    /**
     * retrieve balances of an account
     * 
     * @param Account $account
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBalances(Account $account)
    {
        $aggregateRoot = AccountAggregate::retrieve($account->uuid);
        $currentBalance = $aggregateRoot->getCurrentBalance();

        $availableBalance = $account->balance;

        return response()->json([
            'available_balance' => $availableBalance,
            'current_balance' => sprintf('%0.2f', $currentBalance)
        ]);
    }

    /**
     * retrieve transfer history of given account
     * 
     * @param Account $account
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTransferHistory(Account $account)
    {
        $res = $this->accountService->getTransferHistoryByUuid($account->uuid);

        return response()->json([
            'data' => $res
        ]);
    }
}
