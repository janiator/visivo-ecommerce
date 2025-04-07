<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('main_variant_id')->nullable(); // references product_variants.id
            $table->string('status')->default('draft');
            $table->string('name');
            $table->string('type')->nullable();
            $table->longText('description')->nullable();
            $table->integer('price')->nullable(); // or use decimal
            $table->text('short_description')->nullable();
            $table->timestamps();

            // If you want a formal FK constraint on main_variant_id:
            // but note that it references the same table product_variants.
            // You can add this after product_variants is created, or do a separate migration.
            // $table->foreign('main_variant_id')->references('id')
            //       ->on('product_variants')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
