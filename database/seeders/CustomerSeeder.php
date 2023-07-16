<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::create(['id' => 1, 'name' => 'Arisha Barron']);
        Customer::create(['id' => 2, 'name' => 'Branden Gibson']);
        Customer::create(['id' => 3, 'name' => 'Rhonda Church']);
        Customer::create(['id' => 4, 'name' => 'Georgina Hazel']);
    }
}
