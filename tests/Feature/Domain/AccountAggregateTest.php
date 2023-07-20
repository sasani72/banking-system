<?php

namespace Tests\Feature\Domain;

use App\Domain\AccountAggregate;
use App\Domain\Events\AccountCreated;
use App\Domain\Events\MoneyAdded;
use App\Domain\Events\MoneySubtracted;
use App\Domain\Events\MoneyTransferred;
use App\Domain\Exceptions\ExceptionCouldNotSubtractMoney;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AccountAggregateTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    private const ACCOUNT_UUID = 'account-uuid';
    private const ACCOUNT_NAME = 'account-fake';

    public function setUp(): void
    {
        parent::setUp();

        // Create an admin role and assign necessary permissions here.
        $adminRole = Role::create(['guard_name' => 'api', 'name' => 'Super-Admin']);

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole($adminRole);
    }

    protected function authenticateAdmin()
    {
        $this->actingAs($this->adminUser);
    }

    public function test_can_create_account(): void
    {
        $this->authenticateAdmin();

        $customer = Customer::factory()->create();

        AccountAggregate::fake(self::ACCOUNT_UUID)
        ->given([])
        ->when(function (AccountAggregate $accountAggregate) use ($customer): void {
            $accountAggregate->createAccount($customer->id, self::ACCOUNT_NAME);
        })
        ->assertRecorded([
            new AccountCreated(self::ACCOUNT_NAME, $customer->id)
        ]);
    }

    public function test_can_add_money(): void
    {
        $this->authenticateAdmin();

        $customer = Customer::factory()->create();

        AccountAggregate::fake(self::ACCOUNT_UUID)
            ->given([new AccountCreated(self::ACCOUNT_NAME, $customer->id)])
            ->when(function (AccountAggregate $accountAggregate): void {
                $accountAggregate->addMoney(10);
            })
            ->assertRecorded([
                new MoneyAdded(10)
            ]);
    }

    public function test_can_subtract_money(): void
    {
        $this->authenticateAdmin();

        $customer = Customer::factory()->create();

        AccountAggregate::fake(self::ACCOUNT_UUID)
            ->given([
                new AccountCreated(self::ACCOUNT_NAME, $customer->id),
                new MoneyAdded(1000)
            ])
            ->when(function (AccountAggregate $accountAggregate): void {
                $accountAggregate->subtractMoney(10);
            })
            ->assertRecorded([
                new MoneySubtracted(10),
            ]);
    }

    public function cannot_subtract_money_when_money_below_account_limit(): void
    {
        $this->authenticateAdmin();

        $customer = Customer::factory()->create();

        AccountAggregate::fake(self::ACCOUNT_UUID)
            ->given([
                new AccountCreated(self::ACCOUNT_NAME, $customer->id),
                new MoneySubtracted(5000)
            ])
            ->when(function (AccountAggregate $accountAggregate): void {
                $this->assertExceptionThrown(function () use ($accountAggregate) {
                    $accountAggregate->subtractMoney(1);

                }, ExceptionCouldNotSubtractMoney::class);
            })
            ->assertApplied([
                new AccountCreated(self::ACCOUNT_NAME, $customer->id),
                new MoneySubtracted(5000),
            ])
            ->assertNotRecorded(MoneySubtracted::class);

    }

    public function test_can_transfer_money(): void
    {
        $this->authenticateAdmin();

        $customer = Customer::factory()->create();

        AccountAggregate::fake(self::ACCOUNT_UUID)
            ->given([
                new AccountCreated(self::ACCOUNT_NAME, $customer->id),
                new MoneyAdded(1000)
            ])
            ->when(function (AccountAggregate $accountAggregate): void {
                $accountAggregate->transferMoney('destination-uuid', 50);
            })
            ->assertRecorded([
                new MoneyTransferred('destination-uuid', 50),
            ]);
    }

    public function test_cannot_transfer_money_when_balance_below_account_limit(): void
    {
        $this->authenticateAdmin();

        $customer = Customer::factory()->create();

        AccountAggregate::fake(self::ACCOUNT_UUID)
            ->given([
                new AccountCreated(self::ACCOUNT_NAME, $customer->id),
                new MoneyAdded(100)
            ])
            ->when(function (AccountAggregate $accountAggregate): void {
                $this->expectException(ExceptionCouldNotSubtractMoney::class);
                $accountAggregate->transferMoney('destination-uuid', 150);
            })
            ->assertApplied([
                new AccountCreated(self::ACCOUNT_NAME, $customer->id),
                new MoneyAdded(100),
            ])
            ->assertNotRecorded(MoneyTransferred::class);
    }

    public function test_get_current_balance(): void
    {
        $this->authenticateAdmin();

        $customer = Customer::factory()->create();

        $balance = 100;

        AccountAggregate::fake(self::ACCOUNT_UUID)
            ->given([
                new AccountCreated(self::ACCOUNT_NAME, $customer->id),
                new MoneyAdded($balance)
            ])
            ->when(function (AccountAggregate $accountAggregate) use ($balance): void {
                $currentBalance = $accountAggregate->getCurrentBalance();
                $this->assertEquals($balance, $currentBalance);
            });
    }
}
