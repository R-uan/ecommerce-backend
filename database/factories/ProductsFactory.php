<?php

namespace Database\Factories;

use App\Models\Manufacturers;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Products>
 */
class ProductsFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        return [
            'manufacturers_id'  => Manufacturers::factory(),
            'name'              => $this->faker->name(),
            'short_description' => $this->faker->text(100),
            'long_description'  => $this->faker->text(300),
            'image_url'         => $this->faker->url(),
            'category'          => $this->faker->name(),
            'availability'      => $this->faker->boolean(),
            'unit_price'        => $this->faker->randomFloat(2),
        ];
    }
}
/* $table->string('category');
$table->boolean('availability'); */