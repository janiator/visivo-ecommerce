<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up()
    {
        Schema::create('imageables', function (Blueprint $table) {
            $table
                ->integer('id')
                ->unsigned()
                ->autoIncrement();

            $table->integer('image_id');

            $table->integer('imageable_id');

            $table->string('imageable_type');

            $table->integer('imageables_id')->index();

            $table->string('imageables_type')->index();

            $table->dateTime('created_at')->nullable();

            $table->dateTime('updated_at')->nullable();

            $table
                ->foreign('image_id')
                ->references('id')
                ->on('images')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('imageables');
    }
};
