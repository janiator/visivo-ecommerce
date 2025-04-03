<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStripeOrdersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('store_id');
            // Optionally, associate the order with a Customer record.
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('stripe_order_id')->unique();
            $table->string('payment_intent')->nullable(); // Add payment_intent column
            $table->string('status')->nullable(); // Add status column
            $table->unsignedInteger('subtotal')->nullable(); // Add subtotal column
            $table->unsignedInteger('total_amount'); // in cents
            $table->string('currency', 3);
            $table->json('shipping_address')->nullable();
            $table->json('billing_address')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}
