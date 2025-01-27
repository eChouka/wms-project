<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatetestordermainsTable extends Migration
{
    public function up()
    {
        Schema::create('testordermains', function (Blueprint $table) {
            $table->id();
            $table->string('field_1');
            $table->string('field_2');
            $table->string('field_3');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('testordermains');
    }
}