<?php

namespace Database\Factories;

use App\Models\Manufacturers;
use App\Services\URLService;
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
    $name = $this->faker->name();
    return [
      'manufacturers_id'  => Manufacturers::factory(),
      'name'              => $name,
      'short_description' => $this->faker->text(100),
      'long_description'  => $this->faker->text(300),
      'image_url'         => $this->faker->url(),
      'production_time'   => $this->faker->randomDigit(),
      'category'          => $this->faker->name(),
      'availability'      => $this->faker->boolean(),
      'unit_price'        => $this->faker->randomFloat(2),
      'slug'              => URLService::CreateSlug($name),
    ];
  }
}
/* $table->string('category');
$table->boolean('availability'); */