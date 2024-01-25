<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Subcategory;
use App\Models\Categorie;

class SubcategoryFactory extends Factory
{
    protected $model = Subcategory::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            //'description' => $this->faker->paragraph,
            'category_id' => Categorie::factory(), // assuming a foreign key relationship
        ];
    }
}
