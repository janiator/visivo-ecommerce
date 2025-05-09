<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock', function (Blueprint $table) {
            $table->id();

            $table
                ->bigInteger('available')
                ->default(0)
                ->nullable();

            $table
                ->bigInteger('committed')
                ->default(0)
                ->nullable();

            $table
                ->bigInteger('unavailable')
                ->default(0)
                ->nullable();

            $table
                ->bigInteger('incoming')
                ->default(0)
                ->nullable();

            $table->timestamp('created_at')->nullable();

            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock');
    }
};
