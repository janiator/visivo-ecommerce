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
                ->bigInteger('price')
                ->default(0)
                ->unsigned()
                ->after('id');

            $table
                ->text('short_description')
                ->nullable()
                ->after('type');

            $table
                ->string('type')
                ->nullable()
                ->change();

            $table
                ->text('description')
                ->nullable()
                ->change();
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
            $table->dropColumn('price');
            $table->dropColumn('short_description');

            $table->string('type')->change();

            $table->text('description')->change();
        });
    }
};
