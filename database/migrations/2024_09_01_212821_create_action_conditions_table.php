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
        Schema::create('action_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('action_id')->constrained('actions')->onDelete('cascade');
            $table->string('field_name')->nullable();
            $table->string('operator')->nullable();
            $table->string('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_conditions');
    }
};
