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
        Schema::table('imageables', function (Blueprint $table) {
            $table
                ->unsignedBigInteger('imageables_id')
                ->index()
                ->after('imageable_type');

            $table
                ->string('imageables_type')
                ->index()
                ->after('imageables_id');

            $table
                ->foreign('image_id')
                ->references('id')
                ->on('images')
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
        Schema::table('imageables', function (Blueprint $table) {
            $table->dropColumn('imageables_id');
            $table->dropColumn('imageables_type');

            $table->dropForeign('imageables_image_id_foreign');
        });
    }
};
