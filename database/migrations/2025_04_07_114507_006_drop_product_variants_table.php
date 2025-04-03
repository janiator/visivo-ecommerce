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
        Schema::dropIfExists('product_variants');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table
                ->integer('id')
                ->unsigned()
                ->autoIncrement();

            $table->string('name');

            $table->integer('price')->nullable();

            $table->string('grouping_attribute')->nullable();

            $table->text('short_description')->nullable();

            $table->text('description')->nullable();

            $table->string('status')->default('active');

            $table->dateTime('created_at')->nullable();

            $table->dateTime('updated_at')->nullable();

            $table->string('stripe_product_id')->nullable();
        });
    }
};
