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
        Schema::table('products', function (Blueprint $table) {
            // If the column does not exist, add it.
            if (! Schema::hasColumn('products', 'store_id')) {
                $table->unsignedBigInteger('store_id')
                    ->after('id')
                    ->comment('Foreign key to the stores table');
            }

            // Add foreign key constraint ensuring proper spelling of the column name.
            $table->foreign('store_id', 'products_store_id_foreign')
                ->references('id')
                ->on('stores')
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
        Schema::table('products', function (Blueprint $table) {

            $table->dropForeign('products_store_id_foreign');
        });
    }
};
