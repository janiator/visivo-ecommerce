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
                ->integer('vat_percent')
                ->default(25)
                ->nullable()
                ->after('price');

            $table
                ->string('sku')
                ->nullable()
                ->after('vat_percent');

            $table
                ->string('slug')
                ->unique()
                ->index()
                ->after('short_description');

            $table
                ->string('type')
                ->default('product')
                ->nullable()
                ->change();
            $table->dropColumn('main_variant_id');
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
            $table->dropColumn('vat_percent');
            $table->dropColumn('sku');
            $table->dropColumn('slug');

            $table
                ->string('type')
                ->nullable()
                ->change();

            $table
                ->integer('main_variant_id')
                ->nullable()
                ->after('id');

            $table
                ->text('short_description')
                ->nullable()
                ->after('vat_percent');
        });
    }
};
