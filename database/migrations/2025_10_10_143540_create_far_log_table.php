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
        Schema::create('far_log', function (Blueprint $table) {
            $table->increments('log_id')->unique('log_id');
            $table->string('user_id', 200)->default('1');
            $table->string('meem_code', 200)->nullable();
            $table->string('app_session', 200)->nullable();
            $table->string('log_category', 200)->nullable();
            $table->string('trail_module', 200)->nullable();
            $table->string('trail_method', 200)->nullable();
            $table->string('trail_operation', 200)->nullable();
            $table->string('meta_name', 200)->nullable();
            $table->string('meta_value', 200)->nullable();
            $table->text('log_data_json')->nullable();
            $table->string('create_dttm')->default('2024-07-23 05:23:08');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('far_log');
    }
};
