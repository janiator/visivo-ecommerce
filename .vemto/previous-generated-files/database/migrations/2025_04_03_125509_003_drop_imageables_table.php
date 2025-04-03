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
        Schema::dropIfExists('imageables');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
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
        });
    }
};
