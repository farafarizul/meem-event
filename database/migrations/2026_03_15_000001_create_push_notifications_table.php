<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('push_notifications', function (Blueprint $table) {
            $table->bigIncrements('push_notification_id');
            $table->string('title', 255);
            $table->text('message');
            $table->text('image_url')->nullable();
            $table->string('recipient_mode', 20)->default('all'); // all, selected
            $table->longText('selected_meem_codes')->nullable();  // JSON array of meem_code
            $table->text('additional_data_1')->nullable();
            $table->text('additional_data_2')->nullable();
            $table->text('additional_data_3')->nullable();
            $table->string('onesignal_app_id', 255)->nullable();
            $table->longText('onesignal_request_payload')->nullable(); // JSON payload sent to OneSignal
            $table->longText('onesignal_response')->nullable();        // raw response from OneSignal
            $table->string('onesignal_notification_id', 255)->nullable();
            $table->integer('total_recipient')->default(0);
            $table->string('send_status', 50)->default('failed'); // success, failed
            $table->text('error_message')->nullable();
            $table->string('created_by', 255)->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->nullable();

            $table->index('recipient_mode');
            $table->index('send_status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_notifications');
    }
};
