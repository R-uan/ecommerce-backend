<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Manufacturers;
use App\Models\PlanetDestination;
use App\Models\ProductDetails;
use App\Models\Products;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
  /**
   * Seed the application's database.
   */
  public function run(): void {
    Manufacturers::factory()
      ->count(10)
      ->has(Products::factory()
          ->count(10)
          ->has(ProductDetails::factory()->count(1)))
      ->create();
    User::factory()->count(1)->create();
    PlanetDestination::factory()->count(9)->create();
  }
}
