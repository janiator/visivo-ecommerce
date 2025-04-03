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
        Schema::table('stripe_products', function (Blueprint $table) {
            $table
                ->foreign('account_id')
                ->references('id')
                ->on('stripe_accounts')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->dropColumn('seller_account_id');
            $table->dropColumn('sp_connection_name');
            $table->dropColumn('sp_ctx');
            $table->dropColumn('statement_descriptor');

            $table
                ->foreign('account_id')
                ->references('id')
                ->on('stripe_accounts')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stripe_products', function (Blueprint $table) {
            $table->dropForeign('stripe_products_account_id_foreign');

            $table->string('seller_account_id')->after('account_id');

            $table
                ->string('sp_connection_name')
                ->nullable()
                ->after('shippable');

            $table
                ->text('sp_ctx')
                ->nullable()
                ->after('sp_connection_name');

            $table
                ->string('statement_descriptor')
                ->nullable()
                ->after('sp_ctx');

            $table->dropForeign('stripe_products_account_id_foreign');
        });
    }
};
