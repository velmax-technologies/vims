<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
      public function run(): void
    {
        // general settings
        Setting::create([
            'key' => 'site_name',
            'group' => 'general',
            'value' => 'Veltech Wines & Spirits POS',
        ]);

        Setting::create([
            'key' => 'site_phone',
            'group' => 'general',
            'value' => '+1234567890',
        ]);

        Setting::create([
            'key' => 'site_developer',
            'group' => 'general',
            'value' => 'Veltech +254708222536',
        ]);

        // pricing settings
        Setting::create([
            'key' => 'enable_wholesale_pricing_for_customers',
            'group' => 'pricing',
            'type' => 'boolean',
            'value' => '1', // 1 for true, 0 for false
        ]);

        // receipt settings
        Setting::create([
            'key' => 'receipt_header',
            'group' => 'receipt',
            'value' => 'Veltech Wines & Spirits\nThank you for your purchase!',
        ]);

        Setting::create([
            'key' => 'receipt_footer',
            'group' => 'receipt',
            'value' => 'Visit us again!',
        ]);

        Setting::create([
            'key' => 'receipt_logo',
            'group' => 'receipt',
            'value' => '/images/logo.png', // Assuming the logo is stored in the public/images directory
        ]);

        Setting::create([
            'key' => 'receipt_show_tax',
            'group' => 'receipt',
            'type' => 'boolean',
            'value' => '1', // 1 for true, 0 for false
        ]);

        Setting::create([
            'key' => 'receipt_show_total',
            'group' => 'receipt',
            'type' => 'boolean',
            'value' => '1', // 1 for true, 0 for false
        ]);

        Setting::create([
            'key' => 'receipt_show_date',
            'group' => 'receipt',
            'type' => 'boolean',
            'value' => '1', // 1 for true, 0 for false
        ]);

        Setting::create([
            'key' => 'receipt_show_time',
            'group' => 'receipt',
            'type' => 'boolean',
            'value' => '1', // 1 for true, 0 for false
        ]);

        Setting::create([
            'key' => 'receipt_show_cashier',
            'group' => 'receipt',
            'type' => 'boolean',
            'value' => '1', // 1 for true, 0 for false
        ]);

        Setting::create([
            'key' => 'receipt_show_payment_method',
            'group' => 'receipt',
            'type' => 'boolean',
            'value' => '1', // 1 for true, 0 for false
        ]);

        Setting::create([
            'key' => 'receipt_show_change',
            'group' => 'receipt',
            'type' => 'boolean',
            'value' => '1', // 1 for true, 0 for false
        ]);

        Setting::create([
            'key' => 'receipt_show_customer_name',
            'group' => 'receipt',
            'type' => 'boolean',
            'value' => '1', // 1 for true, 0 for false
        ]);


        Setting::create([
            'key' => 'receipt_show_order_number',
            'group' => 'receipt',
            'type' => 'boolean',
            'value' => '1', // 1 for true, 0 for false
        ]);

        Setting::create([
            'key' => 'receipt_show_order_date',
            'group' => 'receipt',
            'type' => 'boolean',
            'value' => '1', // 1 for true, 0 for false
        ]);

        Setting::create([
            'key' => 'receipt_auto_print',
            'group' => 'receipt',
            'type' => 'boolean',
            'value' => '1', // 1 for true, 0 for false
        ]);

        // payment methods
        Setting::create([
            'key' => 'payment_methods',
            'group' => 'payment',
            'type' => 'json',
            'value' => json_encode([
                'cash' => [
                    'name' => 'Cash',
                    'enabled' => true,
                ],
                'mpesa' => [
                    'name' => 'M-Pesa',
                    'enabled' => true,
                ],
                'equity' => [
                    'name' => 'Equity Paybill',
                    'enabled' => false,
                ],
            ]),
        ]);

        // order settings
        Setting::create([
            'key' => 'allow_online_orders',
            'group' => 'order',
            'type' => 'boolean',
            'value' => '1', // 1 for true, 0 for false
        ]);
        
        Setting::create([
            'key' => 'auto_confirm_orders',
            'group' => 'order',
            'type' => 'boolean',
            'value' => '0', // 1 for true, 0 for false
        ]);

        Setting::create([
            'key' => 'auto_print_order_receipt',
            'group' => 'order',
            'type' => 'boolean',
            'value' => '0', // 1 for true, 0 for false
        ]);

        // payment methods
        Setting::create([
            'key' => 'price_tags',
            'group' => 'price',
            'type' => 'json',
            'value' => json_encode([
                'wholesale' => [
                    'name' => 'Wholesale',
                    'enabled' => true,
                ],
                'retail' => [
                    'name' => 'Retail',
                    'enabled' => true,
                ]
            ]),
        ]);

        // messurement units
        Setting::create([
            'key' => 'measurement_units',
            'group' => 'measurement',
            'type' => 'json',
            'value' => json_encode([
                'pieces' => [
                    'name' => 'Pieces',
                    'enabled' => true,
                    'default' => true,
                ],
                'kgs' => [
                    'name' => 'Kilograms',
                    'enabled' => true,
                    'default' => false,
                ],
               
            ]),
        ]);
        
       

         
    }
}
