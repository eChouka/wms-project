<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->string('name')->change();
            $table->string('area_id')->change();
        });
    }

    public function down()
    {
        Schema::table('locations', function (Blueprint $table) {
        });
    }
};
