<?php

namespace Database\Factories;

use App\Models\Products;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductSpecs>
 */
class ProductSpecsFactory extends Factory {
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

/*
$table->string('propulsion_system');
$table->string('external_structure');
$table->string('energy_system');
$table->string('comunication_system');
$table->string('navigation_system');
$table->string('termic_protection');
$table->string('emergency_system');
$table->string('landing_system');
$table->timestamps();
$table->foreign('product_id')
->references('id')
->on('products')
->onDelete('no action'); */