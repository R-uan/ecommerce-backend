<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlanetDestination>
 */
class PlanetDestinationFactory extends Factory {
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array {
    $planetNames = ['Mercury', 'Venus', 'Earth', 'Mars', 'Jupiter', 'Saturn', 'Uranus', 'Neptune', 'Moon'];
    $planet      = $this->faker->randomElement($planetNames);

    return [
      'name'           => $planet,
      'delivery_price' => $this->faker->randomFloat(2, 10, 100), // Assuming delivery price range between 10 and 100
      'special_conditions' => $this->faker->optional()->text(100), // Generate random special conditions with a 50% chance of being null
    ];

  }
}
