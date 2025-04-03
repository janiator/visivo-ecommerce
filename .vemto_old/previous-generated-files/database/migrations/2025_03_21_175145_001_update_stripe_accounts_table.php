<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stripe_accounts', function (Blueprint $table) {
            $table
                ->string('account_id')
                ->unique()
                ->index()
                ->change();

            $table->unique('account_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stripe_accounts', function (Blueprint $table) {
            $table->string('account_id')->change();

            $table->dropUnique('stripe_accounts_account_id_unique');
        });
    }
};
