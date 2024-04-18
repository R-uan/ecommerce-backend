<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('products', function (Blueprint $table) {
      $table->id();
      $table->timestamps();
      $table->string('name');
      $table->string('category');
      $table->float('unit_price');
      $table->boolean('availability');
      $table->text('slug')->nullable();
      $table->integer('production_time');
      $table->string('image_url')->nullable();
      $table->text('long_description')->nullable();
      $table->text('short_description')->nullable();
      $table->unsignedBigInteger('manufacturers_id');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('products');
  }
};
