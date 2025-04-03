<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('order_id');
            // Optionally link to a product record if available;
            // you might need to adjust this if not all items are from products.
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('product_variant_id')->nullable();
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('unit_price'); // in cents
            $table->unsignedInteger('total_price'); // in cents
            $table->string('name')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            // If you wish to enforce referential integrity and your products exist in the DB,
            // you can add foreign constraints for product_id or product_variant_id.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
}
