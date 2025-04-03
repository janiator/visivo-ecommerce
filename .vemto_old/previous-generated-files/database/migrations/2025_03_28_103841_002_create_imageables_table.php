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
        Schema::create('imageables', function (Blueprint $table) {
            $table->integer('image_id');

            $table->integer('imageable_id')->index();

            $table->string('imageable_type')->index();

            $table->integer('order')->nullable();

            $table->boolean('featured');

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
        Schema::dropIfExists('imageables');
    }
};
