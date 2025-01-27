<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id')->nullable();
            $table->integer('moved_from_location_id')->nullable();
            $table->integer('moved_to_location_id')->nullable();
            $table->integer('qty')->nullable();
            $table->integer('sales_order_id')->nullable();
            $table->integer('purchase_order_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
