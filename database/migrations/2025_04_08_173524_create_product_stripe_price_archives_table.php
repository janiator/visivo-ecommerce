<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductStripePriceArchivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * This creates the product_stripe_price_archives table to store archived Stripe Price IDs.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('product_stripe_price_archives', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('product_id')
                ->comment('Foreign key to the products table');
            $table->string('stripe_price_id')
                ->comment('Archived Stripe Price ID');
            $table->timestamp('archived_at')->useCurrent()
                ->comment('Timestamp when the price was archived');
            $table->timestamps();

            // Add product foreign key constraint if desired.
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the product_stripe_price_archives table.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('product_stripe_price_archives');
    }
}
