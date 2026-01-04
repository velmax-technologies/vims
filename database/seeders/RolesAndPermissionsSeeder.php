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
        $waiterRole = Role::create(['name' => 'waiter']);
        $waitressRole = Role::create(['name' => 'waitress']);

        // system permissions
        // general permissions
        $manageUsers = Permission::create(['name' => 'manage users']);
        $manageItems = Permission::create(['name' => 'manage items']);
        $manageStock = Permission::create(['name' => 'manage stock']);
        $manageCustomers = Permission::create(['name' => 'manage customers']);
        $manageSuppliers = Permission::create(['name' => 'manage suppliers']);
        $managePurchases = Permission::create(['name' => 'manage purchases']);
        $manageSales = Permission::create(['name' => 'manage sales']); // create, view, update, delete sales

        // single action permissions
        $createSale = Permission::create(['name' => 'create sale']);
        $returnCompletedSale = Permission::create(['name' => 'return completed sale']);
        $cancelPendingSale = Permission::create(['name' => 'cancel pending sale']);
        $cancelCompletedSale = Permission::create(['name' => 'cancel completed sale']);

        //order permissions
        $createOrders = Permission::create(['name' => 'create orders']);
        $editOrders = Permission::create(['name' => 'edit orders']);
        $manageOrders = Permission::create(['name' => 'manage orders']);
        $cancelPendingOrders = Permission::create(['name' => 'cancel pending orders']);

        // Assign permissions to roles as needed
        //$saRole->givePermissionTo($manageUsers);
        $adminRole->givePermissionTo($manageUsers, $manageItems, $manageStock, $manageCustomers, $manageSuppliers, $managePurchases, $returnCompletedSale, $cancelPendingSale, $cancelCompletedSale);
        $cashierRole->givePermissionTo($manageSales, $createSale, $manageCustomers, $manageUsers, $managePurchases, $manageOrders, $cancelPendingSale, $createOrders, $editOrders, $cancelPendingOrders);
        $waiterRole->givePermissionTo($createSale);
        $waitressRole->givePermissionTo($createOrders, $editOrders, $cancelPendingOrders);
        
    }
}
