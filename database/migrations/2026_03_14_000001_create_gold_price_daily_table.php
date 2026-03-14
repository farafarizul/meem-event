<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gold_price_daily', function (Blueprint $table) {
            $table->bigIncrements('gold_price_daily_id');
            $table->date('gold_price_date')->unique();
            $table->decimal('sell_price', 12, 2)->nullable();
            $table->decimal('buy_price', 12, 2)->nullable();
            $table->decimal('open_price', 12, 2)->nullable();
            $table->decimal('close_price', 12, 2)->nullable();
            $table->decimal('highest_price', 12, 2)->nullable();
            $table->decimal('lowest_price', 12, 2)->nullable();
            $table->longText('reason_from_ai')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gold_price_daily');
    }
};
