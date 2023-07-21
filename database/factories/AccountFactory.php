<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => function () {
                return Customer::factory()->create()->id;
            },
            'uuid' => $this->faker->uuid,
            'name' => $this->faker->name,
            'balance' => 0,
        ];
    }
}
