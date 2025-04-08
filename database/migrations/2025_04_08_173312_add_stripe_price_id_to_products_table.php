<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStripePriceIdToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds the stripe_price_id column to the products table.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            // Adding stripe_price_id as a nullable string column.
            $table->string('stripe_price_id')->nullable()->after('stripe_product_id')
                ->comment('Stripe Price object ID for the product.');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Removes the stripe_price_id column from the products table.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn('stripe_price_id');
        });
    }
}
