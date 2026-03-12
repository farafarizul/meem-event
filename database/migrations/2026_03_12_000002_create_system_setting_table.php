<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_setting', function (Blueprint $table) {
            $table->bigIncrements('system_setting_id');
            $table->string('setting_key')->unique();
            $table->string('setting_value')->nullable();
            $table->timestamps();
        });

        DB::table('system_setting')->insert([
            'setting_key'   => 'gold_price_sync_interval_minutes',
            'setting_value' => '5',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('system_setting');
    }
};
