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
        Schema::table('product_variants', function (Blueprint $table) {
            $table
                ->string('sku')
                ->nullable()
                ->after('grouping_attribute');

            $table
                ->text('short_description')
                ->nullable()
                ->after('sku');

            $table
                ->longText('description')
                ->nullable()
                ->after('short_description');

            $table
                ->jsonb('metadata')
                ->nullable()
                ->after('description');

            $table
                ->string('status')
                ->default('draft')
                ->after('metadata');
            $table->renameColumn('attribute', 'grouping_attribute');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('sku');
            $table->dropColumn('short_description');
            $table->dropColumn('description');
            $table->dropColumn('metadata');
            $table->dropColumn('status');
            $table->renameColumn('grouping_attribute', 'attribute');
        });
    }
};
