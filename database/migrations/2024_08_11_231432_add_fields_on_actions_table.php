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
        Schema::table('actions', function (Blueprint $table) {
            $table->string('model_name'); // The name of the model this action is associated with
            $table->string('action_type'); // The type of action (post_request, get_request, etc.)
            $table->string('event'); // The event that triggers this action (on_create, on_update, etc.)

            // Optional fields for specific action types
            $table->string('url')->nullable(); // URL for HTTP actions
            $table->string('email_title')->nullable(); // Email title for send_email action
            $table->text('email_content')->nullable(); // Email content for send_email action
            $table->text('notification_content')->nullable(); // Notification content for send_notification action
            $table->text('custom_code')->nullable(); // Custom PHP code for custom actions

            // Where clause fields
            $table->string('where_model')->nullable(); // The model used in the Where clause
            $table->string('where_field')->nullable(); // The field name in the where_model
            $table->string('where_value')->nullable(); // The value or field from the current model for comparison
            $table->string('where_custom_value')->nullable(); // A custom value for comparison in the Where clause

            // JSON field to store dynamic data key-value pairs
            $table->json('data')->nullable(); // Key-value pairs for request data

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actions', function (Blueprint $table) {
            //
        });
    }
};
