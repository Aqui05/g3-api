<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Categorie;
use App\Models\subcategory;
use App\Models\User;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Categorie::factory(5)->create(); // Create 5 categories
        Subcategory::factory(10)->create(); // Create 10 subcategories

            Product::factory(10)->create();



    }
}
