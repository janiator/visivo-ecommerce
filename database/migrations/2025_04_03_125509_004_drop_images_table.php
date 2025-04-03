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
        Schema::dropIfExists('images');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('images', function (Blueprint $table) {
            $table
                ->integer('id')
                ->unsigned()
                ->autoIncrement();

            $table->string('filename');

            $table->string('mime_type')->nullable();

            $table->string('public_url')->nullable();

            $table->dateTime('created_at')->nullable();

            $table->dateTime('updated_at')->nullable();
        });
    }
};
