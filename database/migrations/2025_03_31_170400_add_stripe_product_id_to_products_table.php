<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStripeProductIdToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('products', static function (Blueprint $table): void {
            // Add a nullable string column to store the Stripe product ID.
            $table->string('stripe_product_id')->nullable()->after('short_description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('products', static function (Blueprint $table): void {
            $table->dropColumn('stripe_product_id');
        });
    }
}
