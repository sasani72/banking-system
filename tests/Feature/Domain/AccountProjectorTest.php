<?php

namespace Tests\Feature\Domain;

use App\Domain\AccountAggregate;
use App\Models\Account;
use App\Models\Customer;
use App\Models\User;
use App\Domain\Repositories\AccountRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AccountProjectorTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Customer $customer;
    protected Account $account;
    protected $accountUuid;
    protected AccountRepository $accountRepository;

    public function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::create(['guard_name' => 'api', 'name' => 'Super-Admin']);

        $this->adminUser = User::factory()->create();

        $this->adminUser->assignRole($adminRole);

        $this->customer = Customer::factory()->create();

        $this->accountUuid = 'account-uuid';

        $this->createAccount();

        $this->accountRepository = app(AccountRepository::class);
    }

    public function test_created_account(): void
    {
        $this->assertDatabaseHas('accounts', [
            'uuid' => $this->accountUuid,
            'customer_id' => $this->customer->id,
            'name' => 'Account Name',
        ]);
    }

    public function test_added_money(): void
    {
        
        $initialBalance = $this->accountRepository->getByUuid($this->accountUuid)->balance;
        $this->assertEquals(0, $initialBalance);

        AccountAggregate::retrieve($this->accountUuid)
            ->addMoney(10)
            ->persist();

        $updatedBalance = $this->accountRepository->getByUuid($this->accountUuid)->balance;
        $this->assertEquals(10, $updatedBalance);
    }

    public function test_subtracted_money(): void
    {
        $initialBalance = $this->accountRepository->getByUuid($this->accountUuid)->balance;
        $this->assertEquals(0, $initialBalance);

        $aggregate = AccountAggregate::retrieve($this->accountUuid);
        $aggregate->subtractMoney(10);
        $aggregate->persist();

        $updatedBalance = $this->accountRepository->getByUuid($this->accountUuid)->balance;
        $this->assertEquals(-10, $updatedBalance);
    }

    public function test_transferred_money(): void
    {
        $initialBalance = $this->accountRepository->getByUuid($this->accountUuid)->balance;
        $this->assertEquals(0, $initialBalance);

        $secondAggregate = AccountAggregate::retrieve('second-account-uuid')
            ->createAccount($this->customer->id, 'Second Account Name')
            ->persist();

        $secondAccBalance = $this->accountRepository->getByUuid($secondAggregate->uuid())->balance;
        $this->assertEquals(0, $secondAccBalance);

        $aggregate = AccountAggregate::retrieve($this->accountUuid);
        $aggregate->transferMoney($secondAggregate->uuid(), 10);
        $aggregate->persist();

        $updatedBalance = $this->accountRepository->getByUuid($this->accountUuid)->balance;
        $this->assertEquals(-10, $updatedBalance);
    }

    protected function createAccount()
    {
        AccountAggregate::retrieve($this->accountUuid)
            ->createAccount($this->customer->id, 'Account Name')
            ->persist();
    }

}
