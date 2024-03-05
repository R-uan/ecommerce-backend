<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->float('total');
            $table->string('status');
            $table->dateTime('order_date');
            $table->string("payment_method");
            $table->string("planet_destination");
            $table->dateTime('payment_received')->nullable();
            $table->dateTime('product_finished')->nullable();
            $table->dateTime('order_finalized')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('orders');
    }
};
