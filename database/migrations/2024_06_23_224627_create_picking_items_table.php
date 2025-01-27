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
        Schema::create('picking_items', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id')->nullable();
            $table->integer('qty')->nullable();
            $table->integer('picked_qty')->nullable();
            $table->integer('location_id')->nullable();
            $table->integer('picked_location_id')->nullable();
            $table->integer('picking_job_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('picking_items');
    }
};
