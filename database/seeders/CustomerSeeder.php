<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // seed custpomers table
        $customers = [
            [
                'name' => 'Walkin Customer',
                'email' => 'walkin@example.com',
                'phone' => '0000000000',
                'address' => 'N/A'
            ],
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '1234567890',
                'address' => '123 Main St, City, Country'
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'phone' => '0987654321',
                'address' => '456 Oak Ave, Town, Country'
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
