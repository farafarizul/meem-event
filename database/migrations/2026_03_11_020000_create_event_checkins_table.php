<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_checkins', function (Blueprint $table) {
            $table->id('event_checkin_id')->unique()->autoIncrement();
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('checked_in_at')->useCurrent();
            $table->timestamps();

            //$table->unique(['event_id', 'user_id']);
            $table->index('event_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_checkins');
    }
};
