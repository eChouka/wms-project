<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Createnewmodel111sTable extends Migration
{
    public function up()
    {
        Schema::create('newmodel111s', function (Blueprint $table) {
            $table->id();
            $table->string('field_1');
            $table->string('field_2');
            $table->string('field_3');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('newmodel111s');
    }
}