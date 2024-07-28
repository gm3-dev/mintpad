<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaikocampaigncollectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taikocampaigncollection', function (Blueprint $table) {
            $table->id();
            $table->string('symbol')->nullable();
            $table->string('name')->nullable();
            $table->string('feeRecipient')->nullable();
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
        Schema::dropIfExists('taikocampaigncollection');
    }
}
