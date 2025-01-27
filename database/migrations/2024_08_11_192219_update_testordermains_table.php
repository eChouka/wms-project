<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('testordermains', function (Blueprint $table) {
            $table->string('field_1')->change();
            $table->string('field_2')->change();
            $table->string('field_3')->change();
            $table->dropColumn('field_4');
        });
    }

    public function down()
    {
        Schema::table('testordermains', function (Blueprint $table) {
            // $table->string('field_4'); // This would need to match the original schema
        });
    }
};
