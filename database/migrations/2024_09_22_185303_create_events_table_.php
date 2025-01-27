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
        Schema::table('events', function (Blueprint $table) {
            $table->string('model_name'); // The model the event is associated with (e.g., Product)
            $table->string('page_name')->nullable(); // Page name where the event is available
            $table->enum('type', ['update', 'delete']); // Event type: update or delete
            $table->enum('scope', ['entire_model', 'specific_relation']); // Scope: entire model or specific relation
            $table->string('field')->nullable(); // Field to update (nullable, used for update events)
            $table->string('relation_field')->nullable(); // Field for relation (nullable, used for specific relations)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
