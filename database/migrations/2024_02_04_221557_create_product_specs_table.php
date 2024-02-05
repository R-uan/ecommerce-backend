<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('product_specs', function (Blueprint $table) {
            $table->id();
            $table->string('propulsion_system');
            $table->string('external_structure');
            $table->string('energy_system');
            $table->string('comunication_system');
            $table->string('navigation_system');
            $table->string('termic_protection');
            $table->string('emergency_system');
            $table->string('landing_system');
            $table->unsignedBigInteger('products_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('product_specs');
    }
};

/* product_id    Number
propulsion_system    String
external_structure    String
energy_system    String
comunication_system    String
navigation_system    String
termic_protection    String
emergency_system    String
landing_system    String */