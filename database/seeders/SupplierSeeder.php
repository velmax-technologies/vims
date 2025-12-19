<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        // Example suppliers data
        $suppliers = [
            [
                'name' => 'Supplier One',
                'email' => 's1@example.com',
                'phone' => '0700123456',
                'address' => '123 Main Street, Nairobi',
                'contact_person' => '<NAME>',
                'kra_pin' => 'KRA123456789',
                'bank_account' => '1234567890',
                'notes' => 'Preferred supplier for office supplies'
            ],
            [
                'name' => 'Supplier Two',
                'email' => 's2@example.com',
                'phone' => '0700987654',
                'address' => '456 Business Avenue, Mombasa',
                'contact_person' => '<NAME>',
                'kra_pin' => 'KRA987654321',
                'bank_account' => '0987654321',
                'notes' => ''
            ]
        ];

        foreach ($suppliers as $supplier) {
            \App\Models\Supplier::create($supplier);
        }
    }
}