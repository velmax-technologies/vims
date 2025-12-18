<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(app()->env == "local"){
            $saUser = User::factory()->create([
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'email' => 'sa@veltech.com',
                'phone' => '0708222536',
                'is_super_admin' => true,
            ]);

            $adminUser = User::factory()->create([
                'name' => 'Admin',
                'username' => 'admin',
                'email' => 'admin@veltech.com',
                'phone' => '0708222530',
                'is_admin' => true,
            ]);
            
            $managerUser = User::factory()->create([
                'name' => 'Manager',
                'username' => 'manager',
                'email' => 'manager@veltech.com',
                'phone' => '0708222531',
            ]);

            $cashierUser = User::factory()->create([
                'name' => 'Cashier',
                'username' => 'cashier',
                'email' => 'cashier@veltech.com',
                'phone' => '0708222532',
            ]);
        }else{
            $saUser = User::factory()->create([
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'email' => 'sa@veltech.com',
                'phone' => '0708222536',
                'password' => Hash::make('veltech'),
                'is_super_admin' => true,
            ]);
         }

        $saUser->assignRole('super admin');
        $adminUser->assignRole('admin');
        $managerUser->assignRole('manager');
        $cashierUser->assignRole('cashier');
    }    
}
