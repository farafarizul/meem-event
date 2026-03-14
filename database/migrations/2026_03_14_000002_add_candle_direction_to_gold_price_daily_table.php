<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gold_price_daily', function (Blueprint $table) {
            $table->string('candle_direction')->nullable()->after('lowest_price');
        });
    }

    public function down(): void
    {
        Schema::table('gold_price_daily', function (Blueprint $table) {
            $table->dropColumn('candle_direction');
        });
    }
};
