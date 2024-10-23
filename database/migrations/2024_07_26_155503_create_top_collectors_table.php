<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopCollectorsTable extends Migration
{
    public function up()
    {
        Schema::create('top_collectors', function (Blueprint $table) {
            $table->id();
            $table->integer('rank');
            $table->string('holder')->unique(); // Ethereum address
            $table->integer('points');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('top_collectors');
    }
}