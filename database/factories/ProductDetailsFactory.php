<?php

namespace Database\Factories;

use App\Models\Products;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductDetails>
 */
class ProductDetailsFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        return [
            'products_id'         => Products::factory(),
            'propulsion_system'   => $this->faker->word,
            'external_structure'  => $this->faker->word,
            'energy_system'       => $this->faker->word,
            'comunication_system' => $this->faker->word,
            'navigation_system'   => $this->faker->word,
            'termic_protection'   => $this->faker->word,
            'emergency_system'    => $this->faker->word,
            'landing_system'      => $this->faker->word,
        ];
    }
}
