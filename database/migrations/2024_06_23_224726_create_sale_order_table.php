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
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('ref')->nullable();
            $table->float('total')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->string('delivery_address_1')->nullable();
            $table->string('delivery_address_2')->nullable();
            $table->string('delivery_town')->nullable();
            $table->string('delivery_postcode')->nullable();
            $table->string('delivery_county')->nullable();
            $table->string('delivery_country')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};
