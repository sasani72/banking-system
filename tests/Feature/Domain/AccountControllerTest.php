<?php

namespace Tests\Domain\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Customer;
use App\Models\Account;
use App\Domain\AccountAggregate;
use App\Domain\Events\MoneyAdded;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Tests\TestCase;

class AccountControllerTest extends TestCase
{
    use RefreshDatabase;
    
    protected User $adminUser;

    public function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::create(['guard_name' => 'api', 'name' => 'Super-Admin']);

        $this->adminUser = User::factory()->create();

        $this->adminUser->assignRole($adminRole);

    }

    protected function authenticateAdmin()
    {
        $this->actingAs($this->adminUser);
    }

    public function test_store_method_creates_new_account_and_raises_event()
    {
        $this->authenticateAdmin();

        $customer = Customer::factory()->create();
        $requestData = [
            'customer_id' => $customer->id,
            'name' => 'Test Account',
            'deposit' => 1000,
        ];

        $response = $this->postJson('/api/accounts', $requestData);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Account creation event raised successfully']);

        $this->assertDatabaseHas('accounts', [
            'customer_id' => $customer->id,
            'name' => 'Test Account',
            'balance' => 1000,
        ]);
    }

    public function test_transaction_method_handles_deposit_transaction()
    {
        $this->authenticateAdmin();

        $account = Account::factory()->create();

        $requestData = [
            'transaction_type' => 'deposit',
            'amount' => 500,
        ];

        $response = $this->postJson("/api/accounts/{$account->uuid}/transaction", $requestData);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Transaction completed successfully']);

        $this->assertDatabaseHas('accounts', [
            'uuid' => $account->uuid,
            'balance' => 500,
        ]);
    }

    public function test_transaction_method_handles_withdrawal_transaction()
    {
        $this->authenticateAdmin();

        $account = Account::factory()->create();

        // Deposit money to the account
        $this->handleMoneyAddedEvent($account, 1000);

        $requestData = [
            'transaction_type' => 'withdrawal',
            'amount' => 500,
        ];

        $response = $this->postJson("/api/accounts/{$account->uuid}/transaction", $requestData);
        
        $response->assertStatus(200)
            ->assertJson(['message' => 'Transaction completed successfully']);

        $this->assertDatabaseHas('accounts', [
            'uuid' => $account->uuid,
            'balance' => 500,
        ]);
    }

    public function test_transferTo_method_transfers_money_between_accounts()
    {
        $this->authenticateAdmin();

        $originAccount = Account::factory()->create();
        $this->handleMoneyAddedEvent($originAccount, 1000);

        $destinationAccount = Account::factory()->create();
        $this->handleMoneyAddedEvent($destinationAccount, 500);

        $requestData = [
            'origin_account' => $originAccount->uuid,
            'destination_account' => $destinationAccount->uuid,
            'amount' => 500,
        ];

        $response = $this->postJson("/api/accounts/transfer-to", $requestData);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Money transfer to destination account done successfully']);

        $this->assertDatabaseHas('accounts', [
            'uuid' => $originAccount->uuid,
            'balance' => 500,
        ]);

        $this->assertDatabaseHas('accounts', [
            'uuid' => $destinationAccount->uuid,
            'balance' => 1000,
        ]);
    }

    public function test_getBalances_method_returns_account_balances()
    {
        $this->authenticateAdmin();

        $account = Account::factory()->create();

        $this->handleMoneyAddedEvent($account, 1000);

        $response = $this->getJson("/api/accounts/{$account->uuid}/balances");

        $response->assertStatus(200)
            ->assertJson([
                'available_balance' => '1000.00',
                'current_balance' => '1000.00',
            ]);
    }

    public function test_getTransferHistory_method_returns_account_transfer_history()
    {
        $this->authenticateAdmin();

        $account = Account::factory()->create();

        $this->handleMoneyAddedEvent($account, 10000);

        $destinationAccount1 = Account::factory()->create();
        $destinationAccount2 = Account::factory()->create();
        
        $this->handleMoneyTransferredEvent($account, $destinationAccount1, 2000);
        $this->handleMoneyTransferredEvent($account, $destinationAccount2, 5000);

        $response = $this->getJson("/api/accounts/{$account->uuid}/transfer-history");

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJsonCount(2, 'data');
    }

    protected function handleMoneyAddedEvent(Account $account, int $amount)
    {
        // Simulate the money added event for the account
        $aggregateRoot = AccountAggregate::retrieve($account->uuid);
        $aggregateRoot->addMoney($amount)->persist();
    }


    protected function handleMoneyTransferredEvent(Account $account, Account $destination, int $amount)
    {
        // Simulate the money transferred event for the account
        $aggregateRoot = AccountAggregate::retrieve($account->uuid);
        $aggregateRoot->transferMoney($destination->uuid, $amount)->persist();
    }
}
