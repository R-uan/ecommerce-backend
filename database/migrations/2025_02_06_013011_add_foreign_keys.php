<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::table('products', function (Blueprint $table) {
      $table->foreign('manufacturers_id')
        ->references('id')
        ->on('manufacturers')
        ->onDelete('cascade');
    });

    Schema::table('product_details', function (Blueprint $table) {
      $table->foreign('products_id')
        ->references('id')
        ->on('products')
        ->onDelete('cascade');
    });

    Schema::table('orders', function (Blueprint $table) {
      $table->foreign('client_id')
        ->references('id')
        ->on('users')
        ->onDelete('no action');
      $table->foreign('planet_destination_id')
        ->references('id')
        ->on('planet_destinations')
        ->onDelete('no action');
    });

    Schema::table('order_itens', function (Blueprint $table) {
      $table->foreign('product_id')
        ->references('id')
        ->on('products')
        ->onDelete('no action');

      $table->foreign('orders_id')
        ->references('id')
        ->on('orders')
        ->onDelete('no action');
    });

    Schema::table('users', function (Blueprint $table) {
      $table->foreign('address_id')
        ->references('id')
        ->on('addresses')
        ->onDelete('no action');
    });
  }

  /**'
   * Reverse the migrations.
   */
  public function down(): void {
    //
  }
};
