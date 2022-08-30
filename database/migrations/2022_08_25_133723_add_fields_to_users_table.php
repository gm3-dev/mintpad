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
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('is_company')->default(0)->after('email');
            $table->string('company_name')->nullable()->after('is_company');
            $table->string('vat_id')->nullable()->after('company_name');
            $table->string('country')->nullable()->after('vat_id');
            $table->string('city')->nullable()->after('country');
            $table->string('state')->nullable()->after('city');
            $table->string('postalcode')->nullable()->after('state');
            $table->string('address')->nullable()->after('postalcode');
            $table->string('address2')->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_company');
            $table->dropColumn('company_name');
            $table->dropColumn('vat_id');
            $table->dropColumn('country');
            $table->dropColumn('city');
            $table->dropColumn('state');
            $table->dropColumn('postalcode');
            $table->dropColumn('address');
            $table->dropColumn('address2');
        });
    }
};
