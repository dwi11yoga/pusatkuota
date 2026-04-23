<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('provider')->nullable();
            $table->string('category')->nullable();
            $table->string('name');
            $table->bigInteger('base_price');
            $table->bigInteger('expected_profit'); // misal: 2000, 3000, 5000 (sebelum dibulatkan ke ribuan)
            $table->bigInteger('real_profit'); // keuntungan disesuaikan dengan floor (base_price + profit = 5200 -> 5000)
            $table->string('url')->nullable();
            $table->datetimes();
            $table->softDeletes();

            // indexing
            $table->index('type');
            $table->index(['type', 'provider']);
            $table->index(['provider', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
