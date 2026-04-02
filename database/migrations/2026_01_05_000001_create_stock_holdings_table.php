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
        Schema::create('stock_holdings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('ticker', 10);
            $table->integer('total_shares')->default(0);
            $table->decimal('average_price', 15, 2)->default(0);
            $table->decimal('current_price', 15, 2)->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'ticker']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_holdings');
    }
};
