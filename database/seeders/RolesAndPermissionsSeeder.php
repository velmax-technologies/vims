<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        // Create roles and permissions here
       
        $saRole = Role::create(['name' => 'super admin']);
        $adminRole = Role::create(['name' => 'admin']);
        $managerRole = Role::create(['name' => 'manager']);
        $accountantRole = Role::create(['name' => 'accountant']);
        $cashierRole = Role::create(['name' => 'cashier']);
        $userRole = Role::create(['name' => 'user']);
        $customerRole = Role::create(['name' => 'customer']);

        // Example of creating a permission
        $manageUsers = Permission::create(['name' => 'manage users']);
        $manageItems = Permission::create(['name' => 'manage items']);
        $pointOfSale = Permission::create(['name' => 'point of sale']);
        $manageStock = Permission::create(['name' => 'manage stock']);
        $manageCustomers = Permission::create(['name' => 'manage customers']);
        $managePurchases = Permission::create(['name' => 'manage purchases']);

        // Assign permissions to roles as needed
        //$saRole->givePermissionTo($manageUsers);
        $adminRole->givePermissionTo($manageUsers, $manageItems, $pointOfSale, $manageStock, $manageCustomers, $managePurchases);
        $cashierRole->givePermissionTo($pointOfSale, $manageCustomers, $manageUsers, $managePurchases);
        
    }
}
