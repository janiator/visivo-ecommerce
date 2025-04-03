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
            $table
                ->integer('store_id')
                ->unsigned()
                ->after('id');

            $table
                ->integer('main_variant_id')
                ->unsigned()
                ->after('store_id');

            $table
                ->string('status')
                ->default('draft')
                ->after('main_variant_id');

            $table
                ->foreign('store_id')
                ->references('id')
                ->on('stores')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table
                ->foreign('main_variant_id')
                ->references('id')
                ->on('product_variants')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->dropColumn('name');
            $table->dropColumn('description');
            $table->dropColumn('price');
            $table->dropColumn('short_description');
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
            $table->dropColumn('store_id');
            $table->dropColumn('main_variant_id');
            $table->dropColumn('status');

            $table->dropForeign('products_store_id_foreign');

            $table->dropForeign('products_main_variant_id_foreign');

            $table->string('name')->after('updated_at');

            $table->text('description')->nullable();

            $table
                ->integer('price')
                ->default(0)
                ->after('description');

            $table
                ->text('short_description')
                ->nullable()
                ->after('price');
        });
    }
};
