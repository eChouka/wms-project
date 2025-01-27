<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatemodeltestsTable extends Migration
{
    public function up()
    {
        Schema::create('modeltests', function (Blueprint $table) {
            $table->id();
            $table->string('Field 1');
            $table->string('Field 2');
            $table->string('Field 3');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('modeltests');
    }
}