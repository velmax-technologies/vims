<?php

namespace Database\Seeders;

use Spatie\Tags\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Seed some default tags
            $tags = [
                ['name' => ['en' => 'non-alcoholic'], 'slug' => ['en' => 'non-alcoholic'], 'type' => 'itemCategoryTag'],
                ['name' => ['en' => 'alcoholic'], 'slug' => ['en' => 'alcoholic'], 'type' => 'itemCategoryTag'],
                ['name' => ['en' => 'beverage'], 'slug' => ['en' => 'beverage'], 'type' => 'itemCategoryTag'],
                ['name' => ['en' => 'snack'], 'slug' => ['en' => 'snack'], 'type' => 'itemCategoryTag'],
                ['name' => ['en' => 'wine'], 'slug' => ['en' => 'wine'], 'type' => 'itemCategoryTag'],
                ['name' => ['en' => 'spirit'], 'slug' => ['en' => 'spirit'], 'type' => 'itemCategoryTag'],
                ['name' => ['en' => 'beer'], 'slug' => ['en' => 'beer'], 'type' => 'itemCategoryTag'],
                ['name' => ['en' => 'mixers'], 'slug' => ['en' => 'mixers'], 'type' => 'itemCategoryTag'],
                ['name' => ['en' => 'glassware'], 'slug' => ['en' => 'glassware'], 'type' => 'itemCategoryTag'],
                ['name' => ['en' => 'bottleware'], 'slug' => ['en' => 'bottleware'], 'type' => 'itemCategoryTag'],
                ['name' => ['en' => 'crates'], 'slug' => ['en' => 'crates'], 'type' => 'itemCategoryTag'],
                ['name' => ['en' => 'kitchen-menu'], 'slug' => ['en' => 'kitchen-menu'], 'type' => 'itemCategoryTag'],

                   
            ];  

            foreach ($tags as $tagData) {
                Tag::create($tagData);
            }
    }
}
