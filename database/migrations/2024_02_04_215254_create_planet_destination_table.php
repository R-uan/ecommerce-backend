<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('planet_destinations', function (Blueprint $table) {
      $table->id();
      $table->timestamps();
      $table->string('name');
      $table->float('delivery_price');
      $table->text('special_conditions')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('planet_destinations');
  }
};
