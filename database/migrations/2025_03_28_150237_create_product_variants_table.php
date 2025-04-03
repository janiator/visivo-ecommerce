<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('price')->nullable(); // or decimal
            $table->string('grouping_attribute')->nullable(); // e.g. "color", "size"
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->json('metadata')->nullable(); // optional extra data
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_variants');
    }
};
