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
            'propulsion_system'   => $this->faker->text(10),
            'external_structure'  => $this->faker->text(10),
            'energy_system'       => $this->faker->text(10),
            'comunication_system' => $this->faker->text(10),
            'navigation_system'   => $this->faker->text(10),
            'termic_protection'   => $this->faker->text(10),
            'emergency_system'    => $this->faker->text(10),
            'landing_system'      => $this->faker->text(10),
        ];
    }
}
