<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table): void {
            $table->unsignedInteger('available_stock')->default(0);
            $table->unsignedInteger('committed_stock')->default(0);
            $table->unsignedInteger('unavailable_stock')->default(0);
            $table->unsignedInteger('incoming_stock')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table): void {
            $table->dropColumn([
                'available_stock',
                'committed_stock',
                'unavailable_stock',
                'incoming_stock',
            ]);
        });
    }
};
