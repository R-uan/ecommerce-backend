<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Manufacturers;
use App\Models\Products;
use App\Models\ProductSpecs;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    /**
     * Seed the application's database.
     */
    public function run(): void {
        /* $this->call([ManufacturersSeeder::class, ProductSpecsSeeder::class]); */
        Manufacturers::factory()
            ->count(10)
            ->has(Products::factory()->count(10)->has(ProductSpecs::factory()->count(1)))
            ->create();
        User::factory()->count(1)->create();
    }
}
