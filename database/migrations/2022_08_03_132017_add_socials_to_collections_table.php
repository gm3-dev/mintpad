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
        Schema::table('collections', function (Blueprint $table) {
            $table->string('website')->after('address')->nullable();
            $table->string('roadmap')->after('website')->nullable();
            $table->string('twitter')->after('roadmap')->nullable();
            $table->string('discord')->after('twitter')->nullable();
            $table->text('about')->after('discord')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->dropColumn('website');
            $table->dropColumn('roadmap');
            $table->dropColumn('twitter');
            $table->dropColumn('discord');
            $table->dropColumn('about');
        });
    }
};
