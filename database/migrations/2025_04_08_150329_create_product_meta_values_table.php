<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductMetaValuesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_meta_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id')->comment('Foreign key to products');
            $table->unsignedBigInteger('meta_key_id')->comment('Foreign key to product_meta_keys');
            // Storing values as text for flexibility; can be casted properly on the Model based on meta key type.
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'meta_key_id'], 'product_meta_unique');

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('meta_key_id')
                ->references('id')
                ->on('product_meta_keys')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_meta_values');
    }
}
