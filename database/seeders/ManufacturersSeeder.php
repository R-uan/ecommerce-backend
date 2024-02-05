<?php

namespace Database\Seeders;

use App\Models\Manufacturers;
use App\Models\Products;
use Illuminate\Database\Seeder;

class ManufacturersSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        Manufacturers::factory()
            ->count(10)
            ->has(Products::factory()->count(10))
            ->create();
    }
}
