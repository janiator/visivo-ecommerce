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
        Schema::create('stripe_accounts', function (Blueprint $table) {
            $table
                ->string('account_id')
                ->unique()
                ->index();

            $table->string('name');

            $table->integer('user_id')->nullable();

            $table->dateTime('created_at')->nullable();

            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stripe_accounts');
    }
};
