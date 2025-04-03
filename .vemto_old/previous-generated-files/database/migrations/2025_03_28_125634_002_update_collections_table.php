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
        Schema::table('collections', function (Blueprint $table) {
            $table
                ->integer('store_id')
                ->unsigned()
                ->after('id');

            $table->string('name')->after('store_id');

            $table->boolean('visible')->after('name');

            $table
                ->foreign('store_id')
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
        Schema::table('collections', function (Blueprint $table) {
            $table->dropColumn('store_id');
            $table->dropColumn('name');
            $table->dropColumn('visible');

            $table->dropForeign('collections_store_id_foreign');
        });
    }
};
