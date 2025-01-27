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
        Schema::create('action_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('action_id')->constrained('actions')->onDelete('cascade');
            $table->string('field_name'); // The field in the target model to be updated/created
            $table->string('value_source'); // The source of the value (static, current_model_field, related_model_field)
            $table->string('static_value')->nullable(); // The static value, if applicable
            $table->string('current_model_field')->nullable(); // The field from the current model, if applicable
            $table->string('related_model_relation')->nullable(); // The relationship method in the current model
            $table->string('related_model_field')->nullable(); // The field in the related model
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_fields');
    }
};
