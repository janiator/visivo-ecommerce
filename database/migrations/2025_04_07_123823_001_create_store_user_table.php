<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('store_user', function (Blueprint $table): void {
            // Use unsignedBigInteger when referencing the 'id' columns in users and stores.
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('user_id');
            $table->string('role');

            // Define composite primary key
            $table->primary(['store_id', 'user_id']);

            // Define foreign keys
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('store_id')
                ->references('id')
                ->on('stores')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('store_user');
    }
};
