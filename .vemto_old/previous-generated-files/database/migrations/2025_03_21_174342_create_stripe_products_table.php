<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStripeProductsTable extends Migration
{
    public function up(): void
    {
        Schema::create('stripe_products', function (Blueprint $table) {
            $table->string('id')->index();

            $table->boolean('active')->nullable();

            $table->boolean('livemode')->nullable();

            $table->dateTime('created')->nullable();

            $table->dateTime('updated')->nullable();

            $table->text('description')->nullable();

            $table->text('images')->nullable();

            $table->text('metadata')->nullable();

            $table->string('name')->nullable();

            $table->text('package_dimensions')->nullable();

            $table->boolean('shippable')->nullable();

            $table->string('type')->nullable();

            $table->string('unit_label')->nullable();

            $table->string('url')->nullable();

            $table->integer('price')->nullable();

            $table->string('price_id')->nullable();

            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stripe_products');
    }
}
