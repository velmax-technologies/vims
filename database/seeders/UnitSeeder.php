<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'pieces', 'abbreviation' => 'pc'],
            //['name' => 'Kilogram', 'abbreviation' => 'kg'],
            ['name' => 'grams', 'abbreviation' => 'g'],
            //['name' => 'Liter', 'abbreviation' => 'l'],
            ['name' => 'milliliters', 'abbreviation' => 'ml'],
        ];

        foreach ($units as $unit) {
            DB::table('units')->insert($unit);
        }
    }
}
