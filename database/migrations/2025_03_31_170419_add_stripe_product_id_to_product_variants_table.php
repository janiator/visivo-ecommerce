<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStripeProductIdToProductVariantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('product_variants', static function (Blueprint $table): void {
            // Add a nullable string column to store the Stripe product ID for the variant.
            $table->string('stripe_product_id')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('product_variants', static function (Blueprint $table): void {
            $table->dropColumn('stripe_product_id');
        });
    }
}
