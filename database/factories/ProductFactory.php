<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
// database/factories/ProductFactory.php
use App\Models\Product;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->paragraph,
            'quantity' => $this->faker->numberBetween(1, 100),
            'prix' => $this->faker->randomFloat(2, 10, 1000),
            'user_id' => \App\Models\User::factory(), // assuming a User model and relationship
            'photo_path' => $this->faker->imageUrl(),
            'categorie_id' => \App\Models\Categorie::factory(), // assuming a Categorie model and relationship
            'subcategory_id' => \App\Models\subcategory::factory(),
        ];
    }
}
