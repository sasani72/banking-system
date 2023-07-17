<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    public function setUp(): void
    {
        parent::setUp();

        // Create an admin role and assign necessary permissions here.
        $adminRole = Role::create(['guard_name' => 'api', 'name' => 'Super-Admin']);

        // Create an admin user and assign the admin role to the user.
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole($adminRole);
    }

    protected function authenticateAdmin()
    {
        $this->actingAs($this->adminUser);
    }

    public function test_customer_index()
    {
        $this->authenticateAdmin();

        Customer::factory()->count(3)->create();

        $response = $this->getJson('api/customers');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' =>   [
                    '*' => [
                        'id',
                        'name',
                        'created_at',
                        'updated_at',
                    ]
                ]
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_customer_store()
    {
        $this->authenticateAdmin();

        $data = [
            'name' => 'John Smith',
        ];

        $response = $this->postJson('api/customers', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' =>   [
                    'id',
                    'name',
                    'created_at',
                    'updated_at',
                ],
                'message',
            ])
            ->assertJsonFragment(['name' => 'John Smith'])
            ->assertJson(['message' => 'Customer created successfully']);

        // Check if customer created in DB is correct
        $customer = Customer::first();
        $this->assertDatabaseHas('customers', ['name' => 'John Smith']);
    }
    
    public function test_customer_show()
    {
        $this->authenticateAdmin();

        $customer = Customer::factory()->create();

        $response = $this->getJson('api/customers/' . $customer->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' =>   [
                    'id',
                    'name',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJsonFragment(['name' => $customer->name]);
    }

    public function test_customer_update()
    {
        $this->authenticateAdmin();

        $customer = Customer::factory()->create();

        $response = $this->putJson('api/customers/' . $customer->id, ['name' => 'New Name']);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' =>   [
                    'id',
                    'name',
                    'created_at',
                    'updated_at',
                ],
                'message',
            ])
            ->assertJson(['message' => 'Customer updated successfully']);

        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'name' => 'New Name']);
    }

    public function test_customer_destroy()
    {
        $this->authenticateAdmin();
        
        $customer = Customer::factory()->create();

        $response = $this->deleteJson('api/customers/' . $customer->id);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Customer deleted successfully']);

        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }
}
