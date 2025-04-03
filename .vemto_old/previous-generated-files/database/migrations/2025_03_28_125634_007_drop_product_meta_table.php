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
        Schema::dropIfExists('product_meta');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('product_meta', function (Blueprint $table) {
            $table
                ->integer('id')
                ->unsigned()
                ->autoIncrement();

            $table->string('key');

            $table->text('value');

            $table->dateTime('created_at')->nullable();

            $table->dateTime('updated_at')->nullable();
        });
    }
};
