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

        Schema::table('product_specs', function (Blueprint $table) {
            $table->foreign('products_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        //
    }
};
