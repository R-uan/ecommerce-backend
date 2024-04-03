<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('addresses', function (Blueprint $table) {
      $table->string('planet');
      $table->string('nation');
      $table->string('state');
      $table->string('city');
      $table->string('sector');
      $table->string('residence_id');
      $table->id();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('addresses');
  }
};
