<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductMetaKeysTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_meta_keys', function (Blueprint $table) {
            $table->bigIncrements('id');
            // Link each meta key to a store so it can be reused within that store.
            $table->unsignedBigInteger('store_id');
            $table->string('key')->comment('Unique meta key identifier');
            // Optional fields to define the meta key
            $table->string('data_type')->default('string')->comment('Data type for the meta value (e.g., string, integer, etc.)');
            $table->timestamps();

            $table->unique(['store_id', 'key'], 'store_meta_key_unique');

            // Define foreign key constraint to stores table
            $table->foreign('store_id')
                ->references('id')
                ->on('stores')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_meta_keys');
    }
}
