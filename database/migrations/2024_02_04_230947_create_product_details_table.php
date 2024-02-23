<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('product_details', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('energy_system');
            $table->string('landing_system');
            $table->string('emergency_system');
            $table->string('propulsion_system');
            $table->string('navigation_system');
            $table->string('termic_protection');
            $table->string('external_structure');
            $table->string('comunication_system');
            $table->unsignedBigInteger('products_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('product_details');
    }
};
