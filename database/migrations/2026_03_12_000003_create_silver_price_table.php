<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('silver_price', function (Blueprint $table) {
            $table->bigIncrements('silver_price_id');
            $table->string('type')->nullable();
            $table->string('product')->nullable();
            $table->string('unit')->nullable();
            $table->string('currency')->nullable();
            $table->decimal('sell_price', 12, 2);
            $table->decimal('buy_price', 12, 2);
            $table->string('timezone')->nullable();
            $table->dateTime('last_updated');
            $table->index('last_updated');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('silver_price');
    }
};
