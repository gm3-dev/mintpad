<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaikocampaignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taikocampaign', function (Blueprint $table) {
            $table->id(); 
            $table->string('address')->nullable();
            $table->string('house', 100)->nullable();
            $table->string('housetype', 100)->nullable();
            $table->string('housename')->nullable();
            $table->string('totalmint')->nullable();
            $table->binary('profilepic')->nullable();
            $table->string('categories')->nullable();
            $table->string('twitter')->nullable();
            $table->string('username')->nullable();
            $table->string('bio')->nullable();
            $table->string('ens')->nullable();
            $table->string('opensea')->nullable();
            $table->string('Blockscan')->nullable();
            $table->integer('housenamecount')->default(0);
            $table->string('latestactivity')->nullable();
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     * Check for migrate
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taikocampaign');
    }
}
