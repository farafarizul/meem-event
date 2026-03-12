<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('system_setting')->insert([
            'setting_key'   => 'silver_price_sync_interval_minutes',
            'setting_value' => '5',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('system_setting')
            ->where('setting_key', 'silver_price_sync_interval_minutes')
            ->delete();
    }
};
