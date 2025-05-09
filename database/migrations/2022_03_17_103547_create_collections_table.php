<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('name');
            $table->string('contract_name');
            $table->string('symbol', 20);
            $table->boolean('whitelist');
            $table->boolean('deployed')->default(0);
            $table->integer('collection_size')->nullable();
            $table->string('base_name')->nullable();
            $table->float('mint_cost', 8, 3)->nullable();
            $table->float('royalties', 8, 2)->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('collections');
    }
};
